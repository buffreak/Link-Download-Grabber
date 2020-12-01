<?php
date_default_timezone_set("Asia/Jakarta");
class Copyright{

    protected static $instance;

    public function __construct(){
        // Silence Is Golden!
        // Keep Silent
    }

    public static function run(){
        if(!isset(self::$instance)){
            self::$instance = new Copyright;
        }
        return self::$instance;
    }

    protected function _timeCurrent(){
        $containerBulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $containerHari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
        $timestamp = time();
        $jam = date("G", $timestamp); // Index 0 to 23
        $hari = date("w", $timestamp); // Index from 0 to 6
        $bulan = date("n", $timestamp); // index From 1 to 12
        $tahun = date("Y", $timestamp);
        if((int) $jam < 11){
            $selamat = "Selamat Pagi";
        }elseif((int) $jam < 16){
            $selamat = "Selamat Siang";
        }elseif((int) $jam < 19){
            $selamat = "Selamat Sore";
        }else{
            $selamat = "Selamat Malam";
        }
        $ucapan = "Hai! ".$selamat.", Hari Ini ".$containerHari[(int) $hari].", ".date("d", $timestamp)." ".$containerBulan[(int) $bulan - 1]." ". $tahun;
        echo $ucapan."\n";
        if(PHP_VERSION < 7.1){
            echo strtoupper("[ALERT] Your Version PHP => ".PHP_VERSION." is DEPRECATED! you must Uprgrade to min version v.7.1.x for Compatibility!");
        }
        echo "\n\n";
        return;
    }

    public function contributor(){
        echo
        '
        █▄▄ █░█ █▀▀ █▀▀ █▀█ █▀▀ ▄▀█ █▄▀
        █▄█ █▄█ █▀░ █▀░ █▀▄ ██▄ █▀█ █░█
        ';
        echo "\n======================================================\n\n";
        echo "\n===== Version V15 Changelog [STABLE] =====\n";
        echo "[1] Fixed Regex Pattern in Epson\n";
        echo "[2] Fixed Cookie unauthorized in HP\n";
        echo "===== Version V15 Changelog [STABLE] =====\n\n";
        echo "======================================================\n\n";
        echo "Email: support@rivai.id\n";
        $this->_timeCurrent();
        return;
    }
}
?>