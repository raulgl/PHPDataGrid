<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Framework que se encarga de hacer los resumenes según los campos de la BD puestos en el config.json
 *
 * @author ics_raul
 */
require_once 'groupby.php';
class SQLFrame {
    var $groups;//array donde estan todos los groupby
    var $tipo;//tipo de informe que se va a generar:"html","csv","pdf"
    static $pos_file=0;//posicion actual dentro de la linea del informe
    static $json;//objeto json parseado
    /**
     * constructor de la clase, incializa el tipo de formato en que se va a sacar la consulta.
     * Crea un objeto groupby para cada groupby del json y los guarda en un array. Cada groupby
     * tiene un array de objetos sumatorio, uno para cada sumatorio o contador del json.Asi el json pasa a ser
     * una estructura de arrays y objetos
     */
    function SQLFrame($tipo){
        /*parsea el json donde se configura las posiciones del informe en pdf*/
        $jsonfile = file_get_contents(realpath(dirname(__FILE__)).'\\informes\\PDF.json'); 
        SQLFrame::$json = json_decode($jsonfile,true);
        $this->tipo=$tipo;
        $this->groups = array();
        //$xml = file_get_contents('PHPDataGrid\config.xml'); 
        //$xml = file_get_contents('C:\Archivos de programa\Apache Software Foundation\Apache2.2\htdocs\SQLFrame\PHPDataGrid\config.xml'); 
        //$DOM = new DOMDocument('1.0', 'utf-8');
        //$DOM->loadXML($xml);*/
        //$groupsby = $DOM->getElementsByTagName('groupby'); 
        //$xml = simplexml_load_file("config.xml");//new SimpleXMLElement("config.xml");
        $jsonfile = file_get_contents('PHPDataGrid\config.json'); 
        $json = json_decode($jsonfile,true);
        $groupsby = $json['groupby'];
        foreach ($groupsby as $gby) {
            $group = new groupby($gby['nombre'], $gby['posicion'], $gby['class'], $gby['total']); 
            array_push($this->groups, $group);
        }
        $sumatorios = $json['sumatorio'];
        //$sumatorios = $DOM->getElementsByTagName('sumatorio'); 
        foreach ($sumatorios as $suma) {
            foreach($this->groups as $group){
                $group->add($suma['nombre'],$suma['posicion'],false);                
            }  
        }
        $contadores = $json['$contador'];
        //$contadores = $DOM->getElementsByTagName('contador');
        foreach ($contadores as $cont) {
            foreach($this->groups as $group){
                $group->add($cont['nombre'],$cont->getAttribute['posicion'],true);                
            }  
        }
    }
    /*
     * funcion que se llama cada vez que se añade una fila de la BD al listado.
     * Se le pasa la fila que se ha listado en el informe
     * Basicamente mira en orden si algun dato  de los campos que estan en los group by es diferente del anterior y por lo tanto hay que
     * hacer el resumen de ese groupby. Si esto es asi guardamos los groupby en un array para luego imprimirlo este groupby y los que
     * tienen por debajo y decimos que el group by anterior es ese campo nuevo para el siguiente add.Sino con los datos de la BD actualiamos 
     * los datos de cada groupby.
     */
    public function add($row){
        $i=0;
        $reseteado=false;
        $groupsprintar = array();
        while($i<count($this->groups) && !$reseteado){
            $j=0;
            $encontrado=false;            
            while($j<count($row) && !$encontrado){
                $group = $this->groups[$i];   
                if($group->mismo(mysql_field_name(DataGrid::$data,$j))){
                    $encontrado=true;                    
                    if($group->is_reset($row[mysql_field_name(DataGrid::$data,$j)])){
                        $reseteado=true;
                        array_push($groupsprintar,$group);
                        $group->set_actual($row[mysql_field_name(DataGrid::$data,$j)]);
                    }
                    else{
                        $group->sum($row);
                    }
                }
                $j++;
            }
            $i++;
        }
        $group = $this->groups[0];
        $group->sum($row);
        while($i<count($this->groups)){
            $group = $this->groups[$i];
            array_push($groupsprintar,$group);            
            $encontrado=false;
            while($j<count($row) && !$encontrado){
                $group = $this->groups[$i];
                if($group->mismo(mysql_field_name(DataGrid::$data,$j))){
                    $encontrado=true;
                    $group->set_actual($row[mysql_field_name(DataGrid::$data,$j)]);
                }
                $j++;
            }
            $i++;
        }
        $this->printar($groupsprintar,0,$row);
    }
    /**
     * funcion que printa los groupsby que hay despues de la posicion i, printe,resetee y sume la row al groupby de la posicion i
     * Esta es una llamada recursiva para que los totales por group by se impriman de abajo a arriba segun el orden del json y no de
     * arriba a abajo.
     * Esta funcion tiene los siguientes parametros:
     * groups: array de groupsby
     * i: posicion actual del groupby
     * row: row que viene de la BD
     */
    function printar($groups,$i,$row=NULL){
        if($i<count($groups)){
            $i++;
            $this->printar($groups,$i,$row);
            $i--;
            $group = $groups[$i];
            $group->printar($this->tipo);
            if(strcmp($this->tipo,"pdf")==0){
                DataGrid::$pdf->comprobar_tamaño();
                DataGrid::$pdf->add_linea();
            }
            $group->reset();
            $group->sum($row);
        }
    }
    /**
     * imprime todos los group by.Imprime los ultimos groupby y el total
     */
    function printar_todos(){
        $this->printar($this->groups,0,NULL);
    }
    
}
