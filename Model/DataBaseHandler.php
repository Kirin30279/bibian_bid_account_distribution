<?php
namespace BibianBidAccount\Model;

use mysqli;
class DataBaseHandler{

    public function __construct(){
        $this->connect = new mysqli('192.168.0.151','pt_wuser','pt_wuser1234','pt_develop');
    }

    public function updateOldHighestLose($productID, $oldMemberID){
        $updateOldHighest = "UPDATE `bidder_list` SET `highestNow` = 0  WHERE `productID` = '$productID' and `memberID` = '$oldMemberID'";
        $this->connect->query($updateOldHighest);//將之前最高投標者的標記改為0。
    }

    public function updatePriceNow($productID, $nowPrice)
    {
        $updatePriceNow = "UPDATE `product_list` SET `nowPrice` = $nowPrice WHERE `productID` = '$productID'";
        $this->connect->query($updatePriceNow);
    }
    
    public function saveInfoToDB($dataArray){//這個input應該要設計為一個Array或是JSON檔案
        $memberID = $dataArray['memberID'];
        $usedYahooAccount = $dataArray['usedYahooAccount'];
        $productID = $dataArray['productID'];
        $bidPrice = $dataArray['bidPrice'];
        $sellerID = $dataArray['sellerID'];
        $firstBidingTime = $dataArray['firstBidingTime'];
        $renewBidingTime = $dataArray['renewBidingTime'];
        $bidStatus = $dataArray['bidStatus'];
        $highestNow = $dataArray['highestNow'];

        $stmt = $this->connect->prepare("INSERT INTO bidder_list(memberID, usedYahooAccount, productID, bidPrice, sellerID, firstBidingTime, renewBidingTime, bidStatus, highestNow) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
        usedYahooAccount = VALUES(usedYahooAccount), 
        bidPrice = VALUES(bidPrice), 
        renewBidingTime = VALUES(renewBidingTime), 
        bidStatus = VALUES(bidStatus),
        highestNow = VALUES(highestNow)");

        $stmt->bind_param("ississsii", 
        $memberID, $usedYahooAccount, $productID, $bidPrice, $sellerID , $firstBidingTime, $renewBidingTime, $bidStatus ,$highestNow);
        
        $stmt->execute();
    }

    public function saveBidHistoryToDB($dataArray){
        $memberID = $dataArray['memberID'];
        $usedYahooAccount = $dataArray['usedYahooAccount'];
        $productID = $dataArray['productID'];
        $bidPrice = $dataArray['bidPrice'];
        $renewBidingTime = $dataArray['renewBidingTime'];
        $bidSuccess = $dataArray['bidSuccess'];
        $numOfBidTime = $dataArray['numOfBidTime'];

        $stmt = $this->connect->prepare("INSERT INTO bid_histroy(memberID, usedYahooAccount, productID, bidPrice, BidingTime ,bidSuccess , memberBidTime)
        VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issisii", 
        $memberID, $usedYahooAccount, $productID, $bidPrice, $renewBidingTime, $bidSuccess , $numOfBidTime);

        $stmt->execute();
    }


    public function loadInfoFromDB($memberID, $productID){//這個input應該要設計為一個Array或是JSON檔案
        $stmt = $this->connect->prepare("SELECT * FROM `bidder_list` WHERE `productID` = ? ");
        $stmt->bind_param("s", $productID);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    
    public function returnPriceNow($productID){
        $selectProductList = "SELECT * FROM product_list WHERE `productID` = '$productID'";
        $result = $this->connect->query($selectProductList);
        $result = $result->fetch_all(MYSQLI_ASSOC);
        return $result[0]['nowPrice'];
    }

   
    public function loadSellerDefaultAccount($sellerID){
        $stmt = $this->connect->prepare("SELECT * FROM `Seller_list` WHERE `sellerID`= ?");
        $stmt->bind_param("s",$sellerID);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function saveSellerDefaultAccount($dataArray){
        $sellerID = $dataArray['sellerID'];
        $yahooAccountNow = $dataArray['yahooAccountNow'];
        $accountCounter = $dataArray['accountCounter'];
        $accountList = $dataArray['accountList'];

        $stmt = $this->connect->prepare("INSERT INTO Seller_list(sellerID, yahooAccountNow, accountCounter, accountList) 
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
        yahooAccountNow = VALUES(yahooAccountNow), 
        accountCounter = VALUES(accountCounter)"); 
        $stmt->bind_param("ssis", 
        $sellerID, $yahooAccountNow, $accountCounter, $accountList);
        $stmt->execute();
    }
}