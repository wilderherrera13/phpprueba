<?php

define("DB_USERNAME",'root');
define("DB_SERVER",'localhost');
define("DB_DATABASE",'soapServiceResults');
define("DB_PASSWORD",'root');

class mysqlConnectionClass{

    public function mysqlConnection(){
    
        $connection=new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
        if(!$connection){
        
            return("ERROR AL CONECTAR LA BASE DE DATOS" . mysql_error());
        }
    

        return($connection);
    }

    public function createTables(mysqli $connection){
        

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

}


?>
