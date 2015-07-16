<?php

function init($variable, $def = '') {
	$dato = $def;
	if(isset($_REQUEST[$variable])) $dato=$_REQUEST[$variable];
	return $dato;
}

$path_temp = realpath(dirname(__FILE__)).'\\temp\\';
$path_logs = realpath(dirname(__FILE__)).'\\logs\\';
$path_html = realpath(dirname(__FILE__)).'\\html\\';
$path_fonts = realpath(dirname(__FILE__)).'\\fonts\\';
$path_tickets = realpath(dirname(__FILE__)).'\\tickets\\';

$config_file = realpath(dirname(__FILE__)).'\\config.ini';
$tickets_file = realpath(dirname(__FILE__)).'\\tickets.ini';

//conexion
$dababase_ip = "";
$dababase_user = "";
$dababase_pswd = "";
$dababase_name = "";

//mail
$send_on_test   = false;
$mail_test      = "";
$mail_subject   = "";
$mail_host 	 	= "";
$mail_port	 	= 25;
$mail_user 		= "";
$mail_pswd 		= "";
$mail_from 		= "";

//general
$filtro = "";
$entidad = "";
$formato_pdf = false;
$recintos_path = "";
$cuadro_pos_lado = 20;
$color_cuadroR = 0;
$color_cuadroG = 0;
$color_cuadroB = 0;
$qr_link = "";

class cEntradas {}

?>