<?php
include_once "WSPemindai.inc.php";

/** 
 * WSPengurai.inc.php
 * <br/> Pengurai / Parser Act Web Service
 * <br/> profil  https://id.linkedin.com/in/basitadhi
 * <br/> buat    2021-12-10
 * <br/> rev     -
 * <br/> sifat   open source
 * @author Basit Adhi Prabowo, S.T. <basit@unisayogya.ac.id>
 * @access public
 */
class WSPengurai
{
    /**
     * daftar act yang diambil dari URL WebService 
     */
    public  $ListWS;
    /**
     * url Web Service
     */
    private $url;
    /**
     * sebuah act (kelas WSPemindai)
     */
    public $WSPemindai;
    
    /**
     * konstruktor
     * @param type $url 
     * - url Web Service
     */
    function __construct($url)
    {
        $this->setURL($url);
        $this->WSPemindai  = new WSPemindai();
        $this->WSPemindai->setAlias($this->aliasExport());
    }
    
    /**
     * set URL Web Service
     * @param type $url 
     * - URL Web Service
     */
    function setURL($url)
    {
        $this->url  = $url;
    }
    
    /**
     * get URL Web Service
     * @return type 
     * - URL Web Service
     */
    function getURL()
    {
        return $this->url;
    }
    
    /**
     * apakah act valid? baik method maupun tabelnya
     * @param type $method 
     * - method
     * @param type $tabel  
     * - tabel
     * @param type $keluarga  
     * - keluarga
     * @return type 
     * - kevalidan act
     */
    public function checkAct($method, $tabel, $keluarga)
    {
        $cekKeanggotaan = $this->WSPemindai->periksaKeluarga($method, $keluarga);
        if ($cekKeanggotaan["valid"])
        {
            if (array_key_exists($method, $this->ListWS))
            {
                if (!array_key_exists($tabel, $this->ListWS))
                {
                    return ["valid" => false, "pesan" => "Metode ".$method." valid, tetapi tabel ".$tabel." tidak ditemukan"];
                }
                else 
                {
                    return ["valid" => true, "pesan" => ""];
                }
            }
            elseif (array_key_exists($tabel, $this->ListWS))
            {
                return ["valid" => false, "pesan" => "Tabel ".$tabel." valid, tetapi Metode ".$method." tidak ditemukan"];
            }
        }
        else
        {
            return ["valid" => false, "pesan" => $cekKeanggotaan["pesan"]];
        }
    }
    
    /**
     * generate act
     * @param type $method 
     * - method
     * @param type $tabel  
     * - tabel
     * @param type $keluarga
     * - keluarga
     * @return type 
     * - string act atau string kosong apabila act tidak tersedia
     */
    public function act($method, $tabel, $keluarga)
    {
        $c  = $this->checkAct($method, $tabel, $keluarga);
        if ($c["valid"])
        {
            return $method.$tabel;
        }
        else 
        {
            echo "<BR/>".$c["pesan"];
            return "ActError";
        }
    }
    
    /**
     * dapatkan method get dengan prioritas pertama
     * @param type $tabel 
     * - tabel
     * @return type 
     * - method get dengan prioritas pertama
     */
    public function actGet($tabel, $method=METHOD_GET_OTO)
    {
        if ($method == METHOD_GET_OTO)
        {
            reset($this->ListWS["ALL"][$tabel]["get_prio"]);
            return $this->act(current($this->ListWS["ALL"][$tabel]["get_prio"]), $tabel, KELUARGA_READ);
        }
        else 
        {
            return $this->act($method, $tabel, KELUARGA_READ);
        }
    }
    
    /**
     * membaca semua act Web Service
     * @return type 
     * - semua act
     */
    function bacaListWS()
    {
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $this->url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch); 
        if (empty($result))
        {
            $result = curl_error($ch);
        }
        curl_close($ch);
        return explode("<br>", $result);
    }
    
    /**
     * alias untuk method Export karena menggunakan nama yang berbeda dengan act lainnya, agar terkelompok pada tabel yang sama
     * @return type 
     * - alias
     */
    function aliasExport()
    {
        return  [   
                    "NilaiTransfer"         => "NilaiTransferPendidikanMahasiswa",
                    "PenugasanDosenProdi"   => "PenugasanDosen",
                    "Mahasiswa"             => "Mahasiswa",
                    "MatkulProdi"           => "MataKuliah",
                    "MahasiswaKRS"          => "KRSMahasiswa",
                    "MengajarDosen"         => "AktivitasMengajarDosen",
                    "AktivitasKuliah"       => "AktivitasKuliahMahasiswa",
                    "MahasiswaLulus"        => "MahasiswaLulusDO",
                    "KelasPerkuliahan"      => "KelasKuliah"
                ];
    }
    
    /**
     * method get ada beberapa jenis, perlu diberi prioritaskan untuk otomatisasi.
     * prioritas tidak untuk manual, manual langsung saja
     */
    function prioritasGet()
    {
        return  [
                    METHOD_GET              => 0,
                    METHOD_GETLIST          => 1,
                    METHOD_GETDETAIL        => 2,
                    METHOD_GETREKAP         => 3,
                    METHOD_GETDATALENGKAP   => 4
                ];
    }
    
    /**
     * Memisah Web Service ke dalam 4 kelompok Method. Ada dua sudut pandang, by Method atau by Objek
     */
    function ListSemuaWs()
    {
        /*persiapan*/
        $prio_get           = $this->prioritasGet();
        /*baca ListWS dari URL WebService*/
        $hasil              = $this->bacaListWS();
        $is_ref             = array();
        /*proses pembuatan ListWS/parsing*/
        foreach ($hasil as $act)
        {
            $def = explode(":", $act);
            $currentAct = $this->WSPemindai->pindai($def[0]);
            if ($currentAct->cObjek != "")
            {
                /*tambahan info di get*/
                $this->ListWS["ALL"][$currentAct->cObjek]["token"][$currentAct->cToken] = $currentAct->cToken;
                /*set*/
                $this->ListWS[$currentAct->cObjek][$currentAct->cToken]                 = ["table" => $currentAct->cObjek, "Deskripsi" => $def[1]];
                $this->ListWS[$currentAct->cToken][$currentAct->cObjek]                 = ["table" => $currentAct->cObjek, "Deskripsi" => $def[1]];
                switch ($currentAct->cToken)
                {
                    case METHOD_GETCOUNT:
                    case METHOD_EXPORTDATA:
                        break;
                    case METHOD_GETDATALENGKAP:
                    case METHOD_GETDETAIL:
                    case METHOD_GETREKAP:
                    case METHOD_GETLIST:
                    case METHOD_GET:
                        /* suatu objek dikatakan ref apabila tidak ada Method Update, Insert dan Delete */
                        $is_ref[$currentAct->cObjek]                                                           = ifkeyexists($currentAct->cObjek, $is_ref, true);
                        /*set*/
                        $this->ListWS[$currentAct->cObjek]["ALL"]["table"]                                     = $currentAct->cObjek;
                        $this->ListWS[$currentAct->cObjek]["ALL"]["Deskripsi"]                                 = $def[1];
                        $this->ListWS[$currentAct->cObjek]["ALL"]["ref"]                                       = $is_ref[$currentAct->cObjek];
                        $this->ListWS[$currentAct->cObjek]["ALL"][$currentAct->cToken]                         = $currentAct->cToken;
                        $this->ListWS[$currentAct->cObjek]["ALL"]["get_prio"][$prio_get[$currentAct->cToken]]  = $currentAct->cToken;
                        $this->ListWS["ALL"][$currentAct->cObjek]["table"]                                     = $currentAct->cObjek;
                        $this->ListWS["ALL"][$currentAct->cObjek]["Deskripsi"]                                 = $def[1];
                        $this->ListWS["ALL"][$currentAct->cObjek]["ref"]                                       = $is_ref[$currentAct->cObjek];
                        $this->ListWS["ALL"][$currentAct->cObjek]["token"][$currentAct->cToken]                = $currentAct->cToken;
                        $this->ListWS["ALL"][$currentAct->cObjek]["get_prio"][$prio_get[$currentAct->cToken]]  = $currentAct->cToken;
                        break;
                    case METHOD_UPDATE:
                        /* suatu objek dikatakan ref apabila tidak ada Method Update, Insert dan Delete */
                        if (array_key_exists($currentAct->cObjek, $is_ref))
                        {
                            $this->ListWS[$currentAct->cObjek]["ALL"]["ref"] = false;
                            $this->ListWS["ALL"][$currentAct->cObjek]["ref"] = false;
                        }
                        $is_ref[$currentAct->cObjek]                         = false;
                        break;
                    case METHOD_INSERT:
                        /* suatu objek dikatakan ref apabila tidak ada Method Update, Insert dan Delete */
                        if (array_key_exists($currentAct->cObjek, $is_ref))
                        {
                            $this->ListWS[$currentAct->cObjek]["ALL"]["ref"] = false;
                            $this->ListWS["ALL"][$currentAct->cObjek]["ref"] = false;
                        }
                        $is_ref[$currentAct->cObjek]                         = false;
                        break;
                    case METHOD_DELETE:
                        /* suatu objek dikatakan ref apabila tidak ada Method Update, Insert dan Delete */
                        if (array_key_exists($currentAct->cObjek, $is_ref))
                        {
                            $this->ListWS[$currentAct->cObjek]["ALL"]["ref"] = false;
                            $this->ListWS["ALL"][$currentAct->cObjek]["ref"] = false;
                        }
                        $is_ref[$currentAct->cObjek]                         = false;
                        break;
                }
            }
        }
    }
}