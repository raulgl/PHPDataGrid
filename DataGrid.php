<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Framework donde a partir de una consulta devuelve el resultado de la consulta en html o csv o pdf.
 * Ademas también realiza resumenes despues por cada variable que le indicamos en config.json
 * @author ics_raul
 */
require_once 'SQLFrame.php';
require_once 'funciones.php';
require_once 'PDF.php';
class DataGrid {
    static $data;
    static $pdf;
    /**
     *  se le introduce la consulta en sql y el tipo de formato en que se quiere importar:"pdf","csv" o "html"
     */
    public static function printar($query,$tipo){
        cargar_configuracion();
        $sqlframe = new SQLFrame($tipo);
        $con = conectar();
        DataGrid::$data = mysql_query("$query", $con) or die(mysql_error());
        DataGrid::$pdf = new PDF(rand (5, 50));
        if(strcmp($tipo, "html")==0){
            echo "<table class='tabla'>";
            echo "<tr class='cabecera'>";
        }
        if(strcmp($tipo, "pdf")!=0){
            $i=0;        
            while($i<mysql_num_fields(DataGrid::$data)){
                if(strcmp($tipo, "html")==0){
                    echo "<td>".mysql_field_name(DataGrid::$data,$i)."</td>";
                }
                else{
                    echo mysql_field_name(DataGrid::$data,$i).";";
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
        while($row = mysql_fetch_assoc(DataGrid::$data)){ 
            
            $sqlframe->add($row);
            if(strcmp($tipo, "html")==0){
                echo "<tr class='fila'>";
            }
            $i=0;
            
            if(strcmp($tipo, "pdf")!=0){
                while($i<mysql_num_fields(DataGrid::$data)){
               
                    if(is_float($row[mysql_field_name(DataGrid::$data,$i)]+0)){
                        if(strcmp($tipo, "html")==0){
                            echo "<td>".number_format($row[mysql_field_name(DataGrid::$data,$i)],2)."</td>";
                        }
                        else if(strcmp($tipo, "csv")==0){
                            echo number_format($row[mysql_field_name(DataGrid::$data,$i)],2).";";
                        }
                    }
                    else{
                        if(strcmp($tipo, "html")==0){ 
                            echo "<td>".$row[mysql_field_name(DataGrid::$data,$i)]."</td>"; 
                        }
                        else if(strcmp($tipo, "csv")==0){
                            echo $row[mysql_field_name(DataGrid::$data,$i)].";";
                        }
                    }
                    $i++;
                }
            }
            else{
                DataGrid::$pdf->add_row($row);
                DataGrid::$pdf->add_linea();
                DataGrid::$pdf->comprobar_tamaño();
            }
            if(strcmp($tipo, "html")==0){
                echo "</tr>";
            }
            else if(strcmp($tipo, "csv")==0){
                echo "\n"; 
            }
        }
        $sqlframe->printar_todos();
        
        if(strcmp($tipo, "pdf")==0){
            DataGrid::$pdf->añadir_gif();
            $nompdf = DataGrid::$pdf->crear_PDF();
            DataGrid::printar_pdf($nompdf);
        }
        
        if(strcmp($tipo, "html")==0){
            echo "</table>";
        }
    }
    /**
     * devuelve el html que muestra el 
     * @param string $pdf: nombre donde está pdf generado
     */
    public static function printar_pdf($pdf){
        echo "<object id='confirmar_area' type='application/pdf' data='PHPDataGrid/pdf/$pdf.pdf' width='1200' height='650' > </object>";
    }
}

