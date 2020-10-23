<?php
function doBid($Product_ID, $Bid_Price){
    $Seller_ID = getSellerIDfromProduct($Product_ID);
    //這是去爬商品頁面的Seller_ID，也許可以直接從主程式那邊拿到就好，但函數會變3變數
    $Seller = new Seller($Seller_ID);  
    $Product = new Product($Product_ID, $Seller_ID);
    //$bidder = new MemberForBid();

}



function setProduct($Product_ID){
    $Product = new Product();
    $ProductisExist = $Product->isProductExist($Product_ID);//return boolen
    if ($ProductisExist){
        $Product->setInfoProductFromDB();//抓DB裡面的資料來更新這次用的Product 
    } else {
        $Product->updateProduct($Product_ID);//用給定ID更新Product基本資訊
        $Product->saveInfoToDB();//把這個Product資訊存進DB，以後就可用
    }

    return $Seller;//object of class Seller

}