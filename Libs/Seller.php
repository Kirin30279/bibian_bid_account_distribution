<?php
namespace BibianBidAccount\Libs;
class Seller
{   
    private $_SellerAccount;//賣家帳號

    private $_YahooAccount;//當前指派Yahoo帳號

    private $_Product;//競標中賣場

    private $_AccountCounter;//指派帳號計數器

    public function __construct($SellerAccount)
    {
        $this->$_SellerAccount = $SellerAccount;
        $this->_assignAccount();
        $this->$_AccountCounter = 0;
        $this->_Product = array();
    }

    private function _assignAccount()
    {
        $this->$_YahooAccount = $AccountList[$this->_AccountCounter];
        $this->$_AccountCounter += 1 ;
    }

    public function returnProduct()
    {
        return $this->_Product;
    }

    public function addProduct($Product)
    {
        $this->_Product = array_push($Product);
    }


}
