<?php
require_once 'groupby.php';
require_once 'funciones.php';
$group = new groupby('Actividad', 0) ;
$group->add('vendidas_funciones',4,false);
$group->add('ingresos_funciones',5,true);
echo $group->mismo('Actividad');

echo $group->is_reset('OP');

echo $group->is_reset('OP');
$group->set_actual('DA');
echo $group->is_reset('OP');
cargar_configuracion();
$query="select  ECACTV,AVDESA as Actividad,ecdesc as descripcion,ecdate as fecha,cndesc, def.*
from def,pfcfunc,pfcanal,PFACTIV
where attipo=ectipo and atcicl=eccicl and atfunc=ecfunc and cncanal=atcanal and ECACTV=AVCODI
and AVFLG1='E'
order by ECACTV,ecdesc,attipo,atcicl,atfunc,atcanal";
$con = conectar();
$data = mysql_query("$query", $con) or die(mysql_error());
while($row = mysql_fetch_assoc($data)){  
    $group->sum($row, $data);               
}
$group->printar();
$group->reset();
$group->printar();


/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

