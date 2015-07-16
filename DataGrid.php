<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DataGrid
 *
 * @author ics_raul
 */
require_once 'SQLFrame.php';
require_once 'funciones.php';
class DataGrid {
    
    public static function printar($query){
        cargar_configuracion();
        $sqlframe = new SQLFrame();
        $con = conectar();
        $data = mysql_query("$query", $con) or die(mysql_error());
        echo "<table class='tabla'>";
        $i=0;
        echo "<tr class='cabecera'>";
        while($i<mysql_num_fields($data)){
            echo "<td>".mysql_field_name($data,$i)."</td>";
            $i++;
        }
        echo "</tr>";
        
        while($row = mysql_fetch_assoc($data)){ 
            
            $sqlframe->add($row, $data);
            echo "<tr class='fila'>";
            $i=0;
            while($i<mysql_num_fields($data)){
               
                if(is_float($row[mysql_field_name($data,$i)]+0)){
                    echo "<td>".number_format($row[mysql_field_name($data,$i)],2)."</td>";
                }
                else{
                   echo "<td>".$row[mysql_field_name($data,$i)]."</td>"; 
                }
                $i++;
            }
            echo "<tr>";
        }
        $sqlframe->printar_todos();
        echo "</table>";
    }
}
