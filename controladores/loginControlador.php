<?php
require_once "includes/conn.php";
require_once('clases/clsLog.php');

//--
function validarUsuario($arr){ 
	global $objSQLServer;
	/*
	CODIGOS DE RETORNO
	0:USUARIO NO REGISTRADO
	1:REDIRECCIONA A LA PAGINA PRINCIPAL DEL SISTEMA.
	2:USUARIO INVALIDO
	3:PASS INVALIDO
	4:LLAVE NO VALIDA
	5:Logueo Fallido Reiterado
	6:URL invalida para el cliente logueado.
	*/
  
   $arrDatos['usuario'] = escapear_string($arr['usuario']);
   $arrDatos['pass'] 	= escapear_string($arr['pass']);

	if(empty($arrDatos["usuario"])){
		return 2;
	}
	elseif(empty($arrDatos["pass"])){
		return 3;
	}
	else{		
		require_once("clases/clsUsuarios.php");		
		$objUsuario = new Usuario($objSQLServer);
		
		require_once("clases/clsClientes.php");		
		$objCliente = new Cliente($objSQLServer);
		
                $arrDatos["pass_unHashed"] = $arrDatos["pass"];
		
                //--Ini. Se implementa HASH256 y que conviva con md5 hasta que todos migren a HASH256 mediante el cambio de clave..
                $arrDatos["pass"] = hash('sha256',trim($arrDatos["pass"]));
                $arrUsuario = $objUsuario->login($arrDatos, true);
                
                if($arrUsuario == false){//-- si es false, verificamos si no posee hash256 (borrar cuando se desida sacar por completo md5)
                    $arrDatos['pass'] 	= escapear_string($arr['pass']);
                    $arrDatos["pass"] = md5($arrDatos["pass"]);
                    $arrUsuario = $objUsuario->login($arrDatos, false);
                }
                //--Fin.
                
		$log = new Log($objSQLServer);
		$ip = getRealIP();
		$user_agent = $_SERVER['HTTP_USER_AGENT'];

		if(is_array($arrUsuario) && isset($arrUsuario[0])) {
                    
            if($arrUsuario[0]['idAgente'] != 9048){//--Si no es Fibercorp, se evalua setea de password cada 60 días.
                //-- Ini. Verificar vencimiento Pass
                if(!empty($arrUsuario[0]['us_pass_vence'])){
                    if(strtotime($arrUsuario[0]['us_pass_vence']) < strtotime(date('Y-m-d'))){
                        $arrValid = codificarURL($arrUsuario[0]['us_id']);
                        $url_encode = $arrValid['url_encode'];
                        $code = $arrValid['reset_code'];
                        $link_activacion = 'renovarPass.php?ref='.$url_encode;
                        $objUsuario->habilitarCambioPassword($arrUsuario[0]['us_id'], 0, $code);
                        redireccionarPagina($link_activacion);
                        exit;
                    }
                }
                else{
                    $objUsuario->actualizarPassword($arrUsuario[0]['us_id'], $arrDatos["pass"]);//-- Setea fecha de vencimiento de clave.
                }
                //--Fin.
            }
                    
            if (isset($arrUsuario[0]['us_keyId']) && $arrUsuario[0]['us_keyId']){
			    if (!isset($_SESSION['hkey']) || $_SESSION['hkey']!=$arrUsuario[0]['us_keyId']){
                    return 4;
                }
            }
			
            if(isset($arrUsuario[0]["us_id"]) && !$arrUsuario[0]["us_id"]){
			    return 3;
            }
			
            $urlAutorizada = $objCliente->obtenerUrlAutorizada($arrUsuario[0]["us_cl_id"]);
            if(!empty($urlAutorizada)){
			    if($_SERVER['REQUEST_URI'] != '/'.$urlAutorizada){
                    return 6;
			    }
            }
			
                    $_SESSION["resolucion"] 		= isset($_POST["hidResolucion"]) ? $_POST["hidResolucion"] : 1024;
                    $_SESSION["pass"] 				= $arrDatos["pass"];
                    $_SESSION["pass_inicioSession"] = $arrDatos["pass"];
                    $_SESSION["idUsuario"]			= $arrUsuario[0]["us_id"];
                    $_SESSION["us_nombre"]			= $arrUsuario[0]["us_nombre"];
                    $_SESSION["us_apellido"]		= $arrUsuario[0]["us_apellido"];
                    $_SESSION["idPerfil"] 			= $arrUsuario[0]["us_pe_id"];
                    $_SESSION["nombreUsuario"] 		= $arrUsuario[0]["us_nombreUsuario"];
                    $_SESSION["idEmpresa"] 			= $arrUsuario[0]["us_cl_id"];
                    $_SESSION["idAgente"] 			= $arrUsuario[0]['idAgente'];
                    $_SESSION["nombreAgente"] 		= $arrUsuario[0]['nombreAgente'];
                    $_SESSION["idTipoEmpresa"] 		= $arrUsuario[0]["cl_tipo"];
                    $_SESSION["idPais"] 			= $arrUsuario[0]["cl_pai_id"];
                    $_SESSION['idioma'] = trim($arrUsuario[0]['cl_idioma_definida']);	
                    $aux = explode('_',$_SESSION['idioma']);
                    $_SESSION['language'] = $aux[0];
			
                    //centrado de mapas
                    $_SESSION["lat"] 				= $arrUsuario[0]["pr_lat"];
                    $_SESSION["lng"] 				= $arrUsuario[0]["pr_lng"];
                    $_SESSION["zoom"] 				= $arrUsuario[0]["pr_zoom"];
                    //centrado de mapas
                    $_SESSION["mailAlerta"] 		= $arrUsuario[0]["us_mailContacto"];
			
                    $aux = explode("@",$arrUsuario[0]["us_nombreUsuario"]);
                    $_SESSION["nombreUsuarioCorto"] = $aux[0];
                    $_SESSION["ultimoAcceso"] 		= $arrUsuario[0]["us_ultimo_acceso"];
		
                    // Cargo los per
                    $_SESSION["accesoMobile"] = $arrUsuario[0]["us_accesoMobile"];
			
                    require_once 'includes/navbar_permisos.php';
                    $_SESSION['paginaDefecto'] = $paginaDefecto;
			
                    //-- Validar IP Autorizada --//	
                    if(@trim($arrUsuario[0]["us_ipAutorizada"]) != ''){
			$arr_ip = explode(',',$arrUsuario[0]["us_ipAutorizada"]);
			if(!in_array($ip,$arr_ip)){
                            $log->insertLog($ip, "Ingreso con IP no autorizada desde " . ObtenerNavegador($user_agent), $arrDatos["usuario"], $arrDatos["pass_unHashed"], 0);
                            return 5;
                    	}
                    }
                    //-- --//	
			
                    $log->insertLog($ip, ObtenerNavegador($user_agent), $arrDatos["usuario"], null, 1);
                    return 1;
		}
		else{
                    $log->insertLog($ip, ObtenerNavegador($user_agent), $arrDatos["usuario"], $arrDatos["pass_unHashed"], 0);
                    return 0;
		}
	}
}




