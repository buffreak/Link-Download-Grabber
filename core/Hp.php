<?php
error_reporting(0);
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
set_time_limit(0);
date_default_timezone_set("Asia/jakarta");
/*
// Develop By @BuffFreak 2020
// All Contributors in BeuJaya.com
// This Contain Private License
*/
class Hp{
    protected $_url, $_codename, $_temp, $_pilihan, $_input, $_link, $_listTable, $_container, $_post = null, $_driver, $_switchCookie = 1, $_namePrinter, $_idManual, $_debug, $_table;
    protected static $instance;

    const URL = [
        "driver_path" => "https://support.hp.com/us-en/drivers/selfservice/",
        "fetch_driver" => 'https://support.hp.com/wps/portal/pps/Home/SWDSelfServiceStep/!ut/p/z1/04_Sj9CPykssy0xPLMnMz0vMAfIjo8zifQ08DYy83A28LcK8TA0cHR39jN08gwwNjAz0w8EKnN0dPUzMfQwM3ANNnAw8zX39vV2DLIwNPM30o4jRb4ADOBoQpx-Pgij8xofrR4GV4PMBITMKckNDIwwyHQHZipEy/p0/IZ7_M0I02JG0KGVO00AUBO4GT60082=CZ6_M0I02JG0K8VJ50AAAN3FIR1020=NJgetSoftwareDriverDetails=/',
        'support_home' => 'https://support.hp.com/wps/portal/pps/Home/SWDSelfServiceStep/!ut/p/z1/04_Sj9CPykssy0xPLMnMz0vMAfIjo8zifQ08DYy83A28LcK8TA0cHR39jN08gwwNjAz0w8EKnN0dPUzMfQwM3ANNnAw8zX39vV2DLIwNPM30o4jRb4ADOBoQpx-Pgij8xofrR4GV4PMBITMKckNDIwwyHQHZipEy/p0/IZ7_M0I02JG0KGVO00AUBO4GT60082=CZ6_M0I02JG0K8VJ50AAAN3FIR1020=NJgetSoftwareDriverDetails=/'
        
    ];

    const SUPPORT_OS = [
        'windows' => 'Windows 10 (32-bit), Windows 10 (64-bit), Windows 8.1 (32-bit), Windows 8.1 (64-bit), Windows 8 (32-bit), Windows 8 (64-bit), Windows 7 (32-bit), Windows 7 (64-bit), Windows Vista (32-bit), Windows Vista (64-bit), Windows XP (32-bit), Windows XP (64-bit)',
        'macos' => 'macOS (10.15), macOS (10.14), macOS (10.13), macOS (10.12), OS X (10.11), OS X (10.10)',
        'linux' => 'Linux (rpm), Linux (deb)'
    ];

    const PRIVACY_POLICY = [
        'privyPolicyURL' => 'http://www8.hp.com/us/en/privacy/privacy.html',
        'termsOfUseTitle' => "By downloading you agree to HP's Terms of Use undefined",
        'bitInfoUrl' => 'https://support.hp.com/us-en/document/c03666764',
        'hpTermsOfUseURL' => 'https://support.hp.com/us-en/document/c00581401',
        'inOSDriverLinkURL' => 'https://support.hp.com/us-en/document/c01796879',
        'languageValue' => 'English',
        'sku' => '',
        'osLanguageName' => 'en',
        'osLanguageCode' => 'en'
    ];


    const OS = [
        'Windows' => ['platformId' => '487192269364721453674728010296573', 'osId' => '792898937266030878164166465223921', 'osName' => 'Windows 10 (64-bit)'],
        'Mac OS' => ['platformId' => '275027708611380099388405694207665', 'osId' => '18015185915131310124113888731054140111953115', 'osName' => 'macOS 10.15'],
        'Linux' => ['platformId' => '61630413913869876174022933309613', 'osId' => '530006069043305437166081915438460', 'osName' => 'Linux']
    ];

    protected function __construct($url, $codename, $listTable){
        $this->_url = $url;
        $this->_codename = $codename;
        $this->_listTable = (int) $listTable;
    }

    protected function _cookie(){
        $getContent = explode("\n", file_get_contents(__DIR__."/../inc/HP/cookieDriver.txt"));
        foreach($getContent as $key => $cookie){
            $cookies[] = trim($cookie);
        }
        return $cookies;
    }

    protected function _cookieManual(){
        $getContent = explode("\n", file_get_contents(__DIR__."/../inc/HP/cookieManual.txt"));
        foreach($getContent as $key => $cookie){
            $cookies[] = trim($cookie);
        }
        return $cookies;
    }

    protected function _curl(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_temp);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        if($this->_switchCookie):
            curl_setopt($ch, CURLOPT_HTTPHEADER, ($this->_switchCookie ? $this->_cookie() : $this->_cookieManual()));
        else:
            curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__.'/../inc/HP/cookie.txt');
            curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__.'/../inc/HP/cookie.txt');
        endif;
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        if($this->_post){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_post);
        }
        $result = curl_exec($ch);
        $this->_debug = $result;
        list($header, $body) = explode("\r\n\r\n", $result, 2);
        return json_decode($body, true);
    }

    public static function run($url, $codename, $listTable){
        // if(!isset(self::$instance)){
            self::$instance = new Hp($url, $codename, $listTable);
        // }
        return self::$instance;
    }

    public function cookieStamp(){
        $this->_switchCookie = 0;
        fwrite(fopen(__DIR__.'/../inc/HP/cookie.txt', 'w'), '');
        $this->_temp = self::URL['support_home'];
        $curl = $this->_curl();
        $this->_switchCookie = 1;
        return $this;
    }

    public function getProduct($fetch = false){
        $this->_temp = "https://support.hp.com/typeahead?q=".rawurlencode($this->_codename)."&resultLimit=10&store=tmsstore&languageCode=en&filters=class:(pm_series_value%5E1.1%20OR%20pm_name_value%20OR%20pm_number_value)&printFields=tmspmnamevalue,title,body,childnodes,class,productid,seofriendlyname,shortestnavigationpath";
        $curl = $this->_curl();
        if(count($curl['matches']) === 0 || count($curl['matches']) < 1){
            throw new Exception("No Product Name Available!, please check your codename");
        }
        if($fetch){
            foreach($curl['matches'] as $key => $product){
                echo "[".++$key."] => ".$product['name']."\n";
            }
            echo "Masukkan Pilihan Diatas 1 - ".count($curl['matches'])." : ";
            $this->_input = trim(fgets(STDIN));
            $this->_namePrinter = $curl['matches'][(int) $this->_input - 1]['name'];
            $this->_pilihan = $curl['matches'][(int) $this->_input - 1];
            $this->_link = self::URL['driver_path'].$this->_pilihan['seoFriendlyName']."/".(string) $this->_pilihan['productId'];
        }else{
            $this->_namePrinter = $curl['matches'][0]['name'];
            $this->_pilihan = $curl['matches'][0];
            $this->_link = self::URL['driver_path'].$this->_pilihan['seoFriendlyName']."/".(string) $this->_pilihan['productId'];
        }
        return $this;
    }

    public function fetchLink(){
        foreach(self::OS as $key => $value){
            $this->_post = http_build_query(['requestJson' => $this->_param($key, $value['platformId'], $value['osId'], $value['osName'])]);
            $this->_temp = self::URL['fetch_driver'];
            $curl = $this->_curl();
            $this->_container[$key] = json_decode($curl['swdJson'], true);
        }
        $this->_post = null; // Recasting
        return $this;
    }

    public function parsingDriver(){
        foreach($this->_container as $firstKey => $firstBlock){
            if($firstKey === "Windows"){
                $supportOs = self::SUPPORT_OS['windows'];
            }elseif($firstKey === "Mac OS"){
                $supportOs = self::SUPPORT_OS['macos'];
            }else{
                $supportOs = self::SUPPORT_OS['linux'];
            }
            foreach($firstBlock as $secondKey => $driverList){
                foreach($driverList as $thirdKey => $latestDriver){
                    foreach($latestDriver as $finalKey => $realDriver){
                        $short = $realDriver['latestVersionDriver'];
                        $this->_driver[$firstKey][] = ['title' => $short['title'], 'download' => $short['fileUrl'], 'desc' => strip_tags($short['detailInformation']['description']), 'support_os' => $supportOs];
                    }
                }
            }
        }
        return $this;
    }

    public function fetchManual(){
        $this->_switchCookie = 0;
        $this->_temp = "https://support.hp.com/hp-pps-services/pdpTaxonomy/topicDetail?prodOId=&seriesId=".$this->_pilihan['productId']."&tmsId=97572587488906955347968195879383&seriesName=".str_replace(" ", "+", $this->_namePrinter)."&contentNavType=user+guide+navigation&cc=us&lc=en&_=1579342069891";
        $curl = $this->_curl();
        foreach($curl['attachedManuals'] as $key => $manual){
            if(stripos($manual['langCode'], 'en') !== false || stripos($manual['langName'], 'english') !== false){
                foreach($manual['fullTitleVO'] as $keyManual => $value){
                    $this->_driver['Manual'][] = ['title' => $value['value'], 'download' => $value['url'], 'desc' => $value['value'], 'support_os' => $value['value']];
                }
            }
        }
        return $this;
    }
    
    public function getTable($fetch = false){
        $counter = 0;
        foreach($this->_driver as $keyOS => $value){
            if($keyOS === "Manual"){
                $text = "Download HP Manual User Guide ";
                $category = "Description:";
            }else{
                $text = "Download HP Driver for ";
                $category = "Support OS:";
            }
            if($this->_listTable === 1){
                $this->_table[$counter] = '<div><a href="#" class="hrefLink">'.$text.$keyOS.'</a><div style="display:none;"><div style="text-align: center;"><div><div style="text-align: start;"><table id="zaposphere-table-courtyard" style="border-collapse: collapse; border: #D9D9D9; mso-border-alt: solid #D9D9D9 .5pt; mso-border-insideh-themecolor: background1; mso-border-insideh-themeshade: 217; mso-border-insideh: .5pt solid #D9D9D9; mso-border-insidev-themecolor: background1; mso-border-insidev-themeshade: 217; mso-border-insidev: .5pt solid #D9D9D9; mso-border-themecolor: background1; mso-border-themeshade: 217; mso-border-top-alt: solid red 3.0pt; mso-padding-alt: 0cm 5.4pt 0cm 5.4pt; mso-yfti-tbllook: 1184;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td bgcolor="#CCCCCC" width="168"><div align="center"><strong>Download</strong></div></td><td bgcolor="#CCCCCC" width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><strong>Driver</strong></div></div></div></td></tr></tbody><tbody>';
                for($i = 0; $i < count($value); $i++){
                    $this->_table[$counter] .= '<tr><td width="168"><div align="center"><a href="'.$this->_url.'" class="hrefDownload" id="'.encrypt($value[$i]['download']).'"><button style="background-color: #3377FF; border-bottom-left-radius: 2px; border-bottom-right-radius: 2px; border-top-left-radius: 2px; border-top-right-radius: 2px; border: 0px; color: white; cursor: pointer; font-size: 12px; font-weight: bold; margin: 0px; max-width: 100%; padding: 10px 30px 11px; text-transform: uppercase; vertical-align: bottom;">DOWNLOAD</button></a></div></td><td width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><b>'.$value[$i]['title'].'</b><br>'.$category.'<br><small>'.$value[$i]['support_os'].'</small></div></div></div></td></tr>';
                }
                $this->_table[$counter] .= '</tbody></table><div style="text-align: center;"></div></div></div></div></div></div>';
            }elseif($this->_listTable === 2){
                $this->_table[$counter] = '<div>'.$text.$keyOS.'<div style="text-align: center;"><div><div style="text-align: start;"><table id="zaposphere-table-courtyard" style="border-collapse: collapse; border: #D9D9D9; mso-border-alt: solid #D9D9D9 .5pt; mso-border-insideh-themecolor: background1; mso-border-insideh-themeshade: 217; mso-border-insideh: .5pt solid #D9D9D9; mso-border-insidev-themecolor: background1; mso-border-insidev-themeshade: 217; mso-border-insidev: .5pt solid #D9D9D9; mso-border-themecolor: background1; mso-border-themeshade: 217; mso-border-top-alt: solid red 3.0pt; mso-padding-alt: 0cm 5.4pt 0cm 5.4pt; mso-yfti-tbllook: 1184;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td bgcolor="#CCCCCC" width="168"><div align="center"><strong>Download</strong></div></td><td bgcolor="#CCCCCC" width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><strong>Driver</strong></div></div></div></td></tr></tbody><tbody>';
                for($i = 0; $i < count($value); $i++){
                    $this->_table[$counter] .= '<tr><td width="168"><div align="center"><button class="hrefDownload" id="'.encrypt($value[$i]['download']).'" style="background-color: #3377FF; border-bottom-left-radius: 2px; border-bottom-right-radius: 2px; border-top-left-radius: 2px; border-top-right-radius: 2px; border: 0px; color: white; cursor: pointer; font-size: 12px; font-weight: bold; margin: 0px; max-width: 100%; padding: 10px 30px 11px; text-transform: uppercase; vertical-align: bottom;">DOWNLOAD</button></div></td><td width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><b>'.$value[$i]['title'].'</b><br>'.$category.'<br><small>'.$value[$i]['support_os'].'</small></div></div></div></td></tr>';
                }
                $this->_table[$counter] .= '</tbody></table><div style="text-align: center;"></div></div></div></div></div></div>';
            }else{
                $this->_table[$counter] = '<div>'.$text.$keyOS.'<div style="text-align: center;"><div><div style="text-align: start;"><table id="zaposphere-table-courtyard" style="border-collapse: collapse; border: #D9D9D9; mso-border-alt: solid #D9D9D9 .5pt; mso-border-insideh-themecolor: background1; mso-border-insideh-themeshade: 217; mso-border-insideh: .5pt solid #D9D9D9; mso-border-insidev-themecolor: background1; mso-border-insidev-themeshade: 217; mso-border-insidev: .5pt solid #D9D9D9; mso-border-themecolor: background1; mso-border-themeshade: 217; mso-border-top-alt: solid red 3.0pt; mso-padding-alt: 0cm 5.4pt 0cm 5.4pt; mso-yfti-tbllook: 1184;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td bgcolor="#CCCCCC" width="168"><div align="center"><strong>Download</strong></div></td><td bgcolor="#CCCCCC" width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><strong>Driver</strong></div></div></div></td></tr></tbody><tbody>';
                for($i = 0; $i < count($value); $i++){
                    $this->_table[$counter] .= '<tr><td width="168"><div align="center"><a href="'.$value[$i]['download'].'" target="_blank"><button style="background-color: #3377FF; border-bottom-left-radius: 2px; border-bottom-right-radius: 2px; border-top-left-radius: 2px; border-top-right-radius: 2px; border: 0px; color: white; cursor: pointer; font-size: 12px; font-weight: bold; margin: 0px; max-width: 100%; padding: 10px 30px 11px; text-transform: uppercase; vertical-align: bottom;">DOWNLOAD</button></a></div></td><td width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><b>'.$value[$i]['title'].'</b><br>'.$category.'<br><small>'.$value[$i]['support_os'].'</small></div></div></div></td></tr>';
                }
                $this->_table[$counter] .= '</tbody></table><div style="text-align: center;"></div></div></div></div></div></div>';
            }
            $counter++;
        }
        if($fetch){
            $this->_saveStream();
            fwrite(fopen(__DIR__.'/../inc/HP/cookie.txt', 'w'), '');   
        }
        return $this->_driver;
    }

    protected function _saveStream(){
        return fwrite(fopen(__DIR__."/../saved/".$this->_codename.".html", 'a'), "<div id=\"download-section\">".implode(PHP_EOL, $this->_table)."</div>");
    }

    protected function _param($OSType, $platformId, $osId, $versionName){
        return json_encode([
            'productNameOid' => $this->_pilihan['productId'],
            'urlLanguage' => 'en',
            'language' => 'en',
            'osId' => $osId,
            'countryCode' => 'us',
            'detectedOSBit' => '',
            'platformName' => $OSType,
            'platformId' => $platformId,
            'versionName' => $versionName,
            'versionId' => $osId,
            'osLanguageName' => 'en',
            'osLanguageCode' => 'en',
            'hpTermsOfUseURL' => self::PRIVACY_POLICY['hpTermsOfUseURL'],
            'inOSDriverLinkURL' => self::PRIVACY_POLICY['inOSDriverLinkURL'],
            'languageValue' => 'English',
            'termsOfUseTitle' => self::PRIVACY_POLICY['termsOfUseTitle'],
            'privyPolicyURL' => self::PRIVACY_POLICY['privyPolicyURL'],
            'bitInfoUrl' => self::PRIVACY_POLICY['bitInfoUrl'],
            'sku' => '',
            'productSeriesOid' => $this->_pilihan['productId'],
            'productSeriesName' => $this->_pilihan['seoFriendlyName']
        ], JSON_UNESCAPED_SLASHES);
    }

    public static function input(){
        return trim(fgets(STDIN));
    }
}
echo "Masukkan URL Path Artikel : ";
$url = Hp::input();
echo "Masukkan Codename : ";
$codename = Hp::input();
echo "========= Masukkan Mode Table =========\n";
echo "1. SweetAlert Mode\n2. HashLink Only\n3. No HashLink No Review\n";
echo "Masukkan Pilihan : ";
$listTable = Hp::input();
$increment = 0;
while(1){
    if(stripos(file_get_contents(__DIR__.'/../inc/HP/cookie.txt'), 'JSESSIONID') === false){
        echo "Fetching All Component Cookie Required....\n";
        HP::run($url, $codename, $listTable)
        ->cookieStamp();
    }else{
        try{
            if(!$increment){
                Hp::run($url, $codename, $listTable)
                ->getProduct()
                ->fetchLink()
                ->parsingDriver()
                ->fetchManual()
                ->getTable();
                $increment++;
            }else{
                print_r(
                    Hp::run($url, $codename, $listTable)
                    ->getProduct(true)
                    ->fetchLink()
                    ->parsingDriver()
                    ->fetchManual()
                    ->getTable(true)
                );
                break;
            }
        }catch(Exception $e){
            echo $e->getMessage()."\n";
        }
    }
}

?>