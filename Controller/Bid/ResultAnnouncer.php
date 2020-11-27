<?php
namespace BibianBidAccount\Controller\Bid;
use mysqli;

class ResultAnnouncer{
    
    private $bidSuccess;

    private $finalDisplayStatus;

    private $bidTime;

    private $BidInfo;//array

    public function __construct($BidInfo){
        $this->BidInfo = $BidInfo;
        $this->bidSuccess = $BidInfo['bidSuccess'];
        $this->finalDisplayStatus = $BidInfo['finalDisplayStatus'];
        $this->bidTime = $BidInfo['bidTime'];
    }

    private function showSucessORNot(){
        if ($this->bidSuccess == true){
            switch ($this->finalDisplayStatus) {
                case 'success':
                    echo '<span style="color:#FF0000;">※出價成功※</span>'."<br>";
                    echo '<span style="color:#FF0000;">您目前為最高出價者</span>'."<br>";
                    break;

                case 'fail':
                    echo '<span style="color:#FF0000;">出價被超過，請再加價</span>'."<br>";   
                    break;

                case 'increseInsufficient':
                    echo '<span style="color:#FF0000;">您的出價增額不足，請再加價</span>'."<br>";
                    break;
                
                default:
                    echo '意外狀況，請檢查CODE';
                    break;
            }
        } else{
            echo '<span style="color:#FF0000;">※很抱歉，出價失敗，請回到商品頁面重新投標。※</span>'."<br>";
        }
    }

    private function describeNumOfBidTime(){
        echo "帳號使用情況：";
        switch ($this->bidTime) {
            case '1':
                echo "選用第一個帳號即連上系統"."<br>";
                break;
            case '2':
                echo "第一個帳號調用失敗，使用第二個帳號連上系統"."<br>";
                break;    
            case '3':
                echo "第一、二個帳號調用失敗，使用第三個帳號連上系統"."<br>";
                break;     
            case '4':
                echo "連續三次帳號調用失敗，投標未成功"."<br>";
                break;  
        }
    }

    public function showBidInfo(){//程式測試演示用
        echo "投標者會員編號：".$this->BidInfo['memberID']."<br>";
        echo "賣家ID：".$this->BidInfo['sellerID']."<br>";
        echo "當前指派的Y拍帳號：".$this->BidInfo['usedYahooAccount']."<br>";
        echo "賣場編號：".$this->BidInfo['productID']."<br>";
        echo "出價價格：".$this->BidInfo['bidPrice']."円<br>";
        echo "賣場當前價格：".$this->BidInfo['PriceNow']."円<br>";
        echo "嘗試投標次數：".$this->bidTime."<br>";
        $this->describeNumOfBidTime();
        $this->showSucessORNot();
  
    }
}

?>