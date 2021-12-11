<?php
/** 
 * login.php
 * <br/> Login Page
 * <br/> profil  https://id.linkedin.com/in/basitadhi
 * <br/> buat    2021-12-10
 * <br/> rev     -
 * <br/> sifat   open source
 * @author Basit Adhi Prabowo, S.T. <basit@unisayogya.ac.id>
 * @access public
 */
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
require_once "func.inc.php";
require_once "config.inc.php";
$pasz = filter_input(INPUT_POST, "pass", FILTER_SANITIZE_STRING);
$jump = ifnull(filter_input(INPUT_GET, "jump", FILTER_SANITIZE_STRING), "init.inc");
echo $pasz;
if ($pasz == $passwordUNISAFC20)
{
    /* set the cache expire to x minutes */
    session_start();
    session_cache_expire(($pddikti["ws"]["expire"] / 60) + 1);
    $_SESSION["passthru"] = "leres";
    session_write_close();
    header("location: ".$jump.".php");
}
else
{
    echo '<div style="margin:auto;width:400px;padding:50"><form action="" method="post">
            <div style="margin:auto;width:200px;"><input type="password" id="pass" name="pass" style="width:200px" /><br/><br/></div>
            <div style="margin:auto;width:100px;"><input type="submit" value="Login"/></div>
          </form></div>';
}
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">