<?PHP
include 'config.php';
use BibianBidAccount\Libs\MemberForBid;
$productID = 'T2222111150';//賣場
$sellerID = 'seller_1121';//賣家
$bidPrice = 95400;         //出價
$memberID = 75744;        //會員編號
$bidStatus = 0;           //出價狀態：最後出價、立即出價
$Member = new MemberForBid($memberID, $productID);

$Member->setBidPrice($bidPrice);
$Member->setBidStatus($bidStatus);
$Member->setSellerID($sellerID);


//**$Member->testSucess的參數拿來模擬真正出價時的成功或失敗 */
$Member->testSucess = true;//出價成功
// $Member->testSucess = false;//出價失敗


//失敗次數小於三次，且尚未投標成功則換帳號持續投標 
while ($Member->bidFailTime<3 && $Member->bidSucess === false){
    $Member->doBid($productID, $bidPrice);
}

if ($Member->bidSucess === false){
    echo "三次出價皆失敗"."<BR>";
}


?>