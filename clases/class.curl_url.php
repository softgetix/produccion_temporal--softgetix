<?php
class curl_url{
	
	function curl_url(){}

	function post($url,$datos,$header){
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$datos);
		
		// este seteo me sirve para q no le de bola a la alerta de que no estoy en un SSL
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
		// --
		
		if(!$datos=curl_exec($ch)){
			echo curl_error($ch);
			die();
		}
		curl_close($ch);
		
		return $datos;
	}

	function get($url, $datos = NULL, $header){
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		
		if($datos){
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($datos));
		}
	
		curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
		curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		
		// este seteo me sirve para q no le de bola a la alerta de que no estoy en un SSL
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
		// --
		
		curl_setopt($ch, CURLOPT_FILE);
	
		if(!$json=curl_exec($ch)){
			$error = curl_error($ch);
			if(!empty($error)){
				echo $error;
				die();
			}
		}
	
		curl_close($ch);
	
		$json = json_decode($json, true);
		
		return $json;
	}
}	
?>