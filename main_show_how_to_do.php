<?PHP
include 'config.php';
use BibianBidAccount\Libs\MemberForBid;


//************************************************************** */
echo "案例1".'<br>';
$productID = 'T2222111150';//賣場
$sellerID = 'seller_1121';//賣家
$bidPrice = 95400;         //出價
$memberID = 75744;        //會員編號
$bidStatus = 0;           //出價狀態：最後出價、立即出價
$Member = new MemberForBid($memberID, $productID);

$Member->setBidPrice($bidPrice);
$Member->setBidStatus($bidStatus);
$Member->setSellerID($sellerID);

$Member->testSucess = true;//出價成功
// $Member->testSucess = false;//出價失敗


while ($Member->bidFailTime<3 && $Member->bidSucess === false){
    $Member->doBid($productID, $bidPrice);
}
$Member->showBidInfo();
echo "*********************分隔線***********************************"."<BR>";

//************************************************************** */
echo "案例2".'<br>';
$productID = 'Q489189315';//賣場
$sellerID = 'seller_9955';//賣家
$bidPrice = 11220;         //出價
$memberID = 40005;        //會員編號
$bidStatus = 0;           //出價狀態：最後出價、立即出價
$Member = new MemberForBid($memberID, $productID);

$Member->setBidPrice($bidPrice);
$Member->setBidStatus($bidStatus);
$Member->setSellerID($sellerID);

$Member->testSucess = true;//出價成功
// $Member->testSucess = false;//出價失敗


while ($Member->bidFailTime<3 && $Member->bidSucess === false){
    $Member->doBid($productID, $bidPrice);
}
$Member->showBidInfo();
echo "*********************分隔線***********************************"."<BR>";






//************************************************************** */

echo "案例3，這個案例會連續失敗三次".'<br>';
$productID = 'Q2191981980';//賣場
$sellerID = 'seller_7745';//賣家
$bidPrice = 11220;         //出價
$memberID = 12040;        //會員編號
$bidStatus = 0;           //出價狀態：最後出價、立即出價
$Member = new MemberForBid($memberID, $productID);

$Member->setBidPrice($bidPrice);
$Member->setBidStatus($bidStatus);
$Member->setSellerID($sellerID);

// $Member->testSucess = true;//出價成功
$Member->testSucess = false;//出價失敗


while ($Member->bidFailTime<3 && $Member->bidSucess === false){
    $Member->doBid($productID, $bidPrice);
}

if ($Member->bidSucess === false){
    echo "三次出價皆失敗"."<br>"."<br>";
}
$Member->showBidInfo();
echo "*********************分隔線***********************************"."<BR>";

echo "案例4，這個案例會失敗一次後成功出價".'<br>';
$productID = 'K849198109';//賣場
$sellerID = 'seller_1233';//賣家
$bidPrice = 11220;         //出價
$memberID = 11145;        //會員編號
$bidStatus = 0;           //出價狀態：最後出價、立即出價
$Member = new MemberForBid($memberID, $productID);

$Member->setBidPrice($bidPrice);
$Member->setBidStatus($bidStatus);
$Member->setSellerID($sellerID);

// $Member->testSucess = true;//出價成功
$Member->testSucess = false;//出價失敗


while ($Member->bidFailTime<3 && $Member->bidSucess === false){
    $Member->doBid($productID, $bidPrice);
    $Member->testSucess = true;//出價成功
}

if ($Member->bidSucess === false){
    echo "三次出價皆失敗"."<br>"."<br>";
}

$Member->showBidInfo();
echo "*********************分隔線***********************************"."<BR>";


echo "案例5".'<br>';
$productID = 'G4105681605';//賣場
$sellerID = 'seller_1233';//賣家
$bidPrice = 1400;         //出價
$memberID = 23234;        //會員編號
$bidStatus = 0;           //出價狀態：最後出價、立即出價
$Member = new MemberForBid($memberID, $productID);

$Member->setBidPrice($bidPrice);
$Member->setBidStatus($bidStatus);
$Member->setSellerID($sellerID);

$Member->testSucess = true;//出價成功
// $Member->testSucess = false;//出價失敗


while ($Member->bidFailTime<3 && $Member->bidSucess === false){
    $Member->doBid($productID, $bidPrice);
}

if ($Member->bidSucess === false){
    echo "三次出價皆失敗"."<br>"."<br>";
}
$Member->showBidInfo();

//************************************************************** */


?>