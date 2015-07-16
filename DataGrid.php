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
    
    public static function printar($query,$tipo){
        cargar_configuracion();
        $sqlframe = new SQLFrame($tipo);
        $con = conectar();
        $data = mysql_query("$query", $con) or die(mysql_error());
        if(strcmp($tipo, "html")==0){
            echo "<table class='tabla'>";
            echo "<tr class='cabecera'>";
        }
        $i=0;        
        while($i<mysql_num_fields($data)){
            if(strcmp($tipo, "html")==0){
                echo "<td>".mysql_field_name($data,$i)."</td>";
            }
            else{
                echo mysql_field_name($data,$i).";";
            }
            $i++;
        }
        if(strcmp($tipo, "html")==0){
            echo "</tr>";
        }
        else{
            echo "\n"; 
        }
        
        while($row = mysql_fetch_assoc($data)){ 
            
            $sqlframe->add($row, $data);
            if(strcmp($tipo, "html")==0){
                echo "<tr class='fila'>";
            }
            $i=0;
            while($i<mysql_num_fields($data)){
               
                if(is_float($row[mysql_field_name($data,$i)]+0)){
                    if(strcmp($tipo, "html")==0){
                        echo "<td>".number_format($row[mysql_field_name($data,$i)],2)."</td>";
                    }
                    else{
                        echo number_format($row[mysql_field_name($data,$i)],2).";";
                    }
                }
                else{
                   if(strcmp($tipo, "html")==0){ 
                    echo "<td>".$row[mysql_field_name($data,$i)]."</td>"; 
                   }
                   else{
                       echo $row[mysql_field_name($data,$i)].";";
                   }
                }
                $i++;
            }
            if(strcmp($tipo, "html")==0){
                echo "</tr>";
            }
            else{
                echo "\n"; 
            }
        }
        $sqlframe->printar_todos();
        if(strcmp($tipo, "html")==0){
            echo "</table>";
        }
    }
}
