<?php

namespace BibianBidAccount\Libs;
use mysqli;
include('../config.php');
class MemberForBid
{
    private $memberID;

    private $usedYahooAccount;

    private $product;

    private $bidPrice;

    private $bidStatus;

    private $firstBidingTime;

    private $isMemberExist;

    private $connect;//DB的連接

    public function __construct($memberID, $productID)
    {
        $this->loadInfoFromDB($memberID, $productID);
        if(!($this->isMemberExist)){
            $this->createMember($memberID, $productID);
        }
        $this->connect = new mysqli('localhost','root','','Member_List');
    }

    public function setBidPrice($bidPrice){
        $this->bid_price = $bidPrice;
    }

    public function setBidStatus($bidStatus){
        $this->bidStatus = $bidStatus;
    }

        public function doBid()
        {
            if ($this->isYahooAccountExist()){
                return 0;
            } else{
                
            }
            //$bidInsertSQL = "INSERT INTO bider_list(memberID, usedYahooAccount, productID, bidPrice, Seller_ID, firstBidingTime, renewBidingTime) VALUES ($this->memberID, )";

        }

    private function loadInfoFromDB($memberID, $productID){
        $take_memberId = "SELECT * FROM `bider_list` WHERE `memberID`= $memberID AND `productID` = $productID";   
        $result = $this->connect->query($take_memberId);
        if (is_null($result)){
            $this->isMemberExist = false;
        } else {
            $this->isMemberExist = true;
            $this->memberID = $result['memberID'];
            $this->usedYahooAccount = $result['usedYahooAccount'];
            $this->product = $result['productID'];
            $this->firstBidingTime = $result['firstBidingTime'];
        }

    }

    private function createMember($memberID, $productID){
        $this->memberID = $memberID;
        $this->product = $productID;
    }

    private function getYahooAccount(){

    }

    private function isYahooAccountExist(){
        if(isset($this->usedYahooAccount)){
            return true;
        } else{
            return false;
        }
    }
}



?>