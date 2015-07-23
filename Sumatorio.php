<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Sumatorio
 *
 * @author ics_raul
 */
require_once 'SQLFrame.php';
class Sumatorio {
    var $nombre;
    var $posicion;
    var $escontador;
    var $total;
    function Sumatorio($nom,$pos,$escont){
        $this->nombre=trim($nom);
        $this->posicion=$pos;
        $this->escontador=$escont;
        $this->total=0;
    }
    public function add($num){
        if($this->escontador){
            $this->total++;
        }
        else{
            $this->total+=$num;
        }
    }
    public function reset(){
        $this->total=0;
    }
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
    public function printar_pdf(){
        /*$xml = realpath(dirname(__FILE__)).'\\informes\\PDF.xml';
        $parser = simplexml_load_file($xml);*/
         
        $json = SQLFrame::$json;
        $cur=$json[$this->nombre];
        if(is_float($this->total+0)){
            $text = number_format($this->total,2);
        }
        else{
            $text=$this->total;
        }
        DataGrid::$pdf->print_pdf($text,$cur);
    }
    public function printar_html(){
        while(SQLFrame::$pos_file<$this->posicion){
            echo "<td>&nbsp;</td>";
            SQLFrame::$pos_file++;
        }
        if(is_float($this->total)==0){
            echo "<td>".number_format($this->total,2)."</td>";
        }
        else{
            echo "<td>$this->total</td>";
        }
        SQLFrame::$pos_file++;
    }
    public function printar_csv(){
        while(SQLFrame::$pos_file<$this->posicion){
            echo ";";
            SQLFrame::$pos_file++;
        }
        if(is_float($this->total)){
            echo number_format($this->total,2).";";
        }
        else{
            echo $this->total.";";
        }
        SQLFrame::$pos_file++;
    }
    public function same($row){
        $i=0;
        $encontrado=false;
        while($i<count($row) && !$encontrado){
           if(strcmp(mysql_field_name(DataGrid::$data,$i),$this->nombre)==0){
               $encontrado=true;
               $this->add($row[mysql_field_name(DataGrid::$data,$i)]);
           }
           $i++;
        }
        return $encontrado;
        
    }
}
