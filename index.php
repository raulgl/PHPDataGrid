<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <style>
            .fila {font-family: 'Asap',sans-serif;;font-size:10pt;color:#666;text-decoration:none;}
            .normaln {font-family: 'Asap',sans-serif;;font-size:10pt;color:#666;font-weight:bold;text-decoration:none;}
            .cabecera:hover {text-decoration:underline;}
            .normalo {font-family: 'Asap',sans-serif;;font-size:10pt;color:#666;font-weight:bold;text-decoration:none;}
            .normalon {font-family: 'Asap',sans-serif;;font-size:10pt;color:#666;font-weight:bold;text-decoration:none;}
            .normalr {font-family: 'Asap',sans-serif;;font-size:10pt;color:#FF0000;font-weight:bold;text-decoration:none;}
            .normalp {font-family: 'Asap',sans-serif;;font-size:8pt;color:#666;text-decoration:none;}
            .normalpo {font-family: 'Asap',sans-serif;;font-size:9pt;color:#666;font-weight:bold;text-decoration:underline;}
            .normalmini {font-family: 'Asap',sans-serif;;font-size:6pt;color:#666;text-decoration:none;} 
            .pie {font-size:10pt;color:000000;font-family: 'Asap',sans-serif;;text-decoration:none}
            .enlace {font-family: 'Asap',sans-serif;;font-size:10pt;color:#666;text-decoration:none}
            .enlace:hover {text-decoration:none;}
            .enlacen {text-decoration:none;font-family: 'Asap',sans-serif;;font-size:10pt;font-weight:bold;color:#666;}
            .sub {font-family: 'Asap',sans-serif;;font-size:11px;color:#666;font-weight:bold;text-decoration:underline;}
        </style>
    </head>
    <body>
    <?php
        require_once 'DataGrid.php';
        $query="select AVDESA as Actividad,ecdesc as Descripcion,ecdate as Fecha,cndesc as Canal, def.*
        from def,pfcfunc,pfcanal,PFACTIV
        where attipo=ectipo and atcicl=eccicl and atfunc=ecfunc and cncanal=atcanal and ECACTV=AVCODI
        and AVFLG1='E'
        order by ECACTV,ecdesc,attipo,atcicl,atfunc,atcanal";
        DataGrid::printar($query);
    ?>
    </body>
</html>
