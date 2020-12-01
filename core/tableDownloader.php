<?php
$template = [];
echo "Masukkan Pathname URL Post : ";
$urlPath = trim(fgets(STDIN));
$path = explode("/", $urlPath);
while(true){
    echo "Masukkan Tipe (Manual, Windows32, Windows64, Mac) : ";
    $tipe = trim(fgets(STDIN));
    echo "Masukkan Title : ";
    $title = trim(fgets(STDIN));
    echo "\n";
    $template[$tipe] = '<div><a href="#" class="hrefLink">'.ucwords($title).'</a><div style="display:none;"><div style="text-align: center;"><div><div style="text-align: start;"><table id="zaposphere-table-courtyard" style="border-collapse: collapse; border: #D9D9D9; mso-border-alt: solid #D9D9D9 .5pt; mso-border-insideh-themecolor: background1; mso-border-insideh-themeshade: 217; mso-border-insideh: .5pt solid #D9D9D9; mso-border-insidev-themecolor: background1; mso-border-insidev-themeshade: 217; mso-border-insidev: .5pt solid #D9D9D9; mso-border-themecolor: background1; mso-border-themeshade: 217; mso-border-top-alt: solid red 3.0pt; mso-padding-alt: 0cm 5.4pt 0cm 5.4pt; mso-yfti-tbllook: 1184;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td bgcolor="#CCCCCC" width="168"><div align="center"><strong>Download</strong></div></td><td bgcolor="#CCCCCC" width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><strong>Driver</strong></div></div></div></td></tr></tbody><tbody>';
    while(true){
        echo "Masukkan filename : ";
        $fileName = trim(fgets(STDIN));
        echo "Masukkan Link Download : ";
        $linkDownload = trim(fgets(STDIN));
        $downloadHash = encrypt($linkDownload);
        if(strtolower($tipe) === "manual"){
            echo "Masukkan Description : ";
            $supportOS = trim(fgets(STDIN));
            $desc = "Description:";
        }else{
            echo "Masukkan Support OS : ";
            $supportOS = trim(fgets(STDIN));
            $desc = "Support OS:";
        }
        $template[$tipe] .= '<tr><td width="168"><div align="center"><a href="'.$urlPath.'" class="hrefDownload" id="'.$downloadHash.'"><button style="background-color: #3377FF; border-bottom-left-radius: 2px; border-bottom-right-radius: 2px; border-top-left-radius: 2px; border-top-right-radius: 2px; border: 0px; color: white; cursor: pointer; font-size: 12px; font-weight: bold; margin: 0px; max-width: 100%; padding: 10px 30px 11px; text-transform: uppercase; vertical-align: bottom;">DOWNLOAD</button></a></div></td><td width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><b>'.$fileName.'</b><br>'.$desc.'<br><small>'.$supportOS.'</small></div></div></div></td></tr>'; 
        echo "\n";
        echo "Input File Lain di table => ".$tipe." 1.ya 0.tidak : ";
        $ulangTr = trim(fgets(STDIN));
        if(!$ulangTr){
            break;
        }
    }
    $template[$tipe] .= '</tbody></table><div style="text-align: center;"></div></div></div></div></div></div>';
    echo "Ingin Menambah Kategori dan table baru 1.ya 0.tidak : ";
    $loop = trim(fgets(STDIN));
    echo "\n";
    if(!$loop){
        break;
    }
}
foreach($template as $rawData){
    $fopen = fopen(__DIR__."/../saved/".end($path).".html", 'a');
    fwrite($fopen, $rawData.PHP_EOL);
}
fclose($fopen);
echo "Saved To => ".__DIR__."\\".end($path);
?>