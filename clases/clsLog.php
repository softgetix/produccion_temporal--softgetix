<?php
class Log {
	var $objSQLServer;

	function Log($objSQLServer) {
		$this->objSQL = $objSQLServer;
		return true;
	}
	
	function intentosFallidos($usuario){
		$strSQL = " SELECT count(lg_loginSuccess) ";
		$strSQL.= " FROM tbl_log WITH(NOLOCK) ";
		$strSQL.= " WHERE lg_userUserName = '".$usuario."' AND lg_loginSuccess = 0 AND (datediff(minute,lg_date,getdate())<10)";
		$result = $this->objSQL->dbQuery($strSQL);
		if($intentos_fallidos=$this->objSQL->dbGetRow($result)){
			return $intentos_fallidos;
		}
		return false;
	}
	
	function getLog($limit = 0) {
		if($_SESSION['idTipoEmpresa']==3){			
			$query ="SELECT TOP 100 (SELECT DATEADD(HOUR,server,lg_date) FROM zonaHoraria(NULL,".$_SESSION['idUsuario'].")) as lg_date, lg_ip, lg_userUsername, lg_userPassword, lg_loginSuccess, lg_userAgent, lg_loginSuccess
		FROM tbl_log ORDER BY lg_date DESC";	
		}
		elseif($_SESSION['idTipoEmpresa']==1){
			$query ="SELECT TOP 100 (SELECT DATEADD(HOUR,server,lg_date) FROM zonaHoraria(NULL,".$_SESSION['idUsuario'].")) as lg_date, lg_ip, lg_userUsername, lg_userPassword, lg_loginSuccess, lg_userAgent, lg_loginSuccess
		FROM tbl_log 
		WHERE lg_userUsername IN (
									select us_nombreUsuario  from tbl_usuarios WITH(NOLOCK) 
									inner join tbl_clientes WITH(NOLOCK) on us_cl_id = cl_id
									where (cl_id_distribuidor = ".$_SESSION['idEmpresa']." or cl_id = ".$_SESSION['idEmpresa'].") 
								)
		ORDER BY lg_date DESC";
		}
		else{
			$query ="SELECT TOP 10 (SELECT DATEADD(HOUR,server,lg_date) FROM zonaHoraria(NULL,".$_SESSION['idUsuario'].")) as lg_date, lg_ip, lg_userUsername, lg_userPassword, lg_loginSuccess, lg_userAgent, lg_loginSuccess 
		FROM tbl_log WITH(NOLOCK)
		WHERE lg_userUsername = '".$_SESSION['nombreUsuario']."' ORDER BY lg_date DESC";			
		}
		
		$result = $this->objSQL->dbQuery($query);
		$return->result = $this->objSQL->dbGetAllRows($result, 1);
		if($return->result){
			$return->numRows = $this->objSQL->dbNumRows($result);
			return $return;
		}
		return false;
	}
	
	function insertLog($ip, $user_agent, $usuario_username, $usuario_password, $login_success) {
		
		if($usuario_password != null) {
			$usuario_password = "'$usuario_password'";
		}else{
			$usuario_password = 'null';
		}
		
		$query = " INSERT INTO tbl_log(lg_ip, lg_date, lg_userAgent, lg_userUsername, lg_userPassword, lg_loginSuccess, lg_url) ";
		$query.= " VALUES('$ip', GETDATE(), '$user_agent', '$usuario_username', $usuario_password, ".(int)$login_success.",'".$_SERVER['HTTP_REFERER']." [".$_SERVER['SERVER_ADDR']."]')";
        $this->objSQL->dbQuery($query);
	}
}
?>