<?PHP
include 'config.php';
use BibianBidAccount\Libs\MemberForBid;


//************************************************************** */
echo "案例1".'<br>';

$productID = $_GET['productID'];//賣場
$sellerID = $_GET['sellerID'];//賣家
$bidPrice = 15000;         //出價
$memberID = 75744;        //會員編號
$bidStatus = 0;           //出價狀態：最後出價、立即出價
$Member = new MemberForBid($memberID, $productID);

$Member->setBidPrice($bidPrice);
$Member->setFinalOrImmediate($bidStatus);
$Member->setAccountForSeller($sellerID);
$Member->setBidStatus(array('0'=>true, '1'=>true, '2'=>true));//出價成功


$Member->doBid();
$Member->showBidInfo();

echo "*********************分隔線***********************************"."<BR>";

?>