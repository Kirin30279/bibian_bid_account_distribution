<?PHP
namespace BibianBidAccount\Libs;

use mysqli;

class Account
{

    public $quantityOfAccount;//總帳號個數
    
    private $AccountNow;
   
    private $AccountFirst;
    //private $AccountTerm;

    //private $isTermExpired;
    private $AccountNext;

    private $connect;

    private $AccountList;
    
    private $sellerID;

    private $counter;//指派帳號用計數器

    public function __construct($sellerID)
    {   
        $this->sellerID = $sellerID;
        $this->connect = new mysqli('localhost','root','','bid_account');
        $this->loadInfoFromDB();
        $this->renewCountAccountNumber();
    }

    public function renewShuffleAccountList()
    {
        include "YahooAccount.php";
        echo "取得新的帳號列表(隨機排列)，計數器設為0"."<br>";
        shuffle($AccountList);
        $this->AccountList = $AccountList;
        $this->setAccountCounter(0);
        $this->AccountNow = $this->AccountList["$this->counter"];
        
    }
    public function setAccountFirst($account){
        $this->AccountFirst = $account;
    }
    public function renewCountAccountNumber()
    {
        $this->quantityOfAccount = count($this->AccountList);
    }

    public function shiftToNextAccount()
    { 
        $this->counter += 1;
        $selectNum = $this->counter % $this->quantityOfAccount;
        $this->AccountNext = $this->AccountList["$selectNum"];
        echo "切換到下一個帳號:"."$this->AccountNext"."<BR>"; 
        if($this->AccountNext === $this->AccountFirst){
            echo "輪替的下一個帳號重複，再往下繼續輪"."<BR>";
            $this->counter += 1;
            $selectNum = $this->counter % $this->quantityOfAccount;
            $this->AccountNext = $this->AccountList["$selectNum"];
        } 
        $this->AccountNow = $this->AccountNext;
        echo $this->sellerID."的新使用帳號為：「".$this->AccountNow."」"."<br>";        
    }

    public function returnAccountList()
    {
        return $this->AccountList;
    }

    public function returnAccountNow(){
        return $this->AccountNow;
    }

    public function setAccountCounter($number){
        $this->counter = $number ; 
    }

    public function saveInfoToDB(){
        $listForSave = implode(',', $this->AccountList);
        $stmt = $this->connect->prepare("INSERT INTO Seller_list(sellerID, YahooAccountNow, AccountCounter, AccountList) 
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                YahooAccountNow = VALUES(YahooAccountNow), 
                AccountCounter = VALUES(AccountCounter)"); 
        $stmt->bind_param("ssis", 
        $this->sellerID, $this->AccountNow, $this->counter, $listForSave);
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
            $this->AccountNow = $row['YahooAccountNow'];
            $this->counter = $row['AccountCounter'];
            $this->AccountList = explode(',', $row['AccountList']);
        }
    }

}