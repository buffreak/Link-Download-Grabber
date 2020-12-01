<?php
set_time_limit(0);
class Updater{

    protected $fopen, $body;
    public $status;

    const ENDPOINT = 'https://rivai.id/private/grabber/';
    const FILENAME = 'Grabber09ausasmdJBUHUNO88asn.zip';

    public function __construct($auth){
        echo "Checking Update...\n";
        if($auth !== "Rivai"){
            throw new Exception("Don't Do Anything Stupid!");
        }else{
            @$this->request(self::ENDPOINT);
            if(@error_get_last()['type'] === 8){
                throw new Exception("You Must Online to use this tools!\n");
            }
            if(version_compare(trim(file_get_contents(__DIR__.'/version.txt')), $this->body['version'], '<')){
                echo "Now Updating your Script...\n";
                $this->request(self::ENDPOINT.self::FILENAME, false, true);
                $this->zipExtract();
                unlink(__DIR__.'/../../'.self::FILENAME);
                fwrite(fopen(__DIR__.'/version.txt', 'w'), trim($this->body['version']));
                echo "All Done... Now Running script again!\n";
                $this->status("upgrade");
            }else{
                echo "No Update Found, You're in Latest Version...\n\n";
                $this->status("latest");
            }
        }
    }

    public function status($status){
        $this->status = $status;
        return $this->status;
    }

    protected function zipExtract(){
        $zip = new ZipArchive;
        if($zip->open(__DIR__.'/../../'.self::FILENAME) === true){
            $zip->setPassword('#MkhBTIH8y8whJIOJB==+pJOIHM7nYCR8hdabc--)>><.[.].0999');
            $zip->extractTo(__DIR__.'/../../');
            return $this;
        }else{
            throw new Exception("Wrong \$salt or something, you should know!");
        }
    }

    protected function fileStream(){
        $this->fopen = fopen(__DIR__.'/../../'.self::FILENAME, 'w');
        return $this;
    }

    public function request($url, $post = false, $file = false){
        if($file):
            $this->fileStream();
        endif;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        if($file):
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
            curl_setopt($ch, CURLOPT_FILE, $this->fopen);
        else:
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        endif;
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        if($post):
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        endif;
        $result = curl_exec($ch);
        if($file):
            fwrite($this->fopen, $result);
            fclose($this->fopen);
        else:
            list($header, $body) = explode("\r\n\r\n", $result, 2);
            $this->body = json_decode($body, true);
        endif;
        return $this;
    }
}