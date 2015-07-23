<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/** 
 * Representa el conjunto de sumatorios para un groupby del json concreto
 * @author ics_raul
 */
require_once 'Sumatorio.php';
require_once 'SQLFrame.php';
/**
 * 
 */
class groupby {
    var $nombre;//nombre del group by en el json
    var $posicion;//posicion en que se escribe dentro de una linea en el html o en el csv
    var $sumatorios;//array de sumatorio para ese group by
    var $actual;//actual dato que tiene el campo del group by en la BD
    var $class;//class html a la hora de printar el group by 
    var $total;//atributo del json para ver donde se imprime en el pdf
    /**
     * constructor: inicializa los datos
     * @param type $nom: nombre del group by en el json
     * @param type $pos: posicion en que se escribe dentro de una linea en el html o en el csv
     * @param type $class: html a la hora de printar el group by 
     * @param type $total: atributo del json para ver donde se imprime en el pdf
     */
    function groupby($nom,$pos,$class='',$total=''){
        $this->nombre=trim($nom);
        $this->posicion=$pos;
        $this->sumatorios = array();
        $this->class = $class;
        $this->total = $total;
    }
    /**
     * crea un sumador y lo añade al array de sumadores
     * @param type $nombre: nombre del nuevo sumador
     * @param type $posicion:posicion que ocupa dentro del csv o del html
     * @param type $contador: contador o sumador
     */
    public function add($nombre,$posicion,$contador){
        $suma = new Sumatorio($nombre,$posicion,$contador);
        array_push($this->sumatorios, $suma);
    }
    /**
     * resetea los sumadores y los pone a 0
     */
    public function reset(){
        foreach($this->sumatorios as $suma){
            $suma->reset();
        }
    }
    /**
     * pone como dato actual act
     * @param type $act: nuevo dato actual
     */
    public function set_actual($act){
        $this->actual = $act;
    }
    /**
     * 
     * @param type $nom: nombre a comparar
     * @return boolean: devuelve true si nom es igual que actual
     */
    public function mismo($nom){
        return strcmp($this->nombre,$nom)==0;
    }
    /**
     * Si no se ha inicializado actual sera valor y se devuelve true si valor es igual que actual
     * @param type $valor: valor con que se va a comparar actual
     * @return boolean
     */
    public function is_reset($valor){
        if(!isset($this->actual)){
            $this->actual = $valor;
            return false;
        }
        if($this->actual!=$valor){
            return true;
        }
        else{
            return false;
        }
    }
    /**
     * printa el groupby
     * @param type $tipo: tipo en que se va a printar el formulario:"html","csv","pdf"
     */
    public function printar($tipo){
        if(strcmp($tipo, "html")==0){
            $this->printar_html();
        }
        else if(strcmp($tipo, "csv")==0){
           $this->printar_csv(); 
        }
        else{
            $this->printar_pdf();
            
        }
    }
    /**
     * printa el groupby en pdf, para ello busca en el json el objeto que se llama igual que el y coge los atributos
     */
    public function printar_pdf(){
        /*$xml = realpath(dirname(__FILE__)).'\\informes\\PDF.xml';
        $parser = simplexml_load_file($xml);*/
        $json = SQLFrame::$json;
        $cur=$json[$this->total];
        $text=$cur["texto"];
        DataGrid::$pdf->print_pdf($text,$cur);
        foreach($this->sumatorios as $suma){
            $suma->printar("pdf");
        } 
        
    }
    /**
     * printa el groupby en html
     */
    public function printar_html(){
        echo "<tr class='$this->class'>";
        while(SQLFrame::$pos_file<$this->posicion){
            echo "<td>&nbsp;</td>";
            SQLFrame::$pos_file++;
        }
        echo "<td>TOTAL</td>";
        SQLFrame::$pos_file++;
        foreach($this->sumatorios as $suma){
            $suma->printar("html");
        } 
        echo "</tr>";
        SQLFrame::$pos_file=0;
    }
    /**
     * printa el groupby en csv
     */
    public function printar_csv(){
        while(SQLFrame::$pos_file<$this->posicion){
            echo ";";
            SQLFrame::$pos_file++;
        }
        echo "TOTAL;";
        SQLFrame::$pos_file++;
        foreach($this->sumatorios as $suma){
            $suma->printar("csv");
        } 
        echo "\n";
        SQLFrame::$pos_file=0;
    }
    /**
     * suma o añade a cada sumatorio del groupby lo que hay en la fila
     */
    public function sum($row){
        $i=0;
        $encontrado=false;
        foreach($this->sumatorios as $sum){
            $sum->same($row);          
        }       
    }
}
