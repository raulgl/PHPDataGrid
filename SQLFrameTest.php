<?php
    require_once 'SQLFrame.php';
    require_once 'funciones.php';
    
    cargar_configuracion();
    $sqlframe = new SQLFrame();
    $query="select AVDESA as Actividad,ecdesc as Descripcion,ecdate as Fecha,cndesc as Canal, def.*
    from def,pfcfunc,pfcanal,PFACTIV
    where attipo=ectipo and atcicl=eccicl and atfunc=ecfunc and cncanal=atcanal and ECACTV=AVCODI
    and AVFLG1='E'
    order by ECACTV,ecdesc,attipo,atcicl,atfunc,atcanal";
    $con = conectar();
    $data = mysql_query("$query", $con) or die(mysql_error());
    echo "<html><head></head><body><table>";
    $i=0;
    echo "<tr>";
    while($i<mysql_num_fields($data)){
        echo "<td>".mysql_field_name($data,$i)."</td>";
        $i++;
    }
    echo "</tr>";
    while($row = mysql_fetch_assoc($data)){ 
        $sqlframe->add($row, $data);
        /*echo "<tr>";
        $i=0;
        while($i<mysql_num_fields($data)){
            echo "<td>".$row[mysql_field_name($data,$i)]."</td>";
            $i++;
        }
        echo "<tr>";*/
        
    }
    $sqlframe->printar_todos();
    echo "</table></body></html>";
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

