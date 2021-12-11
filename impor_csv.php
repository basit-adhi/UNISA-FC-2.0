<?php
/** 
 * impor_csv.php
 * <br/> Eksekutor import/ekspor Feeder Berbasis CSV
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
require_once ("config.inc.php");
require_once ("webservice.inc.php");
$filter = (array_key_exists("filter", $_POST)) ? $_POST["filter"] : FILTER_NONE_STRING;
$berkas = (array_key_exists("filter", $_FILES)) ? $_FILES["filter"] : FILTER_NONE_STRING;
$tabel  = (array_key_exists("tabel", $_POST)) ? $_POST["tabel"] : "";
$method = (array_key_exists("act", $_POST)) ? $_POST["act"] : "";
session_start();
if ($_SESSION["passthru"] == "leres")
{
    session_write_close();
    $ws       = new webservice($pddikti, $institusi, false, false);
    $filename = $tabel.'_'.$method.'_'.date('ymd_His').'.csv';
    $keluarga = $ws->WSPengurai->WSPemindai->dapatkanKeluarga($method);
    switch ($keluarga)
    {
        case KELUARGA_READ:
            $data   = $ws->GetRecordsetArray($tabel, $filter, $method);
            if ($data["error_desc"] == "")
            {
               write_csv($filename, $data["data"]);
            }
            break;
        case KELUARGA_CREATE:
        case KELUARGA_DELETE:
            $path_csv = __DIR__ . "/csv/" . str_replace(".", "", str_replace("csv", "", $filename)).".csv";
            move_uploaded_file($berkas['tmp_name']['ceesvi'], $path_csv);
            if (($handle = fopen($path_csv, "r")) !== FALSE) {
                $header   = array();
                $data_    = array();
                $data_ret = array();
                for ($i = -1; ($data = fgetcsv($handle, 1000, ",")) !== FALSE; $i++) {
                    if ($i == -1)
                    {
                        $header = $data;
                    }                        
                    else
                    {
                        for ($j = 0; $j < count($data); $j++)
                        {
                            $data_[trim($header[$j])] = addslashes($data[$j]);
                        }
                        $result = ($keluarga == KELUARGA_CREATE) ? $ws->InsertRecord($tabel, $data_) : $ws->DeleteRecord($tabel, $data_);
                        $data_ret[$i]         = $data_;
                        $data_ret[$i]["UUID"] = ($result["error_desc"] == "") ? array_pop($result["data"]) : $result["error_desc"];
                    }
                }
                fclose($handle);
                write_csv($filename, $data_ret);
            }
            unlink($path_csv);
            break;
        case KELUARGA_UPDATE:
            $path_csv = __DIR__ . "/csv/" . str_replace(".", "", str_replace("csv", "", $filename)).".csv";
            move_uploaded_file($berkas['tmp_name']['ceesvi'], $path_csv);
            if (($handle = fopen($path_csv, "r")) !== FALSE) {
                $header   = array();
                $data_    = array();
                $data_ret = array();
                for ($i = -1; ($data = fgetcsv($handle, 1000, ",")) !== FALSE; $i++) {
                    if ($i == -1)
                    {
                        $header = $data;
                    }                        
                    else
                    {
                        for ($j = 0; $j < count($data); $j++)
                        {
                            $isprimarykey = $_SESSION["dictionaries"][MODE_DICTIONARY_ALL][$method][$tabel][trim($header[$j])]["primary"] == "primary" ? "key" : "record";
                            $data_[$isprimarykey][trim($header[$j])] = addslashes($data[$j]);
                        }
                        $result = $ws->UpdateRecord($tabel, $data_);
                        $data_ret[$i]         = $data_["record"];
                        $data_ret[$i]["UUID"] = ($result["error_desc"] == "") ? array_pop($result["data"]) : $result["error_desc"];
                    }
                }
                fclose($handle);
                write_csv($filename, $data_ret);
            }
            unlink($path_csv);
            break;
    }
    clean_cache();
}
else 
{
    session_write_close();
    header("location: login.php");
}

function write_csv($filename, $data)
{
    header("Content-type: text/csv");
    header("Cache-Control: no-store, no-cache");
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    $f = fopen('php://output','w');

    if ($f === false) {
     die('Error opening the file ' . $filename);
    }

    if (is_array($data))
    {
         if (sizeof($data) > 0)
         {
           $header = array();
           foreach($data[0] as $idx=>$row)
           {
              $header[$idx] = $idx;
           }
           fputcsv($f, $header);
           reset($data);
         }

         // write each row at a time to a file
         foreach ($data as $row) 
         {
          fputcsv($f, $row);
         }
    }
    else 
    {
         fputcsv($f, ["data"]);
         fputcsv($f, [$data]);
    }

    // close the file
    fclose($f);
}

function clean_cache()
{
    $files = glob(__DIR__ . "/csv/*.csv");
    $now   = time();

    foreach ($files as $file) {
      if (is_file($file)) {
        if ($now - filemtime($file) >= 60 * 60 * 24 * 2) { // 2 days
          unlink($file);
        }
      }
    }
}
?>