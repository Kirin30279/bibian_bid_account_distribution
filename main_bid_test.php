<?PHP
include "config.php";
use BibianBidAccount\Libs\Seller;

$Seller_1 = new Seller('賣家帳號AAAA');
$Seller_1->getAccountList($AccountList);
var_dump($Seller_1);



?>