<?php
set_time_limit(0);
class Lexmark{

    protected $url, $codename, $codeID, $temp, $driver, $dataTemp;
    private $_table;

    const URL = [
        'codename' => 'http://support.lexmark.com/js/Categories_en.js',
        'fetch' => 'http://support.lexmark.com/index?',
        'home' => 'http://support.lexmark.com/'
    ];

    const DRIVER = [
        'MICROSOFT' => 'WINDOWS_7_X64', //Customize Not Recommended!
        'MACINTOSH' => 'MAC_OS_X_10.10'

    ];

    const SUPPORT_OS = [
        'windows' => 'Windows XP 32-bit, Windows XP-64-bit, Windows Vista 32-bit, Windows Vista 64-bit, Windows 7 32-bit, Windows 7 64-bit, Windows 8/8.1 32-bit, Windows 8/8.1 64-bit, Windows 10 32-bit, Windows 10-64-bit',
        'macos' => 'OS X 10.10, OS X 10.11, macOS 10.12, macOS 10.13, macOS 10.14, macOS 10.15'
    ];

    public function __construct($url, $codename){
        $this->url = $url;
        $this->codename = $codename;
    }

    protected function curl($url, $post = false){
        if(!file_exists(__DIR__.'/../inc/Lexmark')){
            mkdir(__DIR__.'/../inc/Lexmark', 0777, true);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__.'/..inc/Lexmark/cookie.txt');
        curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__.'/..inc/Lexmark/cookie.txt');
        curl_setopt($ch, CURLOPT_HEADER, 1);
        if($post){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        $result = curl_exec($ch);
        list($header, $body) = explode("\r\n\r\n", $result, 2);
        return ['header' => $header, 'body' => $body];
    }

    public function getCodename(){
        $this->codeID = strtoupper(str_replace(" ", "_", $this->codename));
        foreach(self::DRIVER as $key => $parentKey){
            $buildQuery = 'page=downloadsTab&osVendor='.$key.'&osVer='.$parentKey.'&productCode='.$this->codeID.'&tabDictionary=productPage&linkPage=product&max=&userlocale=EN_US&batch=%23&itData.offset=&locale=en&segment=SUPPORT&frompage=null&page=product&pmv=';
            // echo self::URL['fetch'].$buildQuery."\n\n";
            $this->temp = $this->curl(self::URL['fetch'].$buildQuery)['body'];
            @$explode = preg_split('/id="noDriver"/', $this->temp)[1];
            @$secondExplode = preg_split('/<div id="hidefirmware"/', $explode)[0];
            preg_match_all('/target="_blank">(.*?)</', $secondExplode, $titleDriver); //Index 1 Then Loop
            preg_match_all('/a href="(.*?)"/', $secondExplode, $linkDriver); //Index 1 Then Loop and add self::URL['home']

            foreach($linkDriver[1] as $linkKey => $link){
                if($key === 'MICROSOFT'){
                    $OSKey = 'Windows';
                }else{
                    $OSKey = 'macOS';
                }
                $this->dataTemp['Driver '.$this->codename.' for '.$OSKey][] = ['title' => $titleDriver[1][$linkKey], 'link_download' => self::URL['home'].$link, 'support_os' => self::SUPPORT_OS[strtolower($OSKey)]];
            }

            $getFirmware = preg_split('/<div id="firmware"/', $this->temp)[1];
            if(!preg_match('/<div class="noResults/', $getFirmware)){
                preg_match_all('/target="_blank">(.*?)</', $getFirmware, $titleFirmware); //Index 1 Then Loop
                preg_match_all('/a href="(.*?)"/', $getFirmware, $linkFirmware); //Index 1 Then Loop and add self::URL['home']
                if(count($linkFirmware[1]) > 1){
                    $this->dataTemp['Firmware for '.$this->codename][0] = ['title' => $titleFirmware[1][0], 'link_download' => self::URL['home'].$linkFirmware[1][0], 'support_os' => 'Linux, Windows, macOS Support'];
                    $this->dataTemp['Firmware for '.$this->codename][1] = ['title' => $titleFirmware[1][1], 'link_download' => self::URL['home'].$linkFirmware[1][1], 'support_os' => 'Linux, Windows, macOS Support'];
                }else{
                    $this->dataTemp['Firmware for '.$this->codename][0] = ['title' => $titleFirmware[1][0], 'link_download' => self::URL['home'].$linkFirmware[1][0], 'support_os' => 'Linux, Windows, macOS Support'];
                }
            }
        }
        return $this;
    }

    public function fetchDriver(){
        foreach($this->dataTemp as $key => $data){
            foreach($data as $secondKey => $url){
                $this->temp = $this->curl($url['link_download'])['body'];
                preg_match('/window\.location\.href=\'(.*?)\'/', $this->temp, $fetchFirst); // Index 1 FirstLink Accept term
                $this->temp = $this->curl($fetchFirst[1])['body'];
                preg_match('/parent\.document\.location = \'(.*?)\'/', $this->temp, $realLink); // Index 1 Link Download Driver and Firmware
                $this->dataTemp[$key][$secondKey]['link_download'] = trim($realLink[1]); 
            }
        }
        return $this;
    }

    public function fetchManual(){
        $buildQuery = 'page=publicationsList&locale=en&productCode='.$this->codeID.'&segment=SUPPORT&frompage=null&linkPage=product&pmv=&userlocale=EN_US&max=&batch=&osVendor=MICROSOFT&osVer=VERSION&autodetect=true';
        $this->temp = $this->curl(self::URL['fetch'].$buildQuery)['body'];
        $splitManual = preg_split('/Read\, print or download product documentation/i', $this->temp)[1];
        preg_match_all('/href="(.*?)"/', $splitManual, $linkManual); // Index 1 Then Loop and add self::URL['home']
        preg_match_all('/class="im-answer">(.*?)</', $splitManual, $titleManual); // Index 1 Title Manual
        foreach($linkManual[1] as $key => $link){
            $this->dataTemp['Manual for '.$this->codename][] = ['title' => $titleManual[1][$key], 'link_download' => self::URL['home'].$link, 'support_os' => $titleManual[1][$key]];
        }
        return $this;
    }

    public function setTable(){
        foreach($this->dataTemp as $OS => $criteria){
            $this->_table = '<div><a href="#" class="hrefLink">'.$OS.'</a><div style="display:none;"><div style="text-align: center;"><div><div style="text-align: start;"><table id="zaposphere-table-courtyard" style="border-collapse: collapse; border: #D9D9D9; mso-border-alt: solid #D9D9D9 .5pt; mso-border-insideh-themecolor: background1; mso-border-insideh-themeshade: 217; mso-border-insideh: .5pt solid #D9D9D9; mso-border-insidev-themecolor: background1; mso-border-insidev-themeshade: 217; mso-border-insidev: .5pt solid #D9D9D9; mso-border-themecolor: background1; mso-border-themeshade: 217; mso-border-top-alt: solid red 3.0pt; mso-padding-alt: 0cm 5.4pt 0cm 5.4pt; mso-yfti-tbllook: 1184;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td bgcolor="#CCCCCC" width="168"><div align="center"><strong>Download</strong></div></td><td bgcolor="#CCCCCC" width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><strong>Driver</strong></div></div></div></td></tr></tbody><tbody>';
            foreach($criteria as $category => $value){
                $this->_table .= '<tr><td width="168"><div align="center"><a href="'.$this->url.'" class="hrefDownload" id="'.encrypt($value['link_download']).'"><button style="background-color: #3377FF; border-bottom-left-radius: 2px; border-bottom-right-radius: 2px; border-top-left-radius: 2px; border-top-right-radius: 2px; border: 0px; color: white; cursor: pointer; font-size: 12px; font-weight: bold; margin: 0px; max-width: 100%; padding: 10px 30px 11px; text-transform: uppercase; vertical-align: bottom;">DOWNLOAD</button></a></div></td><td width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><b>'.$value['title'].'</b><br>Support OS:<br><small>'.$value['support_os'].'</small></div></div></div></td></tr>';
            }
            $this->_table .= '</tbody></table><div style="text-align: center;"></div></div></div></div></div></div>';
            fwrite(fopen(__DIR__.'/../saved/'.$this->codename.".html", "a"), $this->_table.PHP_EOL);
        }
        return $this->dataTemp;
    }

    public static function input(){
        return trim(fgets(STDIN));
    }
}
echo "Masukkan URL PostName : ";
$url = Lexmark::input();
echo "Masukkan Codename : ";
$codename = Lexmark::input();
print_r(
    (new Lexmark($url, $codename))
    ->getCodename()
    ->fetchDriver()
    ->fetchManual()
    ->setTable()
);
?>