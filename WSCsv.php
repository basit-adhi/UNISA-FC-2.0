<?php
/** 
 * WSCsv.inc.php
 * <br/> Importer/Exporter CSV
 * <br/> profil  https://id.linkedin.com/in/basitadhi
 * <br/> buat    2021-12-10
 * <br/> rev     -
 * <br/> sifat   open source
 * @author Basit Adhi Prabowo, S.T. <basit@unisayogya.ac.id>
 * @access public
 */
class WSCsv
{
    
    function dropdownTabel()
    {
        @session_start();
        if (!array_key_exists("GetDictionaries", $_SESSION))
        {
            echo "GetDictionaries pada WS belum dijalankan";
        }
        else if (array_key_exists("tembolok_listWS", $_SESSION))
        {
            echo "<div class='selector'>";
            echo "<select name='wstabel' id='wstabel' class='wstabel'>";
            echo "<option value='0'>[ Pilih Salah Satu ]</option>";
            foreach ($_SESSION["tembolok_listWS"]["ALL"] as $idx => $val)
            {
                echo "<option value='$idx'>$idx</option>";
            }
            echo "</select>";
            echo "<select name='wsact' id='wsact' class='wsact'></select>";
            echo "<button type='button' class='btn btn-primary' name='btnCsv' id='btnCsv' onclick=\"download_csv_file()\"> Unduh Template CSV </button>";
            echo "</div>";
            
            echo "<p name='labeldeskripsi' id='labeldeskripsi' ></p>";
            echo "<div class='request' name='request' id='request'></div>";
            echo "<div class='info'>";
            echo "<p name='label' id='label' ></p>";
            echo "<p name='labelfield' id='labelfield' ></p>";
            echo "</div>";
            echo "<script src='https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js'></script>";
            echo "<script>"
                    ."listws = JSON.parse('".json_encode($_SESSION["tembolok_listWS"]["ALL"])."');\n"
                    ."listact = JSON.parse('".json_encode($_SESSION["dictionaries"][MODE_DICTIONARY_ALL])."');\n"
                    ."
                    var valwsact, valwstabel;
                    
                    $('#btnCsv').hide();
                    
                    const selectElementtabel = document.querySelector('.wstabel');
                    selectElementtabel.addEventListener('change', (event) => {
                      valwstabel = `\${event.target.value}`;
                      dropdownact();
                      valwsact   = $('#wsact option:first').val();
                      visibleBtnCsv();
                      generateRequest();
                    });
                    
                    const selectElementact = document.querySelector('.wsact');
                    selectElementact.addEventListener('change', (event) => {
                      valwsact = `\${event.target.value}`;
                      visibleBtnCsv();
                      generateRequest();
                    });
                                        
                    function visibleBtnCsv()
                    {
                        if (valwsact == 'Insert' || valwsact == 'Update' || valwsact == 'Delete')
                            $('#btnCsv').show();
                        else
                            $('#btnCsv').hide();
                    }

                    function dropdownact(){
                        $('#wsact').empty();
                        $.each(listws[valwstabel]['token'], function(i, p) {
                            $('#wsact').append($('<option></option>')
                                .val(p.trim()).html(p.trim()));
                        });
                    }
                    
                    function getPrio1(){ 
                        var ret = '';
                        $.each(listws[valwstabel]['get_prio'], function(i, p) {
                            ret = p;
                            return false;
                        });
                        return ret;
                    }
                    
                    function labelFilter(){
                        $('#labelfield').html('Field/Filter yang tersedia<br/>' + getFilter());
                    }
                    
                    function labelDeskripsi(){
                        $('#labeldeskripsi').html( listws[valwstabel]['Deskripsi'] );
                    }
                    
                    function getFilter(tipe=''){
                        var ret = [], act_, tipe_;
                        act_  = (valwsact == 'GetCount') ? getPrio1() : act_ = valwsact;
                        tipe_ = (valwsact == 'Insert' || valwsact == 'Update' || valwsact == 'Delete') ? 'Request' : 'Response';
                        if (array_key_exists(act_, listact))
                        {
                            $.each(listact[act_][valwstabel], function(i, p) {
                                    if (p['Tipe'] == tipe_ && i != 'token')
                                    {
                                      pk = (p['primary'] == 'primary');
                                      if (tipe == '')
                                        ret.push((pk?'<strong>':'') + i + (pk?'*</strong>':''));
                                      else if (tipe == 'Update')
                                        ret.push(i + (pk?'*':''));
                                      else if (tipe == 'Insert' && !pk)
                                        ret.push(i);
                                      else if (tipe == 'Delete' && pk)
                                        ret.push(i);
                                    }
                                  });
                        }
                        return ret.join(', ');
                    }
                    
                    function generateRequest(){
                        var ret = [], form_open, form_close = '</form>';
                        if (!(valwstabel == 'Token' || valwstabel == 'Dictionary'))
                        {
                            if (array_key_exists(valwsact, listact) && !(valwsact == 'Insert' || valwsact == 'Update' || valwsact == 'Delete'))
                            {
                                form_open = form(false);
                                $.each(listact[valwsact][valwstabel], function(i, p) {
                                        if (p['Tipe'] == 'Request' && i != 'token')
                                        {
                                          ret.push(\"<div><div class='labelrequest'>\" + i + \"</div><div><input type='text' name='filter[\" + i + \"]' id='filter[\" + i + \"]' style='width:400px;' /></div></div>\");
                                        }
                                      });
                                $('#request').html( form_open + ret.join('<br/>') + '<button type=\'button\' class=\'btn btn-success\' name=\'btnImpor\' id=\'btnImpor\'> Impor Data CSV </button>' + form_close );
                                labelDeskripsi();
                                labelFilter();
                                $('#label').html('Isilah boks input di atas untuk menyaring hasil, kemudian tekan <strong>Import Data CSV</strong> untuk mengunduh data.');
                            }
                            else
                            {
                                form_open = form(true);
                                $.each(listact[valwsact][valwstabel], function(i, p) {
                                        pk = (p['primary'] == 'primary');
                                        if (p['Tipe'] == 'Request' && i != 'token')
                                        {
                                          ret.push(i + (pk?'*':''));
                                        }
                                      });
                                $('#request').html( form_open + '<div><input type=\'file\' id=\'filter[ceesvi]\' name=\'filter[ceesvi]\' accept=\'text/csv\'><button type=\'button\' class=\'btn btn-success\' name=\'btnEksekusi\' id=\'btnEksekusi\'> Eksekusi Sesuai dengan Data CSV </button></div>' + form_close );
                                $('#labelfield').html( '' );
                                $('#label').html('Tekan tombol <strong>Unduh Template CSV</strong> untuk mengunduh template. Insert, Update dan Delete memiliki template yang berbeda.<br/>Isi template. Unggah template dengan cara menekan tombol <strong>Eksekusi Sesuai dengan Data CSV</strong>');
                                return ret.join(', ');
                            }
                        }
                    }
                    
                    function form(isfile)
                    {
                        return '<form method=\'POST\' action=\"http".((array_key_exists('HTTPS', $_SERVER) ? (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") : false) ? "s" : "")."://".$_SERVER['HTTP_HOST']."/ws_pddikti/v2/impor_csv.php\" ' + ((isfile)?'enctype=\'multipart/form-data\'':'') + '>' + '<input type=\'hidden\' name=\'tabel\' id=\'tabel\' value=\''+valwstabel+'\' /><input type=\'hidden\' name=\'act\' id=\'act\' value=\''+valwsact+'\' />';
                    }
                    
                    function array_key_exists(key, array){
                        return (typeof array[key] !== 'undefined');
                    }
                    
                    function download_csv_file() {
                       var csv = getFilter((valwsact == 'Insert' || valwsact == 'Update' || valwsact == 'Delete')?valwsact:'');
                       var myWindow = window.open('','Test','width=300,height=300,scrollbars=1,resizable=1');
                       //display the created CSV data on the web browser 
                       myWindow.document.write(csv);
                      
                       var hiddenElement = document.createElement('a');
                       hiddenElement.href = 'data:text/csv;charset=utf-8,' + encodeURI(csv);
                       hiddenElement.target = '_blank';
                       
                       //provide the name for the CSV file to be downloaded
                       hiddenElement.download = valwsact + valwstabel + '.csv';
                       hiddenElement.click();
                    }
                    " 
                    ."\n</script>"
                    . "<style>.labelrequest{min-width:150px; max-width:200px;} p{width:100%;}</style>";
            session_write_close();
        }
        else
        {
            echo "tembolok_listWS belum tersedia. Persiapan WS gagal?";
        }
    }
}
