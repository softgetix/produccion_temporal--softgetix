<?php
class TIPOMOVIL {
    const CELLPHONE = 1;
    const CAR = 2;
    const TRUCK = 3;
    const TAG_PERSONA = 5;
    const BOX = 6;
	const TAG_MOVIL = 7;
	const SEMI = 4;
    //const SATELITAL = 7;
	//const TOKEN = 5;
	
    static function getTipoMoviles()
    {
        return array(
            TIPOMOVIL::CELLPHONE,
            TIPOMOVIL::CAR,
            TIPOMOVIL::TRUCK,
            TIPOMOVIL::TAG_PERSONA,
            TIPOMOVIL::BOX,
			TIPOMOVIL::TAG_MOVIL,
			TIPOMOVIL::SEMI,
        );
    }
}


function getDataMovil($movil){
	
	$arrGrayedEvents = array(
    	980, // Falta de reporte
    	987, // Falta de reporte de mas de 24hs
    	//999, // Evento no definido
	);

	$bEncendido = false;
	$bEsCelular = false;
	$flagEnvioMailGrupo	= 0;
	$mostrarIconoMail = 0;
	$img = "";
	$tipopto = "";
			
		//-- Defino Icono del Vehiculo --//
		if(isset($movil["mo_id_tipo_movil"])){
			switch ($movil["mo_id_tipo_movil"]){
				case TIPOMOVIL::CELLPHONE:
					$bEsCelular = true;
					$img = "1_.png";
					if($movil["dg_velocidad"] > 0){$bEncendido = true;}
					else{$bEncendido = false;}
					$tipopto = 'cellphone';
				break;
				case TIPOMOVIL::TRUCK:
					$bEncendido = getEstadoMotor($movil);
					if($movil["curso"] < 360 && $movil["curso"] >= 180){$img = "1.png";	}
					elseif($movil["curso"] >= 0 && $movil["curso"] < 180){$img = "2.png";}
					$tipopto = 'truck';
				break;
				case TIPOMOVIL::CAR:
					$bEncendido = getEstadoMotor($movil);
					if($movil["curso"] < 360 && $movil["curso"] >= 180){$img = "car1.png";}
					elseif($movil["curso"] >= 0 && $movil["curso"] < 180){$img = "car2.png";}
					$tipopto = 'car';
				break;
				case TIPOMOVIL::SEMI:
					$bEncendido = getEstadoMotor($movil);
					if($movil["curso"] < 360 && $movil["curso"] >= 180){$img = "semi1.png";}
					elseif($movil["curso"] >= 0 && $movil["curso"] < 180){$img = "semi2.png";}
					$tipopto = 'semi';
				break;
				case TIPOMOVIL::TAG_PERSONA:
				case TIPOMOVIL::TAG_MOVIL:
					$img = "usb.png";
					$tipopto = 'token';
				break;
				case TIPOMOVIL::BOX:
					$img = "package_32x32.png";
					$tipopto = 'box';
				break;
			}
		}
	
		if(in_array($movil['dr_valor'], $arrGrayedEvents)){//evento falta de reporte y evento falta de reporte mas de 24 hs
			$img = 'gray_'.$img;
		}
				
		//-- Defino carpeta del Icono del Vehiculo --//
		if($movil["flagEnvioMail"] == 1){
			$flagEnvioMailGrupo = 1;
			$mostrarIconoMail = 1;
					
			if($bEncendido){
				$carpetaImagen = "verdeMail";}
				else{
					$carpetaImagen = "rojoMail";}
		}
		else{
			if($bEncendido){
				$carpetaImagen = "verde";}
			else{
				$carpetaImagen = "rojo";}
		}
				
		if($movil['mo_id_tipo_movil'] == 6){
			$carpetaImagen = "misc";
		}
	
	$arr['bEncendido'] = $bEncendido;
	$arr['bEsCelular'] = $bEsCelular;
	$arr['img'] = $img;
	$arr['tipopto'] = $tipopto;
	$arr['flagEnvioMailGrupo'] = $flagEnvioMailGrupo;
	$arr['mostrarIconoMail'] = $mostrarIconoMail;
	$arr['carpetaImagen'] = $carpetaImagen;
	return $arr;
}