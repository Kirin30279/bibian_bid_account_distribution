<?php
namespace BibianBidAccount\Model;

use mysqli;
class PageDataHandler{

    public function __construct(){
        $this->connect = new mysqli('192.168.0.151','pt_wuser','pt_wuser1234','pt_develop');
    }

    public function getProductList(){
        $query = "SELECT * FROM `product_list`  
        WHERE `endTime` >= CURDATE() ORDER BY `endTime` ASC";
        //過期的無法投標

        return $this->connect->query($query);
    }

    public function getBidHistory($productID){
        $productID = $this->connect->real_escape_string($productID);
        $query = "SELECT * FROM `bid_histroy` WHERE `productID` = '$productID' ORDER BY `bidPrice` DESC , `BidingTime` DESC, `memberBidTime` DESC ";
        return $this->connect->query($query);
    }

    public function getOrderOfBidAccount($productID){
        $productID = $this->connect->real_escape_string($productID);
        $query = "SELECT * FROM `bidder_list` WHERE `productID` = '$productID' ORDER BY `bidPrice` DESC ";
        return $this->connect->query($query);
    }

    public function getProductDetail($productID){
        $productID = $this->connect->real_escape_string($productID);
        $queryForProduct = "SELECT * FROM `product_list` WHERE `productID` = '$productID' AND `endTime` >= TIME(NOW())";
        return $this->connect->query($queryForProduct);
    }

    public function getSellerInfo($sellerID){
        $queryForSeller = "SELECT * FROM `seller_list` WHERE `sellerID` = '$sellerID'";
        return $this->connect->query($queryForSeller);
    }
}