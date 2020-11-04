<?php
namespace BibianBidAccount\Libs;

use BibianBidAccount\Libs\Account;
use mysqli;
class MemberForBid
{
    private $memberID;//會員編號

    private $usedYahooAccount;//目前指派的Y拍帳號

    private $productID;//這次出價的賣場編號

    private $bidPrice;//這次出價的價格

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


    public function __construct($memberID, $productID)
    {
        $this->connect = new mysqli('localhost','root','','bid_account');
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

        if (!($this->isMemberHasYahooAccuont())){
            echo "本使用者該訂單沒有指定Y拍帳號，取得新帳號"."<br>"."<br>";
            $this->firstBidingTime = time();
            $this->getYahooAccount();
        } 
        $this->Account->setAccountFirst($this->usedYahooAccount);
        $this->renewBidingTime = time();
        while($this->bidTime<4 && $this->bidSucess===false){
            echo "【投標】開始投標，本次投標指定帳號為：「".$this->usedYahooAccount."」<br>";
            $this->autoBid($this->bidStatus);//測試用的函數，傳入值為成功或失敗的順序。
            if ($this->testSucess){
                echo "【投標】※※※投標成功※※※".'<Br>'."<br>"."<br>";//成功後把投標資料寫入DB
                $this->bidSucess = true ;
                $bidInsertSQL = "INSERT INTO 
                bidder_list(memberID, usedYahooAccount, productID, bidPrice, sellerID, firstBidingTime, renewBidingTime, bidStatus) 
                VALUES ($this->memberID, '$this->usedYahooAccount', '$this->productID', $this->bidPrice, '$this->sellerID', $this->firstBidingTime, $this->renewBidingTime, $this->finalBidOrImmediateBid)
                ON DUPLICATE KEY UPDATE usedYahooAccount='$this->usedYahooAccount', bidPrice=$this->bidPrice, renewBidingTime=$this->renewBidingTime, bidStatus=$this->finalBidOrImmediateBid";
                $this->connect->query($bidInsertSQL);
            } else{
                $this->bidTime += 1 ;
                echo "【投標】。。投標失敗，換帳號。。".'<Br>'.'<Br>'.'<Br>';
                $this->Account->shiftToNextAccount();//換下一個輪替用的帳號
                $this->usedYahooAccount = $this->Account->returnAccountNow();
    
            }
        }
        if($this->bidTime===4){
            echo "投標已達3次失敗，無法投標第4次，輪替該賣家指定帳號後，退出投標流程"."<br>";
        }
        if($this->bidTime>=2 or !($this->isMemberExist)){
            $this->Account->saveInfoToDB();    
        }
    }

    private function loadInfoFromDB($memberID, $productID){
        $take_memberId = "SELECT * FROM `bidder_list` WHERE `memberID`= $memberID AND `productID` = '$productID'";   
        $result = $this->connect->query($take_memberId);

        if (empty($result->num_rows)){
            $this->isMemberExist = false;
        } else {
            $row = $result -> fetch_array(MYSQLI_BOTH);
            $this->isMemberExist = true;
            $this->memberID = $row['memberID'];
            $this->usedYahooAccount = $row['usedYahooAccount'];
            $this->bidPrice = $row['bidPrice'];
            $this->productID = $row['productID'];
            $this->firstBidingTime = $row['firstBidingTime'];
        }

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
            
            echo '<span style="color:#FF0000;">※恭喜，出價成功※!</span>'."<br>";
        } else{
            echo '<span style="color:#FF0000;">※很抱歉，出價失敗(連續失敗三次)，請回到商品頁面重新投標。※</span>'."<br>";
        }
    }

    private function describeBidTime(){
        echo "本次投標情況：";
        switch ($this->bidTime) {
            case '1':
                echo "一次投標即成功"."<br>";
                break;
            case '2':
                echo "一次投標失敗後，再換帳號投標成功"."<br>";
                break;    
            case '3':
                echo "二次投標失敗後，再換帳號投標成功"."<br>";
                break;     
            case '4':
                echo "連續三次投標失敗，投標未成功"."<br>";
                break;  
        }
    }

    public function showBidInfo(){//程式測試演示用
        echo "投標者會員編號：".$this->memberID."<br>";
        echo "賣家ID：".$this->sellerID."<br>";
        echo $this->sellerID."當前指派的Y拍帳號：".$this->usedYahooAccount."<br>";
        echo "賣場編號：".$this->productID."<br>";
        echo "出價價格：".$this->bidPrice."円<br>";
        echo "嘗試投標次數：".$this->bidTime."<br>";
        $this->describeBidTime();
        $this->showSucessORNot();
  
    }

}



?>