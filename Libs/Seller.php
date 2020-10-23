<?php
namespace BibianBidAccount\Libs;
class Seller
{   
    private $Seller_ID;//賣家帳號

    private $Product;//競標中賣場

    public $AccountCounter;//指派帳號計數器

    private $YahooAccount;//當前指派Yahoo帳號

    private $AccountList;

    public function __construct($Seller_ID)
    {
        if ($this->isSellerExist()){
            $this->loadInfoFromDB($Seller_ID);//抓DB裡面的資料來更新這次用的Seller 
        } else{
            $this->createSeller($Seller_ID);
        }

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

    public function isSellerExist(){
        $take_SellerID = "SELECT * FROM `Seller_list` WHERE `Seller_ID`= $Seller_ID";   
        $result = $connect->query($take_SellerID);
        if (is_null($result)){
            return false;
        } else {
            return true;
        }
    }

    private function createSeller($Seller_ID){
        $this->Seller_ID = $Seller_ID;
        //AccountList不應該寫在Class裡面，找時間拿出去
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
        $this->saveInfoToDB();
    }

    private function saveInfoToDB(){

    }

    private function loadInfoFromDB($Seller_ID){

    }

    public function returnSellerID(){
        return $this->Seller_ID;
    }
}
