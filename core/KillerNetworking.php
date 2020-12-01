<?php
set_time_limit(0);
class KillerNetworking{

    protected $_url, $_codename, $_container, $_table, $_postfields, $_tempURL;

    protected static $instace;

    const MAX_ARCHIEVED = 3;

    protected function __construct($url, $codename){
        $this->_url = $url;
        $this->_codename = $codename;
    }

    public static function run($url, $codename){
        if(!isset(self::$instace)){
            self::$instace = new KillerNetworking($url, $codename);
        }
        return self::$instace;
    }

    protected function _cookieFetch(){
        $getContent = explode("\n", file_get_contents(__DIR__.'/../inc/KillerNetworking/cookie.txt'));
        $cookies = [];
        foreach($getContent as $cookie){
            $cookies[] = trim($cookie);
        }
        return $cookies;
    }

    protected function _curl(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_tempURL);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_cookieFetch());
        if($this->_postfields):
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_postfields);
        endif;
        $result = curl_exec($ch);
        list($header, $body) = explode("\r\n\r\n", $result, 2);
        return ['header' => $header, 'body' => $body];
    }

    public function getProduct(){
        $glob = glob(__DIR__."/../inc/KillerNetworking/require/*.txt");
        foreach($glob as $key => $file){
            $getContent = file_get_contents($file);
            preg_match_all('/<div class=\'package-filter\' style=\'display: none;\'>(.*?)</', $getContent, $listFilter); // [1] Check Matched $this->_codename
            preg_match('/<div class="hts-toggle__title">(.*?)</', $getContent, $title); // [1] Parent Title
            if(strpos($file, "archieved_download") !== false){
                preg_match_all('/<h4 id="(.*?)><a href=\'(.*?)\'>(.*?)</', $getContent, $link); // [2] Link Download [3] Title Download
                preg_match_all('/<strong>Version:<\/strong>(.*?)<span/', $getContent, $version); // [1] Version Tile [NEEDLE] strip_tags
                $increment = 0;
                foreach($listFilter[1] as $keyFilter => $filter){
                    if($increment === 3){
                        break;
                    }
                    if(strpos(strtolower($link[3][$keyFilter]), "beta") !== false){
                        continue;
                    }
                    if(strpos(strtolower($filter), strtolower($this->_codename)) !== false){
                        $this->_tempURL = $link[2][$keyFilter];
                        $curl = $this->_curl()['body'];
                        preg_match('/<span class="badge-download"><a class=\'wpdm-download-link btn btn-primary \' rel=\'nofollow\' href=\'(.*?)\'/', $curl, $linkDownload);
                        $this->_container[$title[1]][] = ['title' => $link[3][$keyFilter]." ".strip_tags($version[1][$keyFilter]), 'link_download' => $linkDownload[1], 'support_os' => 'See In Title'];
                        $increment++;
                    }
                }
            }else{
                preg_match_all('/<div class=\'text-center package-preview\'>(.*?) alt=\'(.*?)\'/', $getContent, $titleValue); // [2] titleValue
                preg_match_all('/<h4 style="padding: 0px;margin:0px;min-height: 80px;"(.*?)<a href=\'(.*?)\'/', $getContent, $linkDownload); // [2] LinkDownload
                foreach($listFilter[1] as $keyFilter => $filter){
                    if(strpos(strtolower($titleValue[2][$keyFilter]), "beta") !== false){
                        continue;
                    }
                    if(strpos(strtolower($filter), strtolower($this->_codename)) !== false){
                        $this->_tempURL = $linkDownload[2][$keyFilter];
                        $curl = $this->_curl()['body'];
                        preg_match('/<span class="badge-download"><a class=\'wpdm-download-link btn btn-primary \' rel=\'nofollow\' href=\'(.*?)\'/', $curl, $realLink);
                        $this->_container[$title[1]][] = ['title' => $titleValue[2][$keyFilter], 'link_download' => $realLink[1], 'support_os' => 'See In Title'];
                    }
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
            $this->_table = '<div><a href="#" class="hrefLink">'.$key.' '.$this->_codename.'</a><div style="display:none;"><div style="text-align: center;"><div><div style="text-align: start;"><table id="zaposphere-table-courtyard" style="border-collapse: collapse; border: #D9D9D9; mso-border-alt: solid #D9D9D9 .5pt; mso-border-insideh-themecolor: background1; mso-border-insideh-themeshade: 217; mso-border-insideh: .5pt solid #D9D9D9; mso-border-insidev-themecolor: background1; mso-border-insidev-themeshade: 217; mso-border-insidev: .5pt solid #D9D9D9; mso-border-themecolor: background1; mso-border-themeshade: 217; mso-border-top-alt: solid red 3.0pt; mso-padding-alt: 0cm 5.4pt 0cm 5.4pt; mso-yfti-tbllook: 1184;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td bgcolor="#CCCCCC" width="168"><div align="center"><strong>Download</strong></div></td><td bgcolor="#CCCCCC" width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><strong>Driver</strong></div></div></div></td></tr></tbody><tbody>';
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
echo "Masukkan URL Pathname : ";
$url = trim(fgets(STDIN));
echo "Masukkan Codename : ";
$codename = trim(fgets(STDIN));
print_r(
    KillerNetworking::run($url, $codename)
    ->getProduct()
    ->setTable()
);
?>