<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Clase para crear un PDF
 *
 * @author ics_raul
 */
class PDF {
    var $rand;//nombre del pdf
    var $img;//gif que se esta rellenado actualmente
    var $linea;//linea en que se esta actualmente
    var $pagina;//numero de pagina que se está rellenando actualmente
    var $array_gif;//array de gif rellenados
    /**
     * constructor que inicializa el pdf
     * @param type $rand: nombre del pdf
     */
    function PDF($rand){
        $this->pagina=0;
        $this->rand=$rand;
        $this->img=$this->crear_gif();
        $this->linea=0;
        $this->array_gif= array();
        
    }
    /**
     * indica que se ha añadido una nueva linea al pdf. Se tiene que llamar cada vez que se rellena un linea, para ir a la siguiente
     */
    function add_linea(){
        $this->linea++;
    }
    /**
     * crea un gif a partir del gif dado añadiendo la cabecera
     */
    function crear_gif(){
        global $path_fonts;    
        $gif = realpath(dirname(__FILE__)).'\\informes\\PDF.GIF';
        $result_img = realpath(dirname(__FILE__)).'\\temp\\'.$this->rand.$this->pagina.'.GIF';
        $ori = imagecreatefromgif($gif);
        $result = NULL;
        if($ori) {
            $width = imagesx($ori);
            $height = imagesy($ori);
            $img =  imagecreatetruecolor($width, $height);
            if($img) {
                if(imagecopy($img, $ori, 0, 0, 0, 0, $width, $height)) {
                    $this->cabecera_informe($img);
                }
            }
            imagedestroy($ori);			
        }
        return $img;
    }
    /**
     * mira si el numero de lineas actual sobrepasa el permitido y si es asi añade el gif actual al array de gif
     * , se crea un nuevo gif y se inicializa linea a 0 y se incrementa pagina
     */
    function comprobar_tamaño(){
                global $n_tickets_pag;
		if($this->linea>=$n_tickets_pag){
			$this->añadir_gif();
			$this->pagina++;
			$this->linea=0;
                        $this->img = $this->crear_gif();		
		}
		
    }
    /**
     * añade el gif actual al array de gifs
     */
    function añadir_gif(){
        $result_img = realpath(dirname(__FILE__)).'\\temp\\'.$this->rand.$this->pagina.'.GIF';
        if(imagegif($this->img, $result_img))
                $result_ok = $result_img;
        array_push($this->array_gif, $result_ok);
        imagedestroy($this->img);
    }
    /**
     * printa la fila de la BD
     * @param type $row:fila de la BD
     */
    function add_row($row){
        $i=0;
        while($i<mysql_num_fields(DataGrid::$data)){
            $name = mysql_field_name(DataGrid::$data,$i);
            /*$xml = realpath(dirname(__FILE__)).'\\informes\\PDF.xml';
            $parser = simplexml_load_file($xml);*/
        
            $json = SQLFrame::$json;
            if(isset ($json[$name])){
                $current=$json[$name];
                if(is_float($row[mysql_field_name(DataGrid::$data,$i)]+0)){
                    $text = number_format($row[mysql_field_name(DataGrid::$data,$i)],2);
                }
                else{
                    if(isset($current["max"])){
                        $text = substr($row[mysql_field_name(DataGrid::$data,$i)],0,intval($current["max"]));
                    }
                    else{
                        $text = $row[mysql_field_name(DataGrid::$data,$i)];
                    }
                }
                $this->print_pdf($text,$current);
            
            }
            $i++;
        }
    }
    /**
     * printa un texto determinado
     * @global type $path_fonts: fuentes para printar el texto
     * @param type $text:texto a printar
     * @param type $current: atributos del texto para printarlo
     */
    function print_pdf($text,$current){
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
        $y = $this->linea*$tlinea+$y;
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
        $color = imagecolorallocate($this->img, $R, $G, $B);    
        $text = utf8_encode($text);
        $rfont = $path_fonts.$font;
        $brect = imagettfbbox((double)$fsize, (double)$dir, $rfont, $text);
        $res = imagettftext($this->img, (double)$fsize, (double)$dir, (double)$x, (double)$y, $color, $rfont, $text);				
    
    }
    
    /**
     * añade la cabecera en un gif determinado
     * @global type $path_fonts: fuentes para printar el texto
     * @param type $img: imagen donde se va a poner la cabecera
     */
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
        /**
         * crea el pdf a partir del array de gif
         * @return type:nombre que tendrá el pdf
         */
        function crear_PDF(){
            $pdf_name = $this->rand.".pdf";
            $pdf = realpath(dirname(__FILE__)).'\\pdf\\'.$pdf_name;
            $gif = realpath(dirname(__FILE__)).'\\informes\PDF.GIF';
            $dimensions = getimagesize($gif);
            $opdf=new FPDF('L','pt', array($dimensions[1],$dimensions[0]));
            $opdf->SetMargins(0,0,0);
            $opdf->AliasNbPages();
            $opdf->SetDrawColor(255, 255, 255);
            $opdf->SetFillColor(255, 255, 255);		
            foreach($this->array_gif as $result_ok){
                $opdf->Image($result_ok);
            }
            $opdf->Output($pdf);
            return $this->rand;
        }
    }
