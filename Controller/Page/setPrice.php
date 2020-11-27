<?PHP
use BibianBidAccount\Model\PageDataHandler;
include '../config.php';
$PageDataHandler = new PageDataHandler();

$resultOfProduct = $PageDataHandler->getProductDetail($productID);
if (!$resultOfProduct) die("Fatal Error");

if ($resultOfProduct->num_rows!=0){
    $dataArray = $resultOfProduct->fetch_array(MYSQLI_ASSOC);
    $sellerID = $dataArray['sellerID'] ; 

    $resultSeller = $PageDataHandler->getSellerInfo($sellerID);
    if ($resultSeller->num_rows!=0){
        $sellerInfo = $resultSeller->fetch_array(MYSQLI_ASSOC);
        $defaultBidAccount = $sellerInfo['yahooAccountNow'];
    } else{
        $defaultBidAccount = "本賣家為第一次投標，沒有預設帳號。";
    }
    echo "投標賣場編號：".$productID."<br>";
    echo "投標賣家ID：".$sellerID."<br>";
    echo "投標賣家預設帳號:".$defaultBidAccount."<br>";
    echo "投標賣場標題：".$dataArray['productTitle']."<br>";
    echo "結標時間：".$dataArray['endTime']."<br>";
    echo "起標價格：".$dataArray['beginPrice']."<br>";
    echo '<span style="color:#FF0000;">'."目前價格：".$dataArray['nowPrice'].'</span>'."<br>";
} else{
    echo '<span style="color:#FF0000;">'."本商品不存在或已下架，請回到商品頁面重新選擇投標商品".'</span>'."<br>";
    echo '<input type="button" value="點我返回商品頁面" onclick="location.href=\'index.php\'">';
exit;
}