<?php
namespace BibianBidAccount\Libs;

use BibianBidAccount\Libs\Account;
use BibianBidAccount\Libs\MemberLastTime;
use BibianBidAccount\Libs\DB\DataBaseHandler;

class MemberForBid
{
    private $memberID;

    private $OldMember;//之前投標的會員資料

    private $usedYahooAccount;//目前指派的Y拍帳號

    private $productID;

    private $bidPrice;

    private $finalBidOrImmediateBid;//立即or最後出

    private $firstBidingTime;//該使用者對該賣場第一次出價的時間

    private $renewBidingTime;//該使用者最後一次出價的時間

    private $sellerID;//賣家ID

    //判斷該使用者是否已經對該賣場投標過，若有，則先用原本使用投標帳號，
    //↓除非投標失敗，才會指定到該賣家新帳號
    private $isMemberExist;
    
    public $testSucess; //拿來測試為投標成功或失敗用的參數，正式版刪除。

    public $numOfBidTime;//當前嘗試次數，超過3次則失敗

    public $bidSuccess;//出價成功則外面迴圈不再出價

    public $bidStatus;//測試專用的參數，3x1的Array

    private $highestNow;//是否為本標單最高投標者
        
    private $usedYahooAccountArray;//該賣場有多少比比昂的Y拍帳號被調用

    private $bidResult;//儲存投標比較的結果

    private $finalDisplayStatus;//最後顯示結果的判斷依據

    private $DataBaseHandler;

    public function __construct($memberID, $productID){
        $this->DataBaseHandler = new DataBaseHandler();
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
        $this->numOfBidTime = 1;//代表這是第一次投標
        $this->bidSuccess = false ;//投標成功後會改成True
        $this->finalDisplayStatus = 'fail' ;//最後判斷的時候會更改這個狀態
    }
    
    private function isMemberHasYahooAccuont(){
        if(isset($this->usedYahooAccount)){
            return true;
        } else{
            return false;
        }
    }

    private function createNewBidder($memberID, $productID){
        $this->memberID = $memberID;
        $this->productID = $productID;
    }

    private function setMemberAttribute($row){
        $this->isMemberExist = true;
        $this->memberID = $row['memberID'];
        $this->usedYahooAccount = $row['usedYahooAccount'];
        $this->bidPrice = $row['bidPrice'];
        $this->productID = $row['productID'];
        $this->firstBidingTime = $row['firstBidingTime'];
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

    private function autoBid($StatusArray){
        $this->testSucess = $StatusArray["$this->numOfBidTime"];
    }

    public function doBid(){
        $this->Account->setAccountUsedArray($this->usedYahooAccountArray);//這是為了防止輪替過程又輪到最一開始失敗的那個帳號
        if (!($this->isMemberHasYahooAccuont())){
            echo "本使用者該訂單沒有指定Y拍帳號，取得新帳號"."<br>"."<br>";
            $this->firstBidingTime = date("Y-m-d H:i:s");
            $this->usedYahooAccount = $this->Account->returnNewAccount();
        } 
        
        while($this->numOfBidTime<4 && $this->bidSuccess===false){
            echo "【投標】開始投標，本次投標指定帳號為：「".$this->usedYahooAccount."」<br>";
            $this->autoBid($this->bidStatus);//測試用的函數，傳入值為成功或失敗的順序。
            if ($this->testSucess){
                $this->renewBidingTime = date("Y-m-d H:i:s");
                echo "【投標】※※※帳號可正常投標※※※".'<Br>'."<br>"."<br>";//成功後把投標資料寫入DB
                $this->bidSuccess = true ;
                $this->compareWithOtherBidder();//與之前最高出價者做比較
                $this->actByBidResult();//根據上面比較出來的結果動作
                $this->saveBidHistoryToDB();
                if($this->finalDisplayStatus == 'success'){
                    echo "【投標】投標成功，將投標資訊寫入DB"."<br>";
                    $this->saveInfoToDB();//投標資訊寫入DB
                }
                

            } else{
                $this->renewBidingTime = date("Y-m-d H:i:s");
                $this->saveBidHistoryToDB();
                $this->numOfBidTime += 1 ;
                $this->Account->addCounterOfSeller();
                echo "【投標】。。投標失敗，換帳號。。".'<Br>'.'<Br>'.'<Br>';
                $this->Account->shiftToNextAccount();//換下一個輪替用的帳號
                $this->Account->sellerDefaultCounter += 1 ;
                $this->usedYahooAccount = $this->Account->returnAccountNow();
                
            }
        }
        if($this->numOfBidTime>3){
            echo "投標已達3次失敗，無法投標第4次，輪替該賣家指定帳號後，退出投標流程"."<br>";
            $this->finalDisplayStatus = 'fail';
        }
        if($this->numOfBidTime>=2 or !($this->isMemberExist)){
            //投標次數兩次以上，表示賣家預設帳號有改變
            $this->Account->saveInfoToDB();//注意:這是對seller_list的儲存帳號列表做更動    
        }
    }

    private function compareWithOtherBidder(){
        //這邊僅能做投標的狀態分析
        $this->OldMember = new MemberLastTime($this->productID);
        $this->OldMember->bidPrice = $this->DataBaseHandler->returnPriceNow($this->productID);
        //$this->getProductLastTimePrice();//取得當前賣場價格
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
                $priceSaveToProductList = $this->bidPrice;
                $this->DataBaseHandler->updatePriceNow($this->productID, $priceSaveToProductList);
                $this->finalDisplayStatus = 'success';
                break;
            
            case 'thisBidderWasHighest':
                echo "本次投標者即為最高投標者，無須加價環節，直接更改該投標者金額上限。"."<BR>";
                $priceSaveToProductList = $this->bidPrice;
                $this->finalDisplayStatus = 'success';
                break;

            case 'thisBidderWasHighestButIllegal':
                echo "上次為最高投標者，但本次出價增額不足"."<BR>";
                $priceSaveToProductList = $this->OldMember->bidPrice;
                $this->finalDisplayStatus = 'increseInsufficient';
                break;
    
            case 'thisBidderLose':
                echo "【比較結果】出價失敗，增額不足。"."<BR>";
                $priceSaveToProductList = $this->bidPrice;//價格被更新為較小的那個
                $this->finalDisplayStatus = 'increseInsufficient';
                break;
    
            case 'thisBidderWin':
                echo "【比較結果】新投標者出價大於「當前最高價再增額一次」。"."<BR>";
                $priceSaveToProductList = $this->bidPrice;//價格被更新為最高價再增額   
                $this->DataBaseHandler->updateOldHighestLose($this->productID, $this->OldMember->memberID);
                $this->finalDisplayStatus = 'success';
                break;
            
            case 'thisBidderAlmostWin':
                echo "【比較結果】新投標者出價大於當前最高價，但增額後不夠，故最高投標者不變，通知新投標者增額不足。"."<BR>";
                $priceSaveToProductList = $this->OldMember->bidPrice;
                $this->finalDisplayStatus = 'increseInsufficient';
                break;
    
            default:
                echo "【比較結果】出現例外狀況，請洽程式撰寫者".'<br>';
                exit;
                break;
        }
        $this->productNowPrice = $priceSaveToProductList;
        if ($this->bidPrice>=$this->addIncreasingValue($this->OldMember->bidPrice)){
            $this->DataBaseHandler->updatePriceNow($this->productID, $priceSaveToProductList);
        } else{
            $this->productNowPrice = $this->OldMember->bidPrice;
            $this->finalDisplayStatus = 'increseInsufficient';
        }
        
    }

    private function saveInfoToDB(){
        $this->checkHighestOrNot();
        $dataArray = array(
            'memberID' => "$this->memberID",
            'usedYahooAccount' => "$this->usedYahooAccount",
            'productID' => "$this->productID",
            'bidPrice' => "$this->bidPrice",
            'sellerID' => "$this->sellerID",
            'firstBidingTime' => "$this->firstBidingTime",
            'renewBidingTime' => "$this->renewBidingTime",
            'bidStatus' => "$this->finalBidOrImmediateBid",
            'highestNow' => "$this->highestNow"
        );
        $this->DataBaseHandler->saveInfoToDB($dataArray);
    }

    private function loadInfoFromDB($memberID, $productID){
        $result = $this->DataBaseHandler->loadInfoFromDB($memberID, $productID);
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

    private function saveBidHistoryToDB(){
        $bidSuccess = $this->checkSuccessWithFinalStatus();
        $dataArray = array(
            'memberID' => "$this->memberID",
            'usedYahooAccount' => "$this->usedYahooAccount",
            'productID' => "$this->productID",
            'bidPrice' => "$this->bidPrice",
            'renewBidingTime' => "$this->renewBidingTime",
            'bidSuccess' => "$bidSuccess",
            'numOfBidTime' => "$this->numOfBidTime"
        );

        $this->DataBaseHandler->saveBidHistoryToDB($dataArray);
    }

    private function checkSuccessWithFinalStatus(){//確認是否出價成功的Fun.，放saveBidHistoryToDB中
        if ($this->finalDisplayStatus == 'success') {
            return 1;
        }else{
            return 0; 
        }
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

    public function sendBidInfoForAnnouncer(){
        $priceNow = $this->DataBaseHandler->returnPriceNow($this->productID);
        $bidInfo = array(
            'memberID' => "$this->memberID",
            'sellerID' => "$this->sellerID",
            'usedYahooAccount' => "$this->usedYahooAccount",
            'productID' => "$this->productID",
            'bidPrice' => "$this->bidPrice",
            'PriceNow' => "$priceNow",
            'bidSuccess' => "$this->bidSuccess",
            'finalDisplayStatus' => "$this->finalDisplayStatus",
            'bidTime' => "$this->numOfBidTime",
        );
        return $bidInfo;
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


}


?>