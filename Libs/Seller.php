<?php
namespace BibianBidAccount\Libs;
class Seller
{   
    private $sellerID;//賣家帳號

    private $product;//競標中賣場

    private $account;//class Account

    public function __construct($sellerID)
    {
        $this->sellerID = $sellerID;
        $this->account = new account($this->sellerID);
        // if ($this->account->isAssignAlready()){
        //     $this->account->loadInfoFromDB($this->sellerID);//抓DB裡面的資料來更新這次用的Seller 
        // } else{
        //     $this->createSeller();
        // }

    }


    private function createSeller(){

        $this->account->setAccountCounter(0);
        $this->account->renewShuffleAccountList();
        $this->shiftToNextAccount();

        $this->product = array();
        $this->account->saveInfoToDB();
    }

   


    public function returnSellerID(){
        return $this->sellerID;
    }

    public function returnYahooAccount(){
        return $this->account->returnAccountNow();
    }



    public function returnProduct()
    {
        return $this->product;
    }

    public function addProduct($product)
    {
        $this->product = array_push($product);
    }

    public function shiftToNextAccount(){
        $this->account->shiftToNextAccount();
    }

}
