<?php
namespace BibianBidAccount\Libs;
class Product
{
    private $title;

    private $Seller_ID;

    private $price_now;

    private $starting_time;

    private $end_time;

    private $bibian_users;

    private $used_acconut;

    public function __construct($Product_ID, $Seller_ID)
    {
        if ($this->isProductExist()){
            $this->loadInfoProductFromDB($Product_ID);
        } else{
            $this->createProduct($Product_id, $Seller_ID);
            $this->saveInfoToDB();
        }

    }

    private function getPriceNow()
    {//取價函數，尚未實作
        $this->price_now = 50;
    }

    private function getStartingTime()
    {//取時函數，尚未實作
        $this->starting_time = time();
    }

    private function getEndTime()
    {//取時函數，尚未實作
        $this->end_time = time()+5000;
    }


    Public function getBibianUser($bibianUser)
    {
        $this->bibian_users = array_push($bibianUser);
    }
 
    public function assignAccount()
    {
        
    }

    public function isProductExist($Product_ID){
        $take_ProductID = "SELECT * FROM `Product_list` WHERE `Product_ID`= $Product_ID";   
        $result = $connect->query($take_ProductID);
        if (is_null($result)){
            return false;
        } else {
            return true;
        }
    }

    private function saveInfoToDB(){
        
    }

    private function createProduct($Product_id, $Seller_ID){
        $this->title = $Product_ID;
        $this->seller = $Seller_ID;
        $this->getPriceNow();
        $this->getStartingTime();
        $this->getEndTime();
        $this->bibian_users = array();
    }

}
