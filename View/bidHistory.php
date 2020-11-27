<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>出價紀錄</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<h1>這是出價紀錄列表</h1>
<h2>以下為賣場：<?PHP $productID = $_GET['productID']; echo $productID ;?>的出價紀錄</h2>

<?php include '../Controller/Page/bidHistory.php';?>



