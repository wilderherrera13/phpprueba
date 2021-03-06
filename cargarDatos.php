<?php
include('backend\soapConnectionClass.php');
include('backend\databaseConnectionMysql.php');
include('backend\getExtension.php');

$database=new mysqlConnectionClass(); //Conexion a base de datos
$db_conection=$database->mysqlConnection();
$database->createTables($db_conection); // Crea las tablas dentro de la base de datos


$soapConnectionResponse=new soapConectionClass();

$archivosFromSoap = (array)json_decode(json_encode($soapConnectionResponse->soapConection()), true); // Parseo de la informacion proveniente del servicio

foreach ($archivosFromSoap['Archivo'] as $key => $archivo){
		
		$extension=getExtensionClass::getExtension($archivo['Nombre']);
		$db_conection->query("insert into soapserviceresults.extension(extension,FK_ID_ARCHIVO_ID_ARCHIVO) values('".$extension."','".$archivo['Id']."')");		
		$db_conection->query("insert into soapServiceResults.archivo(nombre,id_de_archivo) values"."('".$archivo['Nombre']."','".$archivo['Id']."')");
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Aspirante Wilder Herrera</title>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="styles.css"> 
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#">SOAP PHP</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
      <a class="nav-link" href="/">Inicio</a>
      </li>
      <li class="nav-item">
        
        <a class="nav-link" href="/cargarDatos.php">Cargar datos <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Reportes
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="/reporteArchivos.php">Reporte de archivos</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="/ReporteExtensiones.php">Conteo de extensiones</a>
        </div>
      </li>
      
    </ul>
    <p style="text-align:right;font-size:10px;font-weight:bold" class="text-secondary"> Desarrollador: Wilder Herrera</p>
  </div>
</nav>
	<div class="container">
		<div class="col-md-6 offset-md-3 bg-light p-4 text-center" style="margin-top:10%">
        <h6 class="text-secondary">LA INFORMACION FUE CARGADA EN LA BASE DE DATOS CORRECTAMENTE</h6>
        <hr>
           <div class="col-md-6 offset-md-3">
           <a class="btn btn btn-outline-info" href="/ReporteExtensiones.php"> Ver reporte de extensiones</a>
            </div>
            <hr>
            <div class="col-md-6 offset-md-3">
                <a class="btn btn btn-outline-info" href="/ReporteArchivos.php"> Ver reporte de archivos</a>
            </div>
        </div>
	</div>

	

</body>
</html>