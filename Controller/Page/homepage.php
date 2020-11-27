<?PHP
use BibianBidAccount\Model\PageDataHandler;
include 'config.php';
$PageDataHandler = new PageDataHandler();

$result = $PageDataHandler->getProductList();
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
$html .= '<td>';//起標價↓
$html .= $array['beginPrice'];
$html .= '</td>';
$html .= '<td>';//當前價↓
$html .= $array['nowPrice'];
$html .= '</td>';
$html .= '<td>';//賣家ID↓
$html .= $array['sellerID'];
$html .= '</td>';
$html .= '<td>';//投標按鈕↓
$html .= '<a href="View/setPrice.php?productID='.$array['productID'].'"'.'>'.'我要投標'.'</a></td>';
$html .= '</td>';
$html .= '<td>';//入札者順位↓
$html .= '<a href="View/orderOfBidAccount.php?productID='.$array['productID'].'"'.'>'.'入札者順位'.'</a></td>';
$html .= '</td>';
$html .= '<td>';//入札履歷↓
$html .= '<a href="View/bidHistory.php?productID='.$array['productID'].'"'.'>'.'出價紀錄'.'</a></td>';
$html .= '</td>';
$html .= '</tr>';
echo $html;
}