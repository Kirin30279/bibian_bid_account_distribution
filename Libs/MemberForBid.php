<?php
namespace BibianBidAccount\Libs;
class MemberForBid
{
    private $memberID;

    private $usedYahooAccount;

    private $product;

    private $bidPrice;

    private $firstBidingTime;

    private $isMemberExist;

    public function __construct($memberID, $productID)
    {
        $this->loadInfoFromDB($memberID, $productID);
        if(!($this->isMemberExist)){
            $this->createMember($memberID, $productID);
        }
    }

    public function doBid($Product_Number)
    {

    }

    public function setBidPrice($bidPrice){
        $this->bid_price = $bidPrice;
    }


    private function loadInfoFromDB($memberID, $productID){
        $take_memberId = "SELECT * FROM `bider_list` WHERE `memberID`= $memberID AND `productID` = $productID";   
        $result = $connect->query($take_memberId);
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

    private function createMember($memberID){
        $this->memberID = $memberID;
        $this->product = $result['product'];
        $this->firstBidingTime = $result['firstBidingTime'];
    }

}




?>