<?php
namespace BibianBidAccount\Libs;
class Seller
{   
    private $sellerID;//賣家帳號

    private $product;//競標中賣場

    public $accountCounter;//指派帳號計數器

    private $yahooAccount;//當前指派Yahoo帳號

    private $accountList;

    public function __construct($sellerID)
    {
        if ($this->isSellerExist($sellerID)){
            $this->loadInfoFromDB($sellerID);//抓DB裡面的資料來更新這次用的Seller 
        } else{
            $this->createSeller($sellerID);
        }

    }

    private function _assignAccount()
    {
        $this->yahooAccount = $this->accountList[$this->accountCounter];
        $this->accountCounter += 1 ;
    }

    public function returnProduct()
    {
        return $this->product;
    }

    public function addProduct($product)
    {
        $this->product = array_push($product);
    }

    public function getAccountList($accountList)
    {
        $this->accountList = $accountList;
    }

    public function returnaccountCounter(){
        return $this->accountCounter;
    }

    public function isSellerExist(){
        $take_SellerID = "SELECT * FROM `Seller_list` WHERE `sellerID`= $sellerID";   
        $result = $connect->query($take_SellerID);
        if (is_null($result)){
            return false;
        } else {
            return true;
        }
    }

    private function createSeller($sellerID){
        $this->sellerID = $sellerID;
        //AccountList不應該寫在Class裡面，找時間拿出去
        $this->accountList  = array(
            '0' => '帳號0',
            '1' => '帳號1',
            '2' => '帳號2',
            '3' => '帳號3',
            '4' => '帳號4',
            '5' => '帳號5',
        );
        $this->_assignAccount();
        $this->accountCounter = 0;
        $this->product = array();
        $this->saveInfoToDB();
    }

    private function saveInfoToDB(){

    }

    private function loadInfoFromDB($sellerID){

    }

    public function returnSellerID(){
        return $this->sellerID;
    }
}
