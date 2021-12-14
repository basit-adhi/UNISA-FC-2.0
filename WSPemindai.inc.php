<?php
define("METHOD_EXPORTDATA"     , "ExportData");
define("METHOD_GET_OTO"        , "Get_Oto");
define("METHOD_GETCOUNT"       , "GetCount");
define("METHOD_GETDETAIL"      , "GetDetail");
define("METHOD_GETREKAP"       , "GetRekap");
define("METHOD_GETLIST"        , "GetList");
define("METHOD_GETDATALENGKAP" , "GetDataLengkap");
define("METHOD_GET"            , "Get");
define("METHOD_UPDATE"         , "Update");
define("METHOD_INSERT"         , "Insert");
define("METHOD_DELETE"         , "Delete");

define("KELUARGA_CREATE", "KELUARGA_CREATE");
define("KELUARGA_READ"  , "KELUARGA_READ");
define("KELUARGA_UPDATE", "KELUARGA_UPDATE");
define("KELUARGA_DELETE", "KELUARGA_DELETE");
define("KELUARGA_OTO"   , "KELUARGA_OTO");

/** 
 * ObjectAct
 * <br/> Kelas Act
 * <br/> profil  https://id.linkedin.com/in/basitadhi
 * <br/> buat    2021-12-10
 * <br/> rev     -
 * <br/> sifat   open source
 * @author Basit Adhi Prabowo, S.T. <basit@unisayogya.ac.id>
 * @access public
 */
class ObjekAct
{
    /**
     * Token saat ini
     */
    public  $cToken;
    /**
     * Objek saat ini
     */
    public  $cObjek;
    /**
     * Lengkap (Token . Objek) saat ini
     */
    public  $cLengkap;
    
    /**
     * membuat semua variabel menjadi ""
     */
    function reset()
    {
        $this->cToken   = "";
        $this->cObjek   = "";
        $this->cLengkap = "";
    }
    
    /**
     * set objek act, cLengkap otomatis
     * @param type $token
     * - token
     * @param type $objek
     * - objek
     */
    function set($token, $objek)
    {
        $this->cToken   = $token;
        $this->cObjek   = $objek;
        $this->cLengkap = $token.$objek;
    }
}

/** 
 * WSPemindai.inc.php
 * <br/> Scanner / Pemindai Act Web Service
 * <br/> profil  https://id.linkedin.com/in/basitadhi
 * <br/> buat    2021-10-25
 * <br/> rev     -
 * <br/> sifat   open source
 * @author Basit Adhi Prabowo, S.T. <basit@unisayogya.ac.id>
 * @access public
 */
class WSPemindai
{
    /**
     * token (method yang digunakan)
     */
    private $token;
    /**
     * act saat ini
     */
    public  $cAct;
    /**
     * nama asli dari objek yang tidak konsisten
     */
    private $alias;
    
    /**
     * konstruktor
     */
    function __construct()
    {
        $this->token    = array();
        $this->cAct     = new ObjekAct();
        $this->populasiToken();
    }
    
    /**
     * membuat semua variabel pada cAct menjadi ""
     */
    function resetObjekAct()
    {
        $this->cAct->reset();
    }
    
    /**
     * inisialisasi token yang digunakan
     */
    function populasiToken()
    {
        $this->registrasiToken(METHOD_EXPORTDATA    , KELUARGA_READ);
        $this->registrasiToken(METHOD_GET           , KELUARGA_READ);
        $this->registrasiToken(METHOD_GETCOUNT      , KELUARGA_READ);
        $this->registrasiToken(METHOD_GETDETAIL     , KELUARGA_READ);
        $this->registrasiToken(METHOD_GETREKAP      , KELUARGA_READ);
        $this->registrasiToken(METHOD_GETLIST       , KELUARGA_READ);
        $this->registrasiToken(METHOD_GETDATALENGKAP, KELUARGA_READ);
        $this->registrasiToken(METHOD_UPDATE        , KELUARGA_UPDATE);
        $this->registrasiToken(METHOD_INSERT        , KELUARGA_CREATE);
        $this->registrasiToken(METHOD_DELETE        , KELUARGA_DELETE);
    }
    
    /**
     * memasukkan token yang akan digunakan, urut by panjang objek desc, kemudian by token desc.
     * dikelompokkan berdasarkan karakter pertama
     * @param type $token
     * - token yang didaftarkan
     * @param type $keluarga
     * - anggota keluarga yang mana dari CRUD
     */
    function registrasiToken($token, $keluarga)
    {
        $indeks                                 = $this->indeksToken($token);
        $this->token[$indeks][$token]           = ["token" => $token, "panjang" => strlen($token), "keluarga" => $keluarga];
        $this->token["keluarga"][$keluarga][]   = $token;
        $this->urutkanToken($indeks);
    }
    
    /**
     * dapatkan keluarga dari token
     * @param type $token
     * - token yang dicari keluarganya
     */
    function dapatkanKeluarga($token)
    {
        $indeks                                 = $this->indeksToken($token);
        return $this->token[$indeks][$token]["keluarga"];
    }
    
    /**
     * periksa apakah token merupakan anggota keluarga tersebut
     * @param type $token
     * - token yang diperiksa
     * @param type $keluarga
     * - keluarga yang diperiksa
     * @return type
     * - anggota keluarga atau bukan (T/F), apabila $keluarga == "KELUARGA_OTO", maka yang penting token valid
     */
    function periksaKeluarga($token, $keluarga)
    {
        $t  = $this->getToken($token);
        if ($keluarga == KELUARGA_OTO)
        {
            $hasil = array_key_exists("token", $t);
        }
        else
        {
            reset($this->token["keluarga"][$keluarga]);
            $hasil  = array_search($t["token"], $this->token["keluarga"][$keluarga]);
        }
        return ($hasil !== false) ? ["valid" => true, "pesan" => ""] : ["valid" => false, "pesan" => "Token ".$t["token"]." merupakan anggota dari ".$t["keluarga"].", sedangkan perintah hanya untuk anggota dari ".$keluarga.". Anggota dari ".$keluarga.": ".$this->listAnggotaKeluarga($keluarga)];
    }
    
    /**
     * mengembalikan semua anggota keluarga
     * @param type $keluarga
     * - keluarga
     * @return type
     * - string anggota keluarga
     */
    function listAnggotaKeluarga($keluarga)
    {
        return ((array_key_exists($keluarga, $this->token["keluarga"]))?implode(",", $this->token["keluarga"][$keluarga]):"");
    }
    
    /**
     * mendapatkan nilai token dan informasinya
     * @param type $token
     * - token
     * @return type
     * - detail token
     */
    function getToken($token)
    {
        $t  = $this->pindai($token)->cToken;
        return $this->token[$this->indeksToken($t)][$t];
    }
    
    /**
     * mendaftarkan alias
     * @param type $alias
     * - alias
     */
    function setAlias($alias)
    {
        $this->alias    = $alias;
    }
    
    /**
     * jika ada alias maka dikembalikan aliasnya
     * @param type $objek
     * - objek yang akan dicek
     * @return type
     * - mengembalikan alias atau objek
     */
    function cobaAlias($objek)
    {
        return ifkeyexists($objek, $this->alias, $objek);
    }
    
    /**
     * mengambil karakter pertama sebagai indeks token
     * @param type $token
     * - token
     * @return type
     * - indeks
     */
    function indeksToken($token)
    {
        return substr($token, 0, 1);
    }
    
    /**
     * urutkan token by panjang objek desc, kemudian by token desc
     * @param type $indeks
     * - indeks token
     */
    function urutkanToken($indeks)
    {
        $columns_1 = array_column($this->token[$indeks], "panjang");
        $columns_2 = array_column($this->token[$indeks], "token");
        array_multisort($columns_1, SORT_DESC, $columns_2, SORT_DESC, $this->token[$indeks]);
    }
    
    /**
     * memindai act, memecah ke dalam token, objek dan lengkap
     * @param type $act
     * - act
     * @return type
     * - token, objek dan lengkap
     */
    function pindai($act)
    {
        $act = trim($act);
        $this->resetObjekAct();
        $indeks = $this->indeksToken($act);
        if (array_key_exists(ifnull($indeks, ""), $this->token))
        {
            foreach ($this->token[$indeks] as $curToken)
            {    
                if (substr($act, 0, $curToken["panjang"]) == $curToken["token"])
                {
                    $this->cAct->set($curToken["token"], $this->cobaAlias(substr($act, $curToken["panjang"])));
                    return $this->cAct;
                }
            }
        }
        return $this->cAct;
    }
}
