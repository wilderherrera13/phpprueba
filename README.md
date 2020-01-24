# Prueba tecnica PHP Wilder Herrera

Aplicativo desarrollado en PHP sin uso de Frameworks para el consumo y alojamiento en base de datos de peticios a un servicio SOAP.

## Ambiente de desarrollo 

_PHP -V 5.3
APACHE SERVER
MYSQL._


### Estructura del aplicativo

```
\backend
  \databaseConectionMysql.php
  \getExtension.php
  \soapConnectionClass.php
index.php
cargarDatos.php
ReporteArchivos.php
ReporteExtensiones.php
styles.css
```
### Creacion del cliente SOAP 🔧

Dentro del siguiente archivo se presenta la creacion del cliente SOAP para relizar las peticiones, para lograr lo anterior se utilizo la clase nativa 'SoapClient' la cual cuenta con los metodos necesarios para una comunicacion con SOAP por a traves de la url suministrada.

En este caso se dejan como constantes las direcciones de coneccion del servicio ya que estos valores no cambian en tiempo de ejecucion.

Para definir los parametros enviados a la operacion "BuscarArchivos" primero se hizo uso del metodo '__getTypes()' de la clase 'soapClient' el cual nos brinda informacion sobre como estan establecidas las arquitecturas de las funciones dentro del servicio.
analizando el resultado del metodo '__getTypes()' se construye el array $soapRequestParameters que posterirmente se envia como parametro junto con la operacion "Buscar Archivo", Para los parametros "IdUsuarioBusqueda" y 'IdDirectorioBusqueda'no se especifico un valor dentro de las instrucciones de la prueba, por tanto a ambos se les asigna '1' como valor.

```
\backend\soapConnectionClass.php

  <?php

define("SOAP_WSDL_SOURCE",'http://test.analitica.com.co/AZDigital_Pruebas/WebServices/ServiciosAZDigital.wsdl');
define("SOAP_SERVICE_LOCATION",'http://test.analitica.com.co/AZDigital_Pruebas/WebServices/SOAP/index.php');

class soapConectionClass{
	private static $params = array('encoding' => 'UTF-8');
	private static $soapRequestParameters=array('Condiciones'
						 =>array('Condicion'									    							=>array('Tipo'=>'FechaInicial',													        'Expresion'=>"2019-07-01 00:00:00")) , 												'IdUsuarioBusqueda'=>'1','IdDirectorioBusqueda'=>1); //SE DEFINEN LOS PARAMETROS PARA LA SOLICITUD AL SERIVICIO SOAP SEGUN LA ESTRUCTURA ENCONTRADA CON '__getTypes()'


	public function soapConection(){
	
		try {
			$clientSoapObject=new SoapClient(SOAP_WSDL_SOURCE,self::$params); // CREACION DEL CLIENTE SOAP
			$clientSoapObject->__setLocation(SOAP_SERVICE_LOCATION); // ENDPOINT
			return($clientSoapObject->BuscarArchivo(self::$soapRequestParameters)); // LLAMADO AL METODO PARA BUSQUEDA DE ARCHIVOS CON PARAMETROS RESPECTIVOS.
			return($clientSoapObject->__getTypes()); // METODO UTILIZADO PARA VERIFICAR PARAMETROS DE LA OPERACION BUSCARARCHIVO
			}
		catch(SoapFault $err)
		{
			return($err);
		}
			
	}

}



?>

```

### Tratamiento de informacion proveniente del servicio

Los datos provenientes del servicio se someten al algoritmo presentado a continuacion con el fin de darles un formato mas manejable, en este caso el resultado de este algoritmo es un array con la informacion de los archivos.
```
 \cargarDatos.php

    $archivosFromSoap = (array)json_decode(json_encode($soapConnectionResponse->soapConection()), true); // Parseo de la informacion    proveniente del servicio
```

### Creacion de la base de datos 🔧

_Inicialmente el siguiente metodo de la clase 'mysqlConnectionClass' se encarga de crear las bases de datos dentro de MySQL utilizando como parametro de entrada una conexion existente a la base de datos. cabe resaltar que la logica de este metodo trunca los datos cada vez que se consulta el servicio SOAP.
```
\backend\databaseConectionMysql.php

    public function createTables(mysqli $connection){
        
        //Creacion de las tablas y truncamiento de las bases de datos 
        
        $sql_creation_table_archivo='CREATE TABLE IF NOT EXISTS soapserviceresults.archivo (
            PK_ID_ARCHIVO int(11) NOT NULL AUTO_INCREMENT,
            nombre varchar(30) NOT NULL,
            id_de_archivo int(11) NOT NULL,
            PRIMARY KEY (PK_ID_ARCHIVO)
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1';

        $sql_creation_table_extension='CREATE TABLE IF NOT EXISTS soapserviceresults.extension (
            PK_ID_EXTENSION INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            extension VARCHAR( 5 ) NOT NULL,
            FK_ID_ARCHIVO_ID_ARCHIVO int(11) NOT NULL
            ) ENGINE = InnoDB';

        $connection->query("truncate table soapserviceresults.archivo");
        $connection->query("truncate table soapserviceresults.extension");
        
        $sql=$connection->query($sql_creation_table_extension);
        $sql=$connection->query($sql_creation_table_archivo);
        
        return("Tablas creadas");

    }

```
## Almacenamiento de datos
Con los datos provenientes del servicio se procede a la carga de datos, para esto se utilizaron peticiones de sql que se realizaron a la base de datos para cargar la informacion necesaria dentro de un ciclo foreach que recorre cada uno de las posiciones el array que tiene la informacion proveniente del servicio.
 
```
\cargarDatos.php


   foreach ($archivosFromSoap['Archivo'] as $key => $archivo){

            $extension=getExtensionClass::getExtension($archivo['Nombre']);
            $db_conection->query("insert into soapserviceresults.extension(extension,FK_ID_ARCHIVO_ID_ARCHIVO) values('".$extension."','".$archivo['Id']."')");		
            $db_conection->query("insert into soapServiceResults.archivo(nombre,id_de_archivo) values"."('".$archivo['Nombre']."','".$archivo['Id']."')");
        }
```
### Reporte de archivos
Para obtenenr los datos para el reporte de archivos se realizo un cruce de las tablas que contenian la informacion de los archivos y las extenciones de los mismos mediante un 'inner join'
```
\ReporteArchivos.phh

    	<?php
				$results=$db_conection->query("select * from soapserviceresults.archivo inner join soapserviceresults.extension on soapserviceresults.archivo.id_de_archivo = soapserviceresults.extension.FK_ID_ARCHIVO_ID_ARCHIVO where 1"); // Peticion sql para el cruce de informacion entre las dos tablas.
            
                if(!$results)
                {
                        echo "<tr>
                        <td colspan=3 class='text-warning'><b>  NO SE ENCUENTRAN REGISTROS</b></td>
                        </tr>";
                }
                
                else
                {
                    while($result = $results->fetch_assoc() )
				    {   

					echo "<tr><td>".$result['id_de_archivo']."</td>
							<td>".$result['nombre']."</td>	
							<td>".$result['extension']."</td></tr>";
                    }
                }

			?>	
		
```
### Conteo y listado de extensiones.

Para realizar el reporte de conteo de extesione se utilizo un script sql de conteo y agrupamiento como se muestra a continuacion.
```
\ReporteExtensiones.php
			<?php
				$results=$db_conection->query("select soapserviceresults.extension.extension as extension,Count(soapserviceresults.extension.extension) as cantidad from soapserviceresults.extension group by soapserviceresults.extension.extension"); // SQL PARA OBTENER EXTENSIONES Y LA CANTIDAD DE LAS MISMAS. 

                if(!$results)
                {
                        echo "<tr>
                        <td colspan=3 class='text-warning'><b>  NO SE ENCUENTRAN REGISTROS </b></td>
                        </tr>";
                }

                else{
				    while($result = @$results->fetch_assoc() )
				    {

					echo "<tr><td>".$result['extension']."</td>
							<td>".$result['cantidad']."</td>	
							</tr>";
                   }
                }

			?>	
```


