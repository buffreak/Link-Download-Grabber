<?php
error_reporting(0);
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING | E_DEPRECATED));
class Canon{
    protected $_url, $_codename, $_cookies, $_selectOperation, $_firstLink, $_uniqID, $_buildLink, $_tempURL, $_postFields, $_header, $_body, $_OSName, $_tempData, $_dataTable, $_validate, $_tempTable, $_listTable;
    protected static $instance;
    const UA = "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:71.0) Gecko/20100101 Firefox/71.0";
    const URL_HOME = "https://www.usa.canon.com";
    const URL_API = [
        "printer" => "https://www.usa.canon.com/internet/portal/us/home/support/product-finder/support-printers?cm_sp=CSO-_-SupportPFLandingGreyBar-_-printers",
        "scanner" => "https://www.usa.canon.com/internet/portal/us/home/support/product-finder/support-scanners?cm_sp=CSO-_-SupportPFLandingGreyBar-_-scanners",
        "fax" => "https://www.usa.canon.com/internet/portal/us/home/support/product-finder/support-copiers-mfps-fax-machines?cm_sp=CSO-_-SupportPFLandingGreyBar-_-copiers_mfps_fax_machines"
    ];
    protected function _cookie(){
        $this->_cookies = 
            "Host: www.usa.canon.com\r\n".
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:71.0) Gecko/20100101 Firefox/71.0\r\n".
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n".
            "Accept-Language: en-US,en;q=0.5\r\n".
            "Connection: keep-alive\r\n".
            "Referer: ".$this->_selectOperation."\r\n".
            "Cookie: __atuvc=8%7C50%2C38%7C51; __atssc=google%3B4; _ga=GA1.2.1872331243.1575895976; _fbp=fb.1.1575895975901.299423905; _bcvm_vrid_3372322571352680781=193940921336387795TCE3C226E794149207D5EB68FCF2BDA92B3705BFC70393E0CAA2D7F0F9DA72E402AB500F5F45C94E052EBABB04B14C3CBB120BF7E196DA460377702938B671CDF; mcxSurveyQuarantine=mcxSurveyQuarantine; spid=B358FC05-4416-43F4-B565-164B69598EA7; sp_ssid=1576505168126; _gid=GA1.2.1816715458.1576421188; ak_bmsc=9193ACC3DFD8EB69D255EAAA3EB58E662459DC8D351B0000D18EF75D6DE0175B~pl/Y4CSwZa3QahwxM1c65n7XiB8KpUoMsJQ+3nIKgw8xBMMWzptupzqOPsGRetZqRClkesT3+ihN0S3sv3f1yiKZ8gAagv9OJ58Macl7Kv45Z9G8UVamp0XjYjBXYOxpeMvvg2+gXVKkH0PKGGu1rtai2YzXjjwJ4i0hi9v/FHe/yf/j6W7C43R9RO2OGYIiTWnkNQYXiPTGTbvuS7czJUhQ==; NWIDENTITY=Iq1YE/tX/vSz4yrBV2q7ijrGQhpACxjSKQsDejvzSYTkhrkUhXuby0b4TMJyTq/PhttHwX12icu3/WYnPtwvLKlY8D7wIwZjxTBdlyt/13bQh5//3NFZCQCi1WIx8XVsa8nTN/oFpG5BUo+JrGd5NbgXqIJaHrvip9vCrF0U3ncQ7aO+fzSiyW2pIQu9bmPzOzCDaUoqk39/W14seUQOeeMu62EeP8kUx2xvKC8lnCdmyQ6fagXprdHyyTyKKNMkoDQuRdZ2ipc59OLntYmpT3CDCsryx1Ju53jhDwROJmK54LAWxEb65P0UJFd7EOKnv6SuGVM5nNO3SEzisW541dLw+9ZUxlqFgq19qO45xcVLTCWcQwD+pH+sJlCzJ8QvWM0KhKn/m8jYYhQMbcPWpXVJjKEysTjfMXF7ofGR1j/BDw1/T8SB4QuxZ7onsouFu0IiczNjSn49h0g5DhnzQnU+SVpIwBBjSO2DyuoFAW0R8mBlRefMnmxs/cSCVXCJGTID2mA7NBfwqXNB0SWn0lJrrAL8Zv3rxvVxAL4RbKS0gIkBrAnQII5WIVfth3XUdNosk44M7+tPtZWElPDVBu/tBmP5nD8mjRACEXH5HmBU5Vg5H5E2i3sUCZM0f8jvXcwbP5y1gh1+ARJen6JvaXmAKHV6lgJqN2+2ps/k0TfeIbvVSegGVIWREtX7jx/W; JSESSIONID=0000pCODtXjGnpeqYlBpqjvTJlF:1a438obvj; __atuvs=5df78ed2aa2753cd023; McxPageVisit=36; _bcvm_vid_3372322571352680781=193947011539106502TB58BCC70D3BAC3546BB425D0019F7B91FFCC6982B7B628355A82649358A7FCA3394FB2888C8C9514E9EEB59142EC1A7E48979A1EF85B083BC30080BA91EBC949; bc_pv_end=193947023186093365T428DBCAFEA9835C872C9B9EB8DB15F67E3DB5F63DE66E386B823FC8AC847176D1B0894BC33DA5B0B5546B74E10D8A158946A1C9A22FC3C7B7235DCCCAC6248DB; _gat_UA-230717-68=1\r\n".
            "Upgrade-Insecure-Requests: 1\r\n";

        return stream_context_create(
            [
                "http" => [
                    "method" => "GET",
                    "header" => $this->_cookies
                ]
            ]
        );
    }

    private function __fetchStream(){
        return file_get_contents($this->_selectOperation, false, $this->_cookie());
    }

    protected function _curl($mode = false){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_tempURL);
        curl_setopt($ch, CURLOPT_USERAGENT, self::UA);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        if($mode === true){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURL_POSTFIELDS, http_build_query($this->_postFields));
        }
        $result = curl_exec($ch);
        list($header, $body) = explode("\r\n\r\n", $result);
        $this->_body = json_decode($body, true);
        return $this->_body;
    }

    public function fetchOS(){
        $this->_tempURL = "https://www.usa.canon.com/internet/PA_NWSupport/operatingSystems";
        $result = $this->_curl();
        foreach($result['P_SOFTDRIVER']['ListOfProductSoftwareOrDriver']['ProductSoftwareOrDriver']['ListOfSoftwareOrDriver']['SoftwareOrDriver'] as $key => $OS){
            if($OS['SoftwareOrDriverIdentifier'] === "NOT_APPLICABLE" || $OS['ApplicationName'] === "NA" || strpos($OS['SoftwareOrDriverIdentifier'], "SERVER") !== false){
                continue;
            }
            $this->_OSName[strtolower($OS['ApplicationName'])][] = ["keyos" => $OS['SoftwareOrDriverIdentifier'], "osname" => $OS['OperatingSystemName']];
        }
        return $this;
    }

    public function getDriver(){
        foreach($this->_OSName as $key => $value){
            if($key === "windows"){
                $supportOS = "Windows 10 (32bit), Windows 10 (64bit), Windows 8.1(32bit), Windows 8.1(64bit), Windows 8(32bit), Windows 8(64bit), Windows 7(32bit), Windows 7(64bit), Windows Vista SP1 or later(32bit), Windows Vista SP1 or later(64bit), Windows XP SP3 or later";
            }elseif($key === "mac"){
                $supportOS = "macOS Catalina 10.15, macOS Mojave 10.14, macOS High Sierra 10.13, macOS Sierra v10.12.1 or later, OS X El Capitan v10.11, OS X Yosemite v10.10, OS X Mavericks v10.9, OS X Mountain Lion v10.8.5, OS X Lion v10.7.5";
            }else{
                $supportOS = "ARM64 / 32 Architecture";
            }
            foreach($value as $keyOS => $valueOS){
                $this->_tempURL = "https://www.usa.canon.com/internet/PA_NWSupport/driversDownloads?model=".$this->_uniqID."&os=".$valueOS['keyos']."&type=DS&lang=English";
                $curl = $this->_curl();
                $validate = 0;
                if((int) $curl['downloadFiles']['recommendedForYou']['pagination']['totalItems'] === 1){
                    $short = $curl['downloadFiles']['recommendedForYou']['downloadFilesSubSet']['downloadFiles'];
                    if(!isset($short['fileTitle']) || empty($short['fileTitle'])){
                        $name = $short['fileName'];
                    }else{
                        $name = $short['fileTitle'];
                    }
                    if(strpos($fileValue['fileName'], ".pdf") === false){
                        $this->_dataTable["Download Canon Driver or Utilities for ".ucwords($key)][] = ['filename' => $name, 'linkdownload' => $short['fileUrl'], 'supportos' =>  $supportOS];
                        $validate++;
                    }
                }elseif((int) $curl['downloadFiles']['recommendedForYou']['pagination']['totalItems'] > 1){
                    foreach($curl['downloadFiles']['recommendedForYou']['downloadFilesSubSet']['downloadFiles'] as $fileID => $fileValue){
                        if(strpos($fileValue['fileName'], ".pdf") !== false){
                            continue;
                        }
                        if(!isset($fileValue['fileTitle']) || empty($fileValue['fileTitle'])){
                            $name = $fileValue['fileName'];
                        }else{
                            $name = $fileValue['fileTitle'];
                        }
                        $this->_dataTable["Download Canon Driver or Utilities for ".ucwords($key)][] = ['filename' => $name, 'linkdownload' => $fileValue['fileUrl'], 'supportos' =>  $supportOS];
                        $validate++;
                    }
                }
                if((int) $curl['downloadFiles']['drivers']['pagination']['totalItems'] > 1){
                    foreach($curl['downloadFiles']['drivers']['downloadFilesSubSet']['downloadFiles'] as $fileID => $fileValue){
                        if(strpos($fileValue['fileName'], ".pdf") !== false){
                            continue;
                        }
                        if(!isset($fileValue['fileTitle']) || empty($fileValue['fileTitle'])){
                            $name = $fileValue['fileName'];
                        }else{
                            $name = $fileValue['fileTitle'];
                        }
                        $this->_dataTable["Download Canon Driver or Utilities for ".ucwords($key)][] = ['filename' => $name, 'linkdownload' => $fileValue['fileUrl'], 'supportos' =>  $supportOS];
                        $validate++;
                    }
                    $validate++;
                }elseif((int) $curl['downloadFiles']['drivers']['pagination']['totalItems'] === 1){
                    $short = $curl['downloadFiles']['drivers']['downloadFilesSubSet']['downloadFiles'];
                    if(!isset($short['fileTitle']) || empty($short['fileTitle'])){
                        $name = $short['fileName'];
                    }else{
                        $name = $short['fileTitle'];
                    }
                    if(strpos($fileValue['fileName'], ".pdf") === false){
                        $this->_dataTable["Download Canon Driver or Utilities for ".ucwords($key)][] = ['filename' => $name, 'linkdownload' => $short['fileUrl'], 'supportos' =>  $supportOS];
                        $validate++;
                    }
                }
                if((int) $curl['downloadFiles']['software']['pagination']['totalItems'] > 1){
                    foreach($curl['downloadFiles']['software']['downloadFilesSubSet']['downloadFiles'] as $fileID => $fileValue){
                        if(strpos($fileValue['fileName'], ".pdf") !== false){
                            continue;
                        }
                        if(!isset($fileValue['fileTitle']) || empty($fileValue['fileTitle'])){
                            $name = $fileValue['fileName'];
                        }else{
                            $name = $fileValue['fileTitle'];
                        }
                        $this->_dataTable["Download Canon Driver or Utilities for ".ucwords($key)][] = ['filename' => $name, 'linkdownload' => $fileValue['fileUrl'], 'supportos' =>  $supportOS];
                        $validate++;
                    }
                }elseif((int) $curl['downloadFiles']['software']['pagination']['totalItems'] === 1){
                    $short = $curl['downloadFiles']['software']['downloadFilesSubSet']['downloadFiles'];
                    if(!isset($short['fileTitle']) || empty($short['fileTitle'])){
                        $name = $short['fileName'];
                    }else{
                        $name = $short['fileTitle'];
                    }
                    if(strpos($fileValue['fileName'], ".pdf") === false){
                        $this->_dataTable["Download Canon Driver or Utilities for ".ucwords($key)][] = ['filename' => $name, 'linkdownload' => $short['fileUrl'], 'supportos' =>  $supportOS];
                        $validate++;
                    }
                }
                if($validate < 1){
                    continue;
                }else{
                    break;
                }
            }
        }
        return $this;
    }

    public function getManual(){
        $this->_tempURL = "https://www.usa.canon.com/internet/PA_NWSupport/driversDownloads?model=".$this->_uniqID."&type=MB&lang=English";
        $curl = $this->_curl();
        if((int) $curl['downloadFiles']['recommendedForYou']['pagination']['totalItems'] > 1){
            foreach($curl['downloadFiles']['recommendedForYou']['downloadFilesSubSet']['downloadFiles'] as $fileID => $fileValue){
                if(!isset($fileValue['fileTitle']) || empty($fileValue['fileTitle'])){
                    $name = $fileValue['fileName'];
                }else{
                    $name = $fileValue['fileTitle'];
                }
                if(!isset($fileValue['fileDescr']) || empty($fileValue['fileDescr'])){
                    $desc = $fileValue['fileTitle'];
                }else{
                    $desc = $fileValue['fileDescr'];
                }
                $this->_dataTable["Download Canon Manual User Guides"][] = ['filename' => $name, 'linkdownload' => $fileValue['fileUrl'], 'supportos' => $desc];
            }
        }elseif((int) $curl['downloadFiles']['recommendedForYou']['pagination']['totalItems'] === 1){
            $short = $curl['downloadFiles']['recommendedForYou']['downloadFilesSubSet']['downloadFiles'];
            if(!isset($short['fileTitle']) || empty($short['fileTitle'])){
                $name = $short['fileName'];
            }else{
                $name = $short['fileTitle'];
            }
            if(!isset($short['fileDescr']) || empty($short['fileDescr'])){
                $desc = $short['fileTitle'];
            }else{
                $desc = $short['fileDescr'];
            }
            $this->_dataTable["Download Canon Manual User Guides"][] = ['filename' => $name, 'linkdownload' => $short['fileUrl'], 'supportos' => $desc];
        }

        if((int) $curl['downloadFiles']['brochures']['pagination']['totalItems'] === 1){
            $short = $curl['downloadFiles']['brochures']['downloadFilesSubSet']['downloadFiles'];
            if(!isset($short['fileTitle']) || empty($short['fileTitle'])){
                $name = $short['fileName'];
            }else{
                $name = $short['fileTitle'];
            }
            if(!isset($short['fileDescr']) || empty($short['fileDescr'])){
                $desc = $short['fileTitle'];
            }else{
                $desc = $short['fileDescr'];
            }
            $this->_dataTable["Download Canon Manual User Guides"][] = ['filename' => $name, 'linkdownload' => $short['fileUrl'], 'supportos' => $desc];
        }elseif((int) $curl['downloadFiles']['brochures']['pagination']['totalItems'] > 1){
            foreach($curl['downloadFiles']['brochures']['downloadFilesSubSet']['downloadFiles'] as $fileID => $fileValue){
                if(!isset($fileValue['fileTitle']) || empty($fileValue['fileTitle'])){
                    $name = $fileValue['fileName'];
                }else{
                    $name = $fileValue['fileTitle'];
                }
                if(!isset($fileValue['fileDescr']) || empty($fileValue['fileDescr'])){
                    $desc = $fileValue['fileTitle'];
                }else{
                    $desc = $fileValue['fileDescr'];
                }
                $this->_dataTable["Download Canon Manual User Guides"][] = ['filename' => $name, 'linkdownload' => $fileValue['fileUrl'], 'supportos' => $desc];
            }
        }

        if((int) $curl['downloadFiles']['guidesManuals']['pagination']['totalItems'] === 1){
            $short = $curl['downloadFiles']['guidesManuals']['downloadFilesSubSet']['downloadFiles'];
            if(!isset($short['fileTitle']) || empty($short['fileTitle'])){
                $name = $short['fileName'];
            }else{
                $name = $short['fileTitle'];
            }
            if(!isset($short['fileDescr']) || empty($short['fileDescr'])){
                $desc = $short['fileTitle'];
            }else{
                $desc = $short['fileDescr'];
            }
            $this->_dataTable["Download Canon Manual User Guides"][] = ['filename' => $name, 'linkdownload' => $short['fileUrl'], 'supportos' => $desc];
        }elseif((int) $curl['downloadFiles']['guidesManuals']['pagination']['totalItems'] > 1){
            foreach($curl['downloadFiles']['guidesManuals']['downloadFilesSubSet']['downloadFiles'] as $fileID => $fileValue){
                if(!isset($fileValue['fileTitle']) || empty($fileValue['fileTitle'])){
                    $name = $fileValue['fileName'];
                }else{
                    $name = $fileValue['fileTitle'];
                }
                if(!isset($fileValue['fileDescr']) || empty($fileValue['fileDescr'])){
                    $desc = $fileValue['fileTitle'];
                }else{
                    $desc = $fileValue['fileDescr'];
                }
                $this->_dataTable["Download Canon Manual User Guides"][] = ['filename' => $name, 'linkdownload' => $fileValue['fileUrl'], 'supportos' => $desc];
            }
        }

        return $this;
    }

    /*
    @param $listTable int
    1 === SwallAlert Mode
    2 === HashLink Only
    3 === No Review, No HashLink
    */

    public function getTable(){
        $counter = 0;
        foreach($this->_dataTable as $title => $value){
            if(strpos($title, "Download Canon Driver or Utilities for") !== false){
                $desc = "Support OS:";
            }else{
               $desc = "Description:";
            }
            if($this->_listTable === 1){
                $this->_tempTable[$counter] = '<div><a href="#" class="hrefLink">'.$title.'</a><div style="display:none;"><div style="text-align: center;"><div><div style="text-align: start;"><table id="zaposphere-table-courtyard" style="border-collapse: collapse; border: #D9D9D9; mso-border-alt: solid #D9D9D9 .5pt; mso-border-insideh-themecolor: background1; mso-border-insideh-themeshade: 217; mso-border-insideh: .5pt solid #D9D9D9; mso-border-insidev-themecolor: background1; mso-border-insidev-themeshade: 217; mso-border-insidev: .5pt solid #D9D9D9; mso-border-themecolor: background1; mso-border-themeshade: 217; mso-border-top-alt: solid red 3.0pt; mso-padding-alt: 0cm 5.4pt 0cm 5.4pt; mso-yfti-tbllook: 1184;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td bgcolor="#CCCCCC" width="168"><div align="center"><strong>Download</strong></div></td><td bgcolor="#CCCCCC" width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><strong>Driver</strong></div></div></div></td></tr></tbody><tbody>';
                for($i = 0; $i < count($value); $i++){
                    $downloadHash = encrypt($value[$i]['linkdownload']);
                    $this->_tempTable[$counter] .= '<tr><td width="168"><div align="center"><a href="'.$this->_url.'" class="hrefDownload" id="'.$downloadHash.'"><button style="background-color: #3377FF; border-bottom-left-radius: 2px; border-bottom-right-radius: 2px; border-top-left-radius: 2px; border-top-right-radius: 2px; border: 0px; color: white; cursor: pointer; font-size: 12px; font-weight: bold; margin: 0px; max-width: 100%; padding: 10px 30px 11px; text-transform: uppercase; vertical-align: bottom;">DOWNLOAD</button></a></div></td><td width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><b>'.$value[$i]['filename'].'</b><br>'.$desc.'<br><small>'.$value[$i]['supportos'].'</small></div></div></div></td></tr>';
                }
                $this->_tempTable[$counter] .= '</tbody></table><div style="text-align: center;"></div></div></div></div></div></div>';
            }elseif($this->_listTable === 2){
                $this->_tempTable[$counter] = '<div>'.$title.'<div style="text-align: center;"><div><div style="text-align: start;"><table id="zaposphere-table-courtyard" style="border-collapse: collapse; border: #D9D9D9; mso-border-alt: solid #D9D9D9 .5pt; mso-border-insideh-themecolor: background1; mso-border-insideh-themeshade: 217; mso-border-insideh: .5pt solid #D9D9D9; mso-border-insidev-themecolor: background1; mso-border-insidev-themeshade: 217; mso-border-insidev: .5pt solid #D9D9D9; mso-border-themecolor: background1; mso-border-themeshade: 217; mso-border-top-alt: solid red 3.0pt; mso-padding-alt: 0cm 5.4pt 0cm 5.4pt; mso-yfti-tbllook: 1184;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td bgcolor="#CCCCCC" width="168"><div align="center"><strong>Download</strong></div></td><td bgcolor="#CCCCCC" width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><strong>Driver</strong></div></div></div></td></tr></tbody><tbody>';
                for($i = 0; $i < count($value); $i++){
                    $downloadHash = encrypt($value[$i]['linkdownload']);
                    $this->_tempTable[$counter] .= '<tr><td width="168"><div align="center"><button class="hrefDownload" id="'.$downloadHash.'" style="background-color: #3377FF; border-bottom-left-radius: 2px; border-bottom-right-radius: 2px; border-top-left-radius: 2px; border-top-right-radius: 2px; border: 0px; color: white; cursor: pointer; font-size: 12px; font-weight: bold; margin: 0px; max-width: 100%; padding: 10px 30px 11px; text-transform: uppercase; vertical-align: bottom;">DOWNLOAD</button></div></td><td width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><b>'.$value[$i]['filename'].'</b><br>'.$desc.'<br><small>'.$value[$i]['supportos'].'</small></div></div></div></td></tr>';
                }
                $this->_tempTable[$counter] .= '</tbody></table><div style="text-align: center;"></div></div></div></div></div></div>';
            }else{
                $this->_tempTable[$counter] = '<div>'.$title.'<div style="text-align: center;"><div><div style="text-align: start;"><table id="zaposphere-table-courtyard" style="border-collapse: collapse; border: #D9D9D9; mso-border-alt: solid #D9D9D9 .5pt; mso-border-insideh-themecolor: background1; mso-border-insideh-themeshade: 217; mso-border-insideh: .5pt solid #D9D9D9; mso-border-insidev-themecolor: background1; mso-border-insidev-themeshade: 217; mso-border-insidev: .5pt solid #D9D9D9; mso-border-themecolor: background1; mso-border-themeshade: 217; mso-border-top-alt: solid red 3.0pt; mso-padding-alt: 0cm 5.4pt 0cm 5.4pt; mso-yfti-tbllook: 1184;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td bgcolor="#CCCCCC" width="168"><div align="center"><strong>Download</strong></div></td><td bgcolor="#CCCCCC" width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><strong>Driver</strong></div></div></div></td></tr></tbody><tbody>';
                for($i = 0; $i < count($value); $i++){
                    // $downloadHash = encrypt($value[$i]['linkdownload']);
                    $this->_tempTable[$counter] .= '<tr><td width="168"><div align="center"><a href="'.$value[$i]['linkdownload'].'" target="_blank"><button style="background-color: #3377FF; border-bottom-left-radius: 2px; border-bottom-right-radius: 2px; border-top-left-radius: 2px; border-top-right-radius: 2px; border: 0px; color: white; cursor: pointer; font-size: 12px; font-weight: bold; margin: 0px; max-width: 100%; padding: 10px 30px 11px; text-transform: uppercase; vertical-align: bottom;">DOWNLOAD</button></a></div></td><td width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><b>'.$value[$i]['filename'].'</b><br>'.$desc.'<br><small>'.$value[$i]['supportos'].'</small></div></div></div></td></tr>';
                }
                $this->_tempTable[$counter] .= '</tbody></table><div style="text-align: center;"></div></div></div></div></div></div>';
            }
            $counter++;
        }
        $this->_saveStream();
        return $this->_dataTable;
    }

    protected function _saveStream(){
        $realTemplate = "<div id=\"download-section\">".implode(PHP_EOL, $this->_tempTable)."</div>";
        $fopen = fopen(__DIR__."/../saved/".$this->_codename.".html", "a");
        fwrite($fopen, $realTemplate.PHP_EOL);
        fclose($fopen);
        return;
    }

    public function secondLoop(){
        $getContent = file_get_contents(self::URL_HOME.$this->_firstLink, false, $this->_cookie());
        preg_match('/ddObject\.model = "([^"]+)/', $getContent, $uniqID);
        $this->_uniqID = trim($uniqID[1]);
        return $this;
    }

    public function fetchResource(){
        foreach(self::URL_API as $key => $value){
            $this->_selectOperation = self::URL_API[$key];
            if(preg_match('/href="([^">]+)'.$this->_codename.'/i', $this->__fetchStream())){
                preg_match('/href="([^">]+)'.$this->_codename.'/i', $this->__fetchStream(), $matches);
                $this->_firstLink = $matches[1].$this->_codename;
                return $this;
            }
        }
        throw new Exception("Error! Code Name Invalid or No Product Type Available on Line => ".__LINE__ ." ".__METHOD__);
    }

    private function __construct($codename, $url, $listTable){
        $this->_codename = $codename;
        $this->_url = $url;
        $this->_listTable = (int) $listTable;
    }

    public function run($codename, $url, $listTable){
        if(!isset(self::$instance)){
            self::$instance = new Canon($codename, $url, $listTable);
        }
        return self::$instance;
    }

    public static function input(){
        return trim(fgets(STDIN));
    }

}
echo "Notice: Jika ingin menggunakan untuk region AU/UK/CA silahkan masukkan codename yang berdekatan dengan versi US dan akan otomatis meng-overwrite di region yang diinginkan!\n\n";
echo "Masukkan URL Post : ";
$postURL = Canon::input();
echo "Masukkan CODENAME : ";
$codeName = Canon::input();
echo "========= Masukkan Mode Table =========\n";
echo "1. SweetAlert Mode\n2. HashLink Only\n3. No HashLink No Review\n";
echo "Masukkan Pilihan : ";
$listTable = Canon::input();
try{
    print_r(Canon::run($codeName, $postURL, $listTable)
    ->fetchResource()
    ->secondLoop()
    ->fetchOS()
    ->getDriver()
    ->getManual()
    ->getTable()
);
}catch(Exception $e){
    echo $e->getMessage()."\n";
}finally{
    echo "[SAVED] Filename => ".__DIR__."\\".$codeName.".html";
}
?>