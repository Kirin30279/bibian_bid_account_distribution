<?php
namespace BibianBidAccount\Libs;
use mysqli;

class MemberLastTime{
    public $memberID;

    public $bidPrice;

    private $connect;

    public $thereIsNoLastBidder;


    public function __construct($productID){
        $this->connect = new mysqli('192.168.0.151','pt_wuser','pt_wuser1234','pt_develop');
        $selectAllBidder = "SELECT * FROM bidder_list WHERE `productID` = '$productID' AND `highestNow` = TRUE";
        $resultOldArray = $this->connect->query($selectAllBidder);//確認一下該賣場是否有最高投標者
        if($resultOldArray->num_rows===0){
            $this->thereIsNoLastBidder = true;
        } else{
            $this->thereIsNoLastBidder = false;
            $resultOldArray = $resultOldArray->fetch_all(MYSQLI_ASSOC);
            $this->memberID = $resultOldArray[0]['memberID'];
            $this->bidPrice = $resultOldArray[0]['bidPrice'];
        }
    }

}


?>