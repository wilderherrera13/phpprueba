<?php

class getExtensionClass{

    public static function getExtension($fileName){ //Algoritmo para la obtencion de la extension de un nombre de archivo evita errores cuando hay mas de un punto en el string de nombre.

                $aux=explode('.',strrev($fileName));
                $extension=strrev($aux[0]);
                return($extension);


    }

}


?>