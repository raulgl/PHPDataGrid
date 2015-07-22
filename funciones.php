<?php
include("variables.php");
//include("Pago.php")
//include("ReservaResumen.php");
//include("ReservaDetalle.php");
//include("InvitacionResumen.php");
//include("InvitacionDetalle.php");
//require_once("mailsmtp.php");
require_once("fpdf.php");

function get_ini($parser, $tag, $name) {
	$value = "";
	if(isset($parser[$tag][$name])) {
		$value = $parser[$tag][$name];
	}
	return $value;
}
function crear_PDF($array_gif,$rand){
    $pdf_name = $rand.".pdf";
    $pdf = realpath(dirname(__FILE__)).'\\pdf\\'.$pdf_name;
    $gif = realpath(dirname(__FILE__)).'\\informes\PDF.GIF';
    $dimensions = getimagesize($gif);
    $opdf=new FPDF('L','pt', array($dimensions[1],$dimensions[0]));
    $opdf->SetMargins(0,0,0);
    $opdf->AliasNbPages();
    $opdf->SetDrawColor(255, 255, 255);
    $opdf->SetFillColor(255, 255, 255);		
    foreach($array_gif as $result_ok){
	$opdf->Image($result_ok);
    }
    $opdf->Output($pdf);
    return $rand;
}
function trace($data) {
	global $path_logs;
	
	$f = fopen($path_logs.date("Ymd").'.html', "a");
	if($f) {
		fwrite($f, date("H:i:s").' - '.$data."<br>");
		fclose($f);
	}
}

function cargar_configuracion() {
	global $config_file;
	//conexion
	global $dababase_ip;
	global $dababase_user;
	global $dababase_pswd;
	global $dababase_name;
        global $n_tickets_pag;

	//mail
	
	
	if(file_exists($config_file)) {
		//$ini_array = parse_ini_file($config_file, true);
		$jsonfile = file_get_contents('PHPDataGrid\config.json'); 
                $json = json_decode($jsonfile,true);
		//bbdd
		//$dababase_ip = get_ini($ini_array, "bbdd", "ip");
                $dababase_ip = $json["bbdd"]["ip"];
		//$dababase_user = get_ini($ini_array, "bbdd", "user");
                $dababase_user = $json["bbdd"]["user"];
		//$dababase_pswd = get_ini($ini_array, "bbdd", "pswd");
                $dababase_pswd = $json["bbdd"]["pswd"];
		//$dababase_name = get_ini($ini_array, "bbdd", "name");
                $dababase_name = $json["bbdd"]["name"];
                $n_tickets_pag = $json["n_tickets_pag"];
		
				
	}	
}

function conectar() {
	global $dababase_ip;
	global $dababase_user;
	global $dababase_pswd;
	global $dababase_name;

	$p_con = mysql_connect($dababase_ip, $dababase_user, $dababase_pswd) or die (mysql_error());
	mysql_query("use $dababase_name", $p_con) or die(mysql_error());
	return $p_con;
}
function crear_gif($pagina,$rand){
    global $path_fonts;    
    $gif = realpath(dirname(__FILE__)).'\\informes\\PDF.GIF';
    $result_img = realpath(dirname(__FILE__)).'\\temp\\'.$rand.$pagina.'.GIF';
    $ori = imagecreatefromgif($gif);
    $result = NULL;
    if($ori) {
	$width = imagesx($ori);
	$height = imagesy($ori);
	$img =  imagecreatetruecolor($width, $height);
	if($img) {
            if(imagecopy($img, $ori, 0, 0, 0, 0, $width, $height)) {
		cabecera_informe($img);
            }
	}
        imagedestroy($ori);			
    }
    return $img;
}
function comprobar_tamaño(&$img,&$linea,&$array_gif,&$pagina,$rand){
                global $n_tickets_pag;
		if($linea>=$n_tickets_pag){
			$result_img = realpath(dirname(__FILE__)).'\\temp\\'.$rand.$pagina.'.GIF';	
                        if($img) {
                            if(imagegif($img, $result_img)){
				$result_ok = $result_img;
				array_push($array_gif,$result_ok);
                            }
                            imagedestroy($img);
                        }
			$pagina++;
			$linea=0;
                        $img = crear_gif($pagina,$rand);		
		}
		
}
function add_row($img,$row,$data,$linea){
    $i=0;
    while($i<mysql_num_fields($data)){
        $name = mysql_field_name($data,$i);
        /*$xml = realpath(dirname(__FILE__)).'\\informes\\PDF.xml';
        $parser = simplexml_load_file($xml);*/
        
        $json = SQLFrame::$json;
        if(isset ($json[$name])){
            $current=$json[$name];
            if(is_float($row[mysql_field_name($data,$i)]+0)){
                $text = number_format($row[mysql_field_name($data,$i)],2);
            }
            else{
                if(isset($current["max"])){
                    $text = substr($row[mysql_field_name($data,$i)],0,intval($current["max"]));
                }
                else{
                    $text = $row[mysql_field_name($data,$i)];
                }
            }
            print_pdf($img,$text,$current,$linea);
            
        }
        $i++;
    }
    
    
}
function print_pdf($img,$text,$current,$linea){
    global $path_fonts;
    $x = $current["x"];
    /*$xml = realpath(dirname(__FILE__)).'\\informes\\PDF.xml';
    $parser = simplexml_load_file($xml);
    foreach($parser as $cur) {
        $dato = $cur->getName();
        if(strcmp($dato, "offsetres")==0){
            $y = $cur["y"];
            $tlinea = $cur["linea"];
            $y = $linea*$tlinea+$y;
        }
    }*/
   
    $json = SQLFrame::$json;
    $y = $json["offsetres"]["y"];
    $tlinea = $json["offsetres"]["linea"];
    $y = $linea*$tlinea+$y;
    if(isset($current["dir"])){
        $dir = $current["dir"];
    }
    else{
        $dir=0;
    }
    $font = $current["fonttype"];
    $fsize = $current["fontsize"];
    if(isset ($current["r"])){
        $R = intval($current["r"]);
    }
    else{
        $R=0;
    }
    if(isset ($current["g"])){
        $G = intval($current["g"]);
    }
    else{
        $G=0;
    }
    if(isset ($current["b"])){
        $B = intval($current["b"]);
        
    }
    else{
        $B=0;
    }
    if(!$R || strlen($R) == 0) $R = 0;
    if(!$G || strlen($G) == 0) $G = 0;
    if(!$B || strlen($B) == 0) $B = 0;
    $color = imagecolorallocate($img, $R, $G, $B);    
    $text = utf8_encode($text);
    $rfont = $path_fonts.$font;
    $brect = imagettfbbox((double)$fsize, (double)$dir, $rfont, $text);
    $res = imagettftext($img, (double)$fsize, (double)$dir, (double)$x, (double)$y, $color, $rfont, $text);				
    
}
    

function cabecera_informe($img){
		global $path_fonts;
		//$parser = simplexml_load_file($xml);
		$y = 200;
                
                $json = SQLFrame::$json;
                $y = $json["offset"]["y"];
		foreach($json["estatico"] as $current) {
                    $x = $current["x"];
                    $dir = 0;
                    $texto = $current["texto"];
                    $font = $current["fonttype"];
                    $fsize = $current["fontsize"];
                    if(isset ($current["r"])){
                        $R = intval($current["r"]);
                    }
                    else{
                        $R=0;
                    }
                    if(isset ($current["g"])){
                        $G = intval($current["g"]);
                    }
                    else{
                        $G=0;
                    }
                    if(isset ($current["b"])){
                        $B = intval($current["b"]);
        
                    }
                    else{
                        $B=0;
                    }		
                    $text = '';
                    if(!$R || strlen($R) == 0) $R = 0;
                    if(!$G || strlen($G) == 0) $G = 0;
                    if(!$B || strlen($B) == 0) $B = 0;
                    if(!$texto || strlen($texto) == 0) $texto = "#";
                    $text = $texto;
                    if(strlen($text)>0 && strlen($x)>0 && strlen($y)>0 && strlen($dir)>0 && strlen($fsize)>0 && strlen($font)>0) {
                        $color = imagecolorallocate($img, $R, $G, $B);
                        $text = utf8_encode($text);
                        $rfont = $path_fonts.$font;
                        $brect = imagettfbbox((double)$fsize, (double)$dir, $rfont, $text);
                        $res = imagettftext($img, (double)$fsize, (double)$dir, (double)$x, (double)$y, $color, $rfont, $text);
			
                    }
		}
                
	}


?>
