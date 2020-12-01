<?php
set_time_limit(0);
class Nvidia{
    protected $_pathURL, $_codeName, $_urlFix, $_container = [], $_OS = [], $_linkRedirect = [], $_finalLink = [], $_template = [], $_table, $_listTable;
    protected static $run;
    const PARENT_NVIDIA = [
        "TITAN" => 11,
        "GeForce" => 1,
        "Quadro" => 3,
        "NVS" => 8,
        "Tesla" => 7,
        "GRID" => 9,
        "3D Vision" => 5,
        "ION" => 6,
        "Legacy" => 4
    ];

    const URL_LOOKUP = "https://www.nvidia.com/Download/API/lookupValueSearch.aspx?";
    const URL_DRIVER = "https://www.nvidia.com/Download/processDriver.aspx?";
    const NVIDIA_FINAL = "http://us.download.nvidia.com";
    const DRIVER_PROCCESS = [
        "GRD_STANDARD" => "&dtid=1&dtcid=0",
        "SD_STARNDARD" => "&dtid=18&dtcid=0",
        "GRD_DCH" => "&dtid=1&dtcid=1",
        "SD_DCH" => "&dtid=18&dtcid=1"
    ];

    protected function _curlHeader(){
        $explodeElement = explode("\r\n", $this->streamHeader());
        $container = [];
        foreach($explodeElement as $element){
            $container[] = trim($element);
        }
        return $container;
    }

    protected function _contextStream(){
        $opt = [
            'http' => [
                'method' => 'GET',
                'header' => $this->_streamHeader()
            ]
        ];
        return stream_context_create($opt);
    }
    
    public function getStream(){
        $getContent = file_get_contents($this->_urlFix, false, $this->_contextStream());
        return $getContent;
    }

    protected function _curl(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_pathUrl);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_curlHeader());
        $result = curl_exec($ch);
        list($header, $body) = explode("\r\n\r\n", $result, 2);
        return [$header, $body];
    }

    protected function _streamHeader(){
       return 
            "user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:70.0) Gecko/20100101 Firefox/70.0\r\n".
            "accept: */*\r\n".
            "x-requested-with: XMLHttpRequest\r\n".
            "referer: https://www.nvidia.com/Download/index.aspx\r\n".
            "cookie: AMCV_F207D74D549850760A4C98C6%40AdobeOrg=-330454231%7CMCIDTS%7C18212%7CMCMID%7C33784754153291448324614970289844572680%7CMCAID%7CNONE%7CMCOPTOUT-1573485322s%7CNONE%7CMCAAMLH-1574082922%7C3%7CMCAAMB-1574082922%7Cj8Odv6LonN4r3an7LhD3WZrU1bUpAkFkkiY1ncBR96t2PTI%7CMCSYNCSOP%7C411-18214%7CvVersion%7C3.1.2\r\n".
            "cookie: _gcl_au=1.1.426194649.1573048412\r\n".
            "cookie: _gd_visitor=82c83817-d281-4ce4-8345-1784bc8fbaed\r\n".
            "cookie: _gd_svisitor=855f6276107b00005bd0c25d02010000f10c0000\r\n".
            "cookie: s_ecid=MCMID%7C33780957423676620804614897886324805313\r\n".
            "cookie: s_getNewRepeat=1573478122786-Repeat\r\n".
            "cookie: sc_cvp=\r\n".
            "cookie: _mkto_trk=id:156-OFN-742&token:_mch-nvidia.com-1573048413574-71706\r\n".
            "cookie: _fbp=fb.1.1573048413671.1091581781\r\n".
            "cookie: __atuvc=59%7C45\r\n".
            "cookie: __atssc=google%3B11\r\n".
            "cookie: __g_u=323063362880776_2_0_0_5_1573567080138\r\n".
            "cookie: ddlDownloadTypeDch=0\r\n".
            "cookie: ProductSeriesType_WHQL=11\r\n".
            "cookie: ProductSeries_WHQL=106\r\n".
            "cookie: ProductType_WHQL=885\r\n".
            "cookie: OperatingSystem_WHQL=12\r\n".
            "cookie: Language_WHQL=1\r\n".
            "cookie: WHQL_WHQL=\r\n".
            "cookie: _sdsat_authStage=guest\r\n".
            "cookie: AMCVS_F207D74D549850760A4C98C6%40AdobeOrg=1\r\n".
            "cookie: s_ppv=nv%253Anvidia%253Adownload%2C37%2C37%2C750\r\n".
            "cookie: tp=2023\r\n".
            "cookie: s_cc=true\r\n".
            "cookie: _gd_session=29e6f952-480b-44d9-81bf-b5ce471d7a56\r\n".
            "te: trailers";
    }

    public static function input(){
        return trim(fgets(STDIN));
    }

    private function __construct($pathURL, $codeName, $listTable){
        $this->_pathURL = $pathURL;
        $this->_codeName = $codeName;
        $this->_listTable = (int) $listTable;
    }

    public static function run($pathURL, $codeName, $listTable){
        if(!isset(self::$run)){
            self::$run = new Nvidia($pathURL, $codeName, (int) $listTable);
        }
        return self::$run; 
    }
    public function fetchCodeName(){
        foreach(self::PARENT_NVIDIA as $key => $val){
            $query = http_build_query(["TypeID" => "2", "ParentID" => (string) $val]);
            $this->_urlFix = self::URL_LOOKUP.$query;
            $resourceData = $this->getStream();
            $objXml = simplexml_load_string($resourceData);
            foreach($objXml->LookupValues->LookupValue as $secondKey => $nameSecond){
                $query2 = http_build_query(["TypeID" => "3", "ParentID" => (string) $nameSecond->Value]);
                $this->_urlFix = self::URL_LOOKUP.$query2;
                $resourceData2 = $this->getStream();
                $objXml2 = simplexml_load_string($resourceData2);
                foreach($objXml2->LookupValues->LookupValue as $thirdKey => $nameThird){
                    if(trim(strtolower($this->_codeName)) === trim(strtolower($nameThird->Name))){
                        echo "Apakah ".$nameThird->Name." Yang berada di kategori => ".$nameSecond->Name."\n";
                        echo "1. Jika Iya, 0. Jika ingin mencari di kategori lain : ";
                        $validasi = trim(fgets(STDIN));
                        if($validasi):
                            $this->_container = [
                                "codename" => $nameThird->Name,
                                "value" => $nameThird->Value,
                                "parentID" => $nameThird->attributes()['ParentID']
                            ];
                            return $this;
                        endif;
                    }
                }
            }
        }
        throw new Exception("Unable Get Codename\n");
    }

    public function getOS(){
        $query = http_build_query(["TypeID" => "4", "ParentID" => $this->_container["parentID"]]);
        $this->_urlFix = self::URL_LOOKUP.$query;
        $resourceData = $this->getStream();
        $objXml = simplexml_load_string($resourceData);
        foreach($objXml->LookupValues->LookupValue as $key => $OS){
            if(preg_match('/Window/', $OS->Name)){
                $this->_OS['windows'][] = ["ID" => $OS->Value, "name" => $OS->Name];
            }elseif(preg_match('/Linux/', $OS->Name)){
                $this->_OS['linux'][] = ["ID" => $OS->Value,"name" => $OS->Name];
            }elseif(preg_match('/FreeBSD/', $OS->Name)){
                $this->_OS['unix'][] = ["ID" => $OS->Value,"name" => $OS->Name];
            }elseif(preg_match('/Solaris/', $OS->Name)){
                $this->_OS['unix'][] = ["ID" => $OS->Value, "name" => $OS->Name];
            }
        }
        return $this;
    }

    public function saveStream(){
        $fopen = fopen(__DIR__."/../saved/".$this->_codeName.".html", "a");
        fwrite($fopen, $this->_table.PHP_EOL);
        fclose($fopen);
    }

    public function generateDownloadLink(){
        foreach($this->_OS as $key => $value){
            foreach($value as $keyDL => $generate){
                foreach(self::DRIVER_PROCCESS as $keyDriver => $downloadLink){
                    $query = http_build_query([
                        "psid" => (string) $this->_container["parentID"],
                        "pfid" => (string) $this->_container['value'], 
                        "rpf" => "1",
                        "osid" => (string) $this->_OS[$key][$keyDL]['ID'],
                        "lid" => "1",
                        "lang" => "en-us",
                        "ctk" => "0"
                    ]);
                    $queryFinal = self::URL_DRIVER.$query.$downloadLink;
                    $this->_linkRedirect[] = ["type" => $keyDriver, "OS" => (string) $this->_OS[$key][$keyDL]['name'], "param" => $queryFinal, "parentOS" => (string) $key];
                }
            }
        }
        return $this;
    }

    public function fetchResult(){
        foreach($this->_linkRedirect as $OS => $downloadLink){
            $this->_urlFix = $downloadLink['param'];
            $getResource = $this->getStream();
            if(!preg_match('/No certified downloads(.*)/i', $getResource)){
                $this->_urlFix = $getResource;
                $getResource = $this->getStream();
                preg_match('/<td valign="middle" align="left" rowspan="5" class="contentsummaryleft" style="font-weight: bold;white-space:nowrap;">[\r\n]+(.*?)<a href="([^"]+)/', $getResource, $matches);
                $splitURI = preg_split('/url=(.*?)/', $matches[2]);
                $splitFinal = preg_split('/&/', $splitURI[1]); // index 0
                $finalLink = self::NVIDIA_FINAL.$splitFinal[0];
                $this->_finalLink[] = ["type" => $downloadLink['type'], "OS" => $downloadLink['OS'], "final_link" => $finalLink, "parentOS" => $downloadLink['parentOS']];
            }
        }
        return $this;
    }

    public function removeClone(){
        $validation = [];
        foreach($this->_finalLink as $key => $data){
            if(in_array($data['final_link'], $validation)){
                unset($this->_finalLink[$key]);
            }
            $validation[] = $data['final_link'];
            $this->_OSFinal[$data['parentOS']][$data['type']][$data['final_link']][] = $data['OS'];
        }
        // print_r($this->_finalLink);
        // echo "\n";
        // print_r($this->_OSFinal);
        return $this;
    }

    public function parsingData(){
        foreach($this->_finalLink as $key => $data){
            $encrypt = ($data['final_link']);
            if($data['parentOS'] === "windows"){
                if($data['type'] === "GRD_STANDARD"){
                    $this->_template["windows"]["GRD (Game Ready Driver) Standard Driver For Windows x64/x86"][] = ["link" => $encrypt, "support_os" => implode(", ", $this->_OSFinal[$data['parentOS']][$data['type']][$data['final_link']]), "title_name" => "For GRD Standard (See Support OS)"];
                }elseif($data['type'] === "SD_STARNDARD"){
                    $this->_template["windows"]["SD (Studio Driver) Standard Driver For Windows x64/x86"][] = ["link" => $encrypt, "support_os" => implode(", ", $this->_OSFinal[$data['parentOS']][$data['type']][$data['final_link']]), "title_name" => "For SD Standard (See Support OS)"];  
                }elseif($data['type'] === "GRD_DCH"){
                    $this->_template["windows"]["GRD (Game Ready Driver) DCH [Experimental] Driver For Windows x64/x86"][] = ["link" => $encrypt, "support_os" => implode(", ", $this->_OSFinal[$data['parentOS']][$data['type']][$data['final_link']]), "title_name" => "For GRD (Game Ready Driver) DCH [Experimental] (See Support OS)"];  
                }elseif($data['type'] === "SD_DCH"){
                    $this->_template["windows"]["SD (Studio Driver) DCH [Experimental] Driver For Windows x64/x86"][] = ["link" => $encrypt, "support_os" => implode(", ", $this->_OSFinal[$data['parentOS']][$data['type']][$data['final_link']]), "title_name" => "SD (Studio Driver) DCH [Experimental] (See Support OS)"];  
                }
            }elseif($data['parentOS'] === "linux"){
                if($data['type'] === "GRD_STANDARD"){
                    $this->_template["linux"]["GRD (Game Ready Driver) Standard Driver For Linux"][] = ["link" => $encrypt, "support_os" => implode(", ", $this->_OSFinal[$data['parentOS']][$data['type']][$data['final_link']]), "title_name" => "For GRD Standard (See Support OS)"];
                }elseif($data['type'] === "SD_STARNDARD"){
                    $this->_template["linux"]["SD (Studio Driver) Standard Driver For Linux"][] = ["link" => $encrypt, "support_os" => implode(", ", $this->_OSFinal[$data['parentOS']][$data['type']][$data['final_link']]), "title_name" => "For SD Standard (See Support OS)"];  
                }elseif($data['type'] === "GRD_DCH"){
                    $this->_template["linux"]["GRD (Game Ready Driver) DCH [Experimental] Driver For Linux"][] = ["link" => $encrypt, "support_os" => implode(", ", $this->_OSFinal[$data['parentOS']][$data['type']][$data['final_link']]), "title_name" => "For GRD (Game Ready Driver) DCH [Experimental] (See Support OS)"];  
                }elseif($data['type'] === "SD_DCH"){
                    $this->_template["linux"]["SD (Studio Driver) DCH [Experimental] Driver For Linux"][] = ["link" => $encrypt, "support_os" =>implode(", ", $this->_OSFinal[$data['parentOS']][$data['type']][$data['final_link']]), "title_name" => "SD (Studio Driver) DCH [Experimental] (See Support OS)"];  
                }
            }elseif($data['parentOS'] === "unix"){
                if($data['type'] === "GRD_STANDARD"){
                    $this->_template["unix"]["GRD (Game Ready Driver) Standard Driver For Unix"][] = ["link" => $encrypt, "support_os" => implode(", ", $this->_OSFinal[$data['parentOS']][$data['type']][$data['final_link']]), "title_name" => "For GRD Standard (See Support OS)"];
                }elseif($data['type'] === "SD_STARNDARD"){
                    $this->_template["unix"]["SD (Studio Driver) Standard Driver For Unix"][] = ["link" => $encrypt, "support_os" => implode(", ", $this->_OSFinal[$data['parentOS']][$data['type']][$data['final_link']]), "title_name" => "For SD Standard (See Support OS)"];  
                }elseif($data['type'] === "GRD_DCH"){
                    $this->_template["unix"]["GRD (Game Ready Driver) DCH [Experimental] Driver For Unix"][] = ["link" => $encrypt, "support_os" => implode(", ", $this->_OSFinal[$data['parentOS']][$data['type']][$data['final_link']]), "title_name" => "For GRD (Game Ready Driver) DCH [Experimental] (See Support OS)"];  
                }elseif($data['type'] === "SD_DCH"){
                    $this->_template["unix"]["SD (Studio Driver) DCH [Experimental] Driver For Unix"][] = ["link" => $encrypt, "support_os" => implode(", ", $this->_OSFinal[$data['parentOS']][$data['type']][$data['final_link']]), "title_name" => "SD (Studio Driver) DCH [Experimental] (See Support OS)"];  
                }
            }
        }
        return $this;
    }
    
    public function getTable(){
        foreach($this->_template as $OS => $criteria){
            foreach($criteria as $category => $value){
                if($this->_listTable === 1){
                    $this->_table = '<div><a href="#" class="hrefLink">Nvidia '.$category.'</a><div style="display:none;"><div style="text-align: center;"><div><div style="text-align: start;"><table id="zaposphere-table-courtyard" style="border-collapse: collapse; border: #D9D9D9; mso-border-alt: solid #D9D9D9 .5pt; mso-border-insideh-themecolor: background1; mso-border-insideh-themeshade: 217; mso-border-insideh: .5pt solid #D9D9D9; mso-border-insidev-themecolor: background1; mso-border-insidev-themeshade: 217; mso-border-insidev: .5pt solid #D9D9D9; mso-border-themecolor: background1; mso-border-themeshade: 217; mso-border-top-alt: solid red 3.0pt; mso-padding-alt: 0cm 5.4pt 0cm 5.4pt; mso-yfti-tbllook: 1184;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td bgcolor="#CCCCCC" width="168"><div align="center"><strong>Download</strong></div></td><td bgcolor="#CCCCCC" width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><strong>Driver</strong></div></div></div></td></tr></tbody><tbody>';
                    for($i = 0; $i < count($value); $i++){
                        $this->_table .= '<tr><td width="168"><div align="center"><a href="'.$this->_pathURL.'" class="hrefDownload" id="'.encrypt($value[$i]['link']).'"><button style="background-color: #3377FF; border-bottom-left-radius: 2px; border-bottom-right-radius: 2px; border-top-left-radius: 2px; border-top-right-radius: 2px; border: 0px; color: white; cursor: pointer; font-size: 12px; font-weight: bold; margin: 0px; max-width: 100%; padding: 10px 30px 11px; text-transform: uppercase; vertical-align: bottom;">DOWNLOAD</button></a></div></td><td width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><b>'.$value[$i]['title_name'].'</b><br>Support OS:<br><small>'.$value[$i]['support_os'].'</small></div></div></div></td></tr>';
                    }
                    $this->_table .= '</tbody></table><div style="text-align: center;"></div></div></div></div></div></div>';
                }elseif($this->_listTable === 2){
                    $this->_table = '<div>Nvidia '.$category.'<div style="text-align: center;"><div><div style="text-align: start;"><table id="zaposphere-table-courtyard" style="border-collapse: collapse; border: #D9D9D9; mso-border-alt: solid #D9D9D9 .5pt; mso-border-insideh-themecolor: background1; mso-border-insideh-themeshade: 217; mso-border-insideh: .5pt solid #D9D9D9; mso-border-insidev-themecolor: background1; mso-border-insidev-themeshade: 217; mso-border-insidev: .5pt solid #D9D9D9; mso-border-themecolor: background1; mso-border-themeshade: 217; mso-border-top-alt: solid red 3.0pt; mso-padding-alt: 0cm 5.4pt 0cm 5.4pt; mso-yfti-tbllook: 1184;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td bgcolor="#CCCCCC" width="168"><div align="center"><strong>Download</strong></div></td><td bgcolor="#CCCCCC" width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><strong>Driver</strong></div></div></div></td></tr></tbody><tbody>';
                    for($i = 0; $i < count($value); $i++){
                        $this->_table .= '<tr><td width="168"><div align="center"><tr><td width="168"><div align="center"><a href="'.$value[$i]['link'].'"><button style="background-color: #3377FF; border-bottom-left-radius: 2px; border-bottom-right-radius: 2px; border-top-left-radius: 2px; border-top-right-radius: 2px; border: 0px; color: white; cursor: pointer; font-size: 12px; font-weight: bold; margin: 0px; max-width: 100%; padding: 10px 30px 11px; text-transform: uppercase; vertical-align: bottom;">DOWNLOAD</button></a></div></td><td width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><b>'.$value[$i]['title_name'].'</b><br>Support OS:<br><small>'.$value[$i]['support_os'].'</small></div></div></div></td></tr>';
                    }
                    $this->_table .= '</tbody></table><div style="text-align: center;"></div></div></div></div></div></div>'; 
                }
                $this->saveStream();
            }
        }
        return $this;
    }
}
echo "============================ Beta Test v1.0a ============================\n\n";
echo "Masukkan PATH URL artikel : ";
$pathURL = Nvidia::input();
echo "Masukkan Code Name Driver : ";
$codeName = Nvidia::input();
echo "Masukkan List Table (1. SweetAlert Mode, 2.No HashLink) : ";
$listTable = Nvidia::input();
try{
    Nvidia::run($pathURL, $codeName, $listTable)
    ->fetchCodeName()
    ->getOS()
    ->generateDownloadLink()
    ->fetchResult()
    ->removeClone()
    ->parsingData()
    ->getTable();
}catch(Exception $e){
    echo $e->getMessage()."\n";
}finally{
    echo "Done... All File Saved to => ".__DIR__."\\".$codeName.".html";
}
?>