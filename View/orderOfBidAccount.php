<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>使用帳號列表</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<h1>這是使用帳號列表</h1>
<h2>以下為賣場：<?PHP $productID = $_GET['productID']; echo $productID ;?>當前使用的帳號</h2>
<?php include '..\Controller\Page\orderOfBidAccount.php';?>

