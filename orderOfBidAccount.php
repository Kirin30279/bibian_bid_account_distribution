<?PHP
function showTheHighestBidder($token)
{
    if($token == 1){
        return '<span style="color:#FF0000;"><b>最高出價者</b></span>';
    }
}

?>
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
<?PHP
$connect = new mysqli('192.168.0.151','pt_wuser','pt_wuser1234','pt_develop');
$productID = $connect->real_escape_string($productID);
$query = "SELECT * FROM `bidder_list` WHERE `productID` = '$productID' ORDER BY `bidPrice` DESC ";
$result = $connect->query($query);
$bidderNumber = $result->num_rows;
  if ($bidderNumber!=0){

    echo "<h2>本商品有"."$bidderNumber"."人投標</h2>";
    echo '<div class="m-2">
    <table class="table table-bordered">
      <thead>
        <tr>
          <td>#</td>
          <td>入札者</td>
          <td>入札額</td>
          <td>實際用戶</td>
          <td>最高入札者</td>
        </tr>
      </thead>
      <tbody>';
    $dataArray = $result->fetch_all(MYSQLI_ASSOC);
    $i=0;
    while ($i < $bidderNumber) {
        $html  = '';
        $html .= '<tr>';
        $html .= '<td>';
        $html .= $i+1;
        $html .= '</td>';
        $html .= '<td>';//入札者↓
        $html .= $dataArray[$i]['usedYahooAccount'];
        $html .= '<td>';//入札額↓
        $html .= $dataArray[$i]['bidPrice'];
        $html .= '円';
        $html .= '</td>';
        $html .= '<td>';//用戶編號↓
        $html .= '會員編號:';
        $html .= $dataArray[$i]['memberID'];
        $html .= '</td>';
        $html .= '<td>';//最高入札者↓
        $html .= showTheHighestBidder($dataArray[$i]['highestNow']);
        $html .= '</td>';
        $html .= '</tr>';
        echo $html;

        $i++;
    }
    echo "</div>";
    echo '<input type="button" value="點我返回商品頁面" onclick="location.href=\'index.php\'">';
  } else{
    echo '<span style="color:#FF0000;">'."本商品尚未有人投標或賣場不存在".'</span>'."<br>";
    echo '<input type="button" value="點我返回商品頁面" onclick="location.href=\'index.php\'">';
    exit;
  }
  ?>


