<?php
set_time_limit(0);
class Netgear{
    protected $_url, $_codename, $_tempURL, $_postfields, $_container, $_table;
    protected static $instance;

    const URL = [
        'product' => 'https://www.netgear.com/system/supportModels.json',
        'home' => 'https://www.netgear.com'
    ];

    protected function __construct($url, $codename){
        $this->_url = $url;
        $this->_codename = $codename;
    }

    public function run($url, $codename){
        if(!isset(self::$instance)){
            self::$instance = new Netgear($url, $codename);
        }
        return self::$instance;
    }

    protected function _fetchCookie(){
        $cookies = [];
        $getContent = explode("\n", file_get_contents(__DIR__."/../inc/Netgear/cookie.txt"));
        foreach($getContent as $cookie){
            $cookies[] = trim($cookie);
        }
        return $cookies;
    }

    protected function _curl(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_tempURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_fetchCookie());
        if($this->_postfields):
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_postfields);
        endif;
        $result = curl_exec($ch);
        list($header, $body) = explode("\r\n\r\n", $result, 2);
        return ['header' => $header, 'body' => $body];
    }

    public function getProduct(){
        $this->_tempURL = self::URL['product'];
        $curl = json_decode($this->_curl()['body'], true);
        foreach($curl as $key => $name){
            if(strpos(strtolower($name['model']), strtolower($this->_codename)) !== false){
                echo "Apakah Benar product ini => ".$name['model']." 1.ya 0. Deep Search : ";
                $validasi = trim(fgets(STDIN));
                if($validasi){
                    $this->_productName = $name['model'];
                    $this->_urlReview = self::URL['home'].$name['url'];
                    return $this;
                }
            }
        }
        throw new Exception("No Product Found!, or Check Your Internet Connection");
    }

    public function parsingData(){
        $this->_tempURL = $this->_urlReview;
        $curl = $this->_curl()['body'];
        $split = preg_split('/<div class="col topic">/', $curl);
        array_shift($split);
        foreach($split as $key => $content){
            preg_match('/<h3>(.*?)</', $content, $titleTag); //Index [1] Title Tag
            preg_match_all('/<a class="accordion-title" href="#(.*?)"/', $content, $titleCriteria); // Index [1] then: loop
            if(strpos($titleTag[1], "User Guide") !== false):
                preg_match_all('/<a class="btn" target="_blank" href="(.*?)"/', $content, $manual); // Index [1] Manual Link
            else:
                preg_match_all('/<a class="btn" target="" href="(.*?)"/', $content, $driver); // Index [1] Driver
            endif;

            foreach($titleCriteria[1] as $titleKey => $parentTitle){
                if(strpos($titleTag[1], "User Guide") !== false){
                    if(@!preg_match('/netgear\.com/', $manual[1][$titleKey])){
                        @$this->_container[$this->_productName." Manual"][] = ['title' => $parentTitle, 'link_download' => self::URL['home'].$manual[1][$titleKey], "support_os" => $parentTitle];
                    }else{
                        @$this->_container[$this->_productName." Manual"][] = ['title' => $parentTitle, 'link_download' => $manual[1][$titleKey], "support_os" => $parentTitle];
                    }
                }else{
                    @$this->_container[$this->_productName." Driver"][] = ['title' => $parentTitle, 'link_download' => $driver[1][$titleKey], "support_os" => "Windows 7 ~ 10"];
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
echo "Masukkan URL Postname : ";
$url = trim(fgets(STDIN));
echo "Masukkan Codename : ";
$codename = trim(fgets(STDIN));
try{
    print_r(
        Netgear::run($url, $codename)
        ->getProduct()
        ->parsingData()
        ->setTable()
    );
}catch(Exception $e){
    echo $e->getMessage();
}finally{
    // Your Optional Results!
}
?>