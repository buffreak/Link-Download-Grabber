<?php
require_once __DIR__."/inc/component/hash.php";
require_once __DIR__."/inc/component/Copyright.php";
require_once __DIR__."/inc/component/Updater.php";
//try{
//    $updater = new Updater("Rivai");
//    if($updater->status === "upgrade"){
//        die;
//    }
//}catch(Exception $e){
//    echo $e->getMessage();
//    die;
//}
Copyright::run()->contributor();
$globData = glob(__DIR__."/core/*.php");
echo "List Download Grabber\n";
foreach($globData as $key => $value){
    $getLast = explode("/", $value);
    $lastName = end($getLast);
    $getRealName = explode(".", $lastName);
    echo "[".++$key."] {$getRealName[0]} Download Link Grabber\n";
}
echo "\nMasukkan Pilihan 1 - ".count($globData)." : ";
$pilihan = (int) trim(fgets(STDIN));
$fileName = explode("/", $globData[$pilihan - 1]);
require_once __DIR__."/core/".$fileName[2];
?>