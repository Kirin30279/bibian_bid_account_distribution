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
include '..\Controller\Page\setPrice.php';
?>


<h4>請輸入投標金額並按下「出價」以完成投標出價</h4>
<form action="uploadToDB.php" method="post" enctype="multipart/form-data">
   
    <div class="form-group">
        <label>出價金額(日幣)</label>
        <input type="number" name="bidPrice" class="form-control" min="<?php echo $dataArray['nowPrice'] ?>" pattern="[0-9]" required >
    </div>
    <input hidden type="text" name="productID" class="form-control" value="<?php echo $productID ?>">
    <input hidden type="text" name="sellerID" class="form-control" value="<?php echo $dataArray['sellerID'] ?>"> 
    
    <p>測試用選項：請選擇本次投標的會員</p>
    <div>
    <input type="radio" id="memberID1"
     name="memberID" value="12345" checked="checked">
    <label for="memberID1">會員編號:12345</label>

    <input type="radio" id="memberID2"
     name="memberID" value="75744">
    <label for="memberID2">會員編號:75744</label>

    <input type="radio" id="memberID3"
     name="memberID" value="23456">
    <label for="memberID3">會員編號:23456</label>

    <input type="radio" id="memberID4"
     name="memberID" value="99876">
    <label for="memberID4">會員編號:99876</label>
    </div>



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
    <input type="button" value="取消" onclick="location.href='../index.php'"class="btn btn-danger">



</form>



</body>

