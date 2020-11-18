<?php
namespace BibianBidAccount\Libs;

use BibianBidAccount\Libs\Account;
use BibianBidAccount\Libs\MemberLastTime;
use mysqli;

class MemberForBid
{
    private $memberID;//會員編號

    private $OldMember;

    private $lastTimeHighestMember;

    private $usedYahooAccount;//目前指派的Y拍帳號

    private $productID;//這次出價的賣場編號

    private $bidPrice;//這次出價的價格

    private $priceNowHighest;

    private $finalBidOrImmediateBid;//這次出價的狀態(立即or最後出)

    private $firstBidingTime;//該使用者對該賣場第一次出價的時間

    private $renewBidingTime;//該使用者最後一次出價的時間

    private $sellerID;//賣家ID

    private $seller;

    //判斷該使用者是否已經對該賣場投標過，若有，則先用原本使用投標帳號，
    //↓除非投標失敗，才會指定到該賣家新帳號
    private $isMemberExist;
    
    private $connect;//DB的連接

    public $testSucess; //拿來測試為投標成功或失敗用的參數，正式版要刪除。

    public $bidTime;//嘗試次數，超過3次則失敗

    public $bidSucess;//出價成功則外面迴圈不再出價

    public $bidStatus;//測試專用的參數，3x1的Array

    private $highestNow;//是否為本標單最高投標者(要不要再加價上去？)

    private $productNowPrice;

    private $productLastTimePrice;
        
    public $priceBeenExceed;//出價被超過

    //public $needHigherPrice;//增額不足
    
    private $usedYahooAccountArray;//用來儲存該賣場有多少比比昂的Y拍帳號被調用

    private $bidResult;

    private $finalDisplayStatus;

    public function __construct($memberID, $productID)
    {
        $this->connect = new mysqli('192.168.0.151','pt_wuser','pt_wuser1234','pt_develop');
        $this->loadInfoFromDB($memberID, $productID);
        //讀取DB裡面該使用者對該賣場的投標資訊，若無，則isMemberExist屬性指定為False
        if(!($this->isMemberExist)){//未投標過此賣場
            $this->createNewBidder($memberID, $productID);
        } else{
            echo "本使用者之前已投標過本訂單"."<br>";
            echo "↓↓↓↓↓↓↓↓↓↓上次投標資訊↓↓↓↓↓↓↓↓↓↓"."<br>";
            echo "上次使用的yahoo帳戶為：".$this->usedYahooAccount."<br>";
            echo "上次投標金額為：".$this->bidPrice."円<br>";
            echo "↑↑↑↑↑↑↑↑↑↑上次投標資訊↑↑↑↑↑↑↑↑↑↑"."<br>"."<br>"."<br>";
        }
        $this->bidTime = 1;
        $this->bidSucess = false ;//投標成功後會改成True
    }

    public function setBidStatus($array){
        $this->bidStatus = $array;
    }

    public function setAccountForSeller($sellerID){
        $this->sellerID = $sellerID;
        $this->Account = new Account($this->sellerID); 
        $this->Account->setAccountUsedArray($this->usedYahooAccountArray);
    }

    public function setBidPrice($bidPrice){
        $this->bidPrice = $bidPrice;
    }

    public function setFinalOrImmediate($finalBidOrImmediateBid){
        $this->finalBidOrImmediateBid = $finalBidOrImmediateBid;
    }

    public function echoYahooAccuount(){
        echo "目前使用的帳戶是：".$this->usedYahooAccount."<BR>";
    }

    private function autoBid($StatusArray){
        $this->testSucess = $StatusArray["$this->bidTime"];
    }

    public function doBid()
    {
        $this->Account->setAccountUsedArray($this->usedYahooAccountArray);//這是為了防止輪替過程又輪到最一開始失敗的那個帳號
        if (!($this->isMemberHasYahooAccuont())){
            echo "本使用者該訂單沒有指定Y拍帳號，取得新帳號"."<br>"."<br>";
            $this->firstBidingTime = date("Y-m-d H:i:s");
            $this->usedYahooAccount = $this->Account->returnNewAccount();
        } 
        
        while($this->bidTime<4 && $this->bidSucess===false){
            echo "【投標】開始投標，本次投標指定帳號為：「".$this->usedYahooAccount."」<br>";
            $this->autoBid($this->bidStatus);//測試用的函數，傳入值為成功或失敗的順序。
            if ($this->testSucess){
                $this->renewBidingTime = date("Y-m-d H:i:s");
                echo "【投標】※※※帳號可正常投標※※※".'<Br>'."<br>"."<br>";//成功後把投標資料寫入DB
                $this->bidSucess = true ;
                $this->compareWithOtherBidder();//更新商品價格(根據增額規則更新)
                $this->actByBidResult();
                $this->saveInfoToDB();//投標資訊寫入DB
                $this->saveBidHistoryToDB();//入札履歷寫入DB

            } else{
                $this->renewBidingTime = date("Y-m-d H:i:s");
                $this->saveBidHistoryToDB();//入札履歷寫入DB
                $this->bidTime += 1 ;
                $this->Account->addCounterOfSeller();
                echo "【投標】。。投標失敗，換帳號。。".'<Br>'.'<Br>'.'<Br>';
                $this->Account->shiftToNextAccount();//換下一個輪替用的帳號
                $this->Account->sellerDefaultCounter += 1 ;
                $this->usedYahooAccount = $this->Account->returnAccountNow();
                
            }
        }
        if($this->bidTime>3){
            echo "投標已達3次失敗，無法投標第4次，輪替該賣家指定帳號後，退出投標流程"."<br>";
            $this->finalDisplayStatus = 'fail';
        }
        if($this->bidTime>=2 or !($this->isMemberExist)){
            //投標次數兩次以上，表示賣家預設帳號有改變
            $this->Account->saveInfoToDB();    
        }
    }

    // private function judgeHighestOrNot(){
    //     $this->highestNow = true;
    // }

    // private function increaseProductNowPrice(){
    //     $selectPriceNow = "SELECT * FROM `product_list` WHERE `productID` = '$this->productID'";//抓出該商品的當前價格
    //     $productResult = $this->connect->query($selectPriceNow);     
    //     $productResult = $productResult -> fetch_array(MYSQLI_ASSOC);
    //     $nowPrice = $productResult['nowPrice'];
    //     echo "【增額】增額前價格為:".$nowPrice."円<br>";
    //     $nowPrice = $this->addIncreasingValue($nowPrice);//根據Y拍規則跳增額，跳完再寫回去
    //     echo "【增額】增額後，當前價格為:".$nowPrice."円<br>";
    //     $updatePriceNow = "UPDATE `product_list` SET `nowPrice` = $nowPrice WHERE `productID` = '$this->productID'";
    //     $this->connect->query($updatePriceNow);
    
    // }

    private function saveInfoToDB(){
        $stmt = $this->connect->prepare("INSERT INTO bidder_list(memberID, usedYahooAccount, productID, bidPrice, sellerID, firstBidingTime, renewBidingTime, bidStatus, highestNow) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
        usedYahooAccount = VALUES(usedYahooAccount), 
        bidPrice = VALUES(bidPrice), 
        renewBidingTime = VALUES(renewBidingTime), 
        bidStatus = VALUES(bidStatus),
        highestNow = VALUES(highestNow)");
        
        $this->checkHighestOrNot();
        $stmt->bind_param("ississsii", 
        $this->memberID, $this->usedYahooAccount, $this->productID, $this->bidPrice, $this->sellerID , $this->firstBidingTime, $this->renewBidingTime, $this->finalBidOrImmediateBid ,$this->highestNow);
        
        $stmt->execute();
    }
    private function checkHighestOrNot(){
        if ($this->finalDisplayStatus === 'success'){
            $this->highestNow = 1;
        }elseif($this->bidResult == 'thisBidderWasHighestButIllegal') {
            $this->highestNow = 1;
        }else{
            $this->highestNow = 0;
        }
    }

    private function saveBidHistoryToDB(){
        $stmt = $this->connect->prepare("INSERT INTO bid_histroy(memberID, usedYahooAccount, productID, bidPrice, BidingTime ,bidSuccess , memberBidTime)
        VALUES (?, ?, ?, ?, ?, ?, ?)");
        $bidSucess = intval($this->bidSucess);
        $stmt->bind_param("issisii", 
        $this->memberID, $this->usedYahooAccount, $this->productID, $this->bidPrice, $this->renewBidingTime, $bidSucess , $this->bidTime);

        $stmt->execute();
    }

    private function loadInfoFromDB($memberID, $productID){
        $stmt = $this->connect->prepare("SELECT * FROM `bidder_list` WHERE `productID` = ? ");
        $stmt->bind_param("s", $productID);
        $stmt->execute();
        $result = $stmt->get_result();
        $this->usedYahooAccountArray = array();
        if (!empty($result->num_rows)){
            $rows = $result -> fetch_all(MYSQLI_ASSOC);
            foreach ($rows as $row) {
                array_push($this->usedYahooAccountArray ,$row['usedYahooAccount']);
                if ($row['memberID'] == $memberID){
                    $this->setMemberAttribute($row);//當前投標者以前投標過，紀錄資訊
                }
            }
        } 
        if (!isset($this->usedYahooAccount)){
            $this->isMemberExist = false;//當前投標者至此還沒有分配帳號，表示以前他沒投標過該賣場
        }
    }

   private function setMemberAttribute($row){
        $this->isMemberExist = true;
        $this->memberID = $row['memberID'];
        $this->usedYahooAccount = $row['usedYahooAccount'];
        $this->bidPrice = $row['bidPrice'];
        $this->productID = $row['productID'];
        $this->firstBidingTime = $row['firstBidingTime'];
   }

    private function createNewBidder($memberID, $productID){
        $this->memberID = $memberID;
        $this->productID = $productID;
    }

    private function getYahooAccount(){//取得賣家當前指派帳號
        $this->usedYahooAccount = $this->Account->returnAccountNow();
    }

    private function isMemberHasYahooAccuont(){
        if(isset($this->usedYahooAccount)){
            return true;
        } else{
            return false;
        }
    }

    private function showSucessORNot(){
        if ($this->bidSucess === true){
            switch ($this->finalDisplayStatus) {
                case 'success':
                    echo '<span style="color:#FF0000;">※出價成功※</span>'."<br>";
                    echo '<span style="color:#FF0000;">您目前為最高出價者</span>'."<br>";
                    break;

                case 'fail':
                    echo '<span style="color:#FF0000;">出價被超過，請再加價</span>'."<br>";   
                    break;

                case 'increseInsufficient':
                    echo '<span style="color:#FF0000;">您的出價增額不足，請再加價</span>'."<br>";
                    break;
                
                default:
                    echo '意外狀況，請檢查CODE';
                    break;
            }
        } else{
            echo '<span style="color:#FF0000;">※很抱歉，出價失敗，請回到商品頁面重新投標。※</span>'."<br>";
        }
    }

    private function describeBidTime(){
        echo "帳號使用情況：";
        switch ($this->bidTime) {
            case '1':
                echo "選用第一個帳號即連上系統"."<br>";
                break;
            case '2':
                echo "第一個帳號調用失敗，使用第二個帳號連上系統"."<br>";
                break;    
            case '3':
                echo "第一、二個帳號調用失敗，使用第三個帳號連上系統"."<br>";
                break;     
            case '4':
                echo "連續三次帳號調用失敗，投標未成功"."<br>";
                break;  
        }
    }

    public function showBidInfo(){//程式測試演示用
        echo "投標者會員編號：".$this->memberID."<br>";
        echo "賣家ID：".$this->sellerID."<br>";
        echo "當前指派的Y拍帳號：".$this->usedYahooAccount."<br>";
        echo "賣場編號：".$this->productID."<br>";
        echo "出價價格：".$this->bidPrice."円<br>";
        $this->getPriceNow();
        echo "賣場當前價格：".$this->productNowPrice."円<br>";
        echo "嘗試投標次數：".$this->bidTime."<br>";
        // $this->showIncreaseOrNot();
        $this->describeBidTime();
        $this->showSucessORNot();
  
    }


    private function addIncreasingValue($price){
        switch ($price) {
            case $price<1000:
                $price += 10;        
                break;    
            case $price>=1000 && $price<5000:
                $price += 100; 
                break;
            case $price>=5000 && $price<10000:
                $price += 250; 
                break;
            case $price>=10000 && $price<50000:
                $price += 500; 
                break;
            case $price>50000:
                $price += 1000; 
                break;
        }
        return $price ;
}

    private function getPriceNow(){
        $selectProductList = "SELECT * FROM product_list WHERE `productID` = '$this->productID'";
        $resultProductList = $this->connect->query($selectProductList);
        $resultProductList = $resultProductList->fetch_all(MYSQLI_ASSOC);
        $this->productNowPrice = $resultProductList[0]['nowPrice'];
    }

    private function getProductLastTimePrice(){
        $takeProductLastTimePrice = "SELECT * FROM `product_list` WHERE `productID` = '$this->productID'";
        $productLastTime = $this->connect->query($takeProductLastTimePrice);
        $dataArray = $productLastTime->fetch_all(MYSQLI_ASSOC);
        $this->OldMember->bidPrice = $dataArray[0]['nowPrice'];
    }

    private function compareWithOtherBidder(){
        //這邊僅能做投標的狀態分析
        $this->OldMember = new MemberLastTime($this->productID);
        $this->getProductLastTimePrice();//取得當前賣場價格
        if($this->OldMember->thereIsNoLastBidder){
            $this->bidResult = 'firstOneInThisProduct';
        }else{
            echo "本商品存在最高投標者，確認是否為當前投標者．．．"."<BR>";
    
            if($this->OldMember->memberID  == $this->memberID){
                if($this->bidPrice >= $this->addIncreasingValue($this->OldMember->bidPrice)){
                    $this->bidResult = 'thisBidderWasHighest';
                }else{
                    $this->bidResult = 'thisBidderWasHighestButIllegal';
                }

            }else{
                echo "最高投標者與本次使用者不同人，進入競價環節。"."<BR>";               
                if($this->OldMember->bidPrice >= $this->bidPrice){
                    $this->bidResult = 'thisBidderLose';
                } elseif($this->bidPrice >= $this->addIncreasingValue($this->OldMember->bidPrice)){
                    $this->bidResult = 'thisBidderWin';
                } else{
                    $this->bidResult = 'thisBidderAlmostWin';//出價失敗，增額不足
                }
            }
        }


    }
    private function actByBidResult(){
            
        switch ($this->bidResult) {
            case 'firstOneInThisProduct':
                echo "本商品尚未有人投標，本次投標者成為最高投標者，無須比較。"."<BR>";
                //$this->highestNow = true;
                $priceSaveToProductList = $this->bidPrice;
                $updatePriceNow = "UPDATE `product_list` SET `nowPrice` = $priceSaveToProductList WHERE `productID` = '$this->productID'";
                $this->connect->query($updatePriceNow);
                $this->finalDisplayStatus = 'success';
                break;
            
            case 'thisBidderWasHighest':
                echo "本次投標者即為最高投標者，無須加價環節，直接更改該投標者金額上限。"."<BR>";
                //$this->highestNow = true;
                $priceSaveToProductList = $this->bidPrice;
                $this->finalDisplayStatus = 'success';
                break;

            case 'thisBidderWasHighestButIllegal':
                echo "上次為最高投標者，但本次出價增額不足"."<BR>";
                //$this->highestNow = true;
                $priceSaveToProductList = $this->OldMember->bidPrice;
                $this->finalDisplayStatus = 'increseInsufficient';
                break;
    
            case 'thisBidderLose':
                echo "【比較結果】出價失敗，增額不足。"."<BR>";
                $priceSaveToProductList = $this->bidPrice;//價格被更新為較小的那個
                //$this->highestNow = false;
                //$this->priceBeenExceed = true;
                $this->finalDisplayStatus = 'increseInsufficient';
                break;
    
            case 'thisBidderWin':
                echo "【比較結果】新投標者出價大於「當前最高價再增額一次」。"."<BR>";
                $priceSaveToProductList = $this->bidPrice;//價格被更新為最高價再增額   
                //$this->highestNow = true;
                $oldMemberID = $this->OldMember->memberID;
                $updateOldHiighest = "UPDATE `bidder_list` SET `highestNow` = 0  WHERE `productID` = '$this->productID' and `memberID` = '$oldMemberID'";
                $this->connect->query($updateOldHiighest);//將之前最高投標者的標記改為0。
                $this->finalDisplayStatus = 'success';
                break;
            
            case 'thisBidderAlmostWin':
                echo "【比較結果】新投標者出價大於當前最高價，但增額後不夠，故最高投標者不變，通知新投標者增額不足。"."<BR>";
                $priceSaveToProductList = $this->OldMember->bidPrice;
                //$this->highestNow = false;
                $this->finalDisplayStatus = 'increseInsufficient';
                break;
    
            default:
                echo "【比較結果】出現例外狀況，請洽程式撰寫者".'<br>';
                exit;
                break;
        }
        $this->productNowPrice = $priceSaveToProductList;
        if ($this->bidPrice>=$this->addIncreasingValue($this->OldMember->bidPrice)){
            $updatePriceNow = "UPDATE `product_list` SET `nowPrice` = $priceSaveToProductList WHERE `productID` = '$this->productID'";
            $this->connect->query($updatePriceNow);

        } else{
            $this->productNowPrice = $this->OldMember->bidPrice;
            $this->finalDisplayStatus = 'increseInsufficient';
            //$this->needHigherPrice = true;
            //$this->priceBeenExceed = false; 
        }
        
    }
}


?>