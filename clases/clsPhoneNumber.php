<?php
abstract class PhoneNumber{

	protected $local_number;
	protected $guest_number;
	protected $phoneCode;
	protected $phoneNumber;

	protected function __construct($local_number, $guest_number = null){
		$this->phoneNumber = array('local_number_e164'=>null, 'local_number_national'=>null, 'guest_number_e164'=>null, 'guest_number_national'=>null);
		$this->setLocalNumber($local_number);
		$this->setGuestNumber($guest_number);
	}
	
	protected function getLocalNumber(){
		return $this->local_number;
	}

	protected function getGuestNumber(){
		return $this->guest_number;
	}

	public function setLocalNumber($number){
		$this->local_number = $number;
	}

	public function setGuestNumber($number){
		// 20072018 sE AGREGO ESTO PORQUE ANDROID PERMITE GUARDAR CARACTERES ESPECIALES
		$number = str_replace('-','',$number);
		$number = str_replace(' ','',$number);
		$number = str_replace(';','',$number);
		$number = str_replace('(','',$number);
		$number = str_replace(')','',$number);		
		// 20072018 
		$this->guest_number = $number;
	}

	protected function setLocalNumberE164($number){
		$this->phoneNumber['local_number_e164'] = $number;
	}

	protected function setLocalNumberNational($number){
		$this->phoneNumber['local_number_national'] = $number;
	}

	protected function setGuestNumberE164($number){
		$this->phoneNumber['guest_number_e164'] = $number;
	}

	protected function setGuestNumberNational($number){
		$this->phoneNumber['guest_number_national'] = $number;
	}

	abstract protected function validateGuestNumber();//--Numero formateado a E164.
	
	abstract protected function convertE164ToNational($numberE164);
	
	abstract protected function convertNationalToE164($numberNational);

	/**
	 * Obtiene Código de Area
	 */
	abstract protected function searchAC($number);

	/**
	 * Obtiene Número Telefónico
	 */
	abstract protected function searchLN($number);		
}
?>
