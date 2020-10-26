<?PHP
include 'config.php';
use BibianBidAccount\Libs\MemberForBid;
// $productID = $_POST['productID']; //賣場編號
// $bidPrice = $_POST['bidPrice']; //用戶出價
$productID = 'T2222111150';
$sellerID = 'seller_1121';
$bidPrice = 9800;
$memberID = 75744;//會員編號
$bidStatus = 0;//最後出價、立即出價


$Member = new MemberForBid($memberID, $productID);

$Member->setBidPrice($bidPrice);
$Member->setBidStatus($bidStatus);//立即或最後出價
$Member->setSellerID($sellerID);

$Member->testSucess = true;//出價成功
$Member->testSucess = false;//出價成功

// var_dump($Member);
// exit();
while ($Member->bidFailTime<3 && $Member->bidSucess === false){
    $Member->doBid($productID, $bidPrice);
}
//$Member->echoYahooAccuount();
//var_dump($Member);



?>