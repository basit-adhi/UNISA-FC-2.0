<?php
/** 
 * func.inc.php
 * <br/> berisi fungsi-fungsi serbaguna
 * <br/> profil  https://id.linkedin.com/in/basitadhi
 * <br/> buat    2021-10-25
 * <br/> rev     -
 * <br/> sifat   open source
 * @author Basit Adhi Prabowo, S.T. <basit@unisayogya.ac.id>
 * @access public
 */

//------------------------- ok v2
/**
* mengembalikan nilai non-null. Jika input non null kemudian mengembalikan nilai input selain itu mengembalikan nilai alternatif
* Contoh:
* $a = 12;
* echo ifnull($a, 0);  - output: 12
* echo ifnull($x, 0);  - output: 0
* @param type $input      Input yang akan diperiksa
* @param type $alternatif Nilai alternatif
* @return type non null output
*/
function ifnull($input, $alternatif)
{
    return (!isset($input) || is_null($input) || trim($input) == "") ? $alternatif : $input;
}

//------------------------- ok v2
/**
 * mengembalikan nilai non-null. Jika array dengan kunci tertentu tidak ada maka akan mengembalikan nilai alternatif
 * @param type $kunci      indeks/kunci array yang hendak dicari
 * @param type $array      array
 * @param type $alternatif nilai alternatif
 * @return type non null autput
 */
function ifkeyexists($kunci, $array, $alternatif)
{
    if ($kunci != "")
    {
        if (is_array($array))
        {
            if (array_key_exists($kunci, $array))
            {
                return ifnull($array[$kunci], $alternatif);
            }
            else
            {
                return $alternatif;
            }
        }
        else 
        {
            return ifnull($array, $alternatif);
        }
    }
    else
    {
        return $alternatif;
    }
}
