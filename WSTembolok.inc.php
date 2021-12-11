<?php
/** 
 * WSTembolok.inc.php
 * <br/> Tembolok / Cache Web Service
 * <br/> profil  https://id.linkedin.com/in/basitadhi
 * <br/> buat    2021-12-10
 * <br/> rev     -
 * <br/> sifat   open source
 * @author Basit Adhi Prabowo, S.T. <basit@unisayogya.ac.id>
 * @access public
 */
class WSTembolok
{
    /**
     *
     * @var type 
     */
    private $pointer;
    /**
     *
     * @var type 
     */
    private $relativePath;
    /**
     *
     * @var type 
     */
    private $cNamaberkas;
    
    function WSTembolok()
    {
        $this->relativePath = "~";
    }
    
    /**
     * 
     * @param type $path
     */
    function relativePath($path)
    {
        $this->relativePath = $this->antiPathWalking($path);
    }
    
    /**
     * 
     * @param type $path
     * @return type
     */
    function antiPathWalking($path)
    {
        while (stripos("..", $path) !== false)
            $path = str_ireplace("../", "./", $path);
        while (stripos("//", $path) !== false)
            $path = str_ireplace("//", "/", $path);
        return str_ireplace("//", "/", $path)."/";
    }
    
    /**
     * 
     */
    function isikahPath()
    {
        return $this->relativePath != "~";
    }
    
    /**
     * 
     * @param type $namaBerkas
     */
    function setCNamaberkas($namaBerkas)
    {
        $this->cNamaberkas                          = $namaBerkas;
        $this->pointer[$this->cNamaberkas]["nama"]  = $this->cNamaberkas.".cache";
    }
    
    /**
     * 
     * @return type
     */
    function getCNamaberkas()
    {
        return $this->cNamaberkas;
    }
    
    /**
     * 
     * @return type
     */
    function getPointer()
    {
        return $this->pointer;
    }
    
    /**
     * 
     * @return type1
     */
    function pathBerkas()
    {
        if (array_key_exists($this->cNamaberkas, $this->pointer))
            return $this->relativePath.str_ireplace (".cache", "", $this->pointer[$this->cNamaberkas]["nama"]).".cache";
        else
            return false;
    }
    
    /**
     * 
     */
    function bukaTembolok()
    {
        if (array_key_exists($this->cNamaberkas, $this->pointer))
        {
            $this->pointer[$this->cNamaberkas]["berkas"]    = fopen($this->pathBerkas(), "w+") or die("Unable to open file!");
            return true;
        }
        else
        {
            echo "Belum ada Nama Berkas yang terpilih";
            return false;
        }
    }
    
    /**
     * 
     * @param type $konten
     */
    function tulisTembolok($konten)
    {
        if ($this->bukaTembolok())
        {
            fwrite($this->pointer[$this->cNamaberkas]["berkas"], $konten);
            fclose($this->pointer[$this->cNamaberkas]["berkas"]);
        }
    }
    
    /**
     * 
     * @return type
     */
    function adakahTembolok()
    {
        return (file_exists($this->pathBerkas()) && $this->isikahPath());
    }
}