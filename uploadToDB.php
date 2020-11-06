<?PHP
include 'config.php';
use BibianBidAccount\Libs\MemberForBid;
$productID = $_POST['productID'];//賣場
$sellerID = $_POST['sellerID'];//賣家
$bidPrice = $_POST['bidPrice'];
$successChoice = $_POST['success'];
$memberID = 75744;        //會員編號
$bidStatus = 0;           //出價狀態：最後出價、立即出價
$member = new MemberForBid($memberID, $productID);

$member->setBidPrice($bidPrice);
$member->setFinalOrImmediate($bidStatus);
$member->setAccountForSeller($sellerID);
switch ($successChoice) {
    case '1s':
        $bidStatusArray = array('1'=>true, '2'=>true, '3'=>true);
        break;
    case '1f1s':
        $bidStatusArray = array('1'=>false, '2'=>true, '3'=>true);
        break;   
    case '2f1s':
        $bidStatusArray = array('1'=>false, '2'=>false, '3'=>true);
        break;
    case '3f':
        $bidStatusArray = array('1'=>false, '2'=>false, '3'=>false);
        break;   
}

$member->setBidStatus($bidStatusArray);//出價成功
// $member->testSucess = false;//出價失敗


$member->doBid();
$member->showBidInfo();


?>

<input type="button" value="點我返回商品頁面" onclick="location.href='index.php'"class="btn btn-danger">