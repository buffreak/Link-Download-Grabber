<?php
error_reporting(0);
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
$regions = [
    ["US", "https://support.brother.com/g/b/productlist.aspx?c=us&lang=en&content=dl&q="],
    ["UK", "https://support.brother.com/g/b/productlist.aspx?c=gb&lang=en&content=dl&q="],
    ["AU", "https://support.brother.com/g/b/productlist.aspx?c=au&lang=en&content=dl&q="],
    ["SG", "https://support.brother.com/g/b/productlist.aspx?c=sg&lang=en&content=dl&q="],
    ["OCUS", "https://support.brother.com/g/b/productlist.aspx?c=us_ot&lang=en&q="],
    ["OCUK", "https://support.brother.com/g/b/productlist.aspx?c=eu_ot&lang=en&q="]
];
foreach($regions as $key => $region){
    echo "[".++$key."] {$region[0]}\n";
}
echo "Masukkan Region : ";
$region = (int) trim(fgets(STDIN));
define("URL_HOME", "https://support.brother.com");
define("URL_SEARCH", trim($regions[$region - 1][1]));
define("URL_DOWNLOAD", "https://support.brother.com/g/b/downloadtop.aspx");
define("DOWNLOAD_FIX", "https://support.brother.com/g/b/downloadlist.aspx?");
define("MANUAL_LINK", "https://support.brother.com/g/b/manualtop.aspx?");
function getHeader($type = "search"){
    if($type === "search"){
        return "upgrade-insecure-requests: 1\r\n".
                "user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.120 Safari/537.36\r\n".
                "sec-fetch-mode: navigate\r\n".
                "sec-fetch-user: ?1\r\n".
                "accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3\r\n".
                "sec-fetch-site: same-origin\r\n".
                "referer: https://support.brother.com/g/b/productsearch.aspx?c=us&lang=en&content=dl\r\n".
                "accept-language: en-US,en;q=0.9\r\n".
                "cookie: _ga=GA1.2.379212952.1568903672\r\n".
                "cookie: _gcl_au=1.1.1676101868.1569076728\r\n".
                "cookie: _hjid=1d6bf9d1-92be-451f-93e5-c56add3fb3af\r\n".
                "cookie: _gid=GA1.2.702656979.1571371218\r\n".
                "cookie: SelectProduct_us_en=mfcj285dw_us%2Cmfcj985dw_us_eu_as%2Cdcp110c_us%2Cdcp165c_all%2Cdcp130c_all%2Cdcp8025d_us%2Cmfcj280w_us%2Cdcp1000_us%2Cdcp585cw_all%2Cdcp395cn_all\r\n".
                "cookie: ASP.NET_SessionId=ytjpdr3spqnfgmq2s04pu055\r\n".
                "cookie: _gat=1\r\n";
    }else{
        return "upgrade-insecure-requests: 1\r\n".
                "user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.120 Safari/537.36\r\n".
                "sec-fetch-mode: navigate\r\n".
                "sec-fetch-user: ?1\r\n".
                "accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3\r\n".
                "sec-fetch-site: same-origin\r\n".
                "referer: https://support.brother.com/g/b/productsearch.aspx?c=us&lang=en&content=dl\r\n".
                "accept-language: en-US,en;q=0.9\r\n".
                "cookie: _ga=GA1.2.379212952.1568903672\r\n".
                "cookie: _gcl_au=1.1.1676101868.1569076728\r\n".
                "cookie: _hjid=1d6bf9d1-92be-451f-93e5-c56add3fb3af\r\n".
                "cookie: _gid=GA1.2.702656979.1571371218\r\n".
                "cookie: ASP.NET_SessionId=ytjpdr3spqnfgmq2s04pu055\r\n".
                "cookie: _gat=1\r\n".
                "cookie: SelectProduct_us_en=mfcj285dw_us%2cmfcj985dw_us_eu_as%2cdcp110c_us%2cdcp165c_all%2cdcp130c_all%2cdcp8025d_us%2cdcp1000_us%2cdcp585cw_all%2cdcp395cn_all%2cmfcj280w_us\r\n".
                "if-modified-since: Thu, 17 Oct 2019 06:04:02 GMT\r\n";
    }
}

function curl($url, $postfields = null){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
    curl_setopt($ch, CURLOPT_TIMEOUT, 400);
    // curl_setopt($ch, CURLOPT_HTTPHEADER, getHeader($header));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if($postfields != null){
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    }
    $result = curl_exec($ch);
    list($header, $body) = explode("\r\n\r\n", $result);
    return [$header, $body];
}
echo "Masukkan PATHNAME review URL : ";
$urlPath = trim(fgets(STDIN));
echo "Masukkan Code Name Printer / Scanner : ";
$tipe = trim(fgets(STDIN));
echo "========= Masukkan Mode Table =========\n";
echo "1. SweetAlert Mode\n2. HashLink Only\n3. No HashLink No Review\n";
echo "Masukkan Pilihan : ";
$listTable = (int) trim(fgets(STDIN));
$getLink = curl(URL_SEARCH.$tipe)[1];
if(preg_match('/Object Moved/i', $getLink)){
    echo "Getting Link.....\n";
    $linkFetch = preg_match('/href="([^"]+)/', $getLink, $realLink);
    $linkDownload = urldecode($realLink[1]);
    echo "Your Link : ".strtoupper($tipe)."|".$linkDownload."\n";
}else{
    $context = stream_context_create([
        'http' => [
            'method' => "GET",
            'header' => getHeader("search")
        ]
    ]);
    $getLink = file_get_contents(URL_SEARCH.$tipe, false, $context);
    preg_match_all('/href="\/\g\/b\/downloadtop.aspx([^"]+)/', $getLink, $realLink); // Index 1
    foreach($realLink[1] as $key => $foreachLink){
        $getName = explode("=", $foreachLink);
        $getName = strtoupper(end($getName));
        echo "[".++$key."] ".$getName."|".URL_DOWNLOAD.$foreachLink."\n";
    }
    echo "Masukkan Pilihan : ";
    $getInputLink = trim(fgets(STDIN));
    $getInputLink = (int) $getInputLink - 1;
    $linkDownload = $realLink[1][$getInputLink];
}
$context = stream_context_create([
    'http' => [
        'method' => "GET",
        'header' => getHeader("download")
    ]
]);
if(preg_match('/Object Moved/i', $getLink)){
    $fetchDownload = file_get_contents(URL_HOME.$linkDownload);
    $parsingParam = explode("?", $linkDownload);
}else{
    $fetchDownload = file_get_contents(URL_DOWNLOAD.$realLink[1][$getInputLink]);
    $parsingParam = explode("?", $linkDownload);
}
$context = stream_context_create([
    'http' => [
        'method' => "GET",
        'header' => getHeader("download")
    ]
]);
$linkNew = [];
$dumpHTML = file_get_contents(URL_DOWNLOAD."?".$parsingParam[1], false, $context);
$windowsDump = preg_split('/<div class="select-item" id="radio_pane_1">/', $dumpHTML)[1];
$windowsDump = preg_split('/<!-- \/ \.select-item --><\/div>/', $windowsDump)[0];
$dumpMacOS = preg_split('/<div class="select-item" id="radio_pane_2">/', $dumpHTML)[1];
$dumpMacOS = preg_split('/<!-- \/ \.select-item --><\/div>/', $dumpMacOS)[0];
$dumpLinux = preg_split('/<div class="select-item" id="radio_pane_3">/', $dumpHTML)[1];
$dumpLinux = preg_split('/<!-- \/ \.select-item --><\/div>/', $dumpLinux)[0];
$splitFirst = [$windowsDump, $dumpMacOS, $dumpLinux];
foreach($splitFirst as $key => $OSSelect){
    preg_match_all('/<li><input type="radio" id="(.*?)"/', $OSSelect, $topID);
    rsort($topID[1]);
    // print_r($topID[1]);
    if($key === 0){
        $linkNew['windows'] = file_get_contents(DOWNLOAD_FIX.$parsingParam[1]."&os=".$topID[1][0], false, $context); //10013 Win 10 64 Bit   84 Win Vista
    }elseif($key === 1){
        $linkNew['mac'] = file_get_contents(DOWNLOAD_FIX.$parsingParam[1]."&os=".$topID[1][0], false, $context); //10060 10.15   10006 OS x 10.10   10045 OS 10.13    115 OS 10.8
    }else{
        foreach($topID[1] as $linuxGlobal){
            if((int) $linuxGlobal === 128){
                $linkNew['linux (deb)'] = file_get_contents(DOWNLOAD_FIX.$parsingParam[1]."&os=128", false, $context);
            }elseif((int) $linuxGlobal === 127){
                $linkNew['linux (rpm)'] = file_get_contents(DOWNLOAD_FIX.$parsingParam[1]."&os=127", false, $context); 
            }else{
                break;
            } 
        }
    }
}
foreach($linkNew as $bitKey => $getMatch){
    preg_match_all('/<td><em><a href="([^"]+)">([^<]+)/', $getMatch, $desc);
    preg_match_all('/<td><em><a href="([^"]+)/', $getMatch, $down);
    foreach($down[1] as $key => $download){
        $linkDownloadNew = file_get_contents(URL_HOME.$download, false, $context);
        preg_match('/id="pane2">[ \r\n]+(.*?<\/p>)/', $linkDownloadNew, $upportOS);
        preg_match('/<p class="btn"><a href="([^"]+)/', $linkDownloadNew, $linkBaru);
        $getRealNow = file_get_contents(URL_HOME.$linkBaru[1], false, $context);
        $regexValidDownload = preg_match('/<a id="downloadfile" href="([^"]+)/', $getRealNow, $validLink);
        $getTemplate[$bitKey][] = ['title' => $desc[2][$key], 'support_os' => trim(strip_tags($upportOS[1])), 'link_download' => $validLink[1]];
    }
}
if(!array_key_exists('windows', $getTemplate)){
    $getTemplate['windows'][0] = ['title' => "Built-in driver (Doesn't Have Download Link!)", 'support_os' => "Windows 98 ~ Windows 10 (All Bit)", 'link_download' => $urlPath];
}
$manualLinkFetch = file_get_contents(MANUAL_LINK.$parsingParam[1], false, $context);
preg_match_all('/<div class="type3comment">([^<]+)/', $manualLinkFetch, $descriptionManual); // key 1
preg_match_all('/<td class="text-center">[ \r\n]+(.*<a href=")([^"]+)/', $manualLinkFetch, $downloadManual); // key 2
preg_match_all('/class="icon"><\/div><div><em>([^<]+)/', $manualLinkFetch, $titleManual); // key 1
if(count($titleManual[1]) > 5){
    $countManual = 5;
}else{
    $countManual = count($titleManual[1]);
}
$excludeContainer = ["htm", "html"];
$y = 0;
for($i = 0; $i < $countManual; $i++){
    $explodeManual = explode(".", $downloadManual[2][$i]);
    $getEndHTML = end($explodeManual);
    if(in_array($getEndHTML, $excludeContainer) || trim($getEndHTML) === "htm" || trim($getEndHTML) === "html"){
        continue;
    }else{
        $getTemplate['manual'][$y] = ['title' => $titleManual[1][$i], 'support_os' => $descriptionManual[1][$i], 'link_download' => $downloadManual[2][$i]];
        $y++;
    }
}
print_r($getTemplate);
$template = [];
foreach($getTemplate as $bitKey => $criteria){
    if($bitKey === "windows"){
        $nameStrong = "Windows x64/x86";
        $desc = "Support OS:";
    }elseif($bitKey === "mac"){
        $nameStrong = "Mac";
        $desc = "Support OS:";
    }elseif($bitKey === "manual"){
       $nameStrong = "Manual";
       $desc = "Description:";
    }else{
        $nameStrong = $bitKey;
        $desc = "Support OS:";
    }
    /*
    @param $listTable int
    1 === SwallAlert Mode
    2 === HashLink Only
    3 === No Review, No HashLink
    */
    if((int) $listTable === 1){
        $template[$bitKey] = '<div><a href="#" class="hrefLink">Brother '.strtoupper($tipe).' '.$nameStrong.'</a><div style="display:none;"><div style="text-align: center;"><div><div style="text-align: start;"><table id="zaposphere-table-courtyard" style="border-collapse: collapse; border: #D9D9D9; mso-border-alt: solid #D9D9D9 .5pt; mso-border-insideh-themecolor: background1; mso-border-insideh-themeshade: 217; mso-border-insideh: .5pt solid #D9D9D9; mso-border-insidev-themecolor: background1; mso-border-insidev-themeshade: 217; mso-border-insidev: .5pt solid #D9D9D9; mso-border-themecolor: background1; mso-border-themeshade: 217; mso-border-top-alt: solid red 3.0pt; mso-padding-alt: 0cm 5.4pt 0cm 5.4pt; mso-yfti-tbllook: 1184;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td bgcolor="#CCCCCC" width="168"><div align="center"><strong>Download</strong></div></td><td bgcolor="#CCCCCC" width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><strong>Driver</strong></div></div></div></td></tr></tbody><tbody>';
        for($i = 0; $i < count($criteria); $i++){
            $downloadHash = encrypt($criteria[$i]['link_download']);
            $template[$bitKey] .= '<tr><td width="168"><div align="center"><a href="'.$urlPath.'" class="hrefDownload" id="'.$downloadHash.'"><button style="background-color: #3377FF; border-bottom-left-radius: 2px; border-bottom-right-radius: 2px; border-top-left-radius: 2px; border-top-right-radius: 2px; border: 0px; color: white; cursor: pointer; font-size: 12px; font-weight: bold; margin: 0px; max-width: 100%; padding: 10px 30px 11px; text-transform: uppercase; vertical-align: bottom;">DOWNLOAD</button></a></div></td><td width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><b>'.$criteria[$i]['title'].'</b><br>'.$desc.'<br><small>'.$criteria[$i]['support_os'].'</small></div></div></div></td></tr>';
        }
        $template[$bitKey] .= '</tbody></table><div style="text-align: center;"></div></div></div></div></div></div>';
    }elseif((int) $listTable === 2){
        $template[$bitKey] = '<div>Brother '.strtoupper($tipe).' '.$nameStrong.'<div style="text-align: center;"><div><div style="text-align: start;"><table id="zaposphere-table-courtyard" style="border-collapse: collapse; border: #D9D9D9; mso-border-alt: solid #D9D9D9 .5pt; mso-border-insideh-themecolor: background1; mso-border-insideh-themeshade: 217; mso-border-insideh: .5pt solid #D9D9D9; mso-border-insidev-themecolor: background1; mso-border-insidev-themeshade: 217; mso-border-insidev: .5pt solid #D9D9D9; mso-border-themecolor: background1; mso-border-themeshade: 217; mso-border-top-alt: solid red 3.0pt; mso-padding-alt: 0cm 5.4pt 0cm 5.4pt; mso-yfti-tbllook: 1184;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td bgcolor="#CCCCCC" width="168"><div align="center"><strong>Download</strong></div></td><td bgcolor="#CCCCCC" width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><strong>Driver</strong></div></div></div></td></tr></tbody><tbody>';
        for($i = 0; $i < count($criteria); $i++){
            $downloadHash = encrypt($criteria[$i]['link_download']);
            $template[$bitKey] .= '<tr><td width="168"><div align="center"><button class="hrefDownload" id="'.$downloadHash.'" style="background-color: #3377FF; border-bottom-left-radius: 2px; border-bottom-right-radius: 2px; border-top-left-radius: 2px; border-top-right-radius: 2px; border: 0px; color: white; cursor: pointer; font-size: 12px; font-weight: bold; margin: 0px; max-width: 100%; padding: 10px 30px 11px; text-transform: uppercase; vertical-align: bottom;">DOWNLOAD</button></div></td><td width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><b>'.$criteria[$i]['title'].'</b><br>'.$desc.'<br><small>'.$criteria[$i]['support_os'].'</small></div></div></div></td></tr>';
        }
        $template[$bitKey] .= '</tbody></table><div style="text-align: center;"></div></div></div></div></div></div>';
    }else{
        $template[$bitKey] = '<div>Brother '.strtoupper($tipe).' '.$nameStrong.'<div style="text-align: center;"><div><div style="text-align: start;"><table id="zaposphere-table-courtyard" style="border-collapse: collapse; border: #D9D9D9; mso-border-alt: solid #D9D9D9 .5pt; mso-border-insideh-themecolor: background1; mso-border-insideh-themeshade: 217; mso-border-insideh: .5pt solid #D9D9D9; mso-border-insidev-themecolor: background1; mso-border-insidev-themeshade: 217; mso-border-insidev: .5pt solid #D9D9D9; mso-border-themecolor: background1; mso-border-themeshade: 217; mso-border-top-alt: solid red 3.0pt; mso-padding-alt: 0cm 5.4pt 0cm 5.4pt; mso-yfti-tbllook: 1184;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td bgcolor="#CCCCCC" width="168"><div align="center"><strong>Download</strong></div></td><td bgcolor="#CCCCCC" width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><strong>Driver</strong></div></div></div></td></tr></tbody><tbody>';
        for($i = 0; $i < count($criteria); $i++){
            $template[$bitKey] .= '<tr><td width="168"><div align="center"><a href="'.$criteria[$i]['link_download'].'" target="_blank"><button style="background-color: #3377FF; border-bottom-left-radius: 2px; border-bottom-right-radius: 2px; border-top-left-radius: 2px; border-top-right-radius: 2px; border: 0px; color: white; cursor: pointer; font-size: 12px; font-weight: bold; margin: 0px; max-width: 100%; padding: 10px 30px 11px; text-transform: uppercase; vertical-align: bottom;">DOWNLOAD</button></a></div></td><td width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><b>'.$criteria[$i]['title'].'</b><br>'.$desc.'<br><small>'.$criteria[$i]['support_os'].'</small></div></div></div></td></tr>';
        }
        $template[$bitKey] .= '</tbody></table><div style="text-align: center;"></div></div></div></div></div></div>';
    }
}
$realTemplate = "<div id=\"download-section\">".implode(PHP_EOL, $template)."</div>";
$saveFile = fopen(__DIR__."/../saved/".$tipe.".html", "a");
fwrite($saveFile, $realTemplate.PHP_EOL);
fclose($saveFile);
echo "Done File Saved to ".__DIR__."\\".$tipe.".html\n";
?>