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
    <table class="table table-bordered">
      <thead>
        <tr>
          <td>#</td>
          <td>賣場編號(productID)</td>
          <td>賣場標題(productTitle)</td>
          <td>結標時間(endTime)</td>
          <td>賣家ID(sellerID)</td>
          <td>投標按鈕</td>
        </tr>
      </thead>
      <tbody>
        <?php
        $connect = new mysqli('localhost','root','','bid_account');
        
        $query = "SELECT * FROM `product_list`  
                  WHERE `endTime` >= CURDATE() ORDER BY `endTime` ASC";
                  //過期的無法投標
        $result = $connect->query($query);
        if (!$result) die("Fatal Error");
        
        $rows = $result->num_rows;//符合qurey查找資格的行數有幾行，等等for迴圈要用
        for ($j = 0 ; $j < $rows ; ++$j)
        {
        $array = $result->fetch_array(MYSQLI_ASSOC);
        //可以比較看看MYSQLI_NUM、MYSQLI_BOTH
        
        
        $html  = '';
        $html .= '<tr>';
        $html .= '<td>';
        $html .= $j+1;
        $html .= '</td>';
        $html .= '<td>';//賣場編號↓
        $html .= $array['productID'];
        $html .= '<td>';//賣場標題↓
        $html .= $array['productTitle'];
        $html .= '</td>';
        $html .= '<td>';//結標時間↓
        $html .= $array['endTime'];
        $html .= '</td>';
        $html .= '<td>';//賣家ID↓
        $html .= $array['sellerID'];
        $html .= '</td>';
        $html .= '<td>';//回覆狀況↓
        $html .= '<a href="main.php?productID='.$array['productID'].'&'.'productTitle='.$array['productTitle'].'&'.'endTime='.$array['endTime'].'&'.'sellerID='.$array['sellerID'].'"'.'>'.'我要投標'.'</a></td>';
        $html .= '</tr>';
        echo $html;
        }

        ?>

        

      </tbody>
    </table>
    </div>
</body>
</html>