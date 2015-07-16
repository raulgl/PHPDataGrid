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

	//mail
	
	
	if(file_exists($config_file)) {
		$ini_array = parse_ini_file($config_file, true);
		
		//bbdd
		$dababase_ip = get_ini($ini_array, "bbdd", "ip");
		$dababase_user = get_ini($ini_array, "bbdd", "user");
		$dababase_pswd = get_ini($ini_array, "bbdd", "pswd");
		$dababase_name = get_ini($ini_array, "bbdd", "name");
		
				
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



?>
