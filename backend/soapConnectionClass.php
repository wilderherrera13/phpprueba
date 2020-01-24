<?php

define("SOAP_WSDL_SOURCE",'http://test.analitica.com.co/AZDigital_Pruebas/WebServices/ServiciosAZDigital.wsdl');
define("SOAP_SERVICE_LOCATION",'http://test.analitica.com.co/AZDigital_Pruebas/WebServices/SOAP/index.php');


class soapConectionClass{

	
	
	private static $params = array('encoding' => 'UTF-8');
	private static $soapRequestParameters=array('Condiciones'
													=>array('Condicion'
							    							=>array('Tipo'=>'FechaInicial',
																	'Expresion'=>"2019-07-01 00:00:00")) , 
												'IdUsuarioBusqueda'=>'1','IdDirectorioBusqueda'=>1
												);	 //SE DEFINEN LOS PARAMETROS PARA LA SOLICITUD AL SERIVICIO SOAP


	public function soapConection(){
	
		try {
			$clientSoapObject=new SoapClient(SOAP_WSDL_SOURCE,self::$params);
			$clientSoapObject->__setLocation(SOAP_SERVICE_LOCATION);
			return($clientSoapObject->BuscarArchivo(self::$soapRequestParameters));
			return($clientSoapObject->__getTypes()); // METODO UTILIZADO PARA VERIFICAR PARAMETROS DE LA OPERACION BUSCARARCHIVO
			}
		catch(SoapFault $err)
		{
			return($err);
		}
			
	}

}



?>
