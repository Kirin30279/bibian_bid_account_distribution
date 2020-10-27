<?PHP
namespace BibianBidAccount\Libs;

use mysqli;

class Account
{
    public $AccountList;
    
    public $AccoutnNumber;

    private $AccountTerm;

    private $isTermExpired;
    
    private $connect;

    public function __construct()
    {
        $this->connect = new mysqli('localhost','root','','bid_account');
    }

    public function getNewAccountShuffle()
    {
        include "YahooAccount.php";
        echo "取得新的帳號列表(隨機排列)"."<br>";
        shuffle($AccountList);
        $this->AccountList = $AccountList;
        return $this->AccountList;
    }

    public function checkAccountListTerm()
    {
        echo "確認帳號列表的時間是否過期，若過期則取得新列表，未過期繼續沿用"."<br>";
        if(1){//尚未寫入條件
            echo "本組帳號過期了，換帳號"."還沒寫判斷，記得補"."<br>";
            $this->isTermExpired = true;
        } else{
            echo "帳號尚未過期，沿用原先帳號列表"."還沒寫判斷，記得補"."<br>";
            $this->isTermExpired = false;
        }
        return $this->isTermExpired;
    }



    public function loadSellerAccountList($sellerID)
    {

    }

    public function loadProductAccountList($productID)
    {

    }

    public function countAccountNumber()
    {
        var_dump($this->AccountList);//要刪除
        $this->AccoutnNumber = count($this->AccountList);
    }

    public function getNextAccount()
    {
        
    }
}