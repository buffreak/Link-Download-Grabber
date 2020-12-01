<?php
class Razer{

    protected $_url, $_codename, $_tempURL, $_post = 0, $_opt, $_linkReview, $_productName, $_pilihan, $_parentKey, $_newLink, $_titleTag, $_driverFetch, $_finalData;
    protected static $instance;

    const HOME_URL = [
        'driver' => 'http://drivers.razersupport.com/',
        'synapse2' => 'https://www.razerzone.com/synapse-2',
        'synapse3' => 'https://www.razerzone.com/synapse-3'
    ];

    const DOWNLOAD_SYNAPSE = [
        'synapse2_windows' => 'https://dl.razerzone.com/drivers/Synapse2/win/Web_Razer_Synapse_Installer_v2.21.24.10.exe',
        'synapse3_windows' => 'https://dl.razerzone.com/drivers/Synapse3/win/RazerSynapseInstaller_V1.0.125.158.exe',
        'synapse2_macOS' => 'https://dl.razerzone.com/drivers/Synapse2/mac/Razer_Synapse_Mac_Driver_v1.87.dmg'
    ];

    const URL_SUPPORT = [
        'Laptop Series' => 'https://support.razer.com/gaming-laptops',
        'Gaming Mouse and Mousepad' => 'https://support.razer.com/gaming-mice-and-mats',
        'Gaming Keyboard' => 'https://support.razer.com/gaming-keyboards',
        'Headset and Audio' => 'https://support.razer.com/gaming-headsets-and-audio',
        'Game Console' => 'https://support.razer.com/console',
        'Software 3rd-App' => 'https://support.razer.com/software',
        'Mobile Smartphone' => 'https://support.razer.com/mobile',
        'Monitor Screen' => 'https://support.razer.com/monitors',
        'Networking' => 'https://support.razer.com/networking',
        'Gear and Wearables' => 'https://support.razer.com/wearables'
    ];

    protected function __construct($url, $codename){
        $this->_codename = $codename;
        $this->_url = $url;
    }

    public static function run($url, $codename){
        if(!isset(self::$instance)){
            self::$instance = new Razer($url, $codename);
        }
        return self::$instance;
    }

    protected function _fetchCookie(){
        $cookies = "";
        $getContent = explode("\n", file_get_contents(__DIR__."/../inc/Razer/cookie.txt"));
        foreach($getContent as $key => $cookie){
            $cookies .= trim($cookie)."\r\n";
        }
        return $cookies;
    }

    protected function _fetchStream(){
        $this->_opt = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => $this->_fetchCookie()
            ]
        ]);
        return file_get_contents($this->_tempURL, false, $this->_opt);
    }


    public function fetchData(){
        foreach(self::URL_SUPPORT as $key => $url){
            $this->_parentKey = $key;
            $this->_tempURL = $url;
            $curl = $this->_fetchStream();
            preg_match_all('/<li>[ \r\n]+(.*?")([^"]+)[ \r\n]+(.*)[ \r\n]+<p class="text-center">([^<]+)/', $curl, $matches);
            foreach($matches[1] as $key => $linkReview){
                $this->_linkReview[] = trim(substr($linkReview, 9, strlen($linkReview) - 10));
            }
            foreach($matches[4] as $key => $productName){
                $this->_productName[] = trim(html_entity_decode($productName));
            }
            foreach($this->_productName as $key => $name){
                if(strpos($name, $this->_codename) !== false){
                    echo "Apakah ".$name." Sudah Benar? jika tidak tekan 0 untuk deep search : ";
                    $pilihan = trim(fgets(STDIN));
                    if($pilihan){
                        $this->_container = [
                            'link' => $this->_linkReview[$key],
                            'name' => $this->_productName[$key],
                            'parent' => self::URL_SUPPORT[$this->_parentKey]
                        ];
                        return $this;
                    }
                }
            }
        }
    }

    protected function _synapse3(){
        $container = [];
        $explode = explode("\n", file_get_contents(__DIR__."/../inc/Razer/synapse3.txt"));
        foreach($explode as $key => $value){
            $container[] = trim($value);
        }
        return $container;
    }
    
    public function getLinkDownload(){
        $this->_tempURL = $this->_container['link'];
        $curl = $this->_fetchStream();
        preg_match_all('/<div class="product-links">[ \r\n]+(.*)[ \r\n]+(.*)[ \r\n]+(.*)[ \r\n]+(.*)[ \r\n]+(.*)[ \r\n]+(.*)/', $curl, $matches);
        $tempData = [];
        foreach($matches as $key => $value){
            preg_match('/href="([^"]+)/', $value[0], $link);
            if(count($link) !== 0){
                if(!in_array($link[1], $tempData)){
                    $tempData[] = trim($link[1]);
                }
            }
        }
        if(count($tempData) < 2){
            $tempData[] = self::HOME_URL['synapse2'];
        }
        $this->_driverFetch = ['manual' => $tempData[0], 'driver' => $tempData[1]];
        return $this;
    }

    public function generateLink(){
        foreach($this->_driverFetch as $key => $value){
            if($key === "manual"){
                if(strpos($value, ".pdf") !== false || strpos($value, ".xps") !== false){
                    $this->_finalData[$this->_container['name']." ".$key][] = ['title' => trim($this->_container['name'])." Manual User Guide", 'link_download' => trim($value), 'support_os' => 'User or Master guide'];
                }else{
                    $this->_tempURL = $value;
                    $getContent = $this->_fetchStream();
                    preg_match_all('/&raquo; <a href="(.*?)"/', $getContent, $matches);
                    $this->_tempURL = self::HOME_URL['driver'].end($matches[1]);
                    $getContent = $this->_fetchStream();
                    preg_match_all('/id="breadcrumbs">(.*)/', $getContent, $matches);
                    if(strpos($matches[1][0], "Master Guides") !== false || strpos($matches[1][0], "User Guides") !== false){
                        preg_match_all('/<a href="([^"]+)" class="dl-link">Download<\/a>/', $getContent, $downloadManual); // Download Link Manual[1] loop
                        preg_match_all('/class="text-white dl-item-title">([^<]+)/', $getContent, $titleManual); // Title Manual[1] loop
                        foreach($titleManual[1] as $keyManual => $manual){
                            $this->_finalData[$this->_container['name']." ".$key][] = ['title' => trim($manual), 'link_download' => trim($downloadManual[1][$keyManual]), 'support_os' => 'User or Master guide'];
                        }
                    }else{
                        preg_match_all('/<td align="left" valign="middle"><a (.*)/', $getContent, $fetchManual);
                        foreach($fetchManual[1] as $keyLock => $value){
                            if(strpos($value, "Master Guides") !== false || strpos($value, "User Guides") !== false){
                                preg_match('/href="([^"]+)"/', $value, $goManual);
                                $this->_tempURL = $goManual[1];
                                $getContent = $this->_fetchStream();
                                preg_match_all('/<a href="([^"]+)" class="dl-link">Download<\/a>/', $getContent, $downloadManual); // Download Link Manual[1] loop
                                preg_match_all('/class="text-white dl-item-title">([^<]+)/', $getContent, $titleManual); // Title Manual[1] loop
                                foreach($titleManual[1] as $keyManual => $manual){
                                    $this->_finalData[$this->_container['name']." ".$key][] = ['title' => trim($manual), 'link_download' => trim($downloadManual[1][$keyManual]), 'support_os' => 'User or Master guide'];
                                }
                            }
                        }
                    }
                }
            }else{
                $synpase3 = $this->_synapse3();
               if(in_array(trim($this->_container['name']), $synpase3)){
                    $this->_finalData[$this->_container['name']." ".ucwords($key)." for Windows"][] = ['title' => $this->_container['name']." Synapse 3 For Windows", 'link_download' => self::DOWNLOAD_SYNAPSE['synapse3_windows'], 'support_os' => 'Windows 10, Windows 8.1, Windows 8, Windows 7'];
               }elseif(strpos(self::HOME_URL['synapse3'], $this->_driverFetch['driver']) !== false || strpos("https://www.razer.com/synapse-3", $this->_driverFetch['driver']) !== false){
                    $this->_finalData[$this->_container['name']." ".ucwords($key)." for Windows"][] = ['title' => $this->_container['name']." Synapse 3 For Windows", 'link_download' => self::DOWNLOAD_SYNAPSE['443_windows'], 'support_os' => 'Windows 10, Windows 8.1, Windows 8, Windows 7'];
               }elseif(strpos(self::HOME_URL['synapse2'], $this->_driverFetch['driver']) !== false || strpos("https://www.razer.com/synapse-2", $this->_driverFetch['driver']) !== false){
                    $this->_finalData[$this->_container['name']." ".ucwords($key)." for Windows"][] = ['title' => $this->_container['name']." Synapse 2 For Windows", 'link_download' => self::DOWNLOAD_SYNAPSE['synapse2_windows'], 'support_os' => 'Windows 10, Windows 8.1, Windows 8, Windows 7'];
                    $this->_finalData[$this->_container['name']." ".ucwords($key)." for macOS"][] = ['title' => $this->_container['name']." Synapse 2 For macOS", 'link_download' => self::DOWNLOAD_SYNAPSE['synapse2_macOS'], 'support_os' => 'macOS 10.14, macOS 10.13, macOS 10.12, macOS 10.11, macOS 10.10'];
                }elseif($this->_container['parent'] !== self::URL_SUPPORT['Laptop Series']){
                    $this->_finalData[$this->_container['name']." ".ucwords($key)." for Windows"][] = ['title' => $this->_container['name']." Synapse 2 For Windows", 'link_download' => self::DOWNLOAD_SYNAPSE['synapse2_windows'], 'support_os' => 'Windows 10, Windows 8.1, Windows 8, Windows 7'];
                    $this->_finalData[$this->_container['name']." ".ucwords($key)." for macOS"][] = ['title' => $this->_container['name']." Synapse 2 For macOS", 'link_download' => self::DOWNLOAD_SYNAPSE['synapse2_macOS'], 'support_os' => 'macOS 10.14, macOS 10.13, macOS 10.12, macOS 10.11, macOS 10.10'];
                }elseif($this->_container['parent'] === self::URL_SUPPORT['Laptop Series']){
                    $this->_tempURL = $value;
                    $getContent = $this->_fetchStream();
                    preg_match_all('/&raquo; <a href="(.*?)"/', $getContent, $matches);
                    $this->_tempURL = self::HOME_URL['driver'].end($matches[1]);
                    $getContent = $this->_fetchStream();
                    preg_match_all('/id="breadcrumbs">(.*)/', $getContent, $matches);
                    if(strpos($matches[1][0], "Hardware Drivers") !== false || strpos($matches[1][0], "Windows 7 Hardware Drivers") !== false){
                        preg_match_all('/<a href="([^"]+)" class="dl-link">Download<\/a>/', $getContent, $downloadDriver); // Download Link Driver[1] loop
                        preg_match_all('/class="text-white dl-item-title">([^<]+)/', $getContent, $titleDriver); // Title Driver[1] loop
                        foreach($titleDriver[1] as $keyDriver => $driver){
                            $this->_finalData[$this->_container['name']." ".ucwords($key)." for Windows"][] = ['title' => trim($driver), 'link_download' => trim($downloadDriver[1][$keyDriver]), 'support_os' => 'Windows 10, Windows 8.1, Windows 8, Windows 7'];
                        }
                    }else{
                        preg_match_all('/<td align="left" valign="middle"><a (.*)/', $getContent, $fetchDriver);
                        foreach($fetchDriver[1] as $keyLock => $value){
                            if(strpos($value, "Hardware Drivers") !== false || strpos($value, "Windows 7 Hardware Drivers") !== false){
                                preg_match('/href="([^"]+)"/', $value, $goDriver);
                                $this->_tempURL = $goDriver[1];
                                $getContent = $this->_fetchStream();
                                preg_match_all('/<a href="([^"]+)" class="dl-link">Download<\/a>/', $getContent, $downloadDriver); // Download Link Driver[1] loop
                                preg_match_all('/class="text-white dl-item-title">([^<]+)/', $getContent, $titleDriver); // Title Driver[1] loop
                                foreach($titleDriver[1] as $keyDriver => $driver){
                                    $this->_finalData[$this->_container['name']." ".ucwords($key)." for Windows"][] = ['title' => trim($driver), 'link_download' => trim($downloadDriver[1][$keyDriver]), 'support_os' => 'Windows 10, Windows 8.1, Windows 8, Windows 7'];
                                }
                            }
                        }
                    }
                }
            }
        }
        return $this;
    }

    public function setTable(){
        foreach($this->_finalData as $key => $value){
            if(strpos($key, 'manual') !== false){
                $desc = "Description:";
            }else{
                $desc = "Support OS:";
            }
            $this->_tempTable = '<div><a href="#" class="hrefLink">'.$key.'</a><div style="display:none;"><div style="text-align: center;"><div><div style="text-align: start;"><table id="zaposphere-table-courtyard" style="border-collapse: collapse; border: #D9D9D9; mso-border-alt: solid #D9D9D9 .5pt; mso-border-insideh-themecolor: background1; mso-border-insideh-themeshade: 217; mso-border-insideh: .5pt solid #D9D9D9; mso-border-insidev-themecolor: background1; mso-border-insidev-themeshade: 217; mso-border-insidev: .5pt solid #D9D9D9; mso-border-themecolor: background1; mso-border-themeshade: 217; mso-border-top-alt: solid red 3.0pt; mso-padding-alt: 0cm 5.4pt 0cm 5.4pt; mso-yfti-tbllook: 1184;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td bgcolor="#CCCCCC" width="168"><div align="center"><strong>Download</strong></div></td><td bgcolor="#CCCCCC" width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><strong>Driver</strong></div></div></div></td></tr></tbody><tbody>';
            for($i = 0; $i < count($value); $i++){
                $this->_tempTable .= '<tr><td width="168"><div align="center"><a href="'.$this->_url.'" class="hrefDownload" id="'.encrypt($value[$i]['link_download']).'"><button style="background-color: #3377FF; border-bottom-left-radius: 2px; border-bottom-right-radius: 2px; border-top-left-radius: 2px; border-top-right-radius: 2px; border: 0px; color: white; cursor: pointer; font-size: 12px; font-weight: bold; margin: 0px; max-width: 100%; padding: 10px 30px 11px; text-transform: uppercase; vertical-align: bottom;">DOWNLOAD</button></a></div></td><td width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><b>'.$value[$i]['title'].'</b><br>'.$desc.'<br><small>'.$value[$i]['support_os'].'</small></div></div></div></td></tr>';
            }
            $this->_tempTable .= '</tbody></table><div style="text-align: center;"></div></div></div></div></div></div>';
            fwrite(fopen(__DIR__."/../saved/".$this->_codename.".html", 'a'), $this->_tempTable.PHP_EOL);
        }
        echo "Done File Saved To ".__DIR__."\\".$this->_codename.".html";
        return $this->_finalData;
    }
}

echo "Masukkan URL Post : ";
$url = trim(fgets(STDIN));
echo 'Masukkan Codename : ';
$codename = trim(fgets(STDIN));

print_r(
    Razer::run($url, $codename)
    ->fetchData()
    ->getLinkDownload()
    ->generateLink()
    ->setTable()
);
?>