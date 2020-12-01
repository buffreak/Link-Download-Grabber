<?php
error_reporting(0);
error_reporting(E_ALL ^ (E_NOTICE));
class Logitech{
    protected $_url, $_codename, $_tempURL, $_finalData, $_postfields, $_header, $_body, $_dataFetch, $_pilihan, $_temp, $_tempTable, $_data, $_webproduct;
    protected static $instance;
    const URL_SEARCH = "https://api.converseapps.com/v1/instant/search";
    const WEB_OS = [
        "webos=windows-Windows-10",
        "webos=mac-macos-x-10.15",
        "webos=chrome"
    ];

    protected function _cookieLogitech(){
        $header = [
            "user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:71.0) Gecko/20100101 Firefox/71.0",
            "accept: */*",
            "accept-language: en-US,en;q=0.5",
            "x-requested-with: XMLHttpRequest",
            "x-csrf-token: hc:requests:client:CIUraZRS+tnEjXWjPEufbMJIP1vyobIyojx9uKGBC4LM6ty+gcTx6nxucj+tCfSPotQ8V1Ogkw2rO6nvs37MNA==",
            "referer: https://support.logi.com/hc/en-us/articles/360024704814--Downloads-G502-LIGHTSPEED-Wireless-Gaming-Mouse",
            "cookie: __cfduid=d5f5d9728bb15eeea1cc4d430f7cd630b1576748579",
            "cookie: dc:def:u=809c251b-ddab-49fd-8483-ed0d3bc4ee29",
            "cookie: _ga=GA1.2.1308622774.1576748586",
            "cookie: _gid=GA1.2.1060298281.1576748586",
            "cookie: BASE=en-us",
            "cookie: __zlcmid=vpilxAQHghNVGm",
            "cookie: __cfruid=b45e19df6608729e72db8b60e721d6690080a938-1576926328",
            "cookie: _zendesk_shared_session=-ajBic0ZGNE9EVEN3aUNUWWJGZ2dwdGZOWXdpcGIwK3BxRStXOUxsRVVsWXh5aVNNWUFvMjJHdFQ1TGZHM0NzWkFhUVhDS2YrUmZNTmgyVlR1Z0xTR0o3SWc5Sjgxd01LVGh0UmthbjFYN0lmRndLSS92b3JWWG5iRkJUSWRqQ1g5ZFBqdEZpeWI2anNqRDI3MFpETUNZNmRreDYrNGpMVlFwbVNiam9IV1ZVPS0taVNQanZrc1FTWndrYWkzdFNoZUNqZz09--646bd09c26c38e78253628061c18b21abc0b6277",
            "cookie: _help_center_session=S3BPZHlKT0IvR0FGZk1WQmpaTjhodTluV0FhMEVVeHRSRlhaS3ZSOUp3dVRYK3crQlEzME1QU3RwVlIzakFGODkvZUhKM0FnRUZLQzltd3lxMWFjL0pNUnBDdmxZUnJ0RjVaRU5Ib3Rtd3hQckxacW94dm1kcXQxSnZSTVh3dHlrNXVPOWJXaUE4czZUK21FMll5cTZxK3Q2dURWUHlXYjAwbjBlb0VGdzAwc3BZQzdwRm4rZHZNWFpGejlGaHg3TUFnRTlSV0cxNTdMYUY4SWphVEx4V3ZyVmNhYTJodzFFeStZVlgyUXZKOD0tLTIyZ2l1T201ZjIxa2xjdDhhaFpNdWc9PQ%3D%3D--91bf1741b617f366f6128b7eae23e1c05bb147d2",
            "cookie: _gat_gtag_UA_55257712_6=1",
            "te: trailers"
        ];
        return $header;
    }

    protected function _cookieSearch(){
        $header = [
            "user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:71.0) Gecko/20100101 Firefox/71.0",
            "accept: application/json",
            "accept-language: en-US,en;q=0.5",
            "x-site-id: 1b2dfaa778e8af5222c8bc2265501f",
            "x-application-id: 52116A4A",
            "content-type: text/plain;charset=UTF-8",
            "origin: https://support.logi.com",
            "referer: https://support.logi.com/hc/en-001",
            "te: trailers"
        ];
        return $header;
    }


    protected function _curl($url, $post = false, $header){ 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if($post){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        $result = curl_exec($ch);
        list($header, $body) = explode("\r\n\r\n", $result);
        return json_decode($body, true);
    }

    protected function __construct($url, $codename){
        $this->_url = $url;
        $this->_codename = $codename;
    }

    public static function run($url, $codename){
        if(!isset(self::$instance)){
            self::$instance = new Logitech($url, $codename);
        }
        return self::$instance;
    }

   protected function _paramGetID(){
        $query = [
            'query' => $this->_codename,
            'subject' => $this->_codename,
            'size' => (int) 5, 
            'page' => (int) 0,
            'baseUrl' => "https://logitech.zendesk.com/hc/en-001",
            'locale' => "en_001",
            'userId' => "d973cf57-8527-4669-9819-1a577978ae79",
            'visitId' => "0b41a54c-5150-4cb1-832e-bd53732b4a1b",
            'sessionId' => "1bb36fa8-44bf-49f7-9748-e4e780928f05",
            'sequenceId' => (int) 1,
            'searchId' => 'f4db01d1-fd51-4794-ac11-7c4dc2ecb529',
            'dataSource' => "all",
            'widgetSource' => "INSTANT_SEARCH",
            'widgetPage' => "product-support",
            'highlightPreTag' => "<span class=\"dc-hit--highlight\">",
            'highlightPostTag' => "</span>",
            'contextData' => [
                'appId' => "52116A4A",
                'siteId' => "1b2dfaa778e8af5222c8bc2265501f",
                'locale' => "en_001",
                'appUrl' => "https://api.converseapps.com",
                'widgetPage' => "product-support",
                'baseUrl' => "https://logitech.zendesk.com/hc/en-001",
                'searchBoxSelector' => "#dc-search",
                'highlightColor' => '#7a3ff9',
                'searchPageBaseUrl' => "/search#",
                'pageUrl' => "https://support.logi.com/hc/en-001/articles/360024361233",
                'userRole' => "anonymous"
            ],
            'userAgent' => "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:71.0) Gecko/20100101 Firefox/71.0",
            'pageUrl' => "https://support.logi.com/hc/en-001/articles/360024361233",
            'isTypeahead' => (bool) true
        ];
        return json_encode($query, JSON_UNESCAPED_SLASHES);
   }
    
    public function getID(){
        $curl = $this->_curl(self::URL_SEARCH, $this->_paramGetID(), $this->_cookieSearch());
        foreach($curl['hits'] as $key => $value){
            $short = $value['_source']['raw'];
            if(array_key_exists('software_tile_name', $short) || empty($short['product_tile_name'])){
                continue;
            }
            $this->_dataFetch[] = ['id_html' => $value['_id'], 'html_url' => $short['html_url'], 'label_name' => $short['label_names'], 'name' => strip_tags($short['product_tile_name'])." ".strip_tags($short['product_tile_name_2'])];
        }
        foreach($this->_dataFetch as $key => $pilihan){
            echo "[".++$key."] => ".$pilihan['name']."\n";
        }
        echo "Masukkan Pilihan Diatas : ";
        $this->_pilihan = (int) trim(fgets(STDIN)) - 1;
        $this->_selected = $this->_dataFetch[$this->_pilihan];
        foreach($this->_selected['label_name'] as $key => $value){
            if(strpos($value, 'webproduct=') !== false){
                $this->_webproduct = $key;
                break;
            }
        }
        return $this;
    }

    public function getDriver(){
        foreach(self::WEB_OS as $key => $value){
            if(strpos($value, "windows") !== false){
                $supportOS = "Windows 7, Windows 8, Windows 10";
            }elseif(strpos($value, "macos") !== false){
                $supportOS = "macOS 10.15, macOS 10.14, macOS 10.13, macOS 10.12, OS X 10.11, OS X 10.10";
            }else{
                $supportOS = "Chrome OS x86/x64 Architecture";
            }
            $url = "https://support.logi.com/api/v2/help_center/en-us/articles.json?label_names=webcontent=productdownload,".$this->_selected['label_name'][$this->_webproduct].",".$value.",";
            $this->_temp = $this->_curl($url, false, $this->_cookieLogitech());
            $container = [];
            if($this->_temp['count'] > 0){
                foreach($this->_temp['articles'] as $keyResult => $result){
                    preg_match('/a class="download-button" href="([^"]+)/', $result['body'], $linkDownload); // MATCHED KEY INDEX ARR 1
                    if(!in_array(trim($linkDownload[1]), $container)){
                        $name = $result['name'];
                        $this->_data[$value][] = ['name' => $name, 'link_download' => trim($linkDownload[1]), 'support_os' => $supportOS];
                        $container[] = trim($linkDownload[1]);
                    }
                }
            }
        }
        return $this;
    }

    public function getManual(){
        $url = "https://support.logi.com/api/v2/help_center/en-us/articles.json?label_names=".$this->_selected['label_name'][$this->_webproduct].",webcontent=productdocument";
        $this->_temp = $this->_curl($url, false, $this->_cookieLogitech());
        if($this->_temp['count'] > 0){
            foreach($this->_temp['articles'] as $keyResult => $result){
                $container = [];
                if(!in_array($result['name'], $container)){
                    preg_match('/a class="document-button" href="([^"]+)/', $result['body'], $linkDownload); // MATCHED KEY INDEX ARR 1
                    $this->_data["manual"][] = ['name' => $result['name'], 'link_download' => trim($linkDownload[1]), 'support_os' => $result['title']];
                }
            }
        }
        return $this;
    }
    public function correctData(){
        foreach($this->_data as $key => $data){
            if($key === "webos=windows-Windows-10"){
                $arrKey = "Windows";
            }elseif($key === "webos=mac-macos-x-10.15"){
                $arrKey = "macOS";
            }elseif($key === "webos=chrome"){
                $arrKey = "Chrome OS";
            }elseif($key === "manual"){
                $arrKey = "Manual";
            }
            foreach($data as $keyData => $valuePair){
                if(strpos($valuePair['link_download'], "x64") !== false){
                    $name = $valuePair['name']." 64-BIT";
                    $supportOS = $valuePair['support_os']. " (64-Bit)";
                }elseif(strpos($valuePair['link_download'], "x86") !== false){
                    $name = $valuePair['name']." 32-BIT";
                    $supportOS = $valuePair['support_os']. " (32-Bit)";
                }else{
                    $name = $valuePair['name']." 32/64 Bit Support";
                    $supportOS = $valuePair['support_os']." 32/64 Bit Support";
                }
                $this->_finalData[$arrKey][] = ['name' => $name, 'link_download' => ($valuePair['link_download']), 'support_os' => $supportOS];
            }
        }
        return $this;
    }

    public function removeClone(){
        $removeClone = [];
        foreach($this->_finalData as $key => $value){
           foreach($value as $keyChain => $valuePair){
                if(in_array(trim($valuePair['name']), $removeClone)){
                    unset($this->_finalData[$key][$keyChain]);
                }
                $removeClone[] = trim($valuePair['name']);
           }
        }
        return $this;
    }

    protected function _saveStream(){
        $fopen = fopen(__DIR__."/../saved/".$this->_codename.".html", "a");
        fwrite($fopen, $this->_tempTable.PHP_EOL);
        fclose($fopen);
        return;
    }

    public function getTable(){
        foreach($this->_finalData as $title => $value){
            if(strpos($title, "Manual") !== false){
                $desc = "Description:";
            }else{
               $desc = "Support OS:";
            }
            $this->_tempTable = '<div><a href="#" class="hrefLink">Download Logitech '.$this->_selected['name'].' for '.$title.'</a><div style="display:none;"><div style="text-align: center;"><div><div style="text-align: start;"><table id="zaposphere-table-courtyard" style="border-collapse: collapse; border: #D9D9D9; mso-border-alt: solid #D9D9D9 .5pt; mso-border-insideh-themecolor: background1; mso-border-insideh-themeshade: 217; mso-border-insideh: .5pt solid #D9D9D9; mso-border-insidev-themecolor: background1; mso-border-insidev-themeshade: 217; mso-border-insidev: .5pt solid #D9D9D9; mso-border-themecolor: background1; mso-border-themeshade: 217; mso-border-top-alt: solid red 3.0pt; mso-padding-alt: 0cm 5.4pt 0cm 5.4pt; mso-yfti-tbllook: 1184;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td bgcolor="#CCCCCC" width="168"><div align="center"><strong>Download</strong></div></td><td bgcolor="#CCCCCC" width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><strong>Driver</strong></div></div></div></td></tr></tbody><tbody>';
            for($i = 0; $i < count($value); $i++){
                $this->_tempTable .= '<tr><td width="168"><div align="center"><a href="'.$this->_url.'" class="hrefDownload" id="'.encrypt($value[$i]['link_download']).'"><button style="background-color: #3377FF; border-bottom-left-radius: 2px; border-bottom-right-radius: 2px; border-top-left-radius: 2px; border-top-right-radius: 2px; border: 0px; color: white; cursor: pointer; font-size: 12px; font-weight: bold; margin: 0px; max-width: 100%; padding: 10px 30px 11px; text-transform: uppercase; vertical-align: bottom;">DOWNLOAD</button></a></div></td><td width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><b>'.$value[$i]['name'].'</b><br>'.$desc.'<br><small>'.$value[$i]['support_os'].'</small></div></div></div></td></tr>';
            }
            $this->_tempTable .= '</tbody></table><div style="text-align: center;"></div></div></div></div></div></div>';
            $this->_saveStream();
        }
        return $this->_finalData;
    }
    public static function input(){
        return trim(fgets(STDIN));
    }
}
echo "Masukkan URL PATHNAME : ";
$url = Logitech::input();
echo "Masukkan Codename : ";
$codename = Logitech::input();
print_r(
    Logitech::run($url, $codename)
    ->getID()
    ->getDriver()
    ->getManual()
    ->correctData()
    ->removeClone()
    ->getTable()
);
echo "[SAVED] to DIR => ".__DIR__."\\".$codename.".html";
?>