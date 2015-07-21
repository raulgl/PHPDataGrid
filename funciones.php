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
    $xml = realpath(dirname(__FILE__)).'\\informes\\PDF.xml';
    $result = NULL;
    if($ori) {
	$width = imagesx($ori);
	$height = imagesy($ori);
	$img =  imagecreatetruecolor($width, $height);
	if($img) {
            if(imagecopy($img, $ori, 0, 0, 0, 0, $width, $height)) {
		cabecera_informe($img,$xml);
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
        $xml = realpath(dirname(__FILE__)).'\\informes\\PDF.xml';
        $parser = simplexml_load_file($xml);
	foreach($parser as $current) {
            $dato = $current->getName();
            if(strcmp($dato, $name)==0){
                if(is_float($row[mysql_field_name($data,$i)]+0)){
                   $text = number_format($row[mysql_field_name($data,$i)],2);
                }
                else{
                    if($current["max"]){
                        $text = substr($row[mysql_field_name($data,$i)],0,intval($current["max"]));
                    }
                    else{
                        $text = $row[mysql_field_name($data,$i)];
                    }
                }
                print_pdf($img,$text,$current,$linea);
            }
        }
        $i++;
    }
    
    
}
function print_pdf($img,$text,$current,$linea){
    global $path_fonts;
    $x = $current["x"];
    $xml = realpath(dirname(__FILE__)).'\\informes\\PDF.xml';
    $parser = simplexml_load_file($xml);
    foreach($parser as $cur) {
        $dato = $cur->getName();
        if(strcmp($dato, "offsetres")==0){
            $y = $cur["y"];
            $tlinea = $cur["linea"];
            $y = $linea*$tlinea+$y;
        }
    }
    $dato = $current->getName();
    $dir = $current["dir"];
    $align = $current["align"];
    $font = $current["fonttype"];
    $fsize = $current["fontsize"];
    $R = intval($current["r"]);
    $G = intval($current["g"]);
    $B = intval($current["b"]);	
    if(!$R || strlen($R) == 0) $R = 0;
    if(!$G || strlen($G) == 0) $G = 0;
    if(!$B || strlen($B) == 0) $B = 0;
    $color = imagecolorallocate($img, $R, $G, $B);
    
    $text = utf8_encode($text);
    $rfont = $path_fonts.$font;
    $brect = imagettfbbox((double)$fsize, (double)$dir, $rfont, $text);
    $res = imagettftext($img, (double)$fsize, (double)$dir, (double)$x, (double)$y, $color, $rfont, $text);				
    
}
    

function cabecera_informe($img,$xml){
		global $path_fonts;
		$parser = simplexml_load_file($xml);
		$y = 200;
		foreach($parser as $current) {
			$dato = $current->getName();
			if($dato) {
				$x = $current["x"];
				
				$dir = $current["dir"];
				$texto = $current["texto"];
				$align = $current["align"];
				$font = $current["fonttype"];
				$fsize = $current["fontsize"];
				$reduccion = $current["reduccion"];
				$R = $current["r"];
				$G = $current["g"];
				$B = $current["b"];			
				$text = '';

				if(!$R || strlen($R) == 0) $R = 0;
				if(!$G || strlen($G) == 0) $G = 0;
				if(!$B || strlen($B) == 0) $B = 0;
				if(!$texto || strlen($texto) == 0) $texto = "#";
			
				switch($dato)
				{
					case 'estatico':
						$text = $texto;
						break;	
					case 'offset':
						$y = $current["y"];
						break;						
				}
			
				if(strlen($text)>0 && strlen($x)>0 && strlen($y)>0 && strlen($dir)>0 && strlen($fsize)>0 && strlen($font)>0) {
					$color = imagecolorallocate($img, $R, $G, $B);
					$text = utf8_encode($text);
					$rfont = $path_fonts.$font;
					$brect = imagettfbbox((double)$fsize, (double)$dir, $rfont, $text);
				
					switch($align)
					{
						case 'right': //alineado horizontal a la derecha
							$x-= ($brect[2]>$brect[4]) ? $brect[2] : $brect[4];
							break;
						case 'bottom': //alineado vertical abajo
							$y-= ($brect[1]>$brect[3]) ? $brect[1] : $brect[3];
							break;
						case 'center': //centrado
							$x-= ($brect[2]>$brect[4]) ? $brect[2]/2 : $brect[4]/2;
							$y-= ($brect[1]>$brect[3]) ? $brect[1]/2 : $brect[3]/2;
							break;				
					}
					$res = imagettftext($img, (double)$fsize, (double)$dir, (double)$x, (double)$y, $color, $rfont, $text);
				}
			}
		}
	}


?>
