<?
class SqlServer {
	protected $db;
    protected $sqlsrv = false;
    private $debug;
	
	public function __construct($p_debug = false) {
		$this->debug = $p_debug;
		$this->rel = '';
		$this->dirConfig = 'conexion';
	}
	
	function dbConnect() {
		
		$inc = $this->rel.'includes/config_'.$this->dirConfig.'.php';
		include($inc);
		
		$errorDB = "";
		if ($this->sqlsrv){
			try {
				$this->db = new PDO("sqlsrv:Server={$dbserverRead[0]};Database=$dbname", $dbuser[0], $dbpassword[0]);
				return true;
			}
			catch (PDOException $e) {
				try {
					$this->db = new PDO("sqlsrv:Server={$dbserverRead[1]};Database=$dbname", $dbuser[1], $dbpassword[1]);
					return true;
				}
				catch (PDOException $e) {
					$errorDB = $e->getMessage();
				}
			}
		}
		else{
			$conecto = false;
			if($this->db = mssql_connect($dbserverRead[0], $dbuser[0], $dbpassword[0])) {
				$dbserverToRead = $dbserverRead[0];
				$conecto = true;
			}
			elseif($this->db = @mssql_connect($dbserverRead[1], $dbuser[1], $dbpassword[1])){
				$dbserverToRead = $dbserverRead[1];
				$conecto = true;
			}
			
			if($conecto){
				$conect = @mssql_select_db($dbname,$this->db);
				if(empty($conect)){
					if ($this->debug) {
						die(print_r(mssql_get_last_message()));
					}
					else {
						$msg_error = mssql_get_last_message();
						if(!empty($msg_error)){
							$this->setErrorLog("Error setting reading database to: $dbname \r\nMessage DB: ".$msg_error);
						}					
					}
					return false;
				}
				return true;
			}
			else{
				$errorDB = mssql_get_last_message();
			}
		}
		
		if($this->debug){
			echo $errorDB;
		}
		else{
			$msg_error = $errorDB;
			if(!empty($msg_error)){
				$this->setErrorLog("Error setting reading database \r\nMessage DB: ".$msg_error);
			}	
			
		}
		return false;
	}
	
	function dbDisconnect(){
		if ($this->sqlsrv){
			$this->db = null;
		}
		else {
			@mssql_close($this->db);
		}
	}

	//* @return int cantidad de rows
	function dbNumRows($result) {
		if ($this->sqlsrv) {
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
		}
		else{
			if ($this->db){
				$numrows = @mssql_num_rows($result);
			}
		}
		return $numrows;
	}
	
	//* @param int $type 1=BOTH, 2=NUM, 3=ASSOC
	function dbGetRow(&$result,$i=0,$type=1) {
		
		$row = array();
		
		if ($this->sqlsrv){ 
			switch ($type){
				case 2:
					$type = PDO::FETCH_NUM;
				break;
				case 3:
					$type = PDO::FETCH_ASSOC;
				break;
				default:
					$type = null;
				break;
			}
			
			try {
				$row = $result->fetch($type,PDO::FETCH_ORI_NEXT);
			}
			catch (PDOException $e) {
				$row = false;
			}
		}
		else{
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
			
			$row = mssql_fetch_array($result,$type);
			if(empty($row))	{
				$row = false;		
			}
		}
		
		return $row;
	}

	//* @param integer $type 1=BOTH, 2=NUM, 3=ASSOC
	function dbGetAllRows(&$result, $type=3){
			
		$res = array();
		
		if ($this->sqlsrv){
			switch ($type){
				case 2:
					$type = PDO::FETCH_NUM;
				break;
				case 3:
					$type = PDO::FETCH_ASSOC;
				break;
				default:
					$type = null;
				break;
			}
			
			try {
				$res = $result->fetchAll($type);
			}
			catch (PDOException $e) {
				$res = false;
			}
		}
		else{
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
			else{
				$res = false;	
			}
		}
		
		return $res;
	}

	//* Ejecuta un query en la base de datos
	function dbQuery($query) {
		if ($this->sqlsrv) {
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			try {
				if (mb_detect_encoding($query, "UTF-8, ISO-8859-1") == "ISO-8859-1") $query = utf8_encode($query);
				$qr = $this->db->query("SET NOCOUNT ON; $query");
			}
			catch (PDOException $e) {
				if ($this->debug) {
					die(print_r($e));
				}
				else {
					$msg_error = $e->getMessage();
					if(!empty($msg_error)){
						$this->setErrorLog("Error executing: {$query}\r\nMessage DB: ".$msg_error);
					}
					
					return false;
				}
			}
		}
		else{
			$qr = @mssql_query($query, $this->db);
			if(empty($qr)){
				if ($this->debug){
					echo mssql_get_last_message();
				}
				else{
					$msg_error = mssql_get_last_message();
					if(!empty($msg_error)){
						$this->setErrorLog("Error executing: {$query}\r\nMessage DB: ".$msg_error);
					}	
				}
				return false;
			}
		}
		
		return $qr;
	}

	//* last insert id
	function dbLastInsertId() {
		$id = 0;
		if ($this->sqlsrv) {
			$id = @$this->db->lastInsertId();
		}
		else {
			$res = @mssql_query("SELECT @@identity AS i",$this->db) or die("SqlServer: Error executing query");
			if ($row = mssql_fetch_array($res, MSSQL_NUM)) {
				$id = $row[0];
			}
		}
		return $id;
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
	
	function setErrorLog($txt){
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    	elseif(isset($_SERVER ['HTTP_VIA']))  $ip = $_SERVER['HTTP_VIA'];
    	elseif(isset($_SERVER ['REMOTE_ADDR']))  $ip = $_SERVER['REMOTE_ADDR'];
    	
		$horaLog = date("H:i:s");
		$txt = "\r\n \r\n ".$horaLog." - IP: ".@$ip."\r\n".$txt."\r\n";
		
		$arch = $this->rel.'sql_logs/'.date("d-m-Y").'.txt';
		$log = file_exists($arch) ? fopen($arch,"a+") : fopen($arch,"w");
		fwrite($log,$txt);
		fclose($log);		
	}
}

