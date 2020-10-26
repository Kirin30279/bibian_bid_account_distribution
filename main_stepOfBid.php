<?PHP
include 'config.php';
use BibianBidAccount\Libs\MemberForBid;
// $productID = $_POST['productID']; //賣場編號
// $bidPrice = $_POST['bidPrice']; //用戶出價
$productID = 'c1122334455';
$sellerID = 'seller_1121';
$bidPrice = 3260;
$memberID = 15555;//會員編號
$bidStatus = 0;//最後出價、立即出價


$Member = new MemberForBid($memberID, $productID);

$Member->setBidPrice($bidPrice);
$Member->setBidStatus($bidStatus);//立即或最後出價
$Member->setSellerID($sellerID);

// $Member->testSucess = true;//出價成功
$Member->testSucess = false;//出價成功

// var_dump($Member);
// exit();
$Member->doBid($productID, $bidPrice);
//$Member->echoYahooAccuount();
//var_dump($Member);



?>