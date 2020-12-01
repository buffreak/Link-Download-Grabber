<?php
set_time_limit(0);
class Roccat{
    protected $_url, $_codename, $_tempURL, $_container, $_table, $_postfields, $_list;

    protected static $instance;

    const URL = [
        'search' => 'https://en.roccat.org/Frontend,Controller,Support,Search_Product.frg?sQuery=',
        'download' => 'https://media.roccat.org/'
    ];

    protected function __construct($url, $codename){
        $this->_url = $url;
        $this->_codename = $codename;
    }

    public static function run($url, $codename){
        if(!isset(self::$instance)){
            self::$instance = new Roccat($url, $codename);
        }
        return self::$instance;
    }

    protected function _fetchCookie(){
        $getContent = explode("\r\n", file_get_contents(__DIR__.'/../inc/Roccat/cookie.txt'));
        $container = [];
        foreach($getContent as $cookie){
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
        if($this->_postfields){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_postfields);
        }
        $result = curl_exec($ch);
        list($header, $body) = explode("\r\n\r\n", $result, 2);
        return ['header' => $header, 'body' => $body];
    }

    public function fetchLink(){
        $this->_tempURL = self::URL['search'].$this->_codename;
        $curl = json_decode($this->_curl()['body'], true);
        foreach($curl['Content'] as $key => $list){
            if(strtolower(strpos($list['Title'], $this->_codename)) !== false){
                echo "Apakah Iya Product ini => ".$list['Title']." 1.ya 0. deep search : ";
                $validasi = trim(fgets(STDIN));
                if($validasi){
                    $this->_list = [
                        'link' => $list['SupportLink'],
                        'name' => $list['Title'],
                        'parent' => $list['Deeplink']
                    ];
                    return $this;
                }
            }
        }
    }

    public function getData(){
        $this->_tempURL = $this->_list['link'];
        $curl = $this->_curl()['body'];
        preg_match_all('/<h4>(.*?)<\/h4>/', $curl, $parentTitle);
        if(count($parentTitle[1]) > 1){
            preg_match_all('/<li><a href="(.*?).pdf" title="(.*?).pdf" target="_blank">(.*?)</', $curl, $manual);
            foreach($manual[1] as $key => $link){
                $this->_container["Manual ".$parentTitle[1][1]." for ".$this->_list['name']][] = ['title' => $manual[3][$key], 'link_download' => self::URL['download'].$link.".pdf", 'support_os' => $manual[3][$key]];
            }
        }
        preg_match('/<button name="download" value="(.*?)"/', $curl, $driver);
        preg_match('/<div class="Filesize">(.*?)<\/span>/', $curl, $version);
        preg_match('/<div class="OperatingSystems">(.*?)<\/span>/', $curl, $supportOS);
        $this->_container[trim(strip_tags($parentTitle[1][0]))][] = ['title' => trim(strip_tags($parentTitle[1][0]))." ".trim(strip_tags($version[1])), 'link_download' => self::URL['download'].$driver[1], 'support_os' => trim(strip_tags($supportOS[1]))];
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
echo "Masukkan URL Path : ";
$url = trim(fgets(STDIN));
echo "Masukkan Codename : ";
$codename = trim(fgets(STDIN));
print_r(
    Roccat::run($url, $codename)
    ->fetchLink()
    ->getData()
    ->setTable()  
);
?>