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
        //Cargamos los datos de la BD y el numero de lineas por pagina de config.json
        cargar_configuracion();
        //inicializamos el SQLFrame pasandole el tipo en que queremos el informe
        $sqlframe = new SQLFrame($tipo);
        //conectamos con la BD
        $con = conectar();
        //guardamos en data lo que nos viene de la base de datos
        DataGrid::$data = mysql_query("$query", $con) or die(mysql_error());
        //Si el tipo es pdf creamos el objeto pdf para guardarlo
        if(strcmp($tipo, "pdf")==0){
            DataGrid::$pdf = new PDF(rand (5, 50));
        }
        //Si el tipo es html creamos la tabla y la fila donde iran los datos de la cabecera
        if(strcmp($tipo, "html")==0){
            echo "<table class='tabla'>";
            echo "<tr class='cabecera'>";
        }
        /*Si no es el tipo pdf, printamos el nombre de las columnas de la BD, en pdf ya lo hace el cabecera informe*/
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
        /*Para cada fila llamamos al add del sqlframe para ver si tiene que printar algun subtotal y printamos la fila*/
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
                /*Para el pdf ademas incrementamos la linea en que estamos y miramos si hemos llegado al final de la pagina,
                 * si hemos llegado creamo una nueva y guardamos la vieja en el array de gif.
                 */
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
        /* printamos todos los groupby así tenemos los subtotales que quedaban y el TOTAL*/
        $sqlframe->printar_todos();
        /*para pdf añadimos al array de gif el gif actual, montamos el pdf con el array de gif y ponemos el html que muestra el pdf*/
        if(strcmp($tipo, "pdf")==0){
            DataGrid::$pdf->añadir_gif();
            $nompdf = DataGrid::$pdf->crear_PDF();
            DataGrid::printar_pdf($nompdf);
        }
        /* en html cerramos la tabla*/
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

