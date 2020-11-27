<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>拍賣商品頁面</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <h1>本頁為商品頁面，請選擇欲投標的商品</h1>
    <h4>出價成功與否需於下一頁自行選擇(一次成功、一次失敗後成功、三次失敗...等)</h4>
    <div class="m-2">
    <table class="table table-bordered" >
      <thead>
        <tr>
          <td>#</td>
          <td>賣場編號(productID)</td>
          <td style="width:50px;overflow:hidden;">賣場標題(productTitle)</td>
          <td>結標時間(endTime)</td>
          <td>起標價格</td>
          <td>當前價格</td>
          <td>賣家ID(sellerID)</td>
          <td>投標按鈕</td>
          <td>入札者順位</td>
          <td>入札履歷</td>
        </tr>
      </thead>
      <tbody>
        <?php include 'Controller\Page\homepage.php';?>

        

      </tbody>
    </table>
    </div>
</body>
</html>