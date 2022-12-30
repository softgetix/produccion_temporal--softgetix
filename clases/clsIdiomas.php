<?php
class Idioma{
	
	function __construct() {
		$this->rel = '';	
	}
	
	function getIdiomas($idioma = 'es_AR'){
		$lang = explode('_',$idioma);
		$src = $this->rel.'language/'.$lang[0].'/'.$lang[0].'_'.$lang[1].'.xml'; 
		if(file_exists($src)){
			$xml = simplexml_load_file($src);
		}
		elseif($lang[0] == 'en'){
			$xml = simplexml_load_file($this->rel.'language/en/en_EN.xml');
		}
		else{
			$xml = simplexml_load_file($this->rel.'language/es/es_AR.xml');
		}
		return $xml;
	}
	
	function getEmails($idioma = 'es_AR'){
		$lang = explode('_',$idioma);
		$src = $this->rel.'language/'.$lang[0].'/mail_clientes.xml'; 
		if(file_exists($src)){
			$xml = simplexml_load_file($src);
		}
		else{
			$xml = simplexml_load_file($this->rel.'language/es/mail_clientes.xml');
		}
		
		return $xml;
	}
	
	function getHome($lang = 'es'){
		$src = $this->rel.'language/'.$lang.'/home.xml'; 
		if(file_exists($src)){
			$xml = simplexml_load_file($src);
		}
		else{
			$xml = simplexml_load_file($this->rel.'language/es/home.xml');
		}
		
		return $xml;
	}
	
	function getEventos($idioma = 'es_AR'){
		$lang = explode('_',$idioma);
		$src = $this->rel.'language/'.$lang[0].'/eventos.xml'; 
		if(file_exists($src)){
			$xml = simplexml_load_file($src);
		}
		else{
			$xml = simplexml_load_file($this->rel.'language/es/eventos.xml');
		}
				
		$grupo = strtolower(trim($_SESSION['nombreAgente']));
		
		$xml = !empty($grupo)?(isset($xml->$grupo)?$xml->$grupo:$xml->localizart):$xml->localizart;
		return $xml;
	}
}
?>