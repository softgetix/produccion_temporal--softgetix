<?php
/** Codificación Argentina */
require ('clsPhoneNumber.php');

class PhoneNumberAr extends PhoneNumber{

	function __construct($local_number, $guest_number = null){
		parent::__construct($local_number, $guest_number);

		$this->phoneCode = array(
			11
			,220,221,223,230,236,237,249,260,261,263,264,266,280,291,294,297,298,299
			,336,341,342,343,345,348,351,353,358,362,364,370,376,379,380,381,383,385,387,388
			,2202,2221,2223,2224,2225,2226,2227,2229,2241,2242,2243,2244,2245,2246,2252,2254,2255,2257,2261,2262,2264,2265,2266,2267,2268,2271,2272,2273,2274,2281,2283,2284,2285,2286,2291,2292,2296,2297
			,2302,2314,2316,2317,2320,2323,2324,2325,2326,2331,2333,2334,2335,2336,2337,2338,2342,2343,2344,2345,2346,2352,2353,2354,2355,2356,2357,2358,2392,2393,2394,2395,2396
			,2473,2474,2475,2477,2478
			,2622,2624,2625,2626,2646,2647,2648,2651,2652,2655,2656,2657,2658
			,2901,2902,2903,2920,2921,2922,2923,2924,2925,2926,2927,2928,2929,2931,2932,2933,2934,2935,2936,2940,2942,2945,2946,2948,2952,2953,2954,2962,2963,2964,2966,2972,2982,2983
			,3327,3329,3382,3385,3387,3388
			,3400,3401,3402,3404,3405,3406,3407,3408,3409,3435,3436,3437,3438,3442,3444,3445,3446,3447,3454,3455,3456,3458,3460,3462,3463,3464,3465,3466,3467,3468,3469,3471,3472,3476,3482,3483,3487,3489,3491,3492,3493,3496,3497,3498
			,3521,3522,3524,3525,3532,3533,3537,3541,3542,3543,3544,3546,3547,3548,3549,3562,3563,3564,3571,3572,3573,3574,3575,3576,3582,3583,3584,3585
			,3711,3715,3716,3718,3721,3725,3731,3734,3735,3741,3743,3751,3754,3755,3756,3757,3758,3772,3773,3774,3775,3777,3781,3782,3786
			,3821,3825,3826,3827,3832,3835,3837,3838,3841,3843,3844,3845,3846,3854,3855,3856,3857,3858,3861,3862,3863,3865,3867,3868,3869,3873,3876,3877,3878 ,3885,3886,3887,3888,3891,3892,3894
		);
	}

	public function validateGuestNumber(){//--Numero formateado a E164.
		
		//--Ini. Format Local Number
		if(substr($this->getLocalNumber(),0,3) == '549' || substr($this->getLocalNumber(),0,4) == '+549'){
			$this->setLocalNumber(substr($this->getLocalNumber(),((substr($this->getLocalNumber(),0,2) == '54')?0:1),strlen($this->getLocalNumber())));
			$this->setLocalNumberNational($this->convertE164ToNational($this->getLocalNumber()));
			$this->setLocalNumberE164($this->getLocalNumber());
		}
		
		//--Ini. Format Guest Number
		$number = $this->getGuestNumber();
		if(substr($number,0,2) == '54' || substr($number,0,3) == '+54'){//--Se quita "54" en caso que tenga
			$number = substr($number,((substr($number,0,2) == '54')?2:3),strlen($number));
		}

		if(substr($number,0,1) == '9'){//--Se quita "9" en caso que tenga
			$number = substr($number,1,strlen($number));
		}

		if(substr($number,0,1) == '0'){//--Si comienza con "0", se busca AC, y se formatea para obtener el LN
			$number = substr($number,1,strlen($number));
			$AC = $this->searchAC($number);
			$LN = $this->searchLN($number);
		}
		elseif(substr($number,0,2) == '15'){//--Si comienza con "15", se busca AC del numero local
			$number = substr($number,2,strlen($number));
			$AC = $this->searchAC(substr($this->phoneNumber['local_number_national'],1,strlen($this->phoneNumber['local_number_national'])));
			$LN = $number;
		}
		else{//--Ningún caso anterior, se tratará de formatear.
			$AC = $this->searchAC($number);
			$LN = $this->searchLN($number);
		}

		if(!$AC){//--Si no se logro optener el AC, se buscara del numero local.
			$AC = $this->searchAC($this->phoneNumber['local_number_national']);
		}
		if(!$LN){
			$LN = $number;
		}

		if($AC){
			if(substr($LN,0,2) == '15'){
				$LN = substr($LN,2,strlen($LN));
			}
			
			$this->setGuestNumberNational('0'.$AC.'15'.$LN);
			$this->setGuestNumberE164($this->convertNationalToE164($this->phoneNumber['guest_number_national']));
		}

		//--Ini. Validar que tengan 13 carácteres
		$this->phoneNumber['local_number_e164'] = (strlen($this->phoneNumber['local_number_e164']) != 13)?NULL:$this->phoneNumber['local_number_e164'];
		$this->phoneNumber['local_number_national'] = (strlen($this->phoneNumber['local_number_national']) != 13)?NULL:$this->phoneNumber['local_number_national'];
		$this->phoneNumber['guest_number_e164'] = (strlen($this->phoneNumber['guest_number_e164']) != 13)?NULL:$this->phoneNumber['guest_number_e164'];
		$this->phoneNumber['guest_number_national'] = (strlen($this->phoneNumber['guest_number_national']) != 13)?NULL:$this->phoneNumber['guest_number_national'];
		//--Fin.
		return $this->phoneNumber;
	}

	protected function convertE164ToNational($numberE164){
		$number_aux = null;
		if(substr($numberE164,0,3) == '549'){
			$number_aux = substr($numberE164,3,strlen($numberE164));
		}
		elseif(substr($numberE164,0,4) == '+549'){
			$number_aux = substr($numberE164,4,strlen($numberE164));
		}
		else{
			return null;
		}
		
		$AC = $this->searchAC($number_aux);
		if(!$AC){
			return null;
		}

		$LN = $this->searchLN($number_aux);
		if(strlen($LN) >= 6 &&  strlen($LN) <= 8){
			return '0'.$AC.'15'.$LN;
		}

		return null;
	}

	protected function convertNationalToE164($numberNational){
		
		$number_aux = null;
		if(substr($numberNational,0,1) == '0'){
			$number_aux = substr($numberNational,1,strlen($numberNational));
			
			$AC = $this->searchAC($number_aux);
			if(!$AC){
				return null;
			}

			$LN = $this->searchLN($number_aux);
			if(substr($LN,0,2) == '15'){
				$LN = substr($LN,2,strlen($LN));
			}

			return '549'.$AC.$LN;
		}

		return null;
	}

	/**
	 * Obtiene Código de Area
	 */
	protected function searchAC($number){
		
		if(substr($number,0,1) == '0'){
			$number = substr($number,1,strlen($number));
		}

		$search = true;
		$i = 2;
		do{
			$AC = substr($number,0,$i);
			if(in_array($AC,$this->phoneCode)){
				$search = false;
			}
			else{
				$i++;
			}

			if($i > 4){
				return false;
			}
		}
		while($search == true);

		return $AC;
	}

	/**
	 * Obtiene Número Telefónico
	 */
	protected function searchLN($number){
		$AC = $this->searchAC($number);
		if($AC){
			$LN = substr($number,strlen($AC),strlen($number));
			if($LN){
				return $LN;
			}
		}

		return false;
	}	
}
?>
