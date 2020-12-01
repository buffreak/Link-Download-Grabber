<?php
set_time_limit(0);
//error_reporting(0);
//error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
define("URL_HOME", "https://www.epson.com");
define("URL_SEARCH", "https://epson.com/search/autocomplete/SearchBox?term=");
$printerBit = [];
$validLink = [];
function pickHeader($type){
    if($type === "search"){
       return [
            "Host: epson.com",
            "Connection: keep-alive",
            "Accept: application/json, text/javascript, */*; q=0.01",
            "X-NewRelic-ID: VQQFVlJaARABU1VSAggPUg==",
            "X-Requested-With: XMLHttpRequest",
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.120 Safari/537.36",
            "Sec-Fetch-Mode: cors",
            "Sec-Fetch-Site: same-origin",
            "Referer: https://epson.com/usa",
            "Accept-Language: en-US,en;q=0.9",
            'Cookie: visid_incap_1843513=BXzWv2AfQAmhL/QCeoesihkto10AAAAAQUIPAAAAAABR6juO6clk3KLT2lSPvE49; _ga=GA1.2.1349128627.1570975026; _gcl_au=1.1.1308074584.1570975035; _fbp=fb.1.1570975035682.1518884834; _CT_RS_=Recording; WRUIDAWSCD=2472421565892016; visitor_id407272=199866991; visitor_id407272-hash=a259ec204a0162c146a8daf5031305f16496e41093620fd5c36930192d4f46568584835021e262e49c4cdc1fb4e20c9e3e8db82c; _cs_ex=1; _cs_c=1; ki_r=; nlbi_1843513=/q9pbwtL+jRaFsv0tjns7gAAAACtRHxiXJUgqFy46wi3sBhm; incap_ses_724_1843513=oUfHYz6B5HzTyJ/ENSsMCjkmp10AAAAAAJI4ZDzEMhYZMwuVvdynyQ==; originating_page=epson world map:world map page:world map page; camp_type=none; _gid=GA1.2.1785630845.1571235389; JSESSIONID=11C222EAA4E833540644D64E58E4F80F; ROUTEID=.route1; check=true; AMCVS_0FEE68F454E6D1890A4C98A6%40AdobeOrg=1; session_entry_url=epson.com/usa; session_entry_page_name=epson:us:home:homepage; google_match=Ran; utag_vnum=1573567022692&vn=2; utag_invisit=true; s_cc=true; utag_dslv_s=Less than 1 day; ki_t=1570975042292%3B1571235413200%3B1571236152558%3B2%3B7; _gat_tealium_0=1; _gat_tealium_1=1; utag_vs=10; utag_dslv=1571236271991; mbox=PC#4331fbff7694451e902369d2354f0481.29_4#1634480207|session#31a5f21f7d2c4d769eb8e102a558da45#1571238133; __CT_Data=gpv=8&ckp=tld&dm=epson.com&apv_101_www33=8&cpv_101_www33=8&rpv_101_www33=8; prevpt=undefined; utag_main=v_id:016dc5684861000aa23d9d56dcc303072001e06a00978$_sn:2$_ss:0$_st:1571238073991$dc_visit:2$_se:2$ses_id:1571235388379%3Bexp-session$_pn:6%3Bexp-session$_prevpage:epson%3Aus%3Asingle_function_inkjet_printers%3Aspdp%3Aspt_c11cd12201%3Bexp-1571239871341$dc_event:13%3Bexp-session$dc_region:ap-northeast-1%3Bexp-session; AMCV_0FEE68F454E6D1890A4C98A6%40AdobeOrg=-1891778711%7CMCIDTS%7C18186%7CMCMID%7C75773552801794700591866981772346026969%7CMCAAMLH-1571579807%7C3%7CMCAAMB-1571841074%7CRKhpRz8krg2tLO6pguXWp5olkAcUniQYPHaMWWgdJ3xzPWQmdj0y%7CMCOPTOUT-1571242604s%7CNONE%7CMCAID%7CNONE%7CvVersion%7C2.4.0%7CMCCIDH%7C-451895741; ctm=eydwZ3YnOjM3MDM0NTQ1MDE3Mjc5NDF8J3ZzdCc6NjM1NzIwMjQ4MjAwODI0fCd2c3RyJzo0NDcyMjAwODczMzIzNTg2fCdpbnRyJzoxNTcxMjM2MzAxMTY2fCd2JzoxfCdsdnN0Jzo0MzM0fQ==; s_sq=epsonglobalhybrisprod%3D%2526pid%253Depson%25253Aus%25253Ahome%25253Ahomepage%2526pidt%253D1%2526oid%253D%25250A%252509%252509%252509%252509%252509%252509%25250A%252509%252509%252509%252509%252509%2526oidt%253D3%2526ot%253DSUBMIT'
       ];
    }elseif($type === "home"){
        return  "Host: epson.com\r\n".
                "Connection: keep-alive\r\n".
                "Upgrade-Insecure-Requests: 1\r\n".
                "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.120 Safari/537.36\r\n".
                "Sec-Fetch-Mode: navigate\r\n".
                "Sec-Fetch-User: ?1\r\n".
                "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3\r\n".
                "Sec-Fetch-Site: same-origin\r\n".
                "Referer: https://epson.com/usa\r\n".
                "Accept-Language: en-US,en;q=0.9\r\n".
                "Cookie: nav_st=#support; visid_incap_1843513=BXzWv2AfQAmhL/QCeoesihkto10AAAAAQUIPAAAAAABR6juO6clk3KLT2lSPvE49; _ga=GA1.2.1349128627.1570975026; _gcl_au=1.1.1308074584.1570975035; _fbp=fb.1.1570975035682.1518884834; WRUIDAWSCD=2472421565892016; _CT_RS_=Recording; visitor_id407272=199866991; visitor_id407272-hash=a259ec204a0162c146a8daf5031305f16496e41093620fd5c36930192d4f46568584835021e262e49c4cdc1fb4e20c9e3e8db82c; _cs_ex=1; _cs_c=1; _gid=GA1.2.1785630845.1571235389; BVBRANDID=731b4cb1-31d4-4fda-ab1b-340001aea170; ki_s=190377%3A0.0.0.0.2%3B190378%3A0.0.0.0.2%3B190379%3A0.0.0.0.2%3B190385%3A0.0.0.0.2%3B190386%3A0.0.0.0.2%3B190387%3A0.0.0.0.2; originating_page=epson world map:world map page:world map page; camp_type=none; ROUTEID=.route0; check=true; session_entry_page_name=epson:us:home:homepage; session_entry_url=epson.com/usa; google_match=Ran; AMCVS_0FEE68F454E6D1890A4C98A6%40AdobeOrg=1; s_cc=true; ki_r=; BVImplmain_site=5560; JSESSIONID=D53D4363AF9D350BC6A8E388FE830C85; incap_ses_529_1843513=71IRD0tDF0Hg9v9KsGNXB8SZqF0AAAAAt/AKBMBMfuWpECra1kB4Nw==; BVBRANDSID=41dd14ac-65c6-49fa-8d2c-c8c530d43f52; utag_vnum=1573567022692&vn=6; utag_invisit=true; utag_dslv_s=Less than 1 day; incap_ses_725_1843513=JJc0NzKXuiKWxCuf7rgPCjmeqF0AAAAA7qGRMfN2jooK3F2ApuoTxQ==; _gat_tealium_0=1; _gat_tealium_1=1; nlbi_1843513=dI4XWIgPgicnf4hgtjns7gAAAAD+jVQuDxK+Ycvr0yBr9DRF; utag_vs=74; utag_dslv=1571331669568; __CT_Data=gpv=57&ckp=tld&dm=epson.com&apv_101_www33=68&cpv_101_www33=68&rpv_101_www33=65; prevpt=undefined; AMCV_0FEE68F454E6D1890A4C98A6%40AdobeOrg=-1891778711%7CMCIDTS%7C18186%7CMCMID%7C75773552801794700591866981772346026969%7CMCAAMLH-1571579807%7C3%7CMCAAMB-1571936470%7CRKhpRz8krg2tLO6pguXWp5olkAcUniQYPHaMWWgdJ3xzPWQmdj0y%7CMCOPTOUT-1571337703s%7CNONE%7CMCAID%7CNONE%7CvVersion%7C2.4.0%7CMCCIDH%7C-451895741; mbox=PC#4331fbff7694451e902369d2354f0481.29_4#1634575303|session#538c5b2cea65474582d70b548072867c#1571333531; ki_t=1570975042292%3B1571294852280%3B1571331671304%3B3%3B59; utag_main=v_id:016dc5684861000aa23d9d56dcc303072001e06a00978\$_sn:6\$_ss:0\$_st:1571333470249\$dc_visit:6\$_se:7\$_prevpage:epson%3Aus%3Ahome%3Ahomepage%3Bexp-1571335269451\$ses_id:1571330501610%3Bexp-session\$_pn:10%3Bexp-session\$dc_event:23%3Bexp-session\$dc_region:ap-northeast-1%3Bexp-session\$_timing_url:https%3A%2F%2Fepson.com%2Fusa\$_timing_dp1:0-25ms\$_timing_dp2:1-1.5s\$_timing_dp3:%3E3s\$_timing_dp4:%3E3s; ctm=eydwZ3YnOjU1NjU5NjYxMzA2Nzk4Mjl8J3ZzdCc6NzE4NTQzMDI4NjA1NzI4MnwndnN0cic6NDQ3MjIwMDg3MzMyMzU4NnwnaW50cic6MTU3MTMzMTY3NDE0NXwndic6MXwnbHZzdCc6NDN9; s_sq=epsonglobalhybrisprod%3D%2526pid%253Depson%25253Aus%25253Ahome%25253Ahomepage%2526pidt%253D1%2526oid%253D%25250A%252509%252509%252509%252509%252509%252509%25250A%252509%252509%252509%252509%252509%2526oidt%253D3%2526ot%253DSUBMIT\r\n";
    }
}

function curl($url, $header = "search", $postfields = null){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 400);
    curl_setopt($ch, CURLOPT_HTTPHEADER, pickHeader($header));
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
echo "Epson Link Download Grabber US/CA/AU/UK ".date("Y")." Beta Version v4.3\n\n";
echo "Masukkan PATHNAME review URL : ";
$urlPath = trim(fgets(STDIN));
echo "1.US/CA, 2.UK, 3.AU : ";
$pencarian = trim(fgets(STDIN));
echo "Masukkan Codename : ";
$tipe = trim(fgets(STDIN));
echo "========= Masukkan Mode Table =========\n";
echo "1. SweetAlert Mode\n2. HashLink Only\n3. No HashLink No Review\n";
echo "Masukkan Pilihan : ";
$listTable = (int) trim(fgets(STDIN));
if($pencarian === "1"){
    $context = stream_context_create([
        'http'=>[
          'method'=> "GET",
          'header'=> pickHeader("home")
    ]]);
    $linkPrinter = file_get_contents("https://epson.com/search/?text=".urlencode($tipe), false, $context);
    preg_match_all('/<section class="product-info">[ \r\n]+(.*)[ \r\n]+(.*?".*?".*?".*?".*?")([^"]+)/', $linkPrinter, $getLink);
    foreach($getLink[3] as $key => $realLink){
        preg_match('/Epson([^\/]+)/', $realLink, $name);
        $upperName = str_replace("-", " ", "Epson".ucwords($name[1]));
        echo "[".++$key."] => ".$upperName."|".$realLink."\n";
    }
    echo "Masukkan No Pilihan : ";
    $pilihan = trim(fgets(STDIN));
    $pilihanReal = (int) $pilihan - 1;
    $printerBit["windows_64_bit"] = file_get_contents(URL_HOME.$getLink[3][$pilihanReal]."?review-filter=Windows+10+64-bit");
    // $printerBit["windows_32_bit"] = file_get_contents(URL_HOME.$getLink['products'][$pilihanReal]['supportProductId']."?review-filter=Windows+10+32-bit");
    $printerBit["apple_all_bit"] = file_get_contents(URL_HOME.$getLink[3][$pilihanReal]."?review-filter=macOS+10.15.x"); // CUSTOM MANUAL IF MACOS HAVE HIGHER VERSION!
    foreach($printerBit as $bit => $printerDetail){
        $time = time();
        $getElement = [];
        $passedDownload = [];
        preg_match_all('/data-dl_title="([^&]+)/', $printerDetail, $title);
        preg_match_all('/data-dl_lt1="Download"[ \r\n]+(.*)[ \r\n](.*?".*?")/', $printerDetail, $linkDownload);
        preg_match_all('/<p class="sub-header">[ \r\n]+(.*Compatible systems)[ \r\n]+(.*)[ \r\n]+(.*)[ \r\n]+(.*)[ \r\n]+([^<]+)/', $printerDetail, $supportOS);

        // GET DOWNLOAD LINK MATCHED REGEX
        foreach($linkDownload[2] as $realDownload){
            // $downloadFopen = fopen(__DIR__."/".$time."downloadLink.txt", "a");
            // fwrite($downloadFopen, $realDownload.PHP_EOL);
            $getElement[] = $realDownload;
        }
        foreach($getElement as $realElement){
            preg_match('/href="([^"]+)/', $realElement, $containerLink);
            $passedDownload[] = $containerLink[1];
        }
        $matchValue = 0;
        // $getElement = file_get_contents($time."downloadLink.txt");
        // preg_match_all('/href="([^"]+)/', $getElement, $passedDownload);
        $removeClone = [];
        foreach($title[1] as $kunciSakti => $realTitle){
            if(!in_array(strtolower(trim($realTitle)), $removeClone)){
                $validLink[$bit][] = ["title" => trim(ucwords($realTitle)), "link_download" => $passedDownload[$matchValue], "support_os" => trim($supportOS[5][$kunciSakti])];
            }
            $removeClone[] = strtolower(trim($realTitle));
            $matchValue++;
        }
        // unlink($time."downloadLink.txt");
    }
    $y = 0;
    $splitFirst = preg_split('/<div class="simple-responsive-banner-component module-manuals">/', $printerBit["windows_64_bit"])[1];
    $splitSecond = preg_split('/<\!-- SDS -->/', $splitFirst)[0];
    $getListManual = preg_split('/<div class="module-row">/', $splitSecond);
    array_shift($getListManual);
    foreach($getListManual as $key => $manual){
        @preg_match('/(<b>(.*?)<\/b>)/', $manual, $title); // index 0 then strip_tags() titleManual
        @preg_match_all('/class="version-link" href="(.*?)"/', $manual, $linkDownload); // Index 1 Then Loop to get spesific value DownloadManualLink
        @preg_match('/<p>(.*?)<\/p>/', $manual, $description); // Index 1 then strip_tags() DescriptionManual
        if(@count($linkDownload[1]) > 1){
            foreach($linkDownload[1] as $linkManual){
                if(preg_match('/\.htm/', $linkManual) || preg_match('/\.html/', $linkManual)){
                    continue;
                }
                $validLink['manual'][$y] = ["title" => ucwords(strip_tags($title[0])), "link_download" => trim($linkManual), "support_os" => strip_tags($description[1])];
                $y++;
            }
        }else{
            $validLink['manual'][$y] = ["title" => ucwords(strip_tags($title[0])), "link_download" => trim($linkDownload[1][0]), "support_os" => strip_tags($description[1])];
            $y++;
        }
    }
    print_r($validLink);
}elseif($pencarian === "2"){
    define("UK_SEARCH", 'https://www.epson.co.uk/viewcon/corporatesite/search/ajax?type=esupport&limit=99999&search=');
    define("UK_PRODUCT", 'https://www.epson.co.uk/support?productID=');
    define("SUPPORT_OS", [
        'windows_64_bit' => 'Windows 10 64-bit, Windows 10 32-bit, Windows 8/8.1 64-bit, Windows 8/8.1 32-bit, Windows 7 64-bit, Windows 7 32-bit',
        'apple_all_bit' => 'macOS 10.15, macOS 10.14, macOS 10.13, macOS 10.12, OS X 10.11, OS X 10.10'
    ]);
    $context = stream_context_create([
        'http'=>[
          'method'=> "GET"
    ]]);
    $str = int.$sa;
    $getProduct = json_decode(file_get_contents(UK_SEARCH.urlencode($tipe), false, $context), true);
    foreach($getProduct['results'] as $key => $product){
        echo "[".++$key."] => ".$product['name_s']."\n";
    }
    echo "Masukkan Pilihan Diatas : ";
    $pilih = (int) trim(fgets(STDIN)) - 1;
    $idSplit = explode("/", stripslashes($getProduct['results'][$pilih]['link_s']));
    $idProduct = trim($idSplit[count($idSplit) - 1]);
    $dumps = [];
    $dumps['windows_64_bit'] = file_get_contents(UK_PRODUCT.$idProduct.'&os=28', false, $context);
    sleep(1);
    $dumps['apple_all_bit'] = file_get_contents(UK_PRODUCT.$idProduct.'&os=36', false, $context);
    foreach($dumps as $key => $dump){
        $split = preg_split('/id="manuals"/', $dump)[0];
        preg_match_all('/<a href="(.*?)" class="btn btn-grey"/', $split, $downloadDriver); // Index 1 Then Loop Its Driver Download Link
        preg_match_all('/<h4 class="is-toggle">(.*)<small>/', $split, $titleDriver); // Index 1 Then Loop Its Driver Title Name
        foreach($downloadDriver[1] as $keyDriver => $driver){
            $validLink[$key][] = ['title' => $titleDriver[1][$keyDriver], 'link_download' => $driver, 'support_os' => SUPPORT_OS[$key]];
        }
    }
    $getManual = preg_split('/id="manuals"/', $dumps['windows_64_bit'])[1];
    preg_match_all('/<h4 class="is-toggle">[ \r\n]+(.*?)<small>/', $getManual, $manualTitle);
    preg_match_all('/<a href="(.*?)" class="btn btn-grey"/', $getManual, $downloadManual);
    foreach($downloadManual[1] as $key => $manual){
        $validLink['manual'][] = ['title' => $manualTitle[1][$key], 'link_download' => $manual, 'support_os' => $manualTitle[1][$key]];
    }
    print_r($validLink);
}elseif($pencarian === "3"){
    define("SELECT_PRINTER", 'http://tech.epson.com.au/downloads/index.asp');
    define('SELECT_OS', 'http://tech.epson.com.au/downloads/category.asp');
    define("GET_DRIVER", 'http://tech.epson.com.au/downloads/product.asp');
    define('DOWNLOAD_URL', 'http://tech.epson.com.au/downloads/');
    define('DOWNLOAD_URL_SECOND', 'http://www.downloads.epson.com.au/DownloadFile.asp?filename=');
    define("FETCH_LIST", [
        'EcoTank', 'EcoTankInkjet', 'Multi_Functional', 'Inkjet', 'LabelWorksLabelPrinters', 'Laser', 'BusinessPrinter', 'Multi_Functional_Laser', 'Dotmatrix', 'Scanner'
    ]);
    $param = [];
    foreach(FETCH_LIST as $key => $selector){
        $context = stream_context_create([
            'http'=> [
              'method'=> "POST",
              'header'  => 'Content-Type: application/x-www-form-urlencoded',
              'content' => http_build_query(['select' => $selector, 'sCategory' => '', 'as_fid' => '91fbbc27b7f21981e3f9516a2da23bbb08665166'])
        ]]);
        $getContent = file_get_contents(SELECT_PRINTER, false, $context);
        $split = preg_split('<div class="select-list">', $getContent)[1];
        preg_match_all('/<option value="(.*?)">(.*?)</', $split, $nameSelector); //Index 1 for ID, Index 2 For Name Printer
        foreach($nameSelector[2] as $keyPrinter => $namePrinter){
            if(stripos($namePrinter, $tipe) !== false){
                echo "Apakah Ini Product-nya => ".$namePrinter." 1.ya 0.deep search : ";
                $agree = trim(fgets(STDIN));
                if($agree){
                    $param['windows_64_bit'] = http_build_query(['sCategory' => $key, 'id' => $nameSelector[1][$keyPrinter], 'techtips' => '', 'platform' => 'wi1064bit', 'FileType' => '1', 'as_fid' => '82154497d6ce11a4931077a0f70555303a2bb0b2']);
                    $param['apple_all_bit'] = http_build_query(['sCategory' => $key, 'id' => $nameSelector[1][$keyPrinter], 'techtips' => '', 'platform' => 'osx1015', 'FileType' => '1', 'as_fid' => '82154497d6ce11a4931077a0f70555303a2bb0b2']);
                    $param['manual'] = http_build_query(['sCategory' => $key, 'id' => $nameSelector[1][$keyPrinter], 'techtips' => 'techtips', 'platform' => 'wi1064bit', 'FileType' => '1', 'as_fid' => '82154497d6ce11a4931077a0f70555303a2bb0b2']);
                    break 2;
                }
            }
        }
    }
    foreach($param as $key => $section){
        $context = stream_context_create([
            'http'=> [
              'method'=> "POST",
              'header'  => 'Content-Type: application/x-www-form-urlencoded',
              'content' => $section
        ]]);
        $getContents = file_get_contents(GET_DRIVER, false, $context);
        $split = preg_split('/<div class="download container\-fluid">/', $getContents);
        array_shift($split);
        foreach($split as $keyContent => $getContent){
            if($key !== 'manual'){
                preg_match('/<div class="download-name">[ \r\n]+(.*?)<h3>(.*?)<\/h3>(.*)/', $getContent, $nameSelector); // Index 2 Then Loop its name for Driver / manual, Index 3 its Support OS
                if(preg_match('/<div class="download-click">[ \r\n]+(.*?)<a href="(.*?)"/', $getContent)){
                    preg_match('/<div class="download-click">[ \r\n]+(.*?)<a href="(.*?)"/', $getContent, $linkSelector); // Index 2 its Link Download Driver / manual then add url homepage DOWNLOAD_URL
                    $secondFetch = file_get_contents(DOWNLOAD_URL.$linkSelector[2], false, stream_context_create([
                        'http'=>[
                        'method'=> "GET",
                        'follow_location' => true
                    ]]));
                    preg_match('/driver_download\(\'(.*?)\'/', $secondFetch, $linkDrivers);
                    $linkDriver = $linkDrivers[1];
                }else{
                    preg_match('/<div class="download-file col-xs-12 col-sm-4">[ \r\n]+(.*?)<strong>(.*?)</', $getContent, $linkSelector); // Index 2 its Link Download Driver / manual then add url homepage DOWNLOAD_URL_SECOND
                    $linkDriver = DOWNLOAD_URL_SECOND.rawurlencode($linkSelector[2]).'&path=Drivers';
                }
                $validLink[$key][] = ['title' => $nameSelector[2], 'link_download' => $linkDriver, 'support_os' => $nameSelector[3]];
            }else{
                preg_match('/<div class="download-name">[ \r\n]+(.*?)<h3>(.*?)<\/h3>(.*)/', $getContent, $nameSelector); // Index 2 Then Loop its name for Driver / manual, Index 3 its Support OS
                if(preg_match('/<div class="download-file col-xs-12 col-sm-4">[ \r\n]+(.*?)<strong>(.*?)</', $getContent)){
                    preg_match('/<div class="download-file col-xs-12 col-sm-4">[ \r\n]+(.*?)<strong>(.*?)</', $getContent, $linkSelector); // Index 2 its Link Download Driver / manual then add url homepage DOWNLOAD_URL_SECOND
                    $linkManual = DOWNLOAD_URL_SECOND.rawurlencode($linkSelector[2]).'&path=TechTips';
                    $validLink[$key][] = ['title' => $nameSelector[2], 'link_download' => $linkManual, 'support_os' => $nameSelector[2]];
                }
            }
        }
    }
    print_r($validLink);
}
$template = [];
    /*
    @param $listTable int
    1 === SwallAlert Mode
    2 === HashLink Only
    3 === No Review, No HashLink
    */
    foreach($validLink as $bitKey => $criteria){
        if($bitKey === "windows_64_bit"){
            $nameStrong = "Windows x64/x86";
            $desc = "Support OS:";
        }elseif($bitKey === "apple_all_bit"){
            $nameStrong = "Mac";
            $desc = "Support OS:";
        }else{
           $nameStrong = "Manual";
           $desc = "Description:";
        }
        if($listTable === 1){
            $template[$bitKey] = '<div><a href="#" class="hrefLink">Epson '.strtoupper($tipe).' '.$nameStrong.'</a><div style="display:none;"><div style="text-align: center;"><div><div style="text-align: start;"><table id="zaposphere-table-courtyard" style="border-collapse: collapse; border: #D9D9D9; mso-border-alt: solid #D9D9D9 .5pt; mso-border-insideh-themecolor: background1; mso-border-insideh-themeshade: 217; mso-border-insideh: .5pt solid #D9D9D9; mso-border-insidev-themecolor: background1; mso-border-insidev-themeshade: 217; mso-border-insidev: .5pt solid #D9D9D9; mso-border-themecolor: background1; mso-border-themeshade: 217; mso-border-top-alt: solid red 3.0pt; mso-padding-alt: 0cm 5.4pt 0cm 5.4pt; mso-yfti-tbllook: 1184;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td bgcolor="#CCCCCC" width="168"><div align="center"><strong>Download</strong></div></td><td bgcolor="#CCCCCC" width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><strong>Driver</strong></div></div></div></td></tr></tbody><tbody>';
            for($i = 0; $i < count($criteria); $i++){
                $downloadHash = encrypt($criteria[$i]['link_download']);
                $template[$bitKey] .= '<tr><td width="168"><div align="center"><a href="'.$urlPath.'" id="'.$downloadHash.'" class="hrefDownload"><button style="background-color: #3377FF; border-bottom-left-radius: 2px; border-bottom-right-radius: 2px; border-top-left-radius: 2px; border-top-right-radius: 2px; border: 0px; color: white; cursor: pointer; font-size: 12px; font-weight: bold; margin: 0px; max-width: 100%; padding: 10px 30px 11px; text-transform: uppercase; vertical-align: bottom;">DOWNLOAD</button></a></div></td><td width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><b>'.$criteria[$i]['title'].'</b><br>'.$desc.'<br><small>'.$criteria[$i]['support_os'].'</small></div></div></div></td></tr>';
            }
            $template[$bitKey] .= '</tbody></table><div style="text-align: center;"></div></div></div></div></div></div>';
        }elseif($listTable === 2){
            $template[$bitKey] = '<div>'.$tipe.' '.$nameStrong.'<div style="text-align: center;"><div><div style="text-align: start;"><table id="zaposphere-table-courtyard" style="border-collapse: collapse; border: #D9D9D9; mso-border-alt: solid #D9D9D9 .5pt; mso-border-insideh-themecolor: background1; mso-border-insideh-themeshade: 217; mso-border-insideh: .5pt solid #D9D9D9; mso-border-insidev-themecolor: background1; mso-border-insidev-themeshade: 217; mso-border-insidev: .5pt solid #D9D9D9; mso-border-themecolor: background1; mso-border-themeshade: 217; mso-border-top-alt: solid red 3.0pt; mso-padding-alt: 0cm 5.4pt 0cm 5.4pt; mso-yfti-tbllook: 1184;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td bgcolor="#CCCCCC" width="168"><div align="center"><strong>Download</strong></div></td><td bgcolor="#CCCCCC" width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><strong>Driver</strong></div></div></div></td></tr></tbody><tbody>';
            for($i = 0; $i < count($criteria); $i++){
                $downloadHash = encrypt($criteria[$i]['link_download']);
                $template[$bitKey] .= '<tr><td width="168"><div align="center"><button id="'.$downloadHash.'" class="hrefDownload" style="background-color: #3377FF; border-bottom-left-radius: 2px; border-bottom-right-radius: 2px; border-top-left-radius: 2px; border-top-right-radius: 2px; border: 0px; color: white; cursor: pointer; font-size: 12px; font-weight: bold; margin: 0px; max-width: 100%; padding: 10px 30px 11px; text-transform: uppercase; vertical-align: bottom;">DOWNLOAD</button></div></td><td width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><b>'.$criteria[$i]['title'].'</b><br>'.$desc.'<br><small>'.$criteria[$i]['support_os'].'</small></div></div></div></td></tr>';
            }
            $template[$bitKey] .= '</tbody></table><div style="text-align: center;"></div></div></div></div></div></div>';
        }else{
            $template[$bitKey] = '<div>'.$tipe.' '.$nameStrong.'<div style="text-align: center;"><div><div style="text-align: start;"><table id="zaposphere-table-courtyard" style="border-collapse: collapse; border: #D9D9D9; mso-border-alt: solid #D9D9D9 .5pt; mso-border-insideh-themecolor: background1; mso-border-insideh-themeshade: 217; mso-border-insideh: .5pt solid #D9D9D9; mso-border-insidev-themecolor: background1; mso-border-insidev-themeshade: 217; mso-border-insidev: .5pt solid #D9D9D9; mso-border-themecolor: background1; mso-border-themeshade: 217; mso-border-top-alt: solid red 3.0pt; mso-padding-alt: 0cm 5.4pt 0cm 5.4pt; mso-yfti-tbllook: 1184;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td bgcolor="#CCCCCC" width="168"><div align="center"><strong>Download</strong></div></td><td bgcolor="#CCCCCC" width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><strong>Driver</strong></div></div></div></td></tr></tbody><tbody>';
            for($i = 0; $i < count($criteria); $i++){
                // $downloadHash = encrypt($criteria[$i]['link_download']);
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