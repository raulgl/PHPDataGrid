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
        else if(strcmp($tipo, "pdf")==0){
            $pagina=0;
            $rand = rand (5, 50);
            $img = crear_gif($pagina,$rand);
        }
        if(strcmp($tipo, "pdf")!=0){
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
            else if(strcmp($tipo, "csv")==0){
                echo "\n"; 
            }
        }
        $linea=0;
        $array_gif= array();
        while($row = mysql_fetch_assoc($data)){ 
            
            $sqlframe->add($row, $data,$array_gif,$linea,$img,$pagina,$rand);
            if(strcmp($tipo, "html")==0){
                echo "<tr class='fila'>";
            }
            $i=0;
            
            if(strcmp($tipo, "pdf")!=0){
                while($i<mysql_num_fields($data)){
               
                    if(is_float($row[mysql_field_name($data,$i)]+0)){
                        if(strcmp($tipo, "html")==0){
                            echo "<td>".number_format($row[mysql_field_name($data,$i)],2)."</td>";
                        }
                        else if(strcmp($tipo, "csv")==0){
                            echo number_format($row[mysql_field_name($data,$i)],2).";";
                        }
                    }
                    else{
                        if(strcmp($tipo, "html")==0){ 
                            echo "<td>".$row[mysql_field_name($data,$i)]."</td>"; 
                        }
                        else if(strcmp($tipo, "csv")==0){
                            echo $row[mysql_field_name($data,$i)].";";
                        }
                    }
                    $i++;
                }
            }
            else{
                add_row($img,$row,$data,$linea);
                $linea++;
                comprobar_tamaño($img,$linea,$array_gif,$pagina,$rand);
            }
            if(strcmp($tipo, "html")==0){
                echo "</tr>";
            }
            else if(strcmp($tipo, "csv")==0){
                echo "\n"; 
            }
        }
        $sqlframe->printar_todos($array_gif,$linea,$img,$pagina,$rand);
        if(strcmp($tipo, "pdf")==0){
            $result_img = realpath(dirname(__FILE__)).'\\temp\\'.$rand.$pagina.'.GIF';
            if(imagegif($img, $result_img))
                $result_ok = $result_img;
            array_push($array_gif, $result_ok);
            imagedestroy($img);
            $pdf = crear_PDF($array_gif,$rand);
            DataGrid::printar_pdf($pdf);
        }
        
        if(strcmp($tipo, "html")==0){
            echo "</table>";
        }
    }
    public static function printar_pdf($pdf){
        echo "<object id='confirmar_area' type='application/pdf' data='PHPDataGrid/pdf/$pdf.pdf' width='1200' height='650' > </object>";
    }
}

