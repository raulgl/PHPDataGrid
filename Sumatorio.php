<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Representa el sumatorio de un campo de la BD que esta en el json como sumatorio o contador
 *
 * @author ics_raul
 */
require_once 'SQLFrame.php';
class Sumatorio {
    var $nombre; //nombre del campo de la BD y del campo del json
    var $posicion;//en que posicion se printara en el csv o en el html
    var $escontador;// es contador o sumador
    var $total;//total de este sumatorio
    /**
     * constructor que inicializa los atributos del objeto
     * @param type $nom: nombre del campo de la BD y del campo del json
     * @param type $pos:en que posicion se printara en el csv o en el html
     * @param type $escont:es contador o sumador
     */
    function Sumatorio($nom,$pos,$escont){
        $this->nombre=trim($nom);
        $this->posicion=$pos;
        $this->escontador=$escont;
        $this->total=0;
    }
    /**
     * suma num al total si el sumatorio es sumatorio de verdad o suma 1 al total si es contador
     * @param type $num: numero a sumar 
     */
    public function add($num){
        if($this->escontador){
            $this->total++;
        }
        else{
            $this->total+=$num;
        }
    }
    /**
     * inicializa el total a 0;
     */
    public function reset(){
        $this->total=0;
    }
    /**
     * printa el sumatorio
     * @param type $tipo: tipo en que se va a printar:"csv","html o "pdf"
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
     * printa el sumatorio en pdf
     */
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
    /**
     * printa el sumatorio en html
     */
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
    /**
     * printa el sumatorio en csv
     */
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
    /**
     * mira campo a campo de la BD qual de ellos corresponde al campo del sumatorio y cuando lo encuentra le hace un add.
     * @param type $row: fila de la BD
     * @return boolean
     */
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
