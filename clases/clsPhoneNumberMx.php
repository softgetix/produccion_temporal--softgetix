<?php
/** Codificación México */
require ('clsPhoneNumber.php');

class PhoneNumberMx extends PhoneNumber{

	function __construct($local_number, $guest_number = null){
		parent::__construct($local_number, $guest_number);
	}

	public function validateGuestNumber(){//--Numero formateado a E164.
		
		//--Ini. Format Local Number
		if($this->searchLN($this->getLocalNumber())){	
			$this->setLocalNumber($this->searchLN($this->getLocalNumber()));
			$this->setLocalNumberNational($this->getLocalNumber());
			$this->setLocalNumberE164('52'.$this->getLocalNumber());
		}
		
		//--Ini. Format Guest Number
		$number = $this->getGuestNumber();
		$LN = $this->searchLN($number);
		if($LN){
			$this->setGuestNumberNational($LN);
			$this->setGuestNumberE164($this->convertNationalToE164($this->phoneNumber['guest_number_national']));
		}

		//--Ini. Validar que tengan 12 y 10 carácteres
		$this->phoneNumber['local_number_e164'] = (strlen($this->phoneNumber['local_number_e164']) != 12)?NULL:$this->phoneNumber['local_number_e164'];
		$this->phoneNumber['local_number_national'] = (strlen($this->phoneNumber['local_number_national']) != 10)?NULL:$this->phoneNumber['local_number_national'];
		$this->phoneNumber['guest_number_e164'] = (strlen($this->phoneNumber['guest_number_e164']) != 12)?NULL:$this->phoneNumber['guest_number_e164'];
		$this->phoneNumber['guest_number_national'] = (strlen($this->phoneNumber['guest_number_national']) != 10)?NULL:$this->phoneNumber['guest_number_national'];
		//--Fin.

		return $this->phoneNumber;
	}

	protected function convertE164ToNational($numberE164){
		$number_aux = null;
		if(substr($numberE164,0,2) == '52'){
			$number_aux = substr($numberE164,2,strlen($numberE164));
		}
		elseif(substr($numberE164,0,3) == '+52'){
			$number_aux = substr($numberE164,3,strlen($numberE164));
		}
		else{
			return false;
		}
		
		$LN = $number_aux;
		if(strlen($LN) == 10){
			return $LN;
		}

		return null;
	}

	protected function convertNationalToE164($numberNational){		
		$LN = $numberNational;
		if(strlen($LN) == 10){
			return '52'.$LN;
		}

		return null;
	}

	/**
	 * Obtiene Código de Area
	 */
	protected function searchAC($number){}

	/**
	 * Obtiene Número Telefónico
	 */
	protected function searchLN($number){
		if(substr($number,0,2) == '52' || substr($number,0,3) == '+52'){//--Se quita "52" en caso que tenga
			$number = substr($number,((substr($number,0,2) == '52')?2:3),strlen($number));
		}

		$LN = $number;
		if(strlen($LN) == 10){
			return $LN;
		}

		return false;
	}	
}
?>
