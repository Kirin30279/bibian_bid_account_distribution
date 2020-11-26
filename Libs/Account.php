<?PHP
namespace BibianBidAccount\Libs;

use BibianBidAccount\Libs\DB\DataBaseHandler;
use mysqli;

class Account
{

    public $quantityOfSellerAccount;//總帳號個數
    
    private $accountNow;
   
    private $accountUsedArray;

    private $accountNext;

    private $connect;

    private $accountList;
    
    private $sellerID;

    private $sellerCounter;//指派帳號用計數器，需要根據賣家帳號的表格來指派帳號

    public $sellerDefaultCounter;

    public function __construct($sellerID)
    {   
        $this->sellerID = $sellerID;
        $this->connect = new mysqli('192.168.0.151','pt_wuser','pt_wuser1234','pt_develop');
        $this->DataBaseHandler = new DataBaseHandler();
        $this->loadInfoFromDB();
        $this->renewCountAccountQuantity();
    }

    public function renewShuffleAccountList()
    {   //一般來講本次下單是新賣家才會使用到
        include "YahooAccount.php";
        echo "取得新的帳號列表(隨機排列)，計數器設為0"."<br>";
        shuffle($accountList);
        $this->accountList = $accountList;
        $this->renewCountAccountQuantity();
        $this->setSellerAccountCounter(0);

        $this->accountNow = $this->accountList["$this->sellerCounter"];
        $this->saveInfoToDB();
        
    }

    public function setAccountUsedArray($accountArray){
        $this->accountUsedArray = $accountArray;
        $this->quantityOfProductAccount = count($accountArray);
    }
    
    public function renewCountAccountQuantity()
    {
        $this->quantityOfSellerAccount = count($this->accountList);
    }

    public function addCounterOfSeller(){
        $this->sellerCounter += 1;
    }

    public function shiftToNextAccount()
    { 
        $this->sellerCounter += 1;
        $selectNum = ($this->sellerCounter + $this->quantityOfProductAccount) % $this->quantityOfSellerAccount;
        $this->accountNext = $this->accountList["$selectNum"];
        echo "切換到下一個帳號:"."$this->accountNext"."<BR>"; 
        while ($this->isAccountUsed($this->accountNext)) {
            $this->addCounterOfSeller();
            echo "輪替的帳號".$this->accountNext."於該賣場已被使用，再往下繼續輪"."<BR>";
            $selectNum = ($this->sellerCounter + $this->quantityOfProductAccount) % $this->quantityOfSellerAccount;
            $this->accountNext = $this->accountList["$selectNum"];
        }
        $this->accountNow = $this->accountNext;
        echo $this->sellerID."的新使用帳號為：「".$this->accountNow."」"."<br>";        
    }

    private function isAccountUsed($account){
        if (in_array($account, $this->accountUsedArray)){
            return true;
        } else{
            return false;
        }
    }

    public function returnAccountList()
    {
        return $this->accountList;
    }

    public function returnAccountNow(){
        return $this->accountNow;
    }

    public function returnNewAccount(){//用在使用者該訂單並沒有分配到帳號時
        if($this->quantityOfProductAccount != 0 && $this->isAccountUsed($this->accountNow)){
            echo "【多人投標】偵測為多人投標，故本次使用帳號不一定為賣家預設帳號，須從帳號清單往下選擇。"."<br>";
            echo "【多人投標】中間若沒有出價失敗，則本賣家預設帳號依然不變"."<br>";
            $this->shiftToNextAccount();
        }

        return $this->accountNow;
    }

    public function setSellerAccountCounter($number){
        $this->sellerCounter = $number ; 
        $this->sellerDefaultCounter = $number;
    }

    private function getAccountBySellerDefaultCounter(){
        $selectNum = $this->sellerDefaultCounter % $this->quantityOfSellerAccount;
        return $this->accountList["$selectNum"];
    }

    public function saveInfoToDB(){
        $listForSave = implode(',', $this->accountList);
        $defaultAccount = $this->getAccountBySellerDefaultCounter();
        $dataArray = array(
            'sellerID' => "$this->sellerID",
            'yahooAccountNow' => "$defaultAccount",
            'accountCounter' => "$this->sellerDefaultCounter",
            'accountList' => $listForSave
        );
        $this->DataBaseHandler->saveSellerDefaultAccount($dataArray);
    }


    public function loadInfoFromDB(){
        // $stmt = $this->connect->prepare("SELECT * FROM `Seller_list` WHERE `sellerID`= ?");
        // $stmt->bind_param("s",$this->sellerID);
        // $stmt->execute();
        // $result = $stmt->get_result();
        $result = $this->DataBaseHandler->loadSellerDefaultAccount($this->sellerID);
        if($result->num_rows===0){
            echo "該賣家沒有指定帳號，取得新帳號列表"."<br>";
            $this->renewShuffleAccountList();
        } else{
            $row = $result -> fetch_array(MYSQLI_BOTH);
            $this->accountNow = $row['yahooAccountNow'];
            $this->sellerCounter = $row['accountCounter'];
            $this->sellerDefaultCounter = $row['accountCounter'];
            $this->accountList = explode(',', $row['accountList']);
        }
    }

}