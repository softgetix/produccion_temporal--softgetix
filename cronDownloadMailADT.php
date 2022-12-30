<?php 
echo "Ejecutando cron de descarga... <br>";
$rel = '';
//include_once $rel.'includes/validarSesion.php';
include_once $rel.'includes/funciones.php';
include_once $rel.'includes/conn.php';

if (! function_exists('imap_open')) {
	echo "IMAP is not configured.";exit();
}

require_once $rel.'clases/clsAbms.php';
require_once $rel.'clases/ADT.php';
$objADT = new ADT($objSQLServer);

//Please put your mail credential here in which mail will be read
//$yourEmail = "robotagenteoficialadt@gmail.com";$yourEmailPassword = "robot2021!";
 
//$yourEmail = "softgetix.test@gmail.com";$yourEmailPassword = "softgetix@test";
//$hostname = "{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX";

$yourEmail = "notificaciones@localizar-t.com.ar"; $yourEmailPassword = "Notificaciones62022!";
$hostname = "{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX";


//$_SESSION['idAgente'] = 11112; //Testing
$_SESSION['idAgente'] = 11273; //Prod

$inbox = imap_open($hostname, $yourEmail, $yourEmailPassword) or die('Cannot connect to Gmail: ' . imap_last_error());
$inbox_search = 'UNSEEN'; // 'ALL' || 'SUBJECT:"'.$subject.'"'
$receive_emails = imap_search($inbox, $inbox_search);

$result = array();
if (!empty($receive_emails)) {       
  	foreach ($receive_emails as $key => $Email1) {
		if( $Email1 > 0 ) {     
	  		$structure = imap_fetchstructure($inbox, $Email1);
	  		$attachments = array();
	  
	  		if (!empty($structure->parts)){
				for($j = 0; $j < count($structure->parts); $j++){
			
		  			if($structure->parts[$j]->ifdparameters){
						foreach($structure->parts[$j]->dparameters as $object){
							if(strtolower($object->attribute) == 'filename'){
								$attachments[$j]['is_attachment'] = true;
								$attachments[$j]['filename'] = $object->value;
							}
						}
		  			}
		  
		  			if($attachments[$j]['is_attachment']){
						$attachments[$j]['attachment'] = imap_fetchbody($inbox, $Email1, $j+1);
						
						if($structure->parts[$j]->encoding == 3){ 
							$attachments[$j]['attachment'] = base64_decode($attachments[$j]['attachment']);
						}
						elseif($structure->parts[$j]->encoding == 4){ 
							$attachments[$j]['attachment'] = quoted_printable_decode($attachments[$j]['attachment']);
						}
		  			}
				}//for
		
				// iterate through each attachment and save it 
				foreach($attachments as $k  => $attachment){
		  			if($attachment['is_attachment']){
						$filename = $attachment['name'];
						$filename = empty($filename) ? $attachment['filename'] : $filename;

						$path = 'descargas/'.$filename;
						file_put_contents($path, $attachment['attachment']);

						$aux_filename = strtolower($filename);
						$arrFiles = array('tmp_name' => $path, 'name' => $filename);
						
						if(strpos($aux_filename ,'aprobada') !== false && strpos($aux_filename ,'activa') !== false){
							$resultado = $objADT->Importar_Excel($arrFiles);
						}
						elseif(strpos($aux_filename ,'informe') !== false && strpos($aux_filename ,'pendiente') !== false){
							$resultado = $objADT->Importar_Excel2($arrFiles);
						}
						elseif(strpos($aux_filename ,'trabajable') !== false && strpos($aux_filename ,'solicitud') !== false){
							$resultado = $objADT->Importar_Excel3($arrFiles);
						}
						
						unlink($path);
			
					} //if
				} //Foreach 
	  		} //if empty($structure->parts)  
		}//if $Email > 0 
  	}//foreach
}//if (!empty($receive_emails)) { 
	 
imap_close($inbox);   

?>
 