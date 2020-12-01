<?php
class AMD{

    protected $codename, $url, $table, $id, $name, $listDriver = [], $match = [], $driver = [], $_table;

    const DATA_URL = [
        'fetch_product' => 'https://www.amd.com/en/support',
        'resource' => 'https://www.amd.com/rest/support_alias/en/',
        'home' => 'https://www.amd.com',
        'techspot' => 'https://www.techspot.com',
        'ftp' => 'http://ftp.nluug.nl/pub/games/PC/guru3d/amd/2020/?C=M;O=D',
        'ftp_home' => 'http://ftp.nluug.nl/pub/games/PC/guru3d/amd/2020/'
    ];

    const LINUX_DOWNLOAD = [
        'Radeon™ Software for Linux® version 19.20 for Ubuntu 18.04.2' => 'http://gamingdrivers.com/amd/amdgpu-pro-19.20-812932-ubuntu-18.04.tar.xz',
        'Radeon™ Software for Linux® version 19.50 for RHEL 7.6 / CentOS 7.6' => 'http://gamingdrivers.com/amd/amdgpu-pro-19.50-967956-rhel-7.7.tar.xz',
        'Radeon™ Software for Linux® version 19.50 for RHEL 8.0 / CentOS 8.0' => 'http://gamingdrivers.com/amd/amdgpu-pro-19.50-967956-rhel-8.1.tar.xz',
        'Radeon™ Software for Linux® version 19.50 for SLED/SLES 15' => 'http://gamingdrivers.com/amd/amdgpu-pro-19.50-967960-sle-15.tar.xz'
    ];

    public function __construct($url, $codename){
        $this->url = $url;
        $this->codename = $codename;
    }

    protected function curl($url, $follow = true){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if($follow){
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        }else{
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        }
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['referer: https://www.amd.com/en/support']);
        $result = curl_exec($ch);
        list($header, $body) = explode("\r\n\r\n", $result, 2);
        return ['header' => $header, 'body' => $body];

    }

    public function getProduct(){
        $curl = $this->curl(self::DATA_URL['fetch_product'])['body'];
        $splitFirst = preg_split('/<option value="816" data-parent="0">/', $curl)[1];
        $getElement = preg_split('/<\/option><\/select>/', $splitFirst)[0];
        preg_match_all('/value="(.*?)" (.*)[ \r\n]+(.*)/', $getElement, $value); // Index [1] ID, Index [3] Name AMD
        $this->ID = $value[1];
        $this->name = $value[3];
        foreach($this->name as $key => $name){
            if(stripos($name, $this->codename) !== false){
                echo "Apakah Benar Product Name => ".trim($name)." ? 1.ya 0.tidak : ";
                $pilihan = (int) trim(fgets(STDIN));
                if($pilihan){
                    $this->ID = trim($this->ID[$key]);
                    $this->name = trim($name);
                    return $this;
                }
            }
        }
        throw new Exception("Invalid Codename!....\n");
    }

    public function fetchResource(){
        $curl = self::DATA_URL['home'].stripslashes(json_decode($this->curl(self::DATA_URL['resource'].$this->ID)['body'], true)['link']);
        $fetch = $this->curl($curl)['body'];
        $driverPerOS = preg_split('<details class="os-group">', $fetch); // List Driver Per OS Selected
        array_shift($driverPerOS);
        $matchs = [];
        $downloadDriver = $this->getLinkDownload();
        foreach($driverPerOS as $key => $listDriver){
            preg_match('/<summary>(.*?)</', $listDriver, $listOS); // Index 1 Get List OS
            preg_match_all('/<div class="field field--name-revision-number field--type-string field--label-above">[ \r\n]+(.*)[ \r\n]+(.*?)<div class="field__item">(.*?)<\/div>/', $listDriver, $list); // Index 3 List Driver
            foreach($list[3] as $keyList => $listFinal){
                $matchs[strtolower(trim(str_replace(" - ", " ", $listOS[1])))][trim($listFinal)] = $listFinal;
            }
        }
        foreach($matchs as $key => $value){
            foreach($value as $name => $finalDriver){
                if(stripos($key, 'Windows 10 64') !== false){
                    if(stripos($name, 'Adrenalin') !== false){
                        $this->driver[$this->name." Driver for windows 10 64-bit"][] = ['title' => 'Windows 10 64-Bit Radeon Software HOTFIX (Latest Version)', 'link_download' => ($downloadDriver['win10'][0]), 'support_os' => 'Windows 10 64-bit'];
                        $this->driver[$this->name." Driver for windows 10 64-bit"][] = ['title' => 'Windows 10 64-Bit Radeon Software WHQL (Recommended)', 'link_download' => ($downloadDriver['win10'][1]), 'support_os' => 'Windows 10 64-bit'];
                        // $this->driver[$this->name." Driver for windows 10 64-bit"][] = ['title' => 'Windows 10 64-Bit Radeon Software Enterprise For Editing', 'link_download' => ($downloadDriver['enterprise']['windows 10 64']), 'support_os' => 'Windows 10 64-bit'];
                    }elseif(stripos($name, 'Catalyst') !== false){
                        // $this->driver[$this->name." Driver for windows 10 64-bit"][] = ['title' => 'Windows 10 64-Bit Catalyst Software HOTFIX (Latest Version)', 'link_download' => ($downloadDriver['catalyst']['windows 10 64']), 'support_os' => 'Windows 10 64-bit'];
                    }elseif(stripos($name, 'Crimson') !== false){
                        $this->driver[$this->name." Driver for windows 10 64-bit"][] = ['title' => 'Windows 10 64-Bit Crimson Software HOTFIX (Latest Version)', 'link_download' => 'http://ftp.nluug.nl/pub/games/PC/guru3d/amd/%5BGuru3D.com%5D-Non-WHQL-Win10-64Bit-Radeon-Software-Crimson-ReLive-17.11.4-Nov27.exe', 'support_os' => 'Windows 10 64-bit'];
                    }
                }elseif(stripos($key, 'Windows 10 32') !== false){
                    if(stripos($name, 'Adrenalin') !== false){
                        $this->driver[$this->name." Driver for windows 10 32-bit"][] = ['title' => 'Windows 10 32-Bit Radeon Software HOTFIX (Latest Version)', 'link_download' => ($downloadDriver['win10'][0]), 'support_os' => 'Windows 10 32-bit'];
                        $this->driver[$this->name." Driver for windows 10 32-bit"][] = ['title' => 'Windows 10 32-Bit Radeon Software WHQL (Recommended)', 'link_download' => ($downloadDriver['win10'][1]), 'support_os' => 'Windows 10 32-bit'];
                        // $this->driver[$this->name." Driver for windows 10 32-bit"][] = ['title' => 'Windows 10 32-Bit Radeon Software Enterprise For Editing', 'link_download' => ($downloadDriver['enterprise']['windows 10 32']), 'support_os' => 'Windows 10 32-bit'];
                    }elseif(stripos($name, 'Catalyst') !== false){
                        // $this->driver[$this->name." Driver for windows 10 32-bit"][] = ['title' => 'Windows 10 32-Bit Catalyst Software HOTFIX (Latest Version)', 'link_download' => ($downloadDriver['catalyst']['windows 10 32']), 'support_os' => 'Windows 10 32-bit'];
                    }elseif(stripos($name, 'Crimson') !== false){
                        $this->driver[$this->name." Driver for windows 10 32-bit"][] = ['title' => 'Windows 10 32-Bit Crimson Software HOTFIX (Latest Version)', 'link_download' => 'http://ftp.nluug.nl/pub/games/PC/guru3d/amd/%5BGuru3D.com%5D-Non-WHQL-Win10-32Bit-Radeon-Software-Crimson-ReLive-17.11.4-Nov27.exe', 'support_os' => 'Windows 10 32-bit'];
                    }
                }elseif(stripos($key, 'Windows 8.1 64') !== false || stripos('Windows 8 64', $key) !== false ){
                    if(stripos($name, 'Adrenalin') !== false){
                        $this->driver[$this->name." Driver for windows 8/8.1 64-bit"][] = ['title' => 'Windows 8/8.1 64-Bit Radeon Software HOTFIX (Latest Version)', 'link_download' => ($downloadDriver['win7'][0]), 'support_os' => 'Windows 8/8.1 64-bit'];
                        $this->driver[$this->name." Driver for windows 8/8.1 64-bit"][] = ['title' => 'Windows 8/8.1 64-Bit Radeon Software WHQL (Recommended)', 'link_download' => ($downloadDriver['win7'][1]), 'support_os' => 'Windows 8/8.1 64-bit'];
                    }elseif(stripos($name, 'Catalyst') !== false){
                        $this->driver[$this->name." Driver for windows 8/8.1 64-bit"][] = ['title' => 'Windows 8/8.1 64-Bit Catalyst Software HOTFIX (Latest Version)', 'link_download' => 'http://ftp.nluug.nl/pub/games/PC/guru3d/ati/amd_catalyst_13.11_R9_290_series_whql-%5BGuru3D.com%5D.exe', 'support_os' => 'Windows 8/8.1 64-bit'];
                    }elseif(stripos($name, 'Crimson') !== false){
                        $this->driver[$this->name." Driver for windows 8/8.1 64-bit"][] = ['title' => 'Windows 8/8.1 64-Bit Crimson Software HOTFIX (Latest Version)', 'link_download' => 'http://ftp.nluug.nl/pub/games/PC/guru3d/amd/%5BGuru3D.com%5D-non-whql-win7-64bit-radeon-software-crimson-relive-17.10.2-oct23.exe', 'support_os' => 'Windows 8/8.1 64-bit'];
                    }
                }elseif(stripos($key, 'Windows 8.1 32') !== false || stripos('Windows 8 32-Bit', $key) !== false ){
                    if(stripos($name, 'Adrenalin') !== false){
                        $this->driver[$this->name." Driver for windows 8/8.1 32-bit"][] = ['title' => 'Windows 8/8.1 32-Bit Radeon Software HOTFIX (Latest Version)', 'link_download' => 'http://ftp.nluug.nl/pub/games/PC/guru3d/amd/%5BGuru3D.com%5D-win7-64bit-radeon-software-adrenalin-edition-17.12.1-dec11.exe', 'support_os' => 'Windows 8/8.1 32-bit'];
                        // $this->driver['Windows 8/8.1 32-Bit Radeon Software WHQL (Recommended)'] = $downloadDriver['whql_recommended']['windows 8 32'];
                    }elseif(stripos($name, 'Catalyst') !== false){
                        // $this->driver['Windows 8/8.1 32-Bit Catalyst Software HOTFIX (Latest Version)'] = $downloadDriver['catalyst']['windows 8.1 32'];
                    }elseif(stripos($name, 'Crimson') !== false){
                       //  $this->driver['Windows 8/8.1 32-Bit Crimson Software HOTFIX (Latest Version)'] = $downloadDriver['crimson']['windows 18 32'];
                    }
                }elseif(stripos($key, 'Windows 7 64') !== false){
                    if(stripos($name, 'Adrenalin') !== false){
                        $this->driver[$this->name." Driver for windows 7 64-bit"][] = ['title' => 'Windows 7 64-Bit Radeon Software HOTFIX (Latest Version)', 'link_download' => $downloadDriver['win7'][0], 'support_os' => 'Windows 7 64-bit'];
                        $this->driver[$this->name." Driver for windows 7 64-bit"][] = ['title' => 'Windows 7 64-Bit Radeon Software WHQL (Recommended)', 'link_download' => ($downloadDriver['win7'][1]), 'support_os' => 'Windows 7 64-bit'];
                        // $this->driver[$this->name." Driver for windows 7 64-bit"][] = ['title' => 'Windows 7 64-Bit Radeon Software Enterprise For Editing', 'link_download' => ($downloadDriver['enterprise']['windows 7 64']), 'support_os' => 'Windows 7 64-bit'];
                    }elseif(stripos($name, 'Catalyst') !== false){
                        $this->driver[$this->name." Driver for windows 7 64-bit"][] = ['title' => 'Windows 7 64-Bit Catalyst Software HOTFIX (Latest Version)', 'link_download' => 'http://ftp.nluug.nl/pub/games/PC/guru3d/ati/amd_catalyst_13.11_R9_290_series_whql-%5BGuru3D.com%5D.exe', 'support_os' => 'Windows 7 64-bit'];
                    }elseif(stripos($name, 'Crimson') !== false){
                        $this->driver[$this->name." Driver for windows 7 64-bit"][] = ['title' => 'Windows 7 64-Bit Crimson Software HOTFIX (Latest Version)', 'link_download' => 'http://ftp.nluug.nl/pub/games/PC/guru3d/amd/%5BGuru3D.com%5D-Non-WHQL-Win7-64Bit-Radeon-Software-Crimson-ReLive-17.11.4-Nov27.exe', 'support_os' => 'Windows 7 64-bit'];
                    }
                }elseif(stripos($key, 'Windows 7 32') !== false){
                    if(stripos($name, 'Adrenalin') !== false){
                        // $this->driver[$this->name." Driver for windows 7 32-bit"][] = ['title' => 'Windows 7 32-Bit Radeon Software HOTFIX (Latest Version)', 'link_download' => ($downloadDriver['latest_version']['windows 7 32']), 'support_os' => 'Windows 7 32-bit'];;
                        // $this->driver[$this->name." Driver for windows 7 32-bit"][] = ['title' => 'Windows 7 32-Bit Radeon Software WHQL (Recommended)', 'link_download' => ($downloadDriver['whql_recommended']['windows 7 32']), 'support_os' => 'Windows 7 32-bit'];
                        // $this->driver[$this->name." Driver for windows 7 32-bit"][] = ['title' => 'Windows 7 32-Bit Radeon Software Enterprise For Editing', 'link_download' => ($downloadDriver['enterprise']['windows 7 32']), 'support_os' => 'Windows 7 32-bit'];
                    }elseif(stripos($name, 'Catalyst') !== false){
                        $this->driver[$this->name." Driver for windows 7 32-bit"][] = ['title' => 'Windows 7 32-Bit Catalyst Software HOTFIX (Latest Version)', 'link_download' => 'http://ftp.nluug.nl/pub/games/PC/guru3d/ati/amd_catalyst_13.11_R9_290_series_whql-%5BGuru3D.com%5D.exe', 'support_os' => 'Windows 7 32-bit'];
                    }elseif(stripos($name, 'Crimson') !== false){
                        $this->driver[$this->name." Driver for windows 7 32-bit"][] = ['title' => 'Windows 7 32-Bit Crimson Software HOTFIX (Latest Version)', 'link_download' => 'http://ftp.nluug.nl/pub/games/PC/guru3d/amd/%5BGuru3D.com%5D-Non-WHQL-Win7-32Bit-Radeon-Software-Crimson-ReLive-17.11.4-Nov27.exe', 'support_os' => 'Windows 7 32-bit'];
                    }
                }
                break;
            }
        }
        foreach(self::LINUX_DOWNLOAD as $key => $download){
            $this->driver[$this->name." Driver for Linux"][] = ['title' => $key, 'link_download' => $download, 'support_os' => substr($key, strrpos($key, 'for') + 4, strlen($key))];
        }
        return $this;
    }
    public function getTable(){
        foreach($this->driver as $OS => $criteria){
            $this->_table = '<div><a href="#" class="hrefLink">'.$OS.'</a><div style="display:none;"><div style="text-align: center;"><div><div style="text-align: start;"><table id="zaposphere-table-courtyard" style="border-collapse: collapse; border: #D9D9D9; mso-border-alt: solid #D9D9D9 .5pt; mso-border-insideh-themecolor: background1; mso-border-insideh-themeshade: 217; mso-border-insideh: .5pt solid #D9D9D9; mso-border-insidev-themecolor: background1; mso-border-insidev-themeshade: 217; mso-border-insidev: .5pt solid #D9D9D9; mso-border-themecolor: background1; mso-border-themeshade: 217; mso-border-top-alt: solid red 3.0pt; mso-padding-alt: 0cm 5.4pt 0cm 5.4pt; mso-yfti-tbllook: 1184;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td bgcolor="#CCCCCC" width="168"><div align="center"><strong>Download</strong></div></td><td bgcolor="#CCCCCC" width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><strong>Driver</strong></div></div></div></td></tr></tbody><tbody>';
            foreach($criteria as $category => $value){
                $this->_table .= '<tr><td width="168"><div align="center"><a href="'.$this->url.'" class="hrefDownload" id="'.encrypt($value['link_download']).'"><button style="background-color: #3377FF; border-bottom-left-radius: 2px; border-bottom-right-radius: 2px; border-top-left-radius: 2px; border-top-right-radius: 2px; border: 0px; color: white; cursor: pointer; font-size: 12px; font-weight: bold; margin: 0px; max-width: 100%; padding: 10px 30px 11px; text-transform: uppercase; vertical-align: bottom;">DOWNLOAD</button></a></div></td><td width="635"><div align="center"><div style="text-align: left;"><div style="text-align: center;"><b>'.$value['title'].'</b><br>Support OS:<br><small>'.$value['support_os'].'</small></div></div></div></td></tr>';
            }
            $this->_table .= '</tbody></table><div style="text-align: center;"></div></div></div></div></div></div>';
            fwrite(fopen(__DIR__.'/../saved/'.$this->codename.".html", "a"), $this->_table.PHP_EOL);
        }
        return $this->driver;
    }

    public function getLinkDownload(){
        $curl = $this->curl(self::DATA_URL['ftp'])['body'];
        preg_match_all('/<a href="(.*?)">\[Guru3D.com\]/', $curl, $url);
        $counter = 0;
        $container = [];
        foreach($url[1] as $key => $linkDownload){
            if($counter > 3 || (int) $counter === 4){
                break;
            }
            if(stripos($linkDownload, "win7")){
                $container['win7'][] = trim(self::DATA_URL['ftp_home'].$linkDownload);
                $counter++;
            }elseif(stripos($linkDownload, "win10")){
                $container['win10'][] = trim(self::DATA_URL['ftp_home'].$linkDownload);
                $counter++;
            }
        }
        return $container;
    }
}
echo "Masukkan URL : ";
$url = trim(fgets(STDIN));
echo "Masukkan Codename : ";
$codename = trim(fgets(STDIN));
$AMD = new AMD($url, $codename);
try{
    print_r(
        $AMD->getProduct()
        ->fetchResource()
        ->getTable()
    );
}catch(Exception $e){
    echo $e->getMessage();
}finally{
    echo "\nAll Done";
}
?>