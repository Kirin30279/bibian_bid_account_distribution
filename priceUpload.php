<?PHP

//$nowPrice = $_POST['nowPrice'];

//這裡加個if來判斷是否之前為最高出價者(不用再疊加)


switch ($nowPrice) {
    case $nowPrice<1000:
        $nowPrice += 10;        
        break;    
    case $nowPrice>=1000 && $nowPrice<5000:
        $nowPrice += 100; 
        break;
    case $nowPrice>=5000 && $nowPrice<10000:
        $nowPrice += 250; 
        break;
    case $nowPrice>=10000 && $nowPrice<50000:
        $nowPrice += 500; 
        break;
    case $nowPrice>50000:
        $nowPrice += 1000; 
        break;

}





?>