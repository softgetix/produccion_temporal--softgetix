<?php
/**
 *clase para comunicarse con el HardKey.
 *crea los strings que se pasan por js al applet y decodifica las respuestas
 *@author ein
 *@version 1.05082011
 */
class HardKey{
	private $sBox1;
	private $sBox2;
	private $sBox3;
	private $sPassword;
	private $clave1;
	private $clave2;

	//codigos de error
	const ERR_NO_ERROR	= '00000';
	const ERR_NO_KEY	= '00002';
	const ERR_BAD_PARAM	= '00004';
	//codigo de error agregado por ein, para cuando el response tiene distinto random. respuesta fuera de secuencia o forged
	const ERR_BAD_SYNC	= '90001';

	//codigos de tipo de usuario en la respuesta de BuscarLlave
	const USR_USUARIO	= '000';
	const USR_ADMIN		= '001';


	function __construct(){
		include 'includes/sbox.php';
		$this->sBox1=$sBox1;
		$this->sBox2=$sBox2;
		$this->sBox3=$sBox3;
		$this->sPassword=$sPassword;
		$this->clave1=$clave1;
		$this->clave2=$clave2;
	}
	/**
	 *recibe el comando y sus parametros y devuelve la cadena encriptada para mandar
	 *@param array $datosComando
	 *@return string
	 */
	private function crearString($datosComando){
		$hash='1234567890'; //for debug
		//$hash=(string) time();
		//$_SESSION['hkh']=$hash;  //el return no anda como corresponde
		$strToEncode= $hash.' '.$this->clave1.' '.$this->clave2.' '.implode(' ',$datosComando);
//		var_dump($strToEncode);
		return $this->encripta($strToEncode);
	}

	/**
	 *parsea la respuesta del key
	 *@param string $respuestaKey
	 *@return array ['errCode','numSerie','respuesta']
	 */
	private function leerString($respuestaKey){
		$temp=$this->desencripta($respuestaKey);
//		var_dump($temp);
/*		$respuesta=array();
		$hash=substr($temp,0,10);
		if (!isset($_SESSION['hkh']) || $_SESSION['hkh']!=$hash){
			$respuesta['errCode']=self::ERR_BAD_SYNC;
		}else{*/
			$respuesta['errCode']=substr($temp,11,5);
			$respuesta['numSerie']=substr($temp,17,8);
			$respuesta['respuesta']=substr($temp,26);
//		}
		return $respuesta;
	}

	/**
	 *encripta la cadena de comando para enviar al key. devuelve en hexa encoding
	 *@param string $buffer
	 *@return string
	 */
	private function encripta($buffer){
		$cAnterior = 0;
		$bufEnc = "";
		//FIX: el for recorre por posiciones que no existen y tira un millon de warnings
		$err=error_reporting(0);

		for( $i = 0; $i < 1024; $i++){
			$ctemp = Ord($buffer [$i/2]);
			if($ctemp < 0) $ctemp += 256;
			$ctemp = ($ctemp ^ $this->sBox1[$cAnterior]);
			for($k = 0; $k < 16; $k++){
				$pw = Ord($this->sPassword[$k]);
				if(($k % 2) == 1){
					$ctemp = $ctemp ^ ($this->sBox1[$this->sBox2[$pw]]);
					$ctemp = $this->sBox2[$ctemp];
				}else{
					$ctemp = ($ctemp ^ ($this->sBox2[$this->sBox1[$pw]]));
					$ctemp = $this->sBox1[$ctemp];
				}
			}
			$ctemp = ($ctemp ^ ($this->sBox1[$i/2]));
			$cAnterior = (($ctemp*($i%2))+($cAnterior*(($i + 1)%2)));
			$bufEnc .= sprintf("%02x", (($i%2)*($this->sBox3[$i/2]))^ $ctemp);
		}
		error_reporting($err);

		return $bufEnc;
	}

	/**
	 *desencripta la respuesta de key hexa encoded. devuelve en formato plano
	 *@param string $buffer
	 *@return string
	 */
	private function desencripta($buffer){
		$cAnterior = 0;
		$bufEnc = "";
		$a='';

		//FIX: el for recorre por posiciones que no existen y tira un millon de warnings
		$err=error_reporting(0);

		for($i=0; $i < strlen($buffer)/2; $i++)
			$a .= Chr(hexdec(substr($buffer , $i*2, 2)));
		$buffer=$a;
		unset($a);
		for($i = 0; $i < 1024; $i++){
			$ctemp = Ord($buffer[$i]);
			if ($ctemp < 0)
				$ctemp += 256;
			$ctemp = $ctemp ^ $this->sBox1[$cAnterior];
			for($k = 0; $k < 16; $k++){
				$pw = Ord($this->sPassword[$k]);
				if(($k % 2) == 1){
					$ctemp = $ctemp ^ $this->sBox1[$this->sBox2[$pw]];
					$ctemp = $this->sBox2[$ctemp];
				}
				else{
					$ctemp = $ctemp ^ $this->sBox2[$this->sBox1[$pw]];
					$ctemp = $this->sBox1[$ctemp];
				}
			}
			$ctemp = $ctemp ^ $this->sBox1[$i];
			$cAnterior = Ord($buffer[$i]);
			$bufEnc .= Chr($ctemp);
		}

		error_reporting($err);

		return $bufEnc;
	}

	/**
	 *@return string para pasar al js
	 */
	function enviarBuscarLlave(){
		$parametros=array('0000');
		return $this->crearString($parametros);
	}
	/**
	 *@param string $respuestaKey desde el post
	 *@return array ['errCode','numSerie','tipoUsuario','fechaVenc']
	 */
	function recibirBuscarLlave($respuestaKey){
		$temp=$this->leerString($respuestaKey);
		if ($temp['errCode']==self::ERR_NO_ERROR){
			$temp['tipoUsuario']=substr($temp['respuesta'],12,3);
			$temp['fechaVenc']=substr($temp['respuesta'],16,10);
			unset($temp['respuesta']);
		}
		return $temp;
	}

	/**
	 *@param string $idLlave
	 *@return string
	 */
	function enviarLeerUserPass($idLlave){
		$titulo='Ingresar PIN para continuar';
		$param=array('0002',$idLlave,strlen($titulo),$titulo);
		return $this->crearString($param);
	}
	/**
	 *@param string $respuestaKey
	 *@return array ['errCode','numSerie','usuario','password']
	 */
	function recibirLeerUserPass($respuestaKey){
		$temp=$this->leerString($respuestaKey);
		if ($temp['errCode']==self::ERR_NO_ERROR){
			$temp['usuario']=trim(substr($temp['respuesta'],12,20));
			$temp['password']=trim(substr($temp['respuesta'],33,20)); //FIX:? se deberia hacer trim aca, o se aceptan espacios al principio o final de un pass
			unset($temp['respuesta']);
		}
		return $temp;
	}

	/**
	 *@param string $idLlave
	 *@param string $nuevoUsuario maxlen(20)
	 *@param string $nuevoPass maxlen(20) no se respetan espacios al final del pass
	 *@return string
	 */
	function enviarGrabarUserPass($idLlave,$nuevoUsuario,$nuevoPass){
		$titulo='Ingresar PIN para continuar';
		$param=array('0003',$idLlave,str_pad($nuevoUsuario,20,' ',STR_PAD_RIGHT),str_pad($nuevoPass,20,' ',STR_PAD_RIGHT));
		return $this->crearString($param);
	}
	/**
	 *@param string $respuestaKey
	 *@return array ['errCode','numSerie','usuario','password']
	 */
	function recibirGrabarUserPass($respuestaKey){
		$temp=$this->leerString($respuestaKey);
		if ($temp['errCode']==self::ERR_NO_ERROR){
			$temp['usuario']=trim(substr($temp['respuesta'],12,20));
			$temp['password']=rtrim(substr($temp['respuesta'],33,20)); //FIX:? se deberia hacer trim aca, o se aceptan espacios al final de un pass
			unset($temp['respuesta']);
		}
		return $temp;
	}

	/**
	 *dummy, para usarlas hay que hacer bastante quilombo, TODO:implementar cuando se necesite
	 */
	function enviarCambiarClaves($idLlave){
	}
	// dummy
	function recibirCambiarClaves($respuestaKey){
	}

	/**
	 *@param string $idLlave
	 *@param int $inicio posicion en memoria para leer, la parte libre comienza en el byte 109
	 *@param int $largo cantidad de caracteres a leer, maximo aparente 64 o 986
	 *@return string
	 */
	function enviarLeerCadena($idLlave,$inicio,$largo){
		$titulo='Ingresar PIN para continuar';
		$param=array('0003',$idLlave,str_pad($inicio,5,'0',STR_PAD_LEFT),str_pad($largo,5,'0',STR_PAD_LEFT));
		return $this->crearString($param);
	}
	/**
	 *@param string $respuestaKey
	 *@return array ['errCode','numSerie','cadena']
	 */
	function recibirLeerCadena($respuestaKey){
		$temp=$this->leerString($respuestaKey);
		if ($temp['errCode']==self::ERR_NO_ERROR){
			$temp['cadena']=trim(substr($temp['respuesta'],12));
			unset($temp['respuesta']);
		}
		return $temp;
	}
}