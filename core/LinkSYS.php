<?php
set_time_limit(0);
class LinkSYS{
    
    protected $_url, $_codename, $_tempURL, $_postfields, $_productName, $_supportURL, $_table, $_clone = [];

    protected static $instance;

    const URL = [
        'search' => 'https://www.linksys.com/us/search/autocomplete/support?term=',
        'home' => 'https://www.linksys.com/us/',
        'parent' => 'https://www.linksys.com'
    ];

    protected function __construct($url, $codename){
        $this->_url = $url;
        $this->_codename = $codename;
    }

    protected function _fetchCookie(){
        $getContent = explode("\n", file_get_contents(__DIR__.'/../inc/LinkSYS/cookie.txt'));
        $container = [];
        foreach($getContent as $key => $cookie){
            $container[] = trim($cookie);
        }
        return $container;
    }

    protected function _curl(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_tempURL);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_fetchCookie());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        if($this->_postfields):
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_postfields);
        endif;
        $result = curl_exec($ch);
        list($header, $body) = explode("\r\n\r\n", $result, 2);
        return ['header' => $header, 'body' => $body];
    }

    public function run($url, $codename){
        if(!isset(self::$instance)){
            self::$instance = new LinkSYS($url, $codename);
        }
        return self::$instance;
    }

    public function searchProduct(){
        $this->_tempURL = self::URL['search'].$this->_codename;
        $curl = json_decode($this->_curl()['body'], true);
        foreach($curl as $key => $data){
            if(strpos(strtolower($data['name']), strtolower($this->_codename)) !== false){
                echo "Apakah Benar Product Nya ini => [".$data['name']."] ".$data['supportName']." 1. ya 0. deep search : ";
                $validasi = trim(fgets(STDIN));
                if($validasi){
                    $this->_supportURL = self::URL['home'].$data['url'];
                    $this->_productName = $data['supportName'];
                    return $this;
                }
            }
        }
    }

    public function fetchLink(){
        $this->_tempURL = $this->_supportURL;
        $curl = $this->_curl()['body'];
        preg_match('/<a href="(.*?)" title="Downloads \/ Firmware"/', $curl, $firmware);
        preg_match('/<a href="(.*?).pdf"/', $curl, $manual);
        $this->_container["Manual for ".$this->_productName][] = ['title' => 'Manual '.$this->_productName, 'link_download' => $manual[1].".pdf", 'support_os' => 'Manual '.$this->_productName]; //Manual User Guide
        $this->_firmwareLink = self::URL['parent'].$firmware[1];
        return $this;
    }

    public function getFirmware(){
        $this->_tempURL = $this->_firmwareLink;
        $curl = $this->_curl()['body'];
        preg_match_all('/<div class="article\-accordian">(.*?)</', $curl, $version); // Get Version Firmware [1]
        $split = preg_split('/<div class="article\-accordian">(.*?)<\/div>/', $curl);
        array_shift($split);
        foreach($split as $key => $firmware){
            if($key === count($split) - 1){
                $splitFirmware = preg_split('/Was this support article useful?/', $firmware); // Index 0
                preg_match_all('/href="(.*?)"/', $splitFirmware[0], $linkFirmware); // Index 1
            }else{
                preg_match_all('/href="(.*?)"/', $firmware, $linkFirmware); //Index 1
            }
            foreach($linkFirmware[1] as $keyLink => $value){
                if(strpos($value, 'releasenotes') !== false){
                    $this->_container["Firmware / Driver for ".$this->_productName." ".$version[1][$key]][] = ['title' => $this->_productName." v".$this->_keyMatch." Release Note", 'link_download' => $value, 'support_os' => "Release Note ".$this->_productName." v".$this->_keyMatch];
                }elseif(strpos($value, '.pdf') !== false){
                    $this->_container["Firmware / Driver for ".$this->_productName." ".$version[1][$key]][] = ['title' => $this->_productName." Warranty", 'link_download' => $value, 'support_os' => "Warranty ".$this->_productName];
                }elseif(strpos($value, '.img') !== false || strpos($value, '.tar.gz') !== false || strpos($value, '.zip') !== false || strpos($value, '.bin') !== false || strpos($value, '.bix') !== false){
                    if(strpos($value, '.gpg') !== false){
                        $msg = "For US Only";
                    }else{
                        $msg = "Global";
                    }
                    preg_match('/\/firmware\/(.*?)_(.*?)_(.*?)_prod/', $value, $versionFirmware); // Index [3] For Version
                    $this->_keyMatch = $versionFirmware[3];
                    $this->_container["Firmware / Driver for ".$this->_productName." ".$version[1][$key]][] = ['title' => $this->_productName." v".$versionFirmware[3]." ".$msg, 'link_download' => $value, 'support_os' => "If Name contains macOS it's for macOS, if Linux it's for Linux, otherwise for Windows"];
                }
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
// echo "Masukkan URL Path Artikel : ";
// $url = trim(fgets(STDIN));
echo "Masukkan Codename : ";
$codename = trim(fgets(STDIN));
print_r(
    LinkSYS::run("https://softroco.com", $codename)
    ->searchProduct()
    ->fetchLink()
    ->getFirmware()
    ->setTable()
);
?>