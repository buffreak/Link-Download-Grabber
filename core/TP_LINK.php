<?php
set_time_limit(0);

class TP_LINK{
    protected $_url, $_codename, $_tempURL, $_postFields, $_exec, $_linkDriver, $_productName, $_container, $_removeClone, $_table;
    protected static $instance;

    const URL = [
        'us' => 'https://www.tp-link.com/us/support/download/',
        'uk' => 'https://www.tp-link.com/uk/support/download/',
        'au' => 'https://www.tp-link.com/au/support/download/'
    ];

    protected function __construct($url, $codename){
        $this->_url = $url;
        $this->_codename = $codename;
    }

    public function run($url, $codename){
        if(!isset(self::$instance)){
            self::$instance = new TP_LINK($url, $codename);
        }
        return self::$instance;
    }

    protected function _parsingCookie(){
        $container = [];
        $getContent = explode("\n", file_get_contents(__DIR__."/../inc/TP-Link/cookie.txt"));
        foreach($getContent as $key => $cookie){
            $container[] = trim($cookie);
        }
        return $container;
    }

    protected function _curl(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_tempURL);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_parsingCookie());
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if($this->_postFields):
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_postFields);
        endif;
        $result = curl_exec($ch);
        list($header, $body) = explode("\r\n\r\n", $result, 2);
        return ['header' => $header, 'body' => $body];
    }
    
    public function getProduct(){
        $getContent = json_decode(file_get_contents(__DIR__.'/../inc/TP-Link/productList.json'), true);
        foreach($getContent as $row => $blockID){
            foreach($blockID as $secondRow => $type){
                if(strpos(strtolower($type['model_name']), strtolower($this->_codename)) !== false){
                    echo "Apakah Benar ini Codename nya 1.ya 0. deep search => ".$type['model_name']." : ";
                    $eksekusi = trim(fgets(STDIN));
                    if($eksekusi){
                        $this->_linkDriver = str_replace(' ', '-', strtolower($type['model_name']."/"));
                        $this->_productName = $type['model_name'];
                        return $this;
                    }
                }
            }
        }
    }
    public function parsingData(){
        $this->_tempURL = self::URL['us'].$this->_linkDriver;
        $this->_exec = $this->_curl()['body'];
        preg_match('/<span id=\'verison-hidden\'>(.*?)</', $this->_exec, $nameTP);
        if(strpos($this->_exec, 'Please choose hardware version:') !== false){
            preg_match_all('/<li data-value="(.*?)">[ \r\n]+(.*?)<a href="(.*?)"/', $this->_exec, $matches); // [1] Version, [3] Link version
            echo "versi yang Tersedia :\n";
            foreach($matches[3] as $key => $value){
                $keyTemp = ++$key;
                echo "[".$keyTemp."] ".$matches[1][$keyTemp - 1]."\n";
            }
            echo "Masukkan Nomor versi Diatas : ";
            $versi = trim(fgets(STDIN));
            $linkVersion = preg_split('/download\//', $matches[3][$versi - 1]);
            $this->_linkDriver = trim($linkVersion[1]);
            $this->_productName = $this->_productName." ".$matches[1][$versi - 1];
        }else{
            $this->_productName = $this->_productName." ".$nameTP[1];
        }
        return $this;
    }

    public function getData(){
        foreach(self::URL as $regionCode => $link){
            $this->_tempURL = $link.$this->_linkDriver;
            $this->_exec = $this->_curl()['body'];
            if(preg_match('/<a href="#Firmware">/', $this->_exec) || preg_match('/<a href="#Driver">/', $this->_exec)){
                preg_match_all('/<a class="download ga-click" data-ga=\'(.*?)\' target="_blank" href="(.*?)">(.*?)</', $this->_exec, $firmwares);// [2] Link Firmware, [3] Title Firmware
                if(count($firmwares) > 0){
                    foreach($firmwares[2] as $keyFirmware => $firmware){
                        $this->_container['Firmware '.$this->_productName." ".strtoupper($regionCode)][] = ['title' => trim($firmwares[3][$keyFirmware]), 'link_download' => trim($firmware), 'support_os' => "If Name contains macOS it's for macOS, if Linux it's for Linux, otherwise for Windows"];
                    }
                }
            }
        }
        $this->_tempURL = self::URL['us'].$this->_linkDriver;
        $this->_exec = $this->_curl()['body'];
        preg_match_all('/<a target="_blank" class="ga-click" data-ga=\'(.*?)\' href="(.*?)">(.*?)</', $this->_exec, $manuals); // [2] Link Manual, [3] Title Manual 
        if(count($manuals[2]) > 0){
            foreach($manuals[2] as $keyManual => $manual){
                $this->_container['Manual '.$this->_productName." UK/US/AU"][] = ['title' => trim($manuals[3][$keyManual]), 'link_download' => trim($manual), 'support_os' => str_replace('_', ' ', trim($manuals[3][$keyManual]))];
            }
        }
        if(preg_match('/<a href="#Utility">/', $this->_exec) || preg_match('/<a href="#Management_Utility">/', $this->_exec)){
            preg_match_all('/<a class="download ga-click" data-ga=\'(.*?)\' target="_blank"  href="(.*?)" data-id="">(.*?)</', $this->_exec, $utilities);
            preg_match_all('/<td colspan="3" class="more os"><div>(.*?)</', $this->_exec, $supportOS);
            foreach($utilities[2] as $keyUtility => $utility){
                $this->_container['Utility '.$this->_productName." UK/US/AU"][] = ['title' => trim($utilities[3][$keyUtility]), 'link_download' => trim($utility), 'support_os' => str_replace('_', ' ', trim($supportOS[1][$keyUtility]))];
            }
        }
        return $this;
    }

    public function setTable(){
        foreach($this->_container as $key => $value){
            if(strpos($key, 'Manual') !== false){
                $desc = "Description:";
            }else{
                $desc = "Support OS:";
            }
            $this->_table = '<div><a href="#" class="hrefLink">'.$key.'</a><div style="display:none;"><div style="text-align: center;"><div><div style="text-align: start;"><table id="zaposphere-table-courtyard" style="border-collapse: collapse; border: #D9D9D9; mso-border-alt: solid #D9D9D9 .5pt; mso-border-insideh-themecolor: background1; mso-border-insideh-themeshade: 217; mso-border-insideh: .5pt solid #D9D9D9; mso-border-insidev-themecolor: background1; mso-border-insidev-themeshade: 217; mso-border-insidev: .5pt solid #D9D9D9; mso-border-themecolor: background1; mso-border-themeshade: 217; mso-border-top-alt: solid red 3.0pt; mso-padding-alt: 0cm 5.4pt 0cm 5.4pt; mso-yfti-tbllook: 1184;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td bgcolor="#CCCCCC" width="168"><div align="center"><strong>Download</strong></div></td><td bgcolor="#CCCCCC" width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><strong>Driver</strong></div></div></div></td></tr></tbody><tbody>';
            for($i = 0; $i < count($value); $i++){
                $this->_table .= '<tr><td width="168"><div align="center"><a href="'.$this->_url.'" class="hrefDownload" id="'.encrypt($value[$i]['link_download']).'"><button style="background-color: #3377FF; border-bottom-left-radius: 2px; border-bottom-right-radius: 2px; border-top-left-radius: 2px; border-top-right-radius: 2px; border: 0px; color: white; cursor: pointer; font-size: 12px; font-weight: bold; margin: 0px; max-width: 100%; padding: 10px 30px 11px; text-transform: uppercase; vertical-align: bottom;">DOWNLOAD</button></a></div></td><td width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><b>'.$value[$i]['title'].'</b><br>'.$desc.'<br><small>'.$value[$i]['support_os'].'</small></div></div></div></td></tr>';
            }
            $this->_table .= '</tbody></table><div style="text-align: center;"></div></div></div></div></div></div>';
            fwrite(fopen(__DIR__."/../saved/".$this->_codename.".html", 'a'), $this->_table.PHP_EOL);
            echo "Done File Saved To ".__DIR__."\\".$this->_codename.".html";
        }
        return $this->_container;
    }
}
echo "Masukkan Codename : ";
$codename = trim(fgets(STDIN));
print_r(
    TP_LINK::run("https://softroco.com", $codename)
    ->getProduct()
    ->parsingData()
    ->getData()
    ->setTable()
);
?>