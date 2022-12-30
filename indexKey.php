<?php 
$seccion='index';
include 'includes/funciones.php';
require_once('controladores/loginControlador.php');

if(isset($_POST["txtUsuario"])){
	$mensaje.= checkString(trim($_POST["txtUsuario"]), 3, 50,"usuario",1);
	if($mensaje)$mensaje.="<br/>";
	$mensaje.= checkString(trim($_POST["txtPassword"]), 3, 50,"usuario",1);

	if(!$mensaje){
		$arrDatos["usuario"] = escapear_string(trim($_POST["txtUsuario"]));
		$arrDatos["pass"] = escapear_string(trim($_POST["txtPassword"]));
	}else{
		$strError = $mensaje;
	}
}

if(isset($_SESSION["nombreUsuario"])){
	if (isset($_SESSION["seccion"]) && isset($_SESSION["accion"])) {
		$pagina = $_SESSION["seccion"];
	} else {
        $pagina = $_SESSION['paginaDefecto'];
	}

	redireccionarPagina($_SESSION['DIRCONFIG'].'/boot.php?c='.$pagina,true,false);
	exit;
}
elseif(isset($arrDatos["usuario"]) && isset($arrDatos["pass"]) && isset($_POST["txtUsuario"])){
	//DEVUELVE TRUE SI VALIDA. EN CASO CONTRARIO DEVUELVE UN CODIGO DE ERROR.
	$login = validarUsuario($arrDatos);
	switch($login){
		case 0: $strError = encode('Usuario no registrados');break;
		case 1: {
			if (isset($_SESSION["seccion"]) && isset($_SESSION["accion"])) {
				$pagina = $_SESSION["seccion"];
			} else {
		        $pagina = $_SESSION['paginaDefecto'];
			}
			
			redireccionarPagina($_SESSION['DIRCONFIG'].'/boot.php?c='.$pagina,true,false);
			break;
		}
		case 2: $strError = encode('Usuario inválido');break;
		case 3: $strError = encode('Contraseña Invalida');break;
		case 4: $strError = encode('HardKey Inválida');break;
	}
}

require_once 'clases/clsHardKey.php';
$objHK=new HardKey();
$StrToSendHex=$objHK->enviarBuscarLlave();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>Localizar-T</title>
		<link href="css/estilosLogin.css" rel="stylesheet" type="text/css" />
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
		<script type="text/javascript" src="js/jquery/jquery-1.6.1.min.js"></script>
		<script type="text/javascript" src="js/jquery.tools.js"></script>
		<script type="text/javascript" src="js/check.js"></script>
		<script type="text/javascript" src="js/ajax.js"></script>
		<script type="text/javascript" src="js/funciones.js"></script>
		<script type="text/javascript" src="js/login.js"></script>
		<script type="text/javascript" src="js/PluginDetect.js"></script>
	</head>
	<body>
		<?php 
			$html = '';
			$arrInlineStyle = array(
				'position: fixed',
				'left: 0px',
				'top: 0px',
				'right: 0px',
				'bottom: 0px',
				'opacity: 0.15',
				'filter: alpha(opacity = 15)',
				'text-align: center',
				'z-index: -1',
				'background-color: #666',
				'',
			);

			$html .= '<div style="'.implode(';', $arrInlineStyle).'">';
			$html .= '	<img style="height: 100%;" src="imagenes/final.jpeg" />';
			$html .= '</div>';
			echo $html;

			if(isset($_POST['referencia_error'])){
				echo '<div id="warning">';
					echo '<table align="center">';
						echo '<tr>';
							echo '<td align="center">';
								echo '<img src="imagenes/warning.png"></img>';
							echo '</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td align="center">';
								echo $_POST['referencia_error'];
							echo '</td>';
						echo '</tr>';
					echo '</table>';
				echo '</div>';
			}
		?>
		<div id="wrapper">
			<div id="contenedor">
				<!--<div id="logo"></div>-->
				<div id="formulario">
					<form id="frmLogin" name="frmLogin" action="" method="post">
						<p id="mensaje_key" style="display:none">El ingreso al sistema requiere el uso de una llave HardKey.<br/>Inserte su llave HardKey e intente de nuevo</p>
						<table align="center">
							<tr>
								<td width="110" class="tdInfo"><div id="lblUsuario"><?=$lang->system->usuario?></div></td>
								<td width=""><input type="text" name="txtUsuario" id="txtUsuario" class="text" value="<?=($arrDatos["usuario"])? $arrDatos["usuario"]:"";?>" style="width:250px;" /></td>
							</tr>
							<tr>
								<td colspan="2" align="right"><span style="color:#666666;font-size:10px;" id="lblEjemploUsuario"><?=$lang->system->usuario."@".$lang->system->empresa;?></span></td>
                            </tr>
                            <tr>
                                <td class="tdInfo"><div id="lblPass"><?=$lang->system->password?></div></td>
                                <td><input type="password" name="txtPassword" id="txtPassword" class="text" value="<?=($arrDatos["pass"])? $arrDatos["pass"]:"";?>" style="width:250px;"/></td>
                            </tr>
                            <tr>
                                <td colspan="2" align="right">
									<div id="error"><?=($strError) ? $strError:"";?></div>
									<input type="button" name="btnAceptar" id="btnAceptar" value="<?=$lang->botonera->login?>" onclick="javascript:getKey()" />
                                    
							</td>
                            </tr>
                        </table>
					</form>
				</div>
			</div>
		</div>
      
		<applet name="Applet" id="Applet" code="HardKeyApplet.ElApplet" archive="AppletMio.jar" style="width: 0px; height: 0px; visibility: hidden" width="0" height="0" ></applet>
	</body>
</html>

<script type="text/javascript">
	var ap=null;
	jQuery(document).ready(function($){
		inicializar();

		var Java0Status = PluginDetect.isMinVersion('Java', '0', 'getJavaInfo.jar');
		var Java0Installed = Java0Status >=0 ? true : false;
		
		if(Java0Installed){
			ap = document.getElementById('Applet');
			if (!ap){
				$('#formulario').empty().append('<p>Ha ocurrido un error. Por favor cierre el navegador y vuelva a intentarlo. Si el problema persiste, consulte con el administrador.<\/p>');
			}
			else{
				if(typeof(ap.Estado) == 'undefined'){
					$('#formulario').empty().append('<p>El ingreso mediante HardKey requiere la instalaci&oacute;n de software de control. <a href="setupHK.exe">Desc&aacute;rguelo<\/a> , cierre el navegador y vuelva a intentarlo.<\/p>');
				}
			}
		}else{
			$('#formulario').empty().append('<p>El ingreso mediante HardKey requiere el uso de Java. Asegurese de tener <a href="http:\/\/www.java.com\/">una versi&oacute;n actualizada de Java<\/a> y <a href="">vuelva a intentarlo<\/a>.<\/p>');
			}

		$('#txtPassword').keypress(function(e){
			if(capturarEnter(e)) getKey();
		});
	});

	function getKey(){
		var ret=ap.EnviarComando("<?=$StrToSendHex?>");
		var returnVal;
		jQuery.post(
			'ajaxHardKey.php',
			{m:'checkBuscarLlave',s:ret},
			function(d){
				if (typeof d ==='string') d=$.parseJSON(d);
				if (d.r==='OK'){
					//llave conectada, se guardo el id en session para controles posteriores
					returnVal= true;
					validaLogin();
				}
				else{
					// quitar formulario login, ingresar la llave y volver a probar
					$('#mensaje_key').show();
					returnVal= false
				}
			},'text json'
		);
	};
</script>