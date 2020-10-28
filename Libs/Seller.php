<?php
namespace BibianBidAccount\Libs;
use mysqli;
class Seller
{   
    private $sellerID;//賣家帳號

    private $product;//競標中賣場

    public $accountCounter;//指派帳號計數器

    private $yahooAccount;//當前指派Yahoo帳號

    private $connect;//DB的連接

    public function __construct($sellerID)
    {
        $this->sellerID = $sellerID;
        $this->connect = new mysqli('localhost','root','','bid_account');
        if ($this->isSellerExist($this->sellerID)){
            $this->loadInfoFromDB($this->sellerID);//抓DB裡面的資料來更新這次用的Seller 
        } else{
            $this->createSeller();
        }

    }

    private function assignAccount()
    {
        $Account = new Account($this->sellerID);
        //$Account->getNewAccountShuffle();
        //$Account->switchToNextAccount();
        $Account->countAccountNumber();
        $numOfAccount = $Account->AccoutnNumber;
        $this->yahooAccount = $Account->AccountList[($this->accountCounter)%$numOfAccount];
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

    public function isSellerExist($sellerID){
        $take_SellerID = "SELECT * FROM `Seller_list` WHERE `sellerID`= '$sellerID'";
  
        $result = $this->connect->query($take_SellerID);

        if (empty($result->num_rows)){
            echo "該Seller為第一次被投標，等等需指定新帳號"."<BR>";
            return false;
        } else {
            return true;
        }
    }

    private function createSeller(){

  
        $this->accountCounter = 0;
        $this->assignAccount();

        $this->product = array();
        $this->saveInfoToDB();
    }

    private function saveInfoToDB(){
        $saveSellerToSQL="INSERT INTO 
        Seller_list(sellerID, YahooAccountNow, AccountCounter) 
        VALUES ('$this->sellerID', '$this->yahooAccount', '$this->accountCounter')
        ON DUPLICATE KEY UPDATE YahooAccountNow='$this->yahooAccount', AccountCounter='$this->accountCounter'";
        $this->connect->query($saveSellerToSQL);
    }

    private function loadInfoFromDB($sellerID){
        $take_sellerID = "SELECT * FROM `Seller_list` WHERE `sellerID`= '$sellerID'";   
        $result = $this->connect->query($take_sellerID);
        $row = $result -> fetch_array(MYSQLI_BOTH);
        $this->yahooAccount = $row['YahooAccountNow'];
        $this->accountCounter = $row['AccountCounter'];
    }

    public function returnSellerID(){
        return $this->sellerID;
    }

    public function returnYahooAccount(){
        
        return $this->yahooAccount;
    }

    public function changeToNextAccount()
    {
        $this->assignAccount();
        $this->saveInfoToDB();
        echo '賣家'.$this->sellerID.'預設帳號重新指派為:'.$this->yahooAccount.'<br>';
    }

}
