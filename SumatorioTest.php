<?php
    require_once 'Sumatorio.php';
    $sum = new Sumatorio('Funcion',3, false);
    $sum->add(2);
    $sum->add(5);
    $sum->printar();
    $sum->reset();
    $sum->printar();
    $sum2 = new Sumatorio('canal',3, true);
    $sum2->add(2);
    $sum2->add(5);
    $sum2->printar();
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

