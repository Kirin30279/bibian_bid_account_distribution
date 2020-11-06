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
$connect = new mysqli('192.168.0.151','pt_wuser','pt_wuser1234','pt_develop');
$productID = $connect->real_escape_string($productID);
$query = "SELECT * FROM `product_list` WHERE `productID` = '$productID' AND `endTime` >= TIME(NOW())";
$result = $connect->query($query);
  if ($result->num_rows!=0){
    $dataArray = $result->fetch_array(MYSQLI_ASSOC);
    echo "投標賣場編號：".$productID."<br>";
    echo "投標賣家ID：".$dataArray['sellerID']."<br>";
    echo "投標賣場標題：".$dataArray['productTitle']."<br>";
    echo "結標時間：".$dataArray['endTime']."<br>";
    echo "起標價格：".$dataArray['beginPrice']."<br>";
    echo '<span style="color:#FF0000;">'."目前價格：".$dataArray['nowPrice'].'</span>'."<br>";
  } else{
    echo '<span style="color:#FF0000;">'."本商品不存在或已下架，請回到商品頁面重新選擇投標商品".'</span>'."<br>";
    echo '<input type="button" value="點我返回商品頁面" onclick="location.href=\'index.php\'">';
    exit;
  }
  ?>


<h4>請輸入投標金額並按下「出價」以完成投標出價</h4>
<form action="uploadToDB.php" method="post" enctype="multipart/form-data">
   
    <div class="form-group">
        <label>出價金額(日幣)</label>
        <input type="number" name="bidPrice" class="form-control" min="<?php echo $dataArray['nowPrice'] ?>" pattern="[0-9]" required >
    </div>
    <input hidden type="text" name="productID" class="form-control" value="<?php echo $productID ?>">
    <input hidden type="text" name="sellerID" class="form-control" value="<?php echo $dataArray['sellerID'] ?>"> 
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
    <input type="button" value="取消" onclick="location.href='index.php'"class="btn btn-danger">



</form>



</body>

