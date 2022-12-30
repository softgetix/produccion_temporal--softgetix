<?php
class Informes extends SqlServer{
	
	function __construct($objSQLServer, $downloadFile = false){
		$this->objSQL = $objSQLServer;
		$this->downloadFile = $downloadFile;
		$this->nameFile = 'name-default';
	}
	
	function getInformesAEnviar($filtro){
		$strSQL = " SELECT in_id, CONVERT(TEXT,in_consulta) AS in_consulta, in_subject, in_mensaje, in_adjunto, in_adjunto_name,in_enviar_a_txt, in_enviar_a_us_id, in_enviar_copia_a, in_guardar_copia, in_cl_id_agente";
		$strSQL.= " FROM tbl_informes WITH(NOLOCK) ";
		$strSQL.= " WHERE in_borrado = 0 ";
		
		if($filtro['id_test_envio']){
			$strSQL.= " AND in_id = ".(int)$filtro['id_test_envio'];	
		}
		else{
			$strSQL.= " AND in_estado = 1 AND in_ite_id = ".(int)$filtro['tipo_envio']." AND in_hora_envio = '".$filtro['hora_envio']."'";
		}
		$objRes = $this->objSQL->dbQuery($strSQL);	
		$arrRows = $this->objSQL->dbGetAllRows($objRes,3);
		return $arrRows;
	}
	
	
	function limpiarConsulta($strSQL){
		$strSQL = str_replace('&#039',"'",$strSQL);
		$strSQL = str_replace('&#39',"'",$strSQL);
		$strSQL = str_replace('&lt;',"<",$strSQL);
		$strSQL = str_replace('&gt;',">",$strSQL);	
		return $strSQL;
	}
	
	function ejecutarConsulta($strSQL){
		$strSQL = $this->limpiarConsulta($strSQL);
		
		$objRes = $this->objSQL->dbQuery($strSQL);	
		$arrRows = $this->objSQL->dbGetAllRows($objRes,3);
		return $arrRows;
	}
	
	function generarAdjunto($arrConsulta){
		if(!empty($arrConsulta)){
			require_once 'clases/PHPExcel.php';
			$objPHPExcel = new PHPExcel();
		
			$encabezados = array_keys($arrConsulta[0]);
			
			$objPHPExcel->getProperties()
				->setCreator("Localizar-t")
				->setLastModifiedBy("Localizar-t")
				->setTitle('Informes')
				->setSubject('Informes')
				->setDescription('Informes')
				->setKeywords("Excel Office 2007 openxml php")
				->setCategory("Localizar-t");
			
			foreach($encabezados as $k => $item){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($this->getABC($k).'1', encode($item));
				$arralCol = array($this->getABC($k));
				$objPHPExcel->setFormatoRows($arralCol);
				//$alingCenterCol = array('A','E','F','G','H','L','M','N','O');
				//$objPHPExcel->alignCenter($alingCenterCol);
			}
			
			$i = 2;
			foreach($arrConsulta as $row){
				$k = 0;
				foreach($row as $item){
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($this->getABC($k).$i, encode($item));
					$k++;
				}
				$i++;
			}
			
			$this->nameFile.= date('dmY').'.xlsx';
			$objPHPExcel->setActiveSheetIndex(0);	
			if($this->downloadFile){
				if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off'); // required for IE
				header('Content-Type: application/force-download');
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment;filename="'.$this->nameFile.'"');
				header('Cache-Control: max-age=0');
				header('Content-Transfer-Encoding: binary');
				header('Accept-Ranges: bytes');
				header('Pragma: private');
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				$objWriter->save('php://output');
				//return true;
			}
			else{
				$this->nameFile = '../emailer/adjuntos/'.$this->nameFile;
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				$objWriter->save($this->nameFile);
				return true;
			}
			return false;
		}
	}
	
	function getABC($i){
		$arrayABC = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');	
		if($i > count($arrayABC) - 1){
			return $arrayABC[floor($i/count($arrayABC)) - 1].$arrayABC[$i-(floor($i/count($arrayABC)) * count($arrayABC))];
		}
		else{
			 return $arrayABC[$i];
		}
	}
	
	function obtenerUsuariosEnvio($txtIdUsuarios){
		$strSQL = " SELECT us_id, us_nombreUsuario, us_nombre, us_apellido FROM tbl_usuarios WITH(NOLOCK) WHERE us_id IN (".$txtIdUsuarios.") AND us_borrado = 0";
		$objRes = $this->objSQL->dbQuery($strSQL);	
		$arrRows = $this->objSQL->dbGetAllRows($objRes,3);
		return $arrRows;
	}
	
	function generarEnvio($subject, $message, $email, $adjunto = false){
		$message = str_replace('&lt;','<',$message);
		$message = str_replace('&gt;','>',$message);
		
		$styleTXT = 'font-size:10.0pt;font-family:Arial,sans-serif;color:#404040;';
		$message = str_replace('<p>','<p style="line-height:12pt;'.$styleTXT.'">',$message);
		$message = str_replace('<strong>','<strong style="line-height:12pt; '.$styleTXT.'">',$message);
		
		//-- Enviar Mail --//
		$mensaje = '
				<div>
					<p style="line-height:12pt"><span style="'.$styleTXT.'">Hola!</span></p>
				</div>
				<div>
					<p style="line-height:12pt"><span style="'.$styleTXT.'">'.$message.'</span></p>
				</div>
				<br /><br />
				<div>
					<p style="line-height:18.75pt">
						<span style="'.$styleTXT.'">
							Preguntas? Contacte a <a href="mailto:info@localizar-t.com.ar">Atenci&oacute;n al cliente</a>. <br />Este es un email autom&aacute;tico, no lo respondas.
						</span>
					</p>
				</div>
				<br />
				<div>
					<p style="line-height:18.75pt"><span style="'.$styleTXT.'">Muchas gracias,<br><b>El Equipo de Localizar-T</b></span></p>
				</div>
		';
			
		if($email['cc']){
			global $objSQLServer;
			require_once ('clases/clsEmailer.php');
			$objEmailer = new Emailer($objSQLServer);
						
			$emailerMsg['asunto'] = decode($subject);
			$emailerMsg['contenido'] = decode($objEmailer->getContenidoHTML($mensaje));
			
			
			$id_contenido = $objEmailer->setContenidoEmailer($emailerMsg);
			if($id_contenido){
				if(!empty($adjunto)){
					$objEmailer->setAdjuntos($id_contenido, '../'.$adjunto);
				}
					
				$emailerInfo['id_contenido'] = $id_contenido;
				//$emailerInfo['remitente_mail'] = $arrMail['remitente'];
				//$emailerInfo['remitente_name'] = $arrMail['nombre_remitente'];
				$emailerInfo['responder_a'] = 'no-reply@localizar-t.com.ar';
				$emailerInfo['prioridad'] = 5;	
					
				$ban = 0;
				foreach($email['cc'] as $addCC){
					$emailerInfo['id_usuario'] = isset($addCC['ID'])?(int)$addCC['ID']:'NULL';
					$emailerInfo['destinatario_mail'] = $addCC['mail'];
					$emailerInfo['destinatario_name'] = decode($addCC['name']);
					$idEnvio = $objEmailer->setInfoEmailer($emailerInfo);
					
					if(!$ban){
						$ban = 1; 
						if(isset($email['bcc'])){
							if($email['bcc']){
								foreach($email['bcc'] as $addBCC){
									$objEmailer->setMailBCC($idEnvio, $addBCC['mail'], $addBCC['name']);
								}
							}
						}
					}
				}
			}
		}
		//-- --//	
	}
	
	
	function setLog($txt, $ID, $tipo){
		$txt = $this->limpiarConsulta($txt);
		
		$dir = 'log/'.date('Ym');
		$arch = $dir.'/'.$tipo.'_'.date('d').'.txt';
		if(!file_exists($dir)){
			mkdir($dir, 0777, true);
		}

		$fechaLOG=date("d-m-y H:i:s")."  ==> ".$ID."\r\n====================================\r\n";
		$log = file_exists($arch)?fopen($arch,"a"):fopen($arch,"w");
		fwrite($log,"\r\n".$fechaLOG.$txt."\r\n====================================\r\n\r\n");
		fclose($log);	
	}
	
}
?>
