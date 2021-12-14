<?php
/** 
 * config.inc.php
 * <br/> konfigurasi untuk basis data Institusi, FEEDER PDDIKTI dan lainnya
 * <br/> profil  https://id.linkedin.com/in/basitadhi
 * <br/> buat    2021-10-10
 * <br/> rev     -
 * <br/> sifat   open source
 * @author Basit Adhi Prabowo, S.T. <basit@unisayogya.ac.id>
 * @access public
 */

/**
 * WARNING!!!
 * TIdak Boleh Ada Function Pada config.inc.php
 */

define("MODE_SANDBOX", 0);
define("MODE_LIVE", 1);

define("PDDIKTI_FLAG_UNSYNC", 0);
define("PDDIKTI_FLAG_SYNC", 1);
define("PDDIKTI_FLAG_SYNC_UNMATCH", 2);

define("EXECUTION_TIME_LIMIT", 18000); //dalam detik

$http = 'http' . ((array_key_exists('HTTPS', $_SERVER) ? (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") : false) ? "s" : "") . "://";

/**
*** SETTING KHUSUS INJEKTOR
*** Tidak perlu diisi apabila tidak membutuhkan injektor
**/

/* setting basis data institusi */
$institusi["db"]["username"]            = "yourdb_username";
$institusi["db"]["password"]            = "yourdb_password";
$institusi["db"]["port"]                = 3306;
$institusi["db"]["host"]                = "yourdb_host";
$institusi["db"]["database"]            = "yourdb_name";

/* beri komentar jika ssl tidak digunakan */
//$institusi["db"]["ssl"]["client-key"]   = "C:/ssl/client-key.pem";
//$institusi["db"]["ssl"]["client-cert"]  = "C:/ssl/client-cert.pem";
//$institusi["db"]["ssl"]["ca-cert"]      = "C:/ssl/ca.pem";

/**
*** SETTING UMUM
*** Diisi baik untuk CSV maupun injektor
**/

$passwordUNISAFC20              = "passwordAnda";
/* setting webservice PDDIKTI */
$https                          = true; /* apakah server feeder menggunakan https? isi false jika http*/
$pddikti["ws"]["mode"]          = MODE_LIVE; /* MODE_LIVE atau MODE_SANDBOX */
$pddikti["ws"]["host"]          = "localhost";
$pddikti["ws"]["port"]          = 3003;
$pddikti["ws"]["expire"]        = 1800; /* dalam detik */
/* berapa baris data yang diambil dalam satu waktu */
/* tips: */
/* 1. Jangan dibuat terlalu sedikit, karena akan memunculkan banyak proses koneksi ke webservice yang akan memperlambat proses */
/* 2. Jangan dibuat terlalu banyak, karena akan mengakibatkan error berkaitan dengan batasan memory yang diperbolehkan digunakan pada server */
$pddikti["ws"]["limit"]         = 1000; 

/* setting login PDDIKTI */
$pddikti["login"]["username"]   = "your_feederusername";
$pddikti["login"]["password"]   = "your_feederpassword";

/* setting otomatis */
$pddikti["ws"]["protocol"]      = ($https) ? "https://" : "http://";
$pddikti["ws"]["url"]           = $pddikti["ws"]["protocol"].$pddikti["ws"]["host"].":".$pddikti["ws"]["port"]."/ws/".(($pddikti["ws"]["mode"]==MODE_SANDBOX)?"sandbox":"live2").".php";

/**
 * WARNING!!!
 * TIdak Boleh Ada Function Pada config.inc.php
 */
