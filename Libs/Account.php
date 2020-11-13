<?PHP
namespace BibianBidAccount\Libs;

use mysqli;

class Account
{

    public $quantityOfSellerAccount;//總帳號個數
    
    private $accountNow;
   
    //private $accountFirst;
    //private $AccountTerm;

    private $accountUsedArray;

    //private $isTermExpired;
    private $accountNext;

    private $connect;

    private $accountList;
    
    private $sellerID;

    private $sellerCounter;//指派帳號用計數器，需要根據賣家帳號的表格來指派帳號

    private $accountQuantity;

    public function __construct($sellerID)
    {   
        $this->sellerID = $sellerID;
        //$this->connect = new mysqli('localhost','root','','bid_account');
        $this->connect = new mysqli('192.168.0.151','pt_wuser','pt_wuser1234','pt_develop');
        $this->loadInfoFromDB();
        $this->renewCountAccountQuantity();
    }

    public function renewShuffleAccountList()
    {   //一般來講本次下單是新賣家才會使用到
        include "YahooAccount.php";
        echo "取得新的帳號列表(隨機排列)，計數器設為0"."<br>";
        shuffle($accountList);
        $this->accountList = $accountList;
        $this->setSellerAccountCounter(0);
        $this->accountNow = $this->accountList["$this->sellerCounter"];
        $this->saveInfoToDB();
        
    }
    // public function setaccountFirst($account){
    //     $this->accountFirst = $account;
    // }

    public function setAccountUsedArray($accountArray){
        $this->accountUsedArray = $accountArray;
        $this->quantityOfProductAccount = count($accountArray);
    }
    
    public function renewCountAccountQuantity()
    {
        $this->quantityOfSellerAccount = count($this->accountList);
    }

    public function shiftToNextAccount()
    { 
        $this->sellerCounter += 1;
        $selectNum = ($this->sellerCounter + $this->quantityOfProductAccount) % $this->quantityOfSellerAccount;
        $this->accountNext = $this->accountList["$selectNum"];
        echo "切換到下一個帳號:"."$this->accountNext"."<BR>"; 
        while (in_array($this->accountNext, $this->accountUsedArray)) {
            echo "輪替的帳號".$this->accountNext."於該賣場已被使用，再往下繼續輪"."<BR>";
            $selectNum = ($selectNum + 1) % $this->quantityOfSellerAccount;
            $this->accountNext = $this->accountList["$selectNum"];
        }
        $this->accountNow = $this->accountNext;
        echo $this->sellerID."的新使用帳號為：「".$this->accountNow."」"."<br>";        
    }

    public function returnAccountList()
    {
        return $this->accountList;
    }

    public function returnAccountNow(){
        return $this->accountNow;
    }

    public function returnNewAccount(){
        if($this->quantityOfProductAccount != 0){
            $this->shiftToNextAccount();
        }
        return $this->accountNow;
    }

    public function setSellerAccountCounter($number){
        $this->sellerCounter = $number ; 
    }

    private function getAccountBySellerCounter(){
        $selectNum = $this->sellerCounter % $this->quantityOfSellerAccount;
        return $this->accountList["$selectNum"];
    }

    public function saveInfoToDB(){
        $listForSave = implode(',', $this->accountList);
        $defaultAccount = $this->getAccountBySellerCounter();
        $stmt = $this->connect->prepare("INSERT INTO Seller_list(sellerID, yahooAccountNow, accountCounter, accountList) 
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                yahooAccountNow = VALUES(yahooAccountNow), 
                accountCounter = VALUES(accountCounter)"); 
        $stmt->bind_param("ssis", 
        $this->sellerID, $defaultAccount, $this->sellerCounter, $listForSave);
        $stmt->execute();
        echo "<br>";
    }


    public function loadInfoFromDB(){
        $stmt = $this->connect->prepare("SELECT * FROM `Seller_list` WHERE `sellerID`= ?");
        $stmt->bind_param("s",$this->sellerID);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows===0){
            echo "該賣家沒有指定帳號，取得新帳號列表"."<br>";
            $this->renewShuffleAccountList();
        } else{
            $row = $result -> fetch_array(MYSQLI_BOTH);
            $this->accountNow = $row['yahooAccountNow'];
            $this->sellerCounter = $row['accountCounter'];
            $this->accountList = explode(',', $row['accountList']);
        }
    }

}