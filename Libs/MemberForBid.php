<?php

namespace BibianBidAccount\Libs;
use mysqli;
use BibianBidAccount\Libs\Seller;
use BibianBidAccount\Libs\Product;

class MemberForBid
{
    private $memberID;

    private $usedYahooAccount;

    private $productID;

    private $bidPrice;

    private $bidStatus;

    private $firstBidingTime;

    private $renewBidingTime;

    private $sellerID;

    private $isMemberExist;

    private $connect;//DB的連接

    public $testSucess; //拿來測試為投標成功或失敗用的參數，正式版要刪除。

    public $bidFailTime;//嘗試超過3次以上則失敗

    public $bidSucess;//出價成功則外面迴圈不再出價

    public function __construct($memberID, $productID)
    {
        $this->connect = new mysqli('localhost','root','','bid_account');
        $this->loadInfoFromDB($memberID, $productID);
        if(!($this->isMemberExist)){
            $this->createMember($memberID, $productID);
        }
        $this->bidFailTime = 0 ;
        $this->bidSucess = false ;
    }

    public function setSellerID($sellerID){
        $this->sellerID = $sellerID;
    }

    public function setBidPrice($bidPrice){
        $this->bidPrice = $bidPrice;
    }

    public function setBidStatus($bidStatus){
        $this->bidStatus = $bidStatus;
    }

    public function echoYahooAccuount(){
        echo "目前使用的帳戶是：".$this->usedYahooAccount;
    }

    public function doBid($productID, $bidPrice)
    {

        if (!($this->isMemberHasYahooAccuont())){
            echo "沒有Y拍帳號，取得新帳號";
            $this->firstBidingTime = time();
            $this->getYahooAccount();
        } 
        $this->bidPrice = $bidPrice;
        $this->productID = $productID;
        $this->renewBidingTime = time();
        if ($this->testSucess){
            echo "投標成功".'<Br>';//成功後把投標資料寫入DB
            $this->bidSucess = true ;
            $bidInsertSQL = "INSERT INTO 
            bidder_list(memberID, usedYahooAccount, productID, bidPrice, sellerID, firstBidingTime, renewBidingTime, bidStatus) 
            VALUES ($this->memberID, '$this->usedYahooAccount', '$this->productID', $this->bidPrice, '$this->sellerID', $this->firstBidingTime, $this->renewBidingTime, $this->bidStatus)
            ON DUPLICATE KEY UPDATE usedYahooAccount='$this->usedYahooAccount', bidPrice=$this->bidPrice, renewBidingTime=$this->renewBidingTime, bidStatus=$this->bidStatus";
            $this->connect->query($bidInsertSQL);
            echo '這次的SQL指令:'.$bidInsertSQL.'<Br>';
        } else{
            $this->bidFailTime += 1 ;
            echo "投標失敗，換帳號".'<Br>';
            $seller = new Seller($this->sellerID);         
            $seller->changeToNextAccount();
            $this->usedYahooAccount = $seller->returnYahooAccount();

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
            $this->productID = $row['productID'];
            $this->firstBidingTime = $row['firstBidingTime'];
        }

    }

    private function createMember($memberID, $productID){
        $this->memberID = $memberID;
        $this->productID = $productID;
    }

    private function getYahooAccount(){
        $seller = new Seller($this->sellerID); 

        $this->usedYahooAccount = $seller->returnYahooAccount();
    }

    private function isMemberHasYahooAccuont(){
        if(isset($this->usedYahooAccount)){
            return true;
        } else{
            return false;
        }
    }


}



?>