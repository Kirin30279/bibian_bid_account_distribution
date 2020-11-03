<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>商品投標頁面</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<h1>這是投標頁面</h1>
<h2>以下為您本次投標的商品資訊</h2>
<?PHP
include 'config.php';
$productID = $_GET['productID'];//賣場
$sellerID = $_GET['sellerID'];//賣家
$productTitle = $_GET['productTitle'];

echo "投標賣場編號：".$productID."<br>";
echo "投標賣家ID：".$sellerID."<br>";
echo "投標賣場標題：".$productTitle."<br>";

?>


<h4>請輸入投標金額並按下「出價」以完成投標出價</h4>
<form action="bid.php" method="post" enctype="multipart/form-data">
   
    <div class="form-group">
        <label>出價金額(日幣)</label>
        <input type="text" name="price" class="form-control">
    </div>

</form>


</body>





<?PHP

// use BibianBidAccount\Libs\MemberForBid;


// //************************************************************** */
// echo "案例1".'<br>';






// $bidPrice = 15000;         //出價
// $memberID = 75744;        //會員編號
// $bidStatus = 0;           //出價狀態：最後出價、立即出價
// $Member = new MemberForBid($memberID, $productID);

// $Member->setBidPrice($bidPrice);
// $Member->setFinalOrImmediate($bidStatus);
// $Member->setAccountForSeller($sellerID);
// $Member->setBidStatus(array('0'=>true, '1'=>true, '2'=>true));//出價成功


// $Member->doBid();
// $Member->showBidInfo();

// echo "*********************分隔線***********************************"."<BR>";

?>