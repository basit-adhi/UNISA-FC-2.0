<?php
/** 
 * importerFeeder.php
 * <br/> untuk import/ekspor Feeder Berbasis CSV
 * <br/> profil  https://id.linkedin.com/in/basitadhi
 * <br/> buat    2021-11-09
 * <br/> rev     -
 * <br/> sifat   open source
 * @author Basit Adhi Prabowo, S.T. <basit@unisayogya.ac.id>
 * @access public
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once ("config.inc.php");
require_once ("webservice.inc.php");
require_once ("WSCsv.php");
?>
<html>
<head>
</head>
<body>
<?php
session_start();
if ($_SESSION["passthru"] == "leres")
{
    session_write_close();
    $ws     = new webservice($pddikti, $institusi, false, true);
    $ws->GetDictionaries();
    $csv    = new WSCsv();
    $csv->dropdownTabel();
}
else 
{
    session_write_close();
    header("location: login.php?jump=importerFeeder");
}
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<link rel="stylesheet"  href="gaya.css" />
</body>
</html>
