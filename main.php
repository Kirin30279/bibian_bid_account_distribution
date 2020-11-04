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
$productID = $_GET['productID'];//賣場
$sellerID = $_GET['sellerID'];//賣家
$productTitle = $_GET['productTitle'];
$endTime = $_GET['endTime'];

echo "投標賣場編號：".$productID."<br>";
echo "投標賣家ID：".$sellerID."<br>";
echo "投標賣場標題：".$productTitle."<br>";
echo "結標時間：".$endTime."<br>";
?>


<h4>請輸入投標金額並按下「出價」以完成投標出價</h4>
<form action="bid.php" method="post" enctype="multipart/form-data">
   
    <div class="form-group">
        <label>出價金額(日幣)</label>
        <input type="number" name="bidPrice" class="form-control" min="1" pattern="[0-9]" required >
    </div>
    <input hidden type="text" name="productID" class="form-control" value="<?php echo $productID ?>">
    <input hidden type="text" name="sellerID" class="form-control" value="<?php echo $sellerID ?>">
    <input hidden type="text" name="productTitle" class="form-control" value="<?php echo $productTitle?>"> 
    <p>測試用選項：請選擇投標成功狀態</p>
    <div>
    <input type="radio" id="successChoice1"
     name="success" value="1s" checked="checked">
    <label for="successChoice1">一次成功</label>

    <input type="radio" id="successChoice2"
     name="success" value="1f1s">
    <label for="successChoice2">一次失敗一次成功</label>

    <input type="radio" id="successChoice3"
     name="success" value="2f1s">
    <label for="successChoice3">二次失敗一次成功</label>

    <input type="radio" id="successChoice3"
     name="success" value="3f">
    <label for="successChoice4">三次失敗</label>
    </div>
    
    <input type="submit" value='出價' class="btn btn-primary">
    <input type="button" value="取消" onclick="location.href='auctionPage.php'"class="btn btn-danger">



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