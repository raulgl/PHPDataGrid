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
name=pros ;nombre de la BD 
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
NUEVA ACTUALIZACIÓN: exportar a CSV.
A partir de ahora para exportar a HTML, la llamada tiene que ser:
DataGrid::printar($query,"html");
Si se quiere exportar a CSV entonces la llamada es:
DataGrid::printar($query,"csv");
NUEVA ACTUALIZACION: exportar a PDF.A parte de llamar a DataGrid::printar($query,"pdf");
En el PHPDataGrid/informes tiene que haber:
PDF.gif: .gif con la plantilla que usara el programa para montar el pdf. Basicamente cada vez que el programa necesite una nueva
pagina para el informe  cojera este .gif escribira los datos del informe y lo guardar para montar el pdf con los .gif que ha ido cogiendo.
PDF.xml:
un xml donde se dice donde se escribe el campo de la Base de datos en la pagina:
Tiene esta pinta:
<?xml version="1.0" encoding="iso-8859-1"?>
<informe>
	<offset y="250"/>
        <estatico x="5"  dir="0" fonttype="ArialBlack.ttf" fontsize="17" texto="ACTIVIDAD"/>
	<estatico x="150"  dir="0" fonttype="ArialBlack.ttf" fontsize="17" texto="ESPECTACULO"/>
	<estatico x="450"  dir="0" fonttype="ArialBlack.ttf" fontsize="17" texto="FILA"/>
	<estatico x="580"  dir="0" fonttype="ArialBlack.ttf" fontsize="17" texto="BUTACA"/>
	<estatico x="700"  dir="0" fonttype="ArialBlack.ttf" fontsize="17" texto="V. FUNC."/>
	<estatico x="820"  dir="0" fonttype="ArialBlack.ttf" fontsize="17" texto="RECAUDACION"/>
	<offsetres linea="50" y="350" />
        <Actividad x="5"  dir="0" fonttype="verdana.ttf" fontsize="15" max="15"/>
        <total x="5"  dir="0" fonttype="verdana.ttf" fontsize="15" texto="TOTAL"/>
	<Descripcion x="150"  dir="0" fonttype="verdana.ttf" fontsize="15" />
        <total_actividad x="150"  dir="0" fonttype="verdana.ttf" fontsize="15" texto="TOTAL"/>
	<Fila x="450"  dir="0" fonttype="verdana.ttf" fontsize="15"/>
        <total_espectaculo x="450"  dir="0" fonttype="verdana.ttf" fontsize="15" r="255" texto="TOTAL"/>
	<Butaca x="580"  dir="0" fonttype="verdana.ttf" fontsize="15"/>        
	<recaudacion x="750"  dir="0" fonttype="verdana.ttf" fontsize="15"/>    
		
</informe>
tag offset: nos ice cuanto espacio en vertical hay que dejar desde el inicio de la pagina hasta empezar a escribir los datos del informe
tag estatico: printa lo que hay en el texto en la posicion x y como posicion y la del offset.Esto se utiliza para la cabecera del informe.
tag offsetres: en el atributo linea nos dice cuantos pixeles ocupa cada linea del informe y en el atributo y nos indica cuanto espacion hay
que dejar desde el principio de la pagina hasta empezar a escribir los datos del informe(offset+ linea de la cabecera+ espacio de la cabecera).
tag Actividad,Descripcio,Fila,Butaca,recaudacion: para cada campo de la consulta a la BD nos dice en que posicion x debe ir. la posicion y la 
calcula a partir de la linea en que esta el atributo linea de offsetres y la y de offsetres.
los campos total,total_actividad,total_espectaculo los explicaremos mas adelante.
Ademas los tag pueden tener los siguientes atributos:
fonttype: tipo de letra que se va ha utilizar para escribir el dato en el pdf. Los tipos de letra que hay ArialBlack,itcedscr,times,verdana
fontsize: tamaño de letra que se va ha utilizar para escribir el dato en el pdf.
el config.xml tendrá esta pinta:
<?xml version="1.0" encoding="UTF-8"?>
<xml>
    <groupsby>
        <groupby posicion='0' class='normalr' total='total'>
            TOTAL           
        </groupby>
        <groupby posicion='1' class='normalr' total='total_actividad'>
            Actividad           
        </groupby>
        <groupby posicion='2' class='normalr' total='total_espectaculo'>
            Espectaculo          
        </groupby>
    </groupsby>
...
a los tags groupby se les añade el atributo total. Este atributo nos dice que tag del PDF.xml corresponde este group by.
El tag correspondiente informa de que texto, que fontsize, que fonttipe y donde colocar el texto que indica que la linea es el sumatorio
de ese group by.
A parte la carpeta informes y la carpeta fonts, hay que crear la carpeta .pdf, donde se guardan los pdf generados y la carpeta temp
donde se guardan .gif temporales.
Ademas hay que modificar el config.ini añadiendo:
[general]
n_tickets_pag=20
numero de lineas que van a estar en una misma pagina.
NUEVA ACTUALIZACION
Ahora el config no será un .xml sino un .json:
{"groupby":[
    {"nombre":"TOTAL","posicion":0, "class":"normalr", "total":"total"},
    {"nombre":"Actividad","posicion":1, "class":"normalr", "total":"total_actividad"},
    {"nombre":"Espectaculo","posicion":2, "class":"normalr", "total":"total_espectaculo"},
],
"sumatorio":[
    {"nombre":"recaudacion(euros)","posicion":4},
],
"contador":[
    {"nombre":"butaca","posicion":3},
]
}
NUEVA ACTUALIZACION
fusionados en config.json y el config.ini en el config.fson:
{"groupby":[
    {"nombre":"TOTAL","posicion":0, "class":"normalr", "total":"total"},
    {"nombre":"Actividad","posicion":1, "class":"normalr", "total":"total_actividad"},
    {"nombre":"Espectaculo","posicion":2, "class":"normalr", "total":"total_espectaculo"},
],
"sumatorio":[
    {"nombre":"recaudacion(euros)","posicion":4},
],
"contador":[
    {"nombre":"butaca","posicion":3},
],
"bbdd":{"ip":"127.0.0.1:3306","user":"root","pswd":"patata","name":"pros"},
"n_tickets_pag":20
}

 
