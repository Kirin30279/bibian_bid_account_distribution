<?PHP

function showBidSuccessOrFail($token)
{
  if($token == 0){
    return '<span style="color:#FF0000;"><b>出價失敗</b></span>';
  }else {
    return '<span style="color:#00FF00;"><b>出價成功</b></span>';
  }
}

use BibianBidAccount\Model\PageDataHandler;
include '../config.php';
$PageDataHandler = new PageDataHandler();

$result = $PageDataHandler->getBidHistory($productID);
if (!$result) die("Fatal Error");



$bidderNumber = $result->num_rows;
  if ($bidderNumber!=0){

    echo "<h2>本商品有"."$bidderNumber"."次投標紀錄</h2>";
    echo '<div class="m-2">
    <table class="table table-bordered">
      <thead>
        <tr>
          <td>#</td>
          <td>用戶帳號</td>
          <td>使用Y拍帳號</td>
          <td>出價</td>
          <td>出價時間</td>
          <td>出價是否成功</td>
          <td>本次投標第n次調用帳號</td>
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

        $html .= '<td>';//用戶帳號
        $html .= '會員編號:';
        $html .= $dataArray[$i]['memberID'];
        $html .= '</td>';

        $html .= '<td>';//使用Y拍帳號
        $html .= $dataArray[$i]['usedYahooAccount'];
        $html .= '</td>';

        $html .= '<td>';//出價
        $html .= $dataArray[$i]['bidPrice'];
        $html .= '円';
        $html .= '</td>';

        $html .= '<td>';//出價時間
        $html .= $dataArray[$i]['BidingTime'];
        $html .= '</td>';

        $html .= '<td>';//出價是否成功
        $html .= showBidSuccessOrFail($dataArray[$i]['bidSuccess']);
        $html .= '</td>';

        $html .= '<td>';//第幾次的投標紀錄
        $html .= $dataArray[$i]['memberBidTime'];
        $html .= '</td>';

        $html .= '</tr>';
        echo $html;

        $i++;
    }
    echo "</div>";
    
    echo '<input type="button" value="點我返回商品頁面" onclick="location.href=\'../index.php\'">';
  } else{
    echo '<span style="color:#FF0000;">'."本商品尚未有人投標或賣場不存在".'</span>'."<br>";
    echo '<input type="button" value="點我返回商品頁面" onclick="location.href=\'../index.php\'">';
    exit;
  }
  ?>






