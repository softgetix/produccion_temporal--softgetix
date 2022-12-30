<?php 
class SqlServer {
	protected $db;
	protected $typeconnect;
 
	public function __construct(){
		$this->db = NULL;
		$this->typeconnect = 'mssql';//mysql, mssql, pdo_mssql, pdo_mysql
//$this->typeconnect = 'pdo_mssql';
		$this->rel = '';
		$this->dirConfig = NULL;
	}
	
	function dbConnect() {
		
		if(!empty($this->dirConfig)){
			$this->file_conexion = $this->dirConfig;
		}
		elseif(file_exists($this->rel.'includes/config_'.$_SESSION['DIRCONFIG'].'.php')){
			$this->file_conexion = $_SESSION['DIRCONFIG'];
		}
		else{
			$this->file_conexion = 'conexion';	
		}		
		
		$inc = $this->rel.'includes/config_'.$this->file_conexion.'.php';
		include($inc);
		
		
		$errorDB = NULL;
		switch($this->typeconnect){
			case 'pdo_mssql':
				try {
					$this->db = new PDO ("dblib:host={$dbserverRead[0]};dbname={$dbname}",$dbuser[0],$dbpassword[0]);
					return true;
				}
				catch (PDOException $e) {
					$errorDB = $e->getMessage();
				}
			break;
			case 'pdo_mysql':
				$opt = array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false);
				try {
					$this->db = new PDO("mysql:Server={$dbserverRead[0]};Database=$dbname", $dbuser[0],$dbpassword[0], $opt);
					return true;
				}
				catch (PDOException $e) {
					try {
						$this->db = new PDO("mysql:Server={$dbserverRead[1]};Database=$dbname", $dbuser[1],$dbpassword[1], $opt);
						return true;
					}
					catch (PDOException $e) {
						$errorDB = $e->getMessage();
					}
				}
			break;
			case 'mssql':
				$conecto = false;
				if($this->db = mssql_connect($dbserverRead[0], $dbuser[0], $dbpassword[0])){
					$conecto = true;
				}
				elseif($this->db = mssql_connect($dbserverRead[1], $dbuser[1], $dbpassword[1])){
					$conecto = true;
				}
				
				if($conecto){
					$auxConnect = mssql_select_db($dbname,$this->db);
					if(empty($auxConnect)){
						$errorDB = mssql_get_last_message();
						if(!empty($errorDB)){
							$this->setErrorLog("Error setting reading database to: $dbname \r\nMessage DB: ".$errorDB);
						}
					}
					return true;
				}
				else{
					$errorDB = mssql_get_last_message();
				}
			break;
			case 'mysql':
				$conecto = false;
				if($this->db = mysql_connect($dbserverRead[0], $dbuser[0], $dbpassword[0])){
					$conecto = true;
				}
				elseif($this->db = mysql_connect($dbserverRead[1], $dbuser[1], $dbpassword[1])){
					$conecto = true;
				}
				
				if($conecto){
					$auxConnect = mysql_select_db($dbname, $this->db);
					if(empty($auxConnect)){
						$errorDB = mysql_error();
						if(!empty($errorDB)){
							$this->setErrorLog("Error setting reading database to: $dbname \r\nMessage DB: ".$errorDB);
						}
					}
					return true;
				}
				else{
					$errorDB = mysql_error();
				}
			break;
		}
		
		if(!empty($errorDB)){
			$this->setErrorLog("Error setting reading database \r\nMessage DB: ".$errorDB);
		}
			
		$phpSelf = explode('/',$_SERVER['PHP_SELF']);
		$image = '/'.$phpSelf[1].'/imagenes/problema-conexion.png';;
				
		echo '<html><body>';
		echo '	<div align="center" style="position: absolute; top: 50%; left: 50%; margin-top: -200px; margin-left: -180px;">';
		echo '		<img src="'.$image.'" border="0"><br /><br />';
		echo '		<strong style="font-family:Verdana, Geneva, sans-serif; font-size:16px; color:#666; line-height:20px;">Verifica la Conexi&oacute;n</strong>';
		echo '		<p style="font-family:Verdana, Geneva, sans-serif; font-size:12px; color:#666; line-height:16px;">Parece que tienes problemas con tu conexi&oacute;n a internet.
    					<br /> Verifica la conexi&oacute;n e int&eacute;ntalo de nuevo.</p>';
		echo '	</div>';
		echo '</body></html>';
		exit;	
		return false;	
	}

	function dbDisconnect(){
		if($this->db){
			switch($this->typeconnect){
				case 'pdo_mssql':
				case 'pdo_mysql':
					$this->db = NULL;
				break;
				case 'mssql':
					mssql_close($this->db);
				break;
				case 'mysql':
					mysql_close($this->db);
				break;
			}
		}
	}

	//* @return int cantidad de rows
	function dbNumRows($result){
		if($this->db){
			switch($this->typeconnect){
				case 'pdo_mssql':
					$numrows = $result->rowCount();
					if($numrows < 0){
						try {
							$result->fetchAll(PDO::FETCH_COLUMN,0);
							$numrows = $result->rowCount();
						}
						catch (PDOException $e) {
							$numrows = 0;
						}
					}
				break;
				case 'pdo_mysql':
					/***** HAY Q PROBAR 
					$numrows = $result->rowCount();
					if($numrows < 0){
						try {
							$result->fetchAll(PDO::FETCH_COLUMN,0);
							$numrows = $result->rowCount();
						}
						catch (PDOException $e) {
							$numrows = 0;
						}
					}
					*/
				break;
				case 'mssql':
					$numrows = mssql_num_rows($result);
				break;
				case 'mysql':
					$numrows = mysql_num_rows($result);
				break;
			}
			return $numrows;
		}
		return false;
	}
	
	//* @param int $type 1=BOTH, 2=NUM, 3=ASSOC
	function dbGetRow($result, $i=0, $type=1){
		switch($this->typeconnect){
			case 'pdo_mssql':
				switch($type){
					case 2:
						$type = PDO::FETCH_NUM;
					break;
					case 3:
						$type = PDO::FETCH_ASSOC;
					break;
					default:
						$type = NULL;
					break;
				}
				try{
					$row = $result->fetch($type,PDO::FETCH_ORI_NEXT);
				}
				catch(PDOException $e){
					$row = false;
				}
			break;
			case 'pdo_mysql':
				switch($type){
					case 2:
						$type = PDO::FETCH_NUM;
					break;
					case 3:
						$type = PDO::FETCH_ASSOC;
					break;
					default:
						$type = NULL;
					break;
				}
				
				try {
					$result->setFetchMode($type);
					$result->execute();
					$row = $result->fetch();
				}
				catch (PDOException $e){
					$this->setErrorLog($e->getMessage());
					$row = false;
				}
			break;
			case 'mssql':
				switch ($type){
					case 1:
						$type = MSSQL_BOTH;
					break;
					case 2:
						$type = MSSQL_NUM;
					break;
					case 3:
						$type = MSSQL_ASSOC;
					break;
				}
				$row = mssql_fetch_array($result,$type);
				if(empty($row)){
					$row = false;		
				}
			break;
			case 'mysql':
				switch ($type){
					case 1:
						$type = MYSQL_BOTH;
					break;
					case 2:
						$type = MYSQL_NUM;
					break;
					case 3:
						$type = MYSQL_ASSOC;
					break;
				}
				$row = mysql_fetch_array($result, $type);	
			break;
		}
		return $row;
	}

	//* @param integer $type 1=BOTH, 2=NUM, 3=ASSOC
	function dbGetAllRows(&$result, $type=3){
		$res = array();
		switch($this->typeconnect){
			case 'pdo_mssql':
				switch ($type){
					case 2:
						$type = PDO::FETCH_NUM;
					break;
					case 3:
						$type = PDO::FETCH_ASSOC;
					break;
					default:
						$type = NULL;
					break;
				}
				try{
					$res = $result->fetchAll($type);
				}
				catch(PDOException $e){
					$res = false;
				}
			break;
			case 'pdo_mysql':
				switch($type){
					case 2:
						$type = PDO::FETCH_NUM;
					break;
					case 3:
						$type = PDO::FETCH_ASSOC;
					break;
					default:
						$type = NULL;
					break;
				}
				try {
					$result->setFetchMode($type);
					$result->execute();
					while($rs = $result->fetch()) {
					   array_push($res, $rs);
					}
				}
				catch (PDOException $e){
					$this->setErrorLog($e->getMessage());
					$res = false;
				}
			break;
			case 'mssql':
				switch ($type){
					case 1:
						$type=MSSQL_BOTH;
					break;
					case 2:
						$type=MSSQL_NUM;
					break;
					case 3:
						$type=MSSQL_ASSOC;
					break;
				}
				if($this->dbNumRows($result)){
					while($reg = mssql_fetch_array($result,$type)){
						array_push($res, $reg);	
					}
				}
			break;
			case 'mysql':
				switch ($type){
					case 1:
						$type = MYSQL_BOTH;
					break;
					case 2:
						$type = MYSQL_NUM;
					break;
					case 3:
						$type = MYSQL_ASSOC;
					break;
				}
				if($this->dbNumRows($result)){
					while($reg = mysql_fetch_array($result,$type)){
						array_push($res, $reg);	
					}
				}
			break;
		}	
		return $res;
	}

	//* Ejecuta un query en la base de datos
	function dbQuery($query) {
		switch($this->typeconnect){
			case 'pdo_mssql':
				$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				try {
					if(mb_detect_encoding($query, "UTF-8, ISO-8859-1") == "ISO-8859-1") $query = utf8_encode($query);
					$qr = $this->db->query("SET NOCOUNT ON; $query");
				}
				catch(PDOException $e) {
					$msg_error = $e->getMessage();
					if(!empty($msg_error)){
						$this->setErrorLog("Error executing: {$query}\r\nMessage DB: ".$msg_error);
					}
					return false;
				}
			break;
			case 'pdo_mysql':
				try {
					$qr = $this->db->prepare($query);
					$qr->execute();
				}
				catch (PDOException $e){
					$this->setErrorLog($e->getMessage());
					return false;
				}
			break;
			case 'mssql':
				$qr = mssql_query($query, $this->db);
				$this->setLogUser($query);
				if(empty($qr)){
					$msg_error = mssql_get_last_message();
					if(!empty($msg_error)){
						$this->setErrorLog("Error executing: {$query}\r\nMessage DB: ".$msg_error);
					}
					return false;
				}
			break;
			case 'mysql':
				//----- 
				$query = str_replace('WITH(NOLOCK)','',$query);
				$query = str_replace('GETDATE()','NOW()',$query);
				//-----
				$qr = mysql_query($query, $this->db);
				$this->setLogUser($query);
				if(empty($qr)){
					$msg_error = mysql_error($this->db);
					if(!empty($msg_error)){
						$this->setErrorLog("Error executing: {$query}\r\nMessage DB: ".$msg_error);
					}
					return false;
				}
			break;
		}
		return $qr;
	}

	function dbQueryInsert($params, $tabla) {
		foreach($params as $k => $item){
			$params[$k] = ($item === "" || $item === NULL)?'NULL':"'".$item."'";
		}
		
		$campos = implode(',', array_keys($params));
		$valorCampos = implode(',', $params);
		$sql = "INSERT INTO ".$tabla."(".$campos.") VALUES(".$valorCampos.")";
		if($this->dbQuery($sql)){
			return $this->dbLastInsertId();
		}
		return false;
	}
	
	function dbQueryUpdate($params, $tabla, $where) {
		foreach($params as $k => $item){
			$params[$k] = ($item === "" || $item === NULL)?'NULL':"'".$item."'";
		}
		
		$set = $coma = '';
		foreach($params as $k => $item){
			$set.= $coma.$k.'='.$item;
			$coma = ',';
		}
		$sql = "UPDATE ".$tabla." SET ".$set." WHERE ".$where;
		if($this->dbQuery($sql)){
			return true;	
		}
		return false;
	}
	
	
	//* last insert id
	function dbLastInsertId() {
		$id = 0;
		switch($this->typeconnect){
			case 'pdo_mssql':
			case 'pdo_mysql':
				$id = $this->db->lastInsertId();
			break;
			case 'mssql':
				$res = mssql_query("SELECT @@identity AS i", $this->db);
				if ($row = mssql_fetch_array($res, MSSQL_NUM)) {
					$id = $row[0];
				}
			break;
			case 'mysql':
				$id = mysql_insert_id($this->db);
			break;
		}
		
		return $id;
	}
	
	function setErrorLog($txt){
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    	elseif(isset($_SERVER ['HTTP_VIA']))  $ip = $_SERVER['HTTP_VIA'];
    	elseif(isset($_SERVER ['REMOTE_ADDR']))  $ip = $_SERVER['REMOTE_ADDR'];
    	
		$horaLog = date("H:i:s");
		$txt = "\r\n \r\n ".$horaLog." - SECCION: ".@$_GET['c']." - IP: ".@$ip."\r\n".$txt."\r\n";
		
		if(!defined('PATH_LOG_SECURE')){
			if($_SERVER['HTTP_HOST'] == 'localhost'){
				define('PATH_LOG_SECURE','/xampp/htdocs/localizart/Web_system/log/secure/web');
			}
			else{
				define('PATH_LOG_SECURE','/var/www/log/secure/web');
			}  
			
			$txt.= "\r\n No define params: ".$_SERVER['HTTP_REFERER'];
		}

		$arch = PATH_LOG_SECURE.'/sql_log_'.date("d-m-Y").'.txt';
		$log = file_exists($arch) ? fopen($arch,"a+") : fopen($arch,"w");
		fwrite($log,$txt);
		fclose($log);		
	}
	
	function setLogUser($consulta){
		$consulta = trim($consulta);
		if(
			(preg_match("/\bupdate\b/i", strtolower($consulta))
			|| preg_match("/\binsert\b/i", strtolower($consulta))
			|| preg_match("/\bdelete\b/i", strtolower($consulta))
			|| preg_match("/\btruncate\b/i", strtolower($consulta)))
			&& !preg_match("/\bus_acceso_fallido = NULL, us_cant_fallido = 0\b/i", strtolower($consulta))
			&& !preg_match("/\btbl_log\b/i", strtolower($consulta))
			&& !preg_match("/\bUPDATE tbl_usuarios SET us_ultimo_acceso = CURRENT_TIMESTAMP WHERE us_id\b/i", strtolower($consulta))
			){
				if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				elseif(isset($_SERVER ['HTTP_VIA']))  $ip = $_SERVER['HTTP_VIA'];
				elseif(isset($_SERVER ['REMOTE_ADDR']))  $ip = $_SERVER['REMOTE_ADDR'];
				
				
				if(!defined('PATH_LOG_SYSTEM')){
					if($_SERVER['HTTP_HOST'] == 'localhost'){
						define('PATH_LOG_SYSTEM','/xampp/htdocs/localizart/Web_system/log/system/web');
					}
					else{
						define('PATH_LOG_SYSTEM','/var/www/log/system/web');
					}  
					
					$consulta.= "\r\n No define params: ".$_SERVER['HTTP_REFERER'];
				}

				$dir = PATH_LOG_SYSTEM.'/'.date('Ym');
				if(!file_exists($dir)){
					mkdir($dir, 0777, true);
				}
				$arch = $dir.'/'.date('d-m-Y').'.txt';
				$log = file_exists($arch) ? fopen($arch,"a+") : fopen($arch,"w");
				
				$horaLog = date('H:i:s');
				$datosUser = '('.(int)$_SESSION['idUsuario'].') '.$_SESSION['us_apellido'].', '.$_SESSION['us_nombre'].' ==> '.$_SESSION['nombreUsuario'];
				$txt = "\r\n \r\n ".$horaLog.' - IP: '.@$ip.' - User:'.$datosUser."\r\n".$consulta."\r\n";
				
				fwrite($log,$txt);
				fclose($log);		
		}	
	}
}
