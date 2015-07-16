<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of groupby
 *
 * @author ics_raul
 */
require_once 'Sumatorio.php';
require_once 'SQLFrame.php';
class groupby {
    var $nombre;
    var $posicion;
    var $sumatorios;
    var $actual;
    var $class;
    function groupby($nom,$pos,$class){
        $this->nombre=trim($nom);
        $this->posicion=$pos;
        $this->sumatorios = array();
        $this->class = $class;
    }
    public function add($nombre,$posicion,$contador){
        $suma = new Sumatorio($nombre,$posicion,$contador);
        array_push($this->sumatorios, $suma);
    }
    public function reset(){
        foreach($this->sumatorios as $suma){
            $suma->reset();
        }
    }
    public function set_actual($act){
        $this->actual = $act;
    }
    public function mismo($nom){
        return strcmp($this->nombre,$nom)==0;
    }
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
    public function printar(){
        echo "<tr class='$this->class'>";
        while(SQLFrame::$pos_file<$this->posicion){
            echo "<td>&nbsp;</td>";
            SQLFrame::$pos_file++;
        }
        echo "<td>TOTAL</td>";
        SQLFrame::$pos_file++;
        foreach($this->sumatorios as $suma){
            $suma->printar();
        } 
        echo "</tr>";
        SQLFrame::$pos_file=0;
    }
    public function sum($row,$result){
        $i=0;
        $encontrado=false;
        foreach($this->sumatorios as $sum){
            $sum->same($row,$result);          
        }       
    }
}
