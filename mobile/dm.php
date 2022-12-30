<?

require_once "detectorDispositivo.php";
if(esAndroid()){ 	
	$url = 'https://play.google.com/store/apps/details?id=ar.com.localizart.android.report&hl=es-419';
	if($_GET['c']){
		LogIP('Android **Cliente: '.$_GET['c'].'**');
	}
	elseif($_GET['r']){
		LogIP('Android **Recomendado: '.$_GET['r'].'**');
	}
	else{
		LogIP('Android **Entidad: '.$_GET['e'].'**');	
	}
}
elseif(esBlackBerry()){ 	
	$url = 'http://appworld.blackberry.com/webstore/content/34029889';
	if($_GET['c']){
		LogIP('BlackBerry **Cliente: '.$_GET['c'].'**');
	}
	elseif($_GET['r']){
		LogIP('BlackBerry **Recomendado: '.$_GET['r'].'**');
	}
	else{
		LogIP('BlackBerry **Entidad: '.$_GET['e'].'**');	
	}
}
else{	
	$url = '';
	if($_GET['c']){
		LogIP('Disp. no Soportado **Cliente: '.$_GET['c'].'**');
	}
	if($_GET['r']){
		LogIP('Disp. no Soportado **Recomendado: '.$_GET['r'].'**');
	}
	else{
		LogIP('Disp. no Soportado **Entidad: '.$_GET['e'].'**');
	}
}

$c = (int)$_GET['c'];
if($c){
	$code_valid = substr($c,0,4);
	$id_cliente = (int)substr($c,4,strlen($c));
	
	if($id_cliente){
		$rel = '../';
		require_once($rel."clases/clsSqlServer2.php");
		$objSQLServer = new SqlServer();
		$objSQLServer->rel = $rel;
		$objSQLServer->dirConfig = 'adt';
		$objSQLServer->dbConnect();
		
		$sql = " SELECT cl_email ";
		$sql.= " FROM tbl_clientes ";
		$sql.= " WHERE cl_id = ".$id_cliente;
		$res = $objSQLServer->dbQuery($sql);
		$rs = $objSQLServer->dbGetRow($res,0,3);
		$email =  $rs['cl_email'];
		
		$objSQLServer->dbDisconnect();
			
		require_once($rel."../gateway/includes/funciones.php");
		$codigo = generarCodigoValidacion($email);
		
		if($codigo != $code_valid){
			unset($email);
		}
		
		LogStats($id_cliente, 'c');
	}
}
elseif($_GET['r']){
	LogStats($_GET['r'], 'r');	
}
elseif($_GET['e']){
	LogStats($_GET['e'], 'e');	
}
else{
	LogStats($_GET, '');		
}
?>
<html>
	<head>
	<style>
    	body, span, strong{border:0px; padding:0px; margin:0px; font-family:Verdana, Geneva, sans-serif; font-size:12px; background:#EAEAEA;}
		body{padding:6px;}
		.title strong,.title span{font-size:14px; margin:10px 0;}
		.title span{color:#0069AA;}
		ul li{list-style: none; clear:both;}
		ul li span{float:left; display:inline; background:url(option.jpg) left top no-repeat; line-height:15px; padding:2px 0 0 20px;   margin-bottom:6px;}
		.botonMobile{ background-color:rgb(36,102,183); width: 100%; display: block; text-align: center; margin-bottom: 10px; height: 30px; line-height: 30px; border-top: 2px solid rgb(80,135,202); border-bottom: 2px solid rgb(8,65,133); color: white; text-decoration: none; font-weight: bold; }
		.botonMobile:active{ color: #CCC; background-color: rgb(32,84,148); border-top: 2px solid rgb(8,65,133); border-bottom: 2px solid rgb(80,135,202); }
    </style>
    <title>ADT FindU</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, maximum-scale=1">
	<meta name="apple-mobile-web-app-capable" content="yes" />
    </head>
    <body>
        <center>
        	<div class="title">
            <strong>Bienvenido a <span>FindU</span></strong>
        	</div>
        </center>
        <br />
        
        <? if(empty($url)){?>
            <center>
                <span>Tu sistema operativo no es compatible con esta versi&oacute;n de FindU</span>
            </center>
        <? }
		else{?>
            <span>Con esta herramienta podr&aacute;s:</span>
            <ul>
                <li><span>Visualizar la ubicaci&oacute;n geogr&aacute;fica de tu familia en tiempo real.</span></li>
                <li><span>Recibir alertas autom&aacute;ticas en tu correo electr&oacute;nico.</span></li>
                <li><span>Enviar alertas a trav&eacute;s del bot&oacute;n de p&aacute;nico.</span></li>
            </ul>
            <br /><br />
            <? if(!empty($email)){?>
            <span>Activa tu cuenta utilizando tu correo electr&oacute;nico: <strong><?=$email?></strong>, ingresando una clave de 8 caracteres (entre n&uacute;meros y letras). </span>
            <br /><br />
            <span>Tu c&oacute;digo de activaci&oacute;n es: <strong><?=$codigo?></strong></span>
            <? }
            else{?>
            <span>Activa tu cuenta utilizando tu correo electr&oacute;nico habitual, ingresando una clave de 8 caracteres (entre n&uacute;meros y letras). </span>
            <br /><br />
            <span>Tu c&oacute;digo de activaci&oacute;n se te enviar&aacute; a la cuenta de correo electr&oacute;nico.</span>
            <? }?>
            <br /><br /><br />
            <a class="botonMobile" href="<?=$url?>" >Descargar</a>
		<? }?>            
    </body>
</html>

<?

//--------------------
function LogIP($msg){
	$file = '../../gateway/log/descargas/'.(date('dmY')).'.txt';
	$ipadress = $_SERVER['REMOTE_ADDR'];
	$date = date('d/m/Y h:i:s');
	//$webpage = $_SERVER['SCRIPT_NAME'];
	$browser = $_SERVER['HTTP_USER_AGENT'];

	$fp = fopen($file, 'a');
	fwrite($fp, $date.', ##'.$msg.'## - ['.$ipadress.'] ['.$browser."]\r\n");
	fclose($fp);
}

function LogStats($valor, $tipo){
	$file = '../../gateway/log/descargas/'.(
		(
			$tipo=='c')?'cliente':(
				($tipo=='e')?'entidad':(
					($tipo=='r')?'recomendado':'otro'
					)
				)
		).(date('dmY')).'.txt';
	$fp = fopen($file, 'a');
	fwrite($fp, ",".$valor."\r\n");
	fclose($fp);
}
//--------------------

?>

