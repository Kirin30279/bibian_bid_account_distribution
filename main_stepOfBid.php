<?PHP

$Product_Number = $_POST['Product_number']; //賣場編號
$bidPrice = $_POST['bidPrice']; //用戶出價
$member_ID = 78706;//會員編號
$bidStatus = 0;//最後出價、立即出價

$Member_AAA = new MemberForBid($member_ID);
$Member_AAA->setBidPrice($bidPrice);

$Member_AAA->doBid($Product_Number, $bidPrice);




?>