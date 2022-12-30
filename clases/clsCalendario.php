<?php
class Calendario{

	public function Calendario(){
		@session_start();
		$this->lang = $_SESSION['language'];
		if($this->lang == 'en'){
			$this->dias = array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
			$this->dias_sub = array('M','T','W','T','F','S','S');
			$this->meses = array('January','February','March','April','May','June','July','August','September','October','November','December');
		}
		elseif($this->lang == 'br'){
			$this->dias = array('Segunda Feira','Terça Feira','Quarta Feira','Quinta Feira','Sexa Feira','Sábado','Domingo');
			$this->dias_sub = array('S','T','Q','Q','S','S','D');
			$this->meses = array('Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dizembro');
		}
		else{
			$this->dias = array('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo');
			$this->dias_sub = array('L','M','M','J','V','S','D');
			$this->meses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
		}
	}
	
	public function getFormatoFecha($fecha){
		$dateServer = strtotime($fecha);
		if($this->lang == 'en'){
			$orden['st'] = array('1','11','21','31');
			$orden['nd'] = array('2','12','22');
			$orden['rd'] = array('3','13','23');
			$dia = (int)date('d',$dateServer);
			$order = in_array($dia,$orden['st'])?'st':(in_array($dia,$orden['nd'])?'nd':(in_array($dia,$orden['rd'])?'rd':'th'));
			
			$fecha = $this->meses[(date('m',$dateServer))-1];
			$fecha.= ', ';
			$fecha.= date('d',$dateServer).$order;
			$fecha.= ' ';
			$fecha.= date('Y h:i a',$dateServer);
			
		}
		elseif($this->lang == 'br'){
			$fecha = $this->dias[(date('N',$dateServer))-1];
			$fecha.= ', ';
			$fecha.= date('d',$dateServer);
			$fecha.= ' de ';
			$fecha.= $this->meses[(date('m',$dateServer))-1];
			$fecha.= ' de ';
			$fecha.= date('Y H:i',$dateServer).'hs';
			}
		else{
			$fecha = $this->dias[(date('N',$dateServer))-1];
			$fecha.= ', ';
			$fecha.= date('d',$dateServer);
			$fecha.= ' de ';
			$fecha.= $this->meses[(date('m',$dateServer))-1];
			$fecha.= ' del ';
			$fecha.= date('Y H:i',$dateServer).'hs';
		}
		return $fecha;
	}
	
	public function getCalendario($ide, $mes, $anio, $contenido){
		$columna = 1;
		$timestamp = mktime(0,0,0,$mes,1,$anio);
		$dias_hasta_el_1 = date("w",$timestamp);
		$dias_del_mes = date("t",$timestamp);
	
		$this->mes_actual = $this->meses[$mes-1];  
				
		$tabla = '';
		$tabla .= '<table class="calendar" cellpadding="0" cellspacing="0">';
	
		//-- Primera fila con los días de la semana --//
		$tabla .= '<tr class="thead">
				<td>'.$this->dias_sub[6].'</td>
				<td>'.$this->dias_sub[0].'</td>
				<td>'.$this->dias_sub[1].'</td>
				<td>'.$this->dias_sub[2].'</td>
				<td>'.$this->dias_sub[3].'</td>
				<td>'.$this->dias_sub[4].'</td>
				<td>'.$this->dias_sub[5].'</td>
			  </tr>';
		//-- --//
	
		$tabla .= '<tr>';
	
		for ($i=0;$i<$dias_hasta_el_1;$i++){//- Arma los campos vacios al inicio
			$tabla .= '<td><div class="relative"></div></td>';
			$columna++;
		}
	
		for($i=1;$i<=$dias_del_mes;$i++){
			$ide = str_replace('.','',$ide);
			$ide = str_replace(' ','-',$ide);
			
			$tabla .= '<td id="'.$ide.'-'.$i.$mes.$anio.'" class="'.$contenido[(int)$i][(int)$mes][$anio]['class'].'">';
			$tabla .= '<div class="relative '.(empty($contenido[(int)$i][(int)$mes][$anio]['contenido'])?'empty':'').'">';
			if(!$this->define_day){
				$actual = '';
				if((int)getFechaServer('d') == (int)$i  && (int)getFechaServer('m') == (int)$mes && (int)getFechaServer('Y') == (int)$anio){$actual = 'fecha_actual';}
				$tabla .= '<span class="fecha '.$actual.'">'.$i.'</span>';
			}
			$tabla .= $contenido[(int)$i][(int)$mes][$anio]['contenido'];
			$tabla .= '</div>';
			$tabla .= '</td>';// contenido del dia --
		
			if($columna==7){
				$columna = 1;
				$tabla .= '</tr><tr>';
			}else{
				$columna++;
			}
		}
		
		if($columna > 1){
			for ($i=0;$i<=(7-$columna);$i++){//- Arma los campos vacios al final
				$tabla .= '<td><div class="relative"></div></td>';			
			}
		}
	
		$tabla .= '</tr>';
		$tabla .= '</table>';
		
		return $tabla; 
	}
}
?>
