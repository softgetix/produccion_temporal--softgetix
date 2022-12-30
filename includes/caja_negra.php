<?PHP

    
function caja_negra($datos,$filtrar_en ,$json=0,$objSQLServer, $return = 0){

	$inyeccion = 0;
    if($json==1){$datos=json_decode($datos,true);}//Convierto un json a array.
	if(is_numeric($datos)){$datos=explode(',',$datos);}
	if(is_string($datos)){$datos=explode(',',$datos);}//Convierto un String a array.       
		   
	switch($filtrar_en){
		case 'moviles':
			if(is_array($datos)){
				foreach($datos as $movil){//Por cada posicion del array.
					if(is_numeric($movil)){//Verifico si esa posicion del array es numerico
						$strSQL  = "SELECT um_mo_id FROM tbl_usuarios_moviles WHERE um_mo_id=".$movil;
						$strSQL .= " AND um_us_id=".$_SESSION['idUsuario'];
						$query   = $objSQLServer->dbQuery($strSQL);
						$intRows = $objSQLServer->dbNumRows($query);
						if($intRows==0){ 
							$inyeccion = 1;
						}
						elseif(is_string($movil)){//Verifico si esa posicion del array es string
							$strSQL  = "SELECT um_mo_id FROM tbl_usuarios_moviles INNER JOIN tbl_moviles ON um_mo_id=mo_id";
							$strSQL .= " WHERE (mo_matricula='".$movil."' OR mo_otros='".$movil."' OR mo_identificador='".$movil."')";
							$strSQL .= " AND um_us_id=".$_SESSION['idUsuario'];
							$query=$objSQLServer->dbQuery($strSQL);
							$intRows = $objSQLServer->dbNumRows($query);
							if($intRows==0){
								$inyeccion = $cliente;
							}  
						}
					}
				}
			}
			break;
				
				case 'equipos':
				             ///Ahora datos debería ser un array para todos los casos.
						 
						 /*echo gettype($datos);*/
							if(is_array($datos))
								{
								  /*print_r($datos);
								  echo $datos;*/
								  foreach($datos as $unidad)//Por cada posicion del array.
									 {
									    
										if(is_numeric($unidad))//Verifico si esa posicion del array es numerico
									      {
									   		$strSQL  = "SELECT um_id FROM tbl_unidad";
											$strSQL .= " INNER JOIN tbl_usuarios_moviles ON un_mo_id=um_mo_id"; 
											$strSQL .= " WHERE un_id=".$unidad;
											$strSQL .= " AND um_us_id=".$_SESSION['idUsuario']; 
											$query   = $objSQLServer->dbQuery($strSQL);
									        $intRows =$objSQLServer->dbNumRows($query);
									        if($intRows==0){ echo "Se intento filtrar un ID numerico invalido"; die();}
										  }elseif(is_string($movil))//Verifico si esa posicion del array es string
										  {
											$strSQL  = "SELECT um_id FROM tbl_unidad";
											$strSQL .= " INNER JOIN tbl_usuarios_moviles ON un_mo_id=um_mo_id"; 
											$strSQL .= " WHERE un_mostrarComo='".$unidad."'";
											$strSQL .= " AND um_us_id=".$_SESSION['idUsuario']; 
											$query   = $objSQLServer->dbQuery($strSQL);
									        $intRows =$objSQLServer->dbNumRows($query);
									        if($intRows==0){
												             echo "Se intento filtrar un ID numerico invalido"; 
															 $contenido=$_SERVER['PHP_SELF']."-->VALOR=".$cliente."\r\n";
															 $fp=fopen(PATH_LOG_SECURE."/caja_negra.txt","a+");
															 fwrite($fp,$contenido);
															 fclose($fp) ;
															 die();
												
												            }
										  }
									 }
								 }
								 
				
				break;
				case 'clientes':
				             ///Ahora datos debería ser un array para todos los casos.
					        //echo "<pre>".print_r($_SESSION,true)."</pre>";
							if(is_array($datos))
								{
								
								  foreach($datos as $cliente)//Por cada posicion del array.
									 {
									  
										if(is_numeric($cliente))//Verifico si esa posicion del array es numerico
									      {
									   			if($_SESSION['idTipoEmpresa']==3)
												{
												  //Soy LocalizarT no valido nada
												}else
												{
													if(($_SESSION['idTipoEmpresa']==1)||($_SESSION['idTipoEmpresa']==2))
													{
			                                              if($_SESSION['idEmpresa']== $cliente)
														  {
														    //No hago nada porque el Agente o el Cliente coresponden con el dato a obtener.
														  }elseif($_SESSION['idTipoEmpresa']==1)
														  {
															 $strSQL="SELECT cl_id FROM tbl_clientes WHERE cl_id_distribuidor =".$_SESSION['idEmpresa'];
															 $strSQL.="AND cl_id=".$cliente; 
															 $query   = $objSQLServer->dbQuery($strSQL);
									                         $intRows =$objSQLServer->dbNumRows($query);
														     if($intRows==0){
												             echo "Se intento filtrar un ID numerico invalido"; 
															 $contenido=$_SERVER['PHP_SELF']."  (Tipo empresa 1) -->VALOR=".$cliente."\r\n";
															 $fp=fopen(PATH_LOG_SECURE."/caja_negra.txt","a+");
															 fwrite($fp,$contenido);
															 fclose($fp) ;
															 die();
												
												            }
														  }else
														  {
															 echo "Se intento filtrar un ID numerico invalido (Tipo empresa 1 o 2)"; 
															 $contenido=$_SERVER['PHP_SELF']." (Tipo empresa 1 o 2) -->VALOR=".$cliente."\r\n";
															 $fp=fopen(PATH_LOG_SECURE."/caja_negra.txt","a+");
															 fwrite($fp,$contenido);
															 fclose($fp) ;
															 die(); 
														  }
													}
													
											
											    }
										  }
									  }
										 
								 }
								 
				
				break;
		
		  }
		  
	if($return){
		if($inyeccion){return false;}
		else{return true;}
	}
	else{  
		if($inyeccion){
			echo "Se intento filtrar un ID numerico invalido"; 
			$contenido=$_SERVER['PHP_SELF']."-->VALOR=".$inyeccion."\r\n";
			$fp=fopen(PATH_LOG_SECURE."/caja_negra.txt","a+");
			fwrite($fp,$contenido);
			fclose($fp) ;
			die();	  
		}
	}
		
}

?>