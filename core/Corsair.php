<?php
set_time_limit(0);
class Corsair{

    protected $_url, $_codename, $_postfields, $_criteria, $_productName, $_linkReview, $_container, $_table;
    protected static $instance;

    const URL = [
        'search' => 'https://www.corsair.com/ww/en/search/', // ?text={$_codename}&type=all  [ParamList]
        'home' => 'https://www.corsair.com/ww/en',
        'parent' => 'https://www.corsair.com',
        'driver' => 'https://www.corsair.com/ww/en/downloads/search?searchCategory=&search=',
        'macOS' => 'https://www.corsair.com/ww/en/icue-mac'
    ];

    protected function __construct($url, $codename){
        $this->_url = $url;
        $this->_codename = explode(" ", $codename)[0];
    }

    public function run($url, $codename){
        if(!isset(self::$instance)){
            self::$instance = new Corsair($url, $codename);
        }
        return self::$instance;
    }

    protected function _parsingCookie(){
        $getContent = explode("\n", file_get_contents(__DIR__.'/../inc/Corsair/cookie.txt'));
        $cookies = [];
        foreach($getContent as $key => $cookie){
            $cookies[] = trim($cookie);
        }
        return $cookies;
    }

    protected function _curl(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_tempURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_parsingCookie());
        if($this->_postfields):
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_postfields);
        endif;
        $result = curl_exec($ch);
        list($header, $body) = explode("\r\n\r\n", $result, 2);
        return ['header' => $header, 'body' => $body];
    }

    public function getProduct(){
        $this->_tempURL = self::URL['search']."?text=".$this->_codename."&type=all";
        $curl = $this->_curl()['body'];
        preg_match_all('/primaryUrl\-(.*?)" href="(.*?)" title="(.*?)"/', $curl, $productList); // Index [2] Link Manual, Index [3] Name Product
        foreach($productList[2] as $key => $link){
            if(strpos($productList[3][$key], $this->_codename) !== false){
                echo "Apakah Benar ini namanya => ".$productList[3][$key]." 1.ya 0.deep search : ";
                $validasi = trim(fgets(STDIN));
                if($validasi){
                    $this->_productName = $productList[3][$key];
                    $this->_linkReview = $link;
                    return $this;
                }
            }
        }
        throw new Exception("Check Your Connection!, or codename isn't available.\n");
    }

    public function manualFetch(){
        $this->_tempURL = self::URL['search'].$this->_linkReview;
        $curl = $this->_curl()['body'];
        preg_match_all('/<a href="(.*?)" download>(.*?)</', $curl, $manual); // Index [1] Link Download Manual, Index [2] Title Manual
        foreach($manual[1] as $key => $link){
            $this->_container['Manual'][] = ['title' => $manual[2][$key], 'link_download' => self::URL['parent'].$link, 'support_os' => $manual[2][$key]];
        }
        return $this;
    }

    protected function _macOSDriver(){
        $this->_tempURL = self::URL['macOS'];
        $curl = $this->_curl()['body'];
        preg_match('/<a class="cta" href="(.*?)"/', $curl, $macOS);
        return $macOS[1];
    }

    public function driverFetch(){
        $this->_tempURL = self::URL['driver'].$this->_codename;
        $curl = json_decode($this->_curl()['body'], true);
        preg_match_all('/<div class="product_name">(.*?)</', $curl, $productName);
        preg_match_all('/data-url="(.*?)"/', $curl, $downloadDriver);
        preg_match_all('/<span class="version">(.*?)</', $curl, $version);
        $this->_container['Driver Windows'][] = ['title' => $productName[1][0], 'link_download' => $downloadDriver[1][0], 'support_os' => 'iCUE '.$version[1][0]." for Windows"];
        $this->_container['Driver macOS'][] = ['title' => $productName[1][0], 'link_download' => trim($this->_macOSDriver()), 'support_os' => 'iCUE for macOS 10.10 ~ 10.15'];
        return $this;
        // foreach($downloadDriver[1] as $key => $link){
        //     if(strpos($productName[1][$key], $this->_codename) !== false){
        //         echo "[VALIDASI DRIVER] Apakah benar pasangan driver nya namanya => ".$productName[1][$key]." 1.ya 0.deep search : ";
        //         $validasi = trim(fgets(STDIN));
        //         if($validasi){
        //             $this->_container['Driver'][] = ['title' => $productName[1][$key], 'link_download' => $link, 'support_os' => 'iCUE '.$version[1][$key]." for Windows"];
        //             return $this->_container;
        //         }
        //     }
        // }
    }

    public function setTable(){
        foreach($this->_container as $key => $value){
            if(strpos($key, 'Manual') !== false){
                $desc = "Description:";
            }else{
                $desc = "Support OS:";
            }
            $this->_table = '<div><a href="#" class="hrefLink">'.$key.' for '.$this->_productName.'</a><div style="display:none;"><div style="text-align: center;"><div><div style="text-align: start;"><table id="zaposphere-table-courtyard" style="border-collapse: collapse; border: #D9D9D9; mso-border-alt: solid #D9D9D9 .5pt; mso-border-insideh-themecolor: background1; mso-border-insideh-themeshade: 217; mso-border-insideh: .5pt solid #D9D9D9; mso-border-insidev-themecolor: background1; mso-border-insidev-themeshade: 217; mso-border-insidev: .5pt solid #D9D9D9; mso-border-themecolor: background1; mso-border-themeshade: 217; mso-border-top-alt: solid red 3.0pt; mso-padding-alt: 0cm 5.4pt 0cm 5.4pt; mso-yfti-tbllook: 1184;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td bgcolor="#CCCCCC" width="168"><div align="center"><strong>Download</strong></div></td><td bgcolor="#CCCCCC" width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><strong>Driver</strong></div></div></div></td></tr></tbody><tbody>';
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
echo "Masukkan URL Artikel : ";
$url = trim(fgets(STDIN));
echo "Masukkan Codename di bagian Support > Download (Saat Pemilihan Link, Dilihat Baik Baik Codename manual apakah sama dengan Driver) : ";
$codename = trim(fgets(STDIN));
try{
    print_r(
        Corsair::run($url, $codename)
        ->getProduct()
        ->manualFetch()
        ->driverFetch()
        ->setTable()
    );
}catch(Exception $e){
    echo $e->getMessage();
}

?>