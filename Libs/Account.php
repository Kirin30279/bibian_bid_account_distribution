<?PHP
namespace BibianBidAccount\Libs;

use mysqli;

class Account
{

    public $quantityOfAccount;//總帳號個數

    private $AccountTerm;

    private $AccountNow;

    private $isTermExpired;
    
    private $connect;

    private $AccountList;
    
    private $sellerID;

    private $counter;//指派帳號用計數器

    public function __construct($sellerID)
    {   
        $this->sellerID = $sellerID;
        $this->connect = new mysqli('localhost','root','','bid_account');
        
    }

    public function renewShuffleAccountList()
    {
        include "YahooAccount.php";
        echo "取得新的帳號列表(隨機排列)"."<br>";
        shuffle($AccountList);
        $this->AccountList = $AccountList;
        echo "設定新的Term(過期時間）"."<br>";
        $this->setListTerm();
        
    }
    
    private function setListTerm(){
        $this->AccountTerm = time()+3600*24;
    }

    public function checkAccountListTerm()
    {
        echo "確認帳號列表的時間是否過期，若過期則取得新列表，未過期繼續沿用"."<br>";
        if($this->AccountTerm<time()){//尚未寫入條件
            echo "本組帳號過期了，換帳號"."還沒寫判斷，記得補"."<br>";
            $this->isTermExpired = true;
        } else{
            echo "帳號尚未過期，沿用原先帳號列表"."還沒寫判斷，記得補"."<br>";
            $this->isTermExpired = false;
        }
    }

    private function saveInfoToDB()
    {
        $save_Account ="";
        // $this->connect->query($save_Account);
    }

    private function loadInfoFromDB()
    {

        $take_Account = "SELECT * FROM `Seller_list` WHERE `sellerID`= '$this->sellerID'";
  
        $result = $this->connect->query($take_Account);
    }



    public function countAccountNumber()
    {
        echo "計算帳號列表中有多少帳號"."<br>";
        $this->quantityOfAccount = count($this->AccountList);
    }

    public function switchToNextAccount()
    {
        echo "指定列表中的下一個帳號，計數器+1"."<BR>";
        echo "當前使用帳號為：「".$this->AccountNow."」"."<br>";
        $this->checkAccountListTerm();
        if ($this->isTermExpired){
            $this->renewShuffleAccountList();
        }        
        $this->AccountNow = $this->AccountList["$this->counter"];
        $this->counter;
        echo "指派的新使用帳號為：「".$this->AccountNow."」"."<br>";
    }

    public function returnAccountList()
    {
        return $this->AccountList;
    }
}