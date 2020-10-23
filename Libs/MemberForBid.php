<?php
namespace BibianBidAccount\Libs;
class MemberForBid
{
    private $memberID;

    private $usedYahooAccount;

    private $product;

    private $bidPrice;

    private $biding_time;

    public function __construct($member_ID, $Seller_ID)
    {
        if ($this->isProductExist()){
            $this->loadInfoProductFromDB($Product_ID);
        } else{
            $this->createProduct($Product_id, $Seller_ID);
            $this->saveInfoToDB();
        }

    }

    public function doBid($Product_Number)
    {

    }

    public function setBidPrice($bidPrice){
        $this->bid_price = $bidPrice;
    }

    


}




?>