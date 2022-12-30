<?php
if(isset($_SESSION['idUsuario'])) {
	global $objSQLServer;
	require_once 'clases/clsUsuarios.php';
	$arrDatos["usuario"] 	= $_SESSION["nombreUsuario"];
	$arrDatos["pass"] 		= $_SESSION["pass"];

	$objUsuario = new Usuario($objSQLServer);
	$arrUsuario = $objUsuario->login($arrDatos);

	if(!$arrUsuario) {
		session_destroy();
		?>
		<html>
			<body>
				<script type="text/javascript">
					var form = document.createElement('form');
					form.method = 'post';
					form.action = '/localizart/';

					var input;
					input = document.createElement('input');
					input.setAttribute('name', 'referencia_error');
					input.setAttribute('value', 'session expiro');
					input.setAttribute('type', 'hidden');
				    form.appendChild(input);

					document.body.appendChild(form);
					form.submit();
	   			</script>
	   		</body>
		</html>
		<?php
		//echo "Usuario valido";
		trigger_error('validarUsuario.php !$arrUsuario');
		die();
	}
}
?>
