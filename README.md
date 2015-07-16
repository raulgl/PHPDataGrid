# PHPDataGrid
Un  Framework que a partir de una consulta mysql monta la tabla con los resultados de esa consulta mas los sumatorios por configuracion.
Muchas veces me he encontrado con peticiones de informes que basicamente son un consulta a la BD pero que tengan totales o subtotales por algun parametro. 
Por ejemplo, imaginemos que queremos ver los espectaculos que se han vendido y lo que se ha recaudado y el total y los subttotales por actividad.
Este seria una demo del informe que queremos:
  Actividad Espectaculo   entradas    recaudacion(euros)
  Teatro    Espartaco     2             200
  Teatro    El anillo de. 3             330
            TOTAL         5             530
  Cine      El señor...   10            100
            TOTAL         10            100
  TOTAL                   15            630
  
  Aunque es facil pasar la informacion de una consulta de BD a html no es trivial cuando tienes que hacer totales y subtotales. Estuve ojeando por internet pero no encontre nada similar o las alternativas eran muy farragosas o no gratuitas.
  Así que yo mismo hice un framework que ha partir de un fichero .ini para indicar la conexión con la BD y un xml para decir de que queremos los subtotales y que sumar en los subtotales.
  Un ejemplo de xml sería este:
  <?xml version="1.0" encoding="UTF-8"?>
<xml>
    <groupsby>
        <groupby posicion='0' class='normalr'>
            TOTAL    <!-- Este campo tiene que ir siempre-->       
        </groupby>
        <groupby posicion='1' class='normalr'><!-- posicion indica en que columna ira "TOTAL" cuando aparezca el subtotal" y class indica una classe .css que tendrá la fila de se muestre el subtotal-->
            Actividad  <!--Queremos subtotal por actividad-->         
        </groupby>
    </groupsby>
    <sumatorios><!-- dentro de este tag aparecen los campos que se suman en el subtotal-->
        <sumatorio  posicion='2'><!-- la posicion indica en que columna se escribira la suma del campo entradas-->
            entradas
        </sumatorio>
        <sumatorio  posicion='3'>
            recaudacion(euros)
        </sumatorio>
    </sumatorios>
</xml>
Un .ini ejemplo sería:
[bbdd]
ip=127.0.0.1:3306; ip y puerto del servidor de BD (la BD es un mysql que está en mi ordenador
user=root ; usuario de la BD
pswd=patata ; password de la BD
name=prostr ;nombre de la BD 
Una vez configurado el .xml y .ini (el xml tiene que llamarse config.xml y el .ini config.ini), solo se tiene que llamar a DataGrid::printar($query); donde $query es la consulta.

Pensemos ahora que lo que devuelve la BD no es la recaudacion espectaculo a espectaculo, sino butaca a butaca.Es decir:
Actividad Espectaculo   fila  butaca  precio
y que queremos los subtotales por Espectaculo y Actividad.Es decir:
Actividad Espectaculo   fila  butaca   recaudacion(euros)
  Teatro    Espartaco     1   1           100
  Teatro    Espartaco     1   2           100
                        TOTAL 2           200
  Teatro    El anillo     1   1           110
  Teatro    El anillo     1   2           110
  Teatro    El anillo     1   3           110
                        TOTAL 3           330
            TOTAL             5           530
  Cine      El señor...   1   1           10    
  Cine      El señor...   1   2           10 
  Cine      El señor...   1   3           10 
  Cine      El señor...   1   4           10 
  Cine      El señor...   1   5           10 
                        TOTAL 5           50
            TOTAL             5           50
  TOTAL                       10          580

Para esto solo tenemos que modificar nuestro config.xml:
<?xml version="1.0" encoding="UTF-8"?>
<xml>
    <groupsby>
        <groupby posicion='0' class='normalr'>
            TOTAL    <!-- Este campo tiene que ir siempre-->       
        </groupby>
        <groupby posicion='1' class='normalr'><!-- posicion indica en que columna ira "TOTAL" cuando aparezca el subtotal" y class indica una classe .css que tendrá la fila de se muestre el subtotal-->
            Actividad  <!--Queremos subtotal por actividad-->         
        </groupby>
        <groupby posicion='2' class='normalr'>
            Espectaculo  <!--Queremos subtotal por Espectáculo-->         
        </groupby>
    </groupsby>
    <sumatorios><!-- dentro de este tag aparecen los campos que se suman en el subtotal-->
        <sumatorio  posicion='4'>
            recaudacion(euros)
        </sumatorio>
    </sumatorios>
    <contadores><!--para cada fila se incrementa el contador que aparecera en el subtotal en la posicion especificada-->
      <contador posicion='3'>
        butaca
      </contador>
    </contadores>
</xml>
Importante: el orden de los group by tiene que ser de mas generico a mas especifico:
1º TOTAL: todas las entradas.
2º Actvidad: todas las entradas de todos los espectaculos de una determinada actividad
3º Espectaculos: todas las entradas de un espectaculo en concreto.

