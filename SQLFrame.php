<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of newPHPClass
 *
 * @author ics_raul
 */
require_once 'groupby.php';
class SQLFrame {
    var $groups;
    var $tipo;
    static $pos_file=0;
    function SQLFrame($tipo){
        $this->tipo=$tipo;
        $this->groups = array();
        $xml = file_get_contents('PHPDataGrid\config.xml'); 
        //$xml = file_get_contents('C:\Archivos de programa\Apache Software Foundation\Apache2.2\htdocs\SQLFrame\PHPDataGrid\config.xml'); 
        $DOM = new DOMDocument('1.0', 'utf-8');
        $DOM->loadXML($xml);
        $groupsby = $DOM->getElementsByTagName('groupby'); 
        //$xml = simplexml_load_file("config.xml");//new SimpleXMLElement("config.xml");
        foreach ($groupsby as $gby) {
            $group = new groupby($gby->nodeValue, $gby->getAttribute('posicion'), $gby->getAttribute('class'), $gby->getAttribute('total')); 
            array_push($this->groups, $group);
        }
        $sumatorios = $DOM->getElementsByTagName('sumatorio'); 
        foreach ($sumatorios as $suma) {
            foreach($this->groups as $group){
                $group->add($suma->nodeValue,$suma->getAttribute('posicion'),false);                
            }  
        }
        $contadores = $DOM->getElementsByTagName('contador');
        foreach ($contadores as $cont) {
            foreach($this->groups as $group){
                $group->add($cont->nodeValue,$cont->getAttribute('posicion'),true);                
            }  
        }
    }
    public function add($row,$result,&$array_gif,&$linea,&$img,&$pagina,$rand){
        $i=0;
        $reseteado=false;
        $groupsprintar = array();
        while($i<count($this->groups) && !$reseteado){
            $j=0;
            $encontrado=false;            
            while($j<count($row) && !$encontrado){
                $group = $this->groups[$i];   
                if($group->mismo(mysql_field_name($result,$j))){
                    $encontrado=true;                    
                    if($group->is_reset($row[mysql_field_name($result,$j)])){
                        $reseteado=true;
                        array_push($groupsprintar,$group);
                        $group->set_actual($row[mysql_field_name($result,$j)]);
                    }
                    else{
                        $group->sum($row,$result);
                    }
                }
                $j++;
            }
            $i++;
        }
        $group = $this->groups[0];
        $group->sum($row,$result);
        while($i<count($this->groups)){
            $group = $this->groups[$i];
            array_push($groupsprintar,$group);            
            $encontrado=false;
            while($j<count($row) && !$encontrado){
                $group = $this->groups[$i];
                if($group->mismo(mysql_field_name($result,$j))){
                    $encontrado=true;
                    $group->set_actual($row[mysql_field_name($result,$j)]);
                }
                $j++;
            }
            $i++;
        }
        $this->printar($groupsprintar,0,$row,$result,$array_gif,$linea,$img,$pagina,$rand);
    }
    function printar($groups,$i,$row=NULL,$result=NULL,$array_gif,&$linea,&$img,&$pagina,$rand){
        if($i<count($groups)){
            $i++;
            $this->printar($groups,$i,$row,$result,$array_gif,$linea,$img,$pagina,$rand);
            $i--;
            $group = $groups[$i];
            $group->printar($this->tipo,$linea,$img);
            comprobar_tamaño($img,$linea,$array_gif,$pagina,$rand);
            $linea++;
            $group->reset();
            $group->sum($row,$result);
        }
    }
    function printar_todos(&$array_gif,&$linea,&$img,&$pagina,$rand){
        $this->printar($this->groups,0,NULL,NULL,$array_gif,$linea,$img,$pagina,$rand);
    }
    
}
