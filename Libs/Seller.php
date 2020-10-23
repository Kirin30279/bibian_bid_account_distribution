<?php
namespace BibianBidAccount\Libs;
class Seller
{   
    private $SellerAccount;//賣家帳號

    private $Product;//競標中賣場

    public $AccountCounter;//指派帳號計數器

    private $YahooAccount;//當前指派Yahoo帳號

    private $AccountList;

    public function __construct($SellerAccount)
    {
        $this->SellerAccount = $SellerAccount;
        $this->AccountList  = array(
            '0' => '帳號0',
            '1' => '帳號1',
            '2' => '帳號2',
            '3' => '帳號3',
            '4' => '帳號4',
            '5' => '帳號5',
        );
        $this->_assignAccount();
        $this->AccountCounter = 0;
        $this->Product = array();
    }

    private function _assignAccount()
    {
        $this->YahooAccount = $this->AccountList[$this->AccountCounter];
        $this->AccountCounter += 1 ;
    }

    public function returnProduct()
    {
        return $this->Product;
    }

    public function addProduct($Product)
    {
        $this->Product = array_push($Product);
    }

    public function getAccountList($AccountList)
    {
        $this->AccountList = $AccountList;
    }

    public function returnAccountCounter(){
        return $this->AccountCounter;
    }
}
