<?PHP

$Product_ID = $_POST['Product_ID']; //賣場編號
$bidPrice = $_POST['bidPrice']; //用戶出價
$member_ID = 78706;//會員編號
$bidStatus = 0;//最後出價、立即出價


$Member = new MemberForBid($member_ID, $Product_ID);
$Member->setBidPrice($bidPrice);
$Member->setBidStatus($bidStatus);//立即或最後出價
$Member->doBid();




?>