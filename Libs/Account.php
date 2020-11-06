<?PHP
namespace BibianBidAccount\Libs;

use mysqli;

class Account
{

    public $quantityOfAccount;//總帳號個數
    
    private $accountNow;
   
    private $accountFirst;
    //private $AccountTerm;

    //private $isTermExpired;
    private $accountNext;

    private $connect;

    private $accountList;
    
    private $sellerID;

    private $counter;//指派帳號用計數器

    public function __construct($sellerID)
    {   
        $this->sellerID = $sellerID;
        //$this->connect = new mysqli('localhost','root','','bid_account');
        $this->connect = new mysqli('192.168.0.151','pt_wuser','pt_wuser1234','pt_develop');
        $this->loadInfoFromDB();
        $this->renewCountAccountNumber();
    }

    public function renewShuffleAccountList()
    {
        include "YahooAccount.php";
        echo "取得新的帳號列表(隨機排列)，計數器設為0"."<br>";
        shuffle($accountList);
        $this->accountList = $accountList;
        $this->setAccountCounter(0);
        $this->accountNow = $this->accountList["$this->counter"];
        
    }
    public function setaccountFirst($account){
        $this->accountFirst = $account;
    }
    public function renewCountAccountNumber()
    {
        $this->quantityOfAccount = count($this->accountList);
    }

    public function shiftToNextAccount()
    { 
        $this->counter += 1;
        $selectNum = $this->counter % $this->quantityOfAccount;
        $this->accountNext = $this->accountList["$selectNum"];
        echo "切換到下一個帳號:"."$this->accountNext"."<BR>"; 
        if($this->accountNext === $this->accountFirst){
            echo "輪替的下一個帳號重複，再往下繼續輪"."<BR>";
            $this->counter += 1;
            $selectNum = $this->counter % $this->quantityOfAccount;
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

    public function setAccountCounter($number){
        $this->counter = $number ; 
    }

    public function saveInfoToDB(){
        $listForSave = implode(',', $this->accountList);
        $stmt = $this->connect->prepare("INSERT INTO Seller_list(sellerID, yahooAccountNow, accountCounter, accountList) 
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                yahooAccountNow = VALUES(yahooAccountNow), 
                accountCounter = VALUES(accountCounter)"); 
        $stmt->bind_param("ssis", 
        $this->sellerID, $this->accountNow, $this->counter, $listForSave);
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
            $this->counter = $row['accountCounter'];
            $this->accountList = explode(',', $row['accountList']);
        }
    }

}