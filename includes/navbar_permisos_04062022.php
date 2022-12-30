<?php
$arrMenu[0] = 'rastreo';
$arrMenu[1] = 'informes';
$arrMenu[2] = 'abmReferencias';
$arrMenu[3] = 'abmViajesDelivery'; //'abmViajes';
$arrMenu[4] = 'abmUsuarios';
$arrMenu[5] = 'abmClientes';
$arrMenu[6] = 'abmMoviles';
$arrMenu[7] = 'abmEquipos';
$arrMenu[8] = 'cuenta';
$arrMenu[9] = 'abmProtocolo';
$arrMenu[10] = 'abmMarcaEquipos';
$arrMenu[11] = 'abmModeloEquipos';
$arrMenu[12] = 'abmDefinicionesEntradas';
$arrMenu[13] = 'verificarEquipo';
//$arrMenu[14] = 'equiposLista';
$arrMenu[15] = 'abmEquiposTelemetria';
$arrMenu[16] = 'abmDefinicionReportes';
//$arrMenu[17] = 'abmTraduccionesReportes';
$arrMenu[18] = 'abmComandos';
$arrMenu[19] = 'abmGruposComandos';
$arrMenu[20] = 'envioComandos';
$arrMenu[21] = 'abmGrupoMoviles';
//$arrMenu[22] = 'abmConductores';
$arrMenu[23] = 'abmAllInOne';
$arrMenu[24] = 'abmHistorico60dias';
$arrMenu[25] = 'aMovilesUsuariosMasivo';
$arrMenu[26] = 'abmAlertas';
$arrMenu[27] = 'abmViajesDeliveryArribosPartidas';
$arrMenu[28] = 'abmInterfazGenerica';
$arrMenu[29] = 'abmReferenciasTrafico';
//$arrMenu[30] = 'abmHubs';
$arrMenu[31] = 'abmEstadisticas';
$arrMenu[32] = 'grillaAlertas';
$arrMenu[33] = 'abmProbadorDePanico';
$arrMenu[34] = 'abmLogPanico';
$arrMenu[35] = 'abmLogAltaMobile';
$arrMenu[36] = 'abmLogGateway';
//$arrMenu[37] = 'tableroMilkrun';
$arrMenu[38] = 'Helper';
$arrMenu[39] = 'wizards';
$arrMenu[40] = 'abmViajesAltaMasiva';//'abmViajesAltaMasiva';  'abmViajesDeliveryAltaMasiva' // 17102018.
$arrMenu[41] = 'abmAllInOneMoviles';
$arrMenu[42] = 'abmViajesDeliveryMapa';//'abmViajesMapa';
$arrMenu[43] = 'abmEquiposMoviles';
$arrMenu[44] = 'abmReferenciasAjustar';
$arrMenu[45] = 'abmGeneradorDeInformes';
$arrMenu[46] = 'agendaGPS';
//$arrMenu[47] = 'agendaGPSAltaMasiva';
$arrMenu[48] = 'informesPaquetizado';

$arrMenu[49] = 'cuenta_usuarios';
$arrMenu[50] = 'cuenta_clientes';
$arrMenu[51] = 'cuenta_moviles';
$arrMenu[52] = 'cuenta_conductores';
$arrMenu[53] = 'cuenta_api';
$arrMenu[54] = 'informes_historico_avanzado';
$arrMenu[55] = 'cuenta_connect_team';
$arrMenu[56] = 'cuenta_accesos_cuenta';
$arrMenu[57] = 'cuenta_payment';
$arrMenu[58] = 'timeline';
$arrMenu[59] = 'webservicesControl';
$arrMenu[60] = 'abmOctopus';
$arrMenu[61] = 'abmViajesDeliveryArribosPartidasPlanta';
$arrMenu[62] = 'abmViajesDeliveryArribosPartidasCliente';
$arrMenu[63] = 'abmViajes';
$arrMenu[64] = 'logGenerico';
$arrMenu[65] = 'notificaciones';
$arrMenu[68] = 'cuenta_moviles_semi';
$arrMenu[69] = 'availableTrip';
$arrMenu[70] = 'abmAlertasXGeocercas';

//---- ----//
$paginaDefecto = $arrMenu[8];
$arrSolapas = array();
$arrPermisos = array();

switch($_SESSION['idPerfil']){
	case 19:		##-- LOCALIZART --##
		$arrPermisos = $arrMenu;
		
		array_push($arrPermisos,$arrMenu[49]);######-NO VISIBLE EN EL MENU-(cuenta_usuarios)#####
		array_push($arrPermisos,$arrMenu[50]);######-NO VISIBLE EN EL MENU-(cuenta_clientes)#####
		array_push($arrPermisos,$arrMenu[51]);######-NO VISIBLE EN EL MENU-(cuenta_moviles)#####
		array_push($arrPermisos,$arrMenu[52]);######-NO VISIBLE EN EL MENU-(cuenta_conductores)#####
		array_push($arrPermisos,$arrMenu[53]);######-NO VISIBLE EN EL MENU-(cuenta_api)#####
		array_push($arrPermisos,$arrMenu[54]);######-NO VISIBLE EN EL MENU-(informes_historico_avanzado)#####
	
		
		$paginaDefecto = $arrMenu[8].'&solapa=usuarios';
		
		//-- Arbol Menu --//
		$arrNavBar = array(
			$arrMenu[0] => ''		//rastreo
			,$arrMenu[1] => ''		//informes

			,'administracion' => array(
				$arrMenu[23] => ''		//abmAllInOne
				,'referencias' => array(
					$arrMenu[2]	=> ''	//abmReferencias
					,$arrMenu[29] => ''	//abmReferenciasTrafico
				)
				,'moviles' => array(
					$arrMenu[21] => ''	//abmGrupoMoviles
					,$arrMenu[25] => ''	//aMovilesUsuariosMasivo
					,$arrMenu[24] => '' //abmHistorico60Dias
				)
				,'equipos' => array(
					$arrMenu[7]	=> ''	//abmEquipos
					,$arrMenu[10] => ''	//abmMarcaEquipos
					,$arrMenu[11] => ''	//abmModeloEquipos
					,$arrMenu[20] => ''	//envioComandos
					,$arrMenu[13] => ''	//verificarEquipo
					,$arrMenu[12] => ''	//abmDefinicionesEntradas
					,$arrMenu[34] => ''	//abmLogPanico
					,$arrMenu[35] => ''	//abmLogAltaMobile
					,$arrMenu[36] => ''	//abmLogGateway
				)
				,$arrMenu[26] => ''		//abmAlertas
				,$arrMenu[70] => ''		//abmAlertasXGeocercas
				,$arrMenu[32] => ''		//grillaAlertas
				,'comandos' => array(
					$arrMenu[18] => ''	//abmComandos
					,$arrMenu[19] => ''	//abmGrupoComandos
				)
				
				,$arrMenu[28] => ''		//abmInterfaszGenerica
				,$arrMenu[31] => ''		//abmEstadisticas
				,$arrMenu[45] => ''		//abmInformes
				,$arrMenu[9] => ''		//abmProtocolos
				,$arrMenu[60] => ''		//abmOctopus
				,$arrMenu[64] => ''		//logGenerico
			)
			,'logistica' => array(
				$arrMenu[3] => ''	//abmViajesDelivery
				// 17102018,$arrMenu[40] => ''	//abmViajesDeliveryAltaMasiva
				//,$arrMenu[37] => '' //tableroMilkrun
				,$arrMenu[44] => ''	//abmReferenciasAjustar
				//,$arrMenu[58] => '' //timeline
			)
			
			,'Pallet Swap' => array(
		
			'entregasforza' => ''	// vales para Pallet Swap
			,'retirosforza' => ''  // viajes de recupero Pallet Swap
			,$arrMenu[2] => ''		//Clientes y Stock
			)

			,'opciones' => array(
				$arrMenu[8] => ''	//cuenta
				,$arrMenu[38] => ''	//Helper
			)
		);	

		array_push($arrPermisos,'entregasforza');
		array_push($arrPermisos,'retirosforza');
	break;
	##-- INICIO. Premium --##
	case 5:		##-- Premium  -> Premium-Administrador --##
		$paginaDefecto = $arrMenu[3];
		
		//-- Arbol Menu --//
		$arrNavBar = array(
			'logistica' => array(
				 $arrMenu[3] => ''	//abmViajesDelivery
				,$arrMenu[61] => ''	//abmViajesDelivery
				,$arrMenu[62] => ''	//abmViajesDelivery
				
				,$arrMenu[27] => ''	//abmViajesDeliveryArribosPartidas
				,$arrMenu[44] => ''	//abmReferenciasAjustar
				// 17102018 ,$arrMenu[40] => ''	//abmViajesDeliveryAltaMasiva
			)
			//,$arrMenu[58] => 'target_blank'
			,$arrMenu[26] => ''		//abmAlertas
			,$arrMenu[0] => ''		//rastreo
			,$arrMenu[1] => ''		//informes
			,$arrMenu[2] => ''	//abmReferencias
			,$arrMenu[8] => ''
		);
		
		$arrPermisos = asignarPermisos($arrNavBar);
		//-- NO VISIBLE EN EL MENU --//
		array_push($arrPermisos,$arrMenu[42]); 	//abmViajesMoviles
		array_push($arrPermisos,$arrMenu[6]);	//abmMoviles Config Moviles desde Rastreo
		array_push($arrPermisos,$arrMenu[21]);	//abmGrupoMoviles
		array_push($arrPermisos,$arrMenu[26]);//alertas
		array_push($arrPermisos,$arrMenu[49]);	//cuenta_usuarios
		array_push($arrPermisos,$arrMenu[50]);	//cuenta_clientes
		// comentamos para que arauco administre tractor-semi array_push($arrPermisos,$arrMenu[51]);	//cuenta_moviles
		array_push($arrPermisos,$arrMenu[52]);	//cuenta_conductores
		array_push($arrPermisos,$arrMenu[53]);	//cuenta_api
		array_push($arrPermisos,$arrMenu[56]);	//cuenta_accesos_cuenta	
		array_push($arrPermisos,$arrMenu[68]);	//cuenta_moviles_semi
	break;
	case 6: ##-- Premium -> Premium-Operador --##
		$paginaDefecto = $arrMenu[3];
		
		//-- Arbol Menu --//
		$arrNavBar = array(
			$arrMenu[0] => ''		//rastreo
			,$arrMenu[1] => ''		//informes
			,$arrMenu[2] => ''	//abmReferencias
			//,$arrMenu[26] => ''	//abmAlertas
			,'logistica' => array(
				$arrMenu[3] => ''	//abmViajesDelivery
				,$arrMenu[61] => ''	//abmViajesDelivery
				,$arrMenu[62] => ''	//abmViajesDelivery
				//17102018 ,$arrMenu[40] => ''	//abmViajesDeliveryAltaMasiva
				,$arrMenu[27] => ''	//abmViajesDeliveryArribosPartidas
			)
			,$arrMenu[8] => ''
		);
		
		$arrPermisos = asignarPermisos($arrNavBar);
		//-- NO VISIBLE EN EL MENU --//
		array_push($arrPermisos,$arrMenu[42]); 	//abmViajesMoviles
		array_push($arrPermisos,$arrMenu[21]);	//abmGrupoMoviles
		// array_push($arrPermisos,$arrMenu[54]);	//informes_historico_avanzado
		array_push($arrPermisos,$arrMenu[50]);	//cuenta_clientes
		//array_push($arrPermisos,$arrMenu[51]);	//cuenta_moviles
		array_push($arrPermisos,$arrMenu[68]);	//cuenta_moviles_semi
		array_push($arrPermisos,$arrMenu[52]);	//cuenta_conductores
		array_push($arrPermisos,$arrMenu[56]);	//cuenta_accesos_cuenta	
	break;
	case 7: 	##-- Premium -> Premium-Logístico --##
		$paginaDefecto = $arrMenu[3];
		
		//-- Arbol Menu --//
		$arrNavBar = array(
			$arrMenu[0] => ''		//rastreo
			,$arrMenu[1] => ''		//informes
			,$arrMenu[2] => ''		//abmReferencias
			,$arrMenu[3] => ''		//abmViajesDelivery
			,$arrMenu[8] => ''
		);
		
		$arrPermisos = asignarPermisos($arrNavBar);
		//-- NO VISIBLE EN EL MENU --//
		array_push($arrPermisos,$arrMenu[42]); 	//abmViajesMoviles
		array_push($arrPermisos,$arrMenu[21]);	//abmGrupoMoviles
		// array_push($arrPermisos,$arrMenu[54]);	//informes_historico_avanzado
		array_push($arrPermisos,$arrMenu[50]);	//cuenta_clientes
		// array_push($arrPermisos,$arrMenu[51]);	//cuenta_moviles
		array_push($arrPermisos,$arrMenu[68]);	//cuenta_moviles_semi
		array_push($arrPermisos,$arrMenu[52]);	//cuenta_conductores
		array_push($arrPermisos,$arrMenu[56]);	//cuenta_accesos_cuenta		
	break;
	case 8: 	##-- Premium -> Transportista-Dador --##
		$paginaDefecto = $arrMenu[69];
		
		//-- Arbol Menu --//
		$arrNavBar = array(
			
			$arrMenu[0] => '' //rastreo
			,$arrMenu[1] => ''	//informes
			,$arrMenu[3] => ''	//abmViajesDelivery
			,$arrMenu[8] => ''
		);
		
		$arrPermisos = asignarPermisos($arrNavBar);
		//-- NO VISIBLE EN EL MENU --//
		array_push($arrPermisos,$arrMenu[42]); 	//abmViajesMoviles
		array_push($arrPermisos,$arrMenu[21]);	//abmGrupoMoviles
		// array_push($arrPermisos,$arrMenu[54]);	//informes_historico_avanzado
		array_push($arrPermisos,$arrMenu[68]);	//cuenta_moviles_semi
		array_push($arrPermisos,$arrMenu[52]);	//cuenta_conductores
		array_push($arrPermisos,$arrMenu[56]);	//cuenta_accesos_cuenta	
		array_push($arrPermisos,$arrMenu[69]);	//availableTrip
	break;
	case 20: 	##-- Premium -> Satelital --##
		$paginaDefecto = $arrMenu[59];
		
		//-- Arbol Menu --//
		$arrNavBar = array(
			$arrMenu[59] => '' //WebservicesControl
			,$arrMenu[8] => '' //Cuenta
		);
		
		$arrPermisos = asignarPermisos($arrNavBar);
	break;
	##-- FIN. Premium --##
	case 9:		##-- Paquetizado -> Premium-Administrador --##
		$paginaDefecto = $arrMenu[8].'&solapa=usuarios';
		
		//-- Arbol Menu --//
		$arrNavBar = array(
			//$arrMenu[0] => ''	//rastreo
			//,$arrMenu[1] => ''	//informes
			//,$arrMenu[2] => ''	//abmReferencias
			//,$arrMenu[26] => ''	//abmAlertas
			//,$arrMenu[46] => ''	//agendaGPS
			$arrMenu[8] => ''
		);
		
		$arrPermisos = asignarPermisos($arrNavBar);
		//-- NO VISIBLE EN EL MENU --//
		array_push($arrPermisos,$arrMenu[6]);  //abmMoviles - Config Moviles desde Rastreo
		array_push($arrPermisos,'abmViajesMapa');
		array_push($arrPermisos,$arrMenu[49]);	//cuenta_usuarios
		array_push($arrPermisos,$arrMenu[50]);	//cuenta_clientes
		array_push($arrPermisos,$arrMenu[51]);	//cuenta_moviles
		//array_push($arrPermisos,$arrMenu[55]);	//cuenta_api
		//array_push($arrPermisos,$arrMenu[53]);	//cuenta_connect_team
		array_push($arrPermisos,$arrMenu[56]);	//cuenta_accesos_cuenta		
		// array_push($arrPermisos,$arrMenu[57]);	//cuenta_payment
	break;
	case 10:	##-- Paquetizado -> Premium-Operador --##
		$paginaDefecto = $arrMenu[46];
		
		//-- Arbol Menu --//
		$arrNavBar = array(
			$arrMenu[0] => ''	//rastreo
			,$arrMenu[1] => ''	//informes
			,$arrMenu[2] => ''	//abmReferencias
			,$arrMenu[26] => ''	//abmAlertas
			,$arrMenu[46] => ''	//agendaGPS
			,$arrMenu[8] => ''
		);
		
		$arrPermisos = asignarPermisos($arrNavBar);
		//-- NO VISIBLE EN EL MENU --//
		array_push($arrPermisos,$arrMenu[6]);  //abmMoviles - Config Moviles desde Rastreo
		array_push($arrPermisos,'abmViajesMapa');
		array_push($arrPermisos,$arrMenu[50]);	//cuenta_clientes
		array_push($arrPermisos,$arrMenu[51]);	//cuenta_moviles
		array_push($arrPermisos,$arrMenu[56]);	//cuenta_accesos_cuenta		
	break;
	/*case 11:	##-- Paquetizado -> Premium-Logístico --##
	break;*/
	case 12:	##-- Paquetizado -> Transportista-Dador --##
		$paginaDefecto = 'homeavanti';
		
		//-- Arbol Menu --//
		$arrNavBar = array(
			//$arrMenu[0] => '' //rastreo
			//,$arrMenu[1] => ''	//informes
			//,$arrMenu[46] => ''	//agendaGPS
			'homeavanti' => ''
			,'exposiciones' => ''
			,'aglomeraciones' => ''	
			,'contactosestrechos' => ''	
			,$arrMenu[8] => ''
			
		);
		
		$arrPermisos = asignarPermisos($arrNavBar);

		//-- NO VISIBLE EN EL MENU --//
		array_push($arrPermisos,$arrMenu[51]);	//cuenta_moviles
		//array_push($arrPermisos,$arrMenu[21]);	//abmGrupoMoviles
		array_push($arrPermisos,'cuenta_recoleccion_datos');
		array_push($arrPermisos,'cuenta_auto_evaluaciones');
		array_push($arrPermisos,'cuenta_grupos_moviles');
		//array_push($arrPermisos,$arrMenu[53]);	//api
	break;
	case 13: 	##-- Básico -> Básico-Administrador --##
		$paginaDefecto = $arrMenu[0] ; //-- 'homeavanti'; --//
		
		//-- Arbol Menu --//
		$arrNavBar = array(
			$arrMenu[0] => ''		//rastreo
			,$arrMenu[1] => ''		//informes
			,$arrMenu[2] => ''		//abmReferencias
			,$arrMenu[26] => ''		//abmAlertas
			,$arrMenu[8] => ''
		);
		
		$arrPermisos = asignarPermisos($arrNavBar);
		//-- NO VISIBLE EN EL MENU --//
		array_push($arrPermisos,$arrMenu[6]);  //abmMoviles - Config Moviles desde Rastreo
		array_push($arrPermisos,$arrMenu[21]);	//abmGrupoMoviles
		array_push($arrPermisos,$arrMenu[49]);	//cuenta_usuarios
		array_push($arrPermisos,$arrMenu[50]);	//cuenta_clientes
		array_push($arrPermisos,$arrMenu[51]);	//cuenta_moviles
		array_push($arrPermisos,$arrMenu[52]);	//cuenta_conductores
		array_push($arrPermisos,$arrMenu[53]);	//cuenta_api
		//array_push($arrPermisos,$arrMenu[54]);	//informes_historico_avanzado
		//array_push($arrPermisos,$arrMenu[55]);	//cuenta_connect_team
		array_push($arrPermisos,$arrMenu[56]);	//cuenta_accesos_cuenta		
	break;
	case 14: 	##-- Básico -> Básico-Operador --##
		$paginaDefecto = $arrMenu[0];
		
		//-- Arbol Menu --//
		$arrNavBar = array(
			$arrMenu[0] => ''		//rastreo
			,$arrMenu[1] => ''		//informes
			,$arrMenu[2] => ''		//abmReferencias
			,$arrMenu[26] => ''		//abmAlertas
			,$arrMenu[8] => ''
		);
		
		$arrPermisos = asignarPermisos($arrNavBar);
		//-- NO VISIBLE EN EL MENU --//
		array_push($arrPermisos,$arrMenu[21]);	//abmGrupoMoviles
		array_push($arrPermisos,$arrMenu[50]);	//cuenta_clientes
		array_push($arrPermisos,$arrMenu[51]);	//cuenta_moviles
		array_push($arrPermisos,$arrMenu[52]);	//cuenta_conductores
		// array_push($arrPermisos,$arrMenu[54]);	//informes_historico_avanzado
		array_push($arrPermisos,$arrMenu[56]);	//cuenta_accesos_cuenta	
	break;
	case 15: 	##-- Básico -> Transportista --##
		$paginaDefecto = $arrMenu[0];
		
		//-- Arbol Menu --//
		$arrNavBar = array(
			$arrMenu[0] => ''		//rastreo
			,$arrMenu[1] => ''		//informes
			,$arrMenu[2] => ''		//abmReferencias
			,$arrMenu[26] => ''		//abmAlertas
			,$arrMenu[8] => ''
		);
		
		$arrPermisos = asignarPermisos($arrNavBar);
		//-- NO VISIBLE EN EL MENU --//
		array_push($arrPermisos,$arrMenu[21]);	//abmGrupoMoviles
		array_push($arrPermisos,$arrMenu[54]);	//informes_historico_avanzado
	break;
	case 16: 	##-- Mobile -> Final --##
		$paginaDefecto = $arrMenu[0];
		
		//-- Arbol Menu --//
		$arrNavBar = array(
			$arrMenu[0] => ''		//rastreo
			,$arrMenu[1] => ''		//informes
			,$arrMenu[26] => ''		//abmAlertas
			,$arrMenu[2] => ''		//abmReferencias
			,'opciones' => array(
				$arrMenu[8] => ''	//cuenta
				,$arrMenu[38] => ''	//Helper
			)
		);
		
		$arrPermisos = asignarPermisos($arrNavBar);
		//-- NO VISIBLE EN EL MENU --//
		array_push($arrPermisos,$arrMenu[21]);	//abmGrupoMoviles
		array_push($arrPermisos,$arrMenu[39]);	//wizard
	break;
	case 17: 	##-- Mobile -> Operador --##
		$paginaDefecto = $arrMenu[5];
		
		//-- Arbol Menu --//
		$arrNavBar = array(
			'exposiciones' => ''
			,'aglomeraciones' => ''	
			,$arrMenu[5] => ''		//Clientes
			,$arrMenu[32] => ''		//grillaAlertas
			,$arrMenu[31] => ''		//abmEstadisticas
			,'opciones' => array(
				$arrMenu[8] => ''	//cuenta
				,$arrMenu[38] => ''	//Helper
			)
		);
		
		$arrPermisos = asignarPermisos($arrNavBar);
		//-- NO VISIBLE EN EL MENU --//
		array_push($arrPermisos,$arrMenu[23]);	//abmAllInOne
		array_push($arrPermisos,$arrMenu[33]);	//abmProbadorPanico
		array_push($arrPermisos,$arrMenu[41]);	//abmAllInOneMoviles
		array_push($arrPermisos,$arrMenu[2]);	//abmReferencias
		array_push($arrPermisos,'cuenta_recoleccion_datos');
		array_push($arrPermisos,'cuenta_auto_evaluaciones');
	break;
	case 18: 	##-- Mobile -> Seguridad --##
		array_push($arrPermisos,$arrMenu[32]);
		array_push($arrPermisos,$arrMenu[8]);
		array_push($arrPermisos,$arrMenu[38]);
		
		$paginaDefecto = $arrMenu[32];
		
		//-- Arbol Menu --//
		$arrNavBar = array(
			$arrMenu[32] => ''		//grillaAlertas
			,'opciones' => array(
				$arrMenu[8] => ''	//cuenta
				,$arrMenu[38] => ''	//Helper
			)
		);
		
		$arrPermisos = asignarPermisos($arrNavBar);
	break;
	##-- INI. ADT --##
	case 22:		##-- ADT -> Administrador --##
		if(ES_MOBILE){
            $paginaDefecto = 'adttablerodecontrol';
			$arrNavBar = array(
				'adttablerodecontrol' => '' //Tablero de Control
			);
		}
		else{
            $paginaDefecto = 'adthome';
			$arrNavBar = array(
				'adthome' => '' //Home			
				,'adtbiblioteca' => '' //Biblioteca			
				,'adttablerodecontrol' => '' //Tablero de Control
				,'adtcargaarchivos' => array(
					'adtaltamasiva' => '' //Subida Archivo Excel - Aprobaciones y Activaciones
					,'adtaltamasiva2' => '' //Subida Archivo Excel - Coordinaciones y Pendientes
					,'adtaltamasiva3' => '' //Subida Archivo Excel - No trabajables
				)
				//,'adtintranet' => '' //Intranet
				,'Centro de Ayuda' => 'https://adtargentina.zendesk.com/hc/es/'
				,$arrMenu[8] => ''
			);
	
			//if($_SESSION['idUsuario'] != 7056){
			//	unset($arrNavBar['adthome']);
			//}
		}
		
		
		$arrPermisos = asignarPermisos($arrNavBar);

		if(!ES_MOBILE){
			array_push($arrPermisos,$arrMenu[49]);######-NO VISIBLE EN EL MENU-(cuenta_usuarios)#####
			array_push($arrPermisos,$arrMenu[56]);######-NO VISIBLE EN EL MENU-(cuenta_usuarios)#####
			array_push($arrPermisos,$arrMenu[50]);	//cuenta_clientes
			array_push($arrPermisos,'adt_manual_de_marca');
			array_push($arrPermisos,'adt_terminos_y_condiciones');
			array_push($arrPermisos,'adt_promociones');
			array_push($arrPermisos,'adt_lista_de_precios');
			array_push($arrPermisos,'adt_novedades');
			array_push($arrPermisos,'adt_normas');
            array_push($arrPermisos,'adt_panel');
            array_push($arrPermisos,'adt_contrato');
		}
	break;
	case 24:		##-- ADT -> Transportista I --##
		//$paginaDefecto = 'adtbiblioteca';
		$paginaDefecto = 'adthome';

		//-- Arbol Menu --//
		$arrNavBar = array(
			'adthome' => '' //Home			
			,'adtbiblioteca' => '' //Biblioteca
			//,'adtintranet' => '' //Intranet
			,'Centro de Ayuda' => 'https://adtargentina.zendesk.com/hc/es/'
			,$arrMenu[8] => ''
			
		);
		$arrPermisos = asignarPermisos($arrNavBar);
		array_push($arrPermisos,'adt_manual_de_marca');
		array_push($arrPermisos,'adt_terminos_y_condiciones');
		array_push($arrPermisos,'adt_promociones');
		array_push($arrPermisos,'adt_lista_de_precios');
		array_push($arrPermisos,'adt_novedades');
		array_push($arrPermisos,'adt_normas');
		array_push($arrPermisos,'adt_panel');
		array_push($arrPermisos,'adt_contrato');
	break;
	case 25:		##-- ADT -> Transportista II --##
		if(ES_MOBILE){
		    $paginaDefecto = 'adttablerodecontrol';
		
        	$arrNavBar = array(
				'adttablerodecontrol' => '' //Tablero de Control
			);
		}
		else{
            $paginaDefecto = 'adthome';

			$arrNavBar = array(
				'adthome' => '' //Home			
			    ,'adtbiblioteca' => '' //Biblioteca
				,'adttablerodecontrol' => '' //Tablero de Control
				//,'adtintranet' => '' //Intranet
				,'Centro de Ayuda' => 'https://adtargentina.zendesk.com/hc/es/'
				,$arrMenu[8] => ''
			);
		}

		$arrPermisos = asignarPermisos($arrNavBar);
		
		if(!ES_MOBILE){
			array_push($arrPermisos,'adt_manual_de_marca');
			array_push($arrPermisos,'adt_terminos_y_condiciones');
			array_push($arrPermisos,'adt_promociones');
			array_push($arrPermisos,'adt_lista_de_precios');
			array_push($arrPermisos,'adt_novedades');
			array_push($arrPermisos,'adt_normas');
            array_push($arrPermisos,'adt_panel');
            array_push($arrPermisos,'adt_contrato');
		}
	break;
	case 23:	##-- ADT -> Administrador Facturas --##
		$paginaDefecto = 'adtcargafacturas';
		
		//-- Arbol Menu --//
		$arrNavBar = array(
			'adtcargafacturas' => '' //Carga Faturas
			,$arrMenu[8] => ''
		);
		$arrPermisos = asignarPermisos($arrNavBar);
	break;
	##-- FIN. ADT --##


##-- INI. FORZA --##
	case 27:		##-- FORZA -> Administrador --##
		$paginaDefecto = 'homeavanti';
		
		//-- Arbol Menu --//
		$arrNavBar = array(
			
			//$arrMenu[67] => ''	//intermil tracking

			'homeavanti' => ''
			,'entregasforza' => ''	
			,'retirosforza' => ''	
			//,$arrMenu[65] => ''	//notificaciones
			,$arrMenu[2] => ''
			,$arrMenu[8] => ''
			,'Ayuda' => 'https://palletswap.zendesk.com/hc/es-ar'
		);
		
		$arrPermisos = asignarPermisos($arrNavBar);
		//-- NO VISIBLE EN EL MENU --//
		 array_push($arrPermisos,$arrMenu[49]);	//cuenta_usuarios
		 array_push($arrPermisos,$arrMenu[50]);	//cuenta_clientes
		 //array_push($arrPermisos,$arrMenu[51]);	//cuenta_moviles
		 array_push($arrPermisos,$arrMenu[52]);	//cuenta_conductores
		 array_push($arrPermisos,$arrMenu[53]);	//cuenta_api
		// array_push($arrPermisos,$arrMenu[55]);	//cuenta_connect_team
		array_push($arrPermisos,$arrMenu[56]);	//cuenta_accesos_cuenta
		//array_push($arrPermisos,$arrMenu[66]);	//cuenta_referencias
		array_push($arrPermisos,'abmViajesDeliveryMapa');
	break;
	case 28:	##-- Paquetizado -> Transportista-Dador --##
		$paginaDefecto = 'entregasforza';
		
		//-- Arbol Menu --//
		$arrNavBar = array(
			'entregasforza' => ''	
			,'retirosforza' => ''	
			,$arrMenu[8] => ''
		);
		
		$arrPermisos = asignarPermisos($arrNavBar);
		array_push($arrPermisos,'abmViajesDeliveryMapa');
	break;
	##-- FIN. FORZA --##


}

#####--- EXCEPCIONES POR EMPRESA ---####
switch($_SESSION['idEmpresa']){
	case 13156://adt		
		/*if(array_key_exists('cuenta', $arrNavBar)) {
			$keys = array_keys($arrNavBar);
			$values = array_values($arrNavBar);

			$i = array_search('cuenta', $keys, true);

			$keys[$i+1] = $keys[$i];
			$values[$i+1] = $values[$i];

			$keys[$i] = 'adtintranet';
			$values[$i] = '';

			$arrNavBar = array_combine($keys, $values);
		}
		else{
			$arrNavBar['adtintranet'] = '';
		}
		array_push($arrPermisos, 'adtintranet');*/
		$arrNavBar = array('adtintranet' => '', $arrMenu[8] => '');

		$auxUser = array(8125, 8126, 8127, 8128);
		if(in_array($_SESSION['idUsuario'], $auxUser)){
			$arrNavBar = array('adtintranet' => '', 'adtintranetadmin' => '', $arrMenu[8] => '');
		}

		$arrPermisos = asignarPermisos($arrNavBar);
		$paginaDefecto = 'adtintranet';
	break;
}

#####--- EXCEPCIONES POR AGENTE ---####
switch($_SESSION['idAgente']){
	case 10827://KCC-Arg
	
	$paginaDefecto = $arrMenu[8].'&solapa=usuarios';
	
		$arrExcepciones = array();
		$arrExcepciones[0] = 'abmIntermill';
		$arrExcepciones[1] = 'abmIntermillAP';
		$arrExcepciones[2] = 'abmCruces';
		$arrExcepciones[3] = 'abmViajesArribosPartidas';
		$arrExcepciones[4] = 'abmViajesDeliveryMapa';
		$arrExcepciones[5] = 'abmReferenciasAjustar';
		$arrExcepciones[6] = 'abmViajes';
		$arrExcepciones[7] = 'abmViajesAltaMasiva';
		
		array_push($arrPermisos,$arrExcepciones[4]);
		$arrPermisos = quitarPermisos('abmViajesDeliveryMapa');
		
		unset($arrNavBar['administracion'][$arrMenu[26]]); //No tienen Alertas
		switch($_SESSION['idPerfil']){
			case 5:
			case 6:
				$arrPermisos = quitarPermisos($arrNavBar['logistica']);
				
				$arrNavBar['logistica'] = array(
					$arrExcepciones[6] => ''	//abmViajes
					,$arrExcepciones[3] => ''	//abmViajesArribosPartidas
					,$arrExcepciones[7] => ''	//abmViajesAltaMasiva
					,$arrExcepciones[5] => ''	//abmReferenciasAjustar
				);
	
				unset($arrNavBar[$arrMenu[8]]);
				$arrNavBar['intermil'] = array(
					$arrExcepciones[0] => ''
					,$arrExcepciones[1] => ''
					,$arrExcepciones[2] => ''
				);
				$arrNavBar[$arrMenu[8]] = '';
				
				$arrPermisos = array_merge($arrPermisos, asignarPermisos($arrNavBar['logistica']));
				$arrPermisos = array_merge($arrPermisos, asignarPermisos($arrNavBar['intermil']));
			break;
			case 7:
				$arrPermisos = quitarPermisos($arrNavBar['logistica']);
				
				$arrNavBar['logistica'] = array(
					$arrExcepciones[6] => ''	//abmViajes
					,$arrExcepciones[3] => ''	//abmViajesArribosPartidas
					
				);
				
				unset($arrNavBar[$arrMenu[8]]);
				$arrNavBar['intermil'] = array(
					$arrExcepciones[0] => ''
					,$arrExcepciones[1] => ''
					,$arrExcepciones[2] => ''
				);
				$arrNavBar[$arrMenu[8]] = '';
				
				$arrPermisos = array_merge($arrPermisos, asignarPermisos($arrNavBar['logistica']));
				$arrPermisos = array_merge($arrPermisos, asignarPermisos($arrNavBar['intermil']));
			break;
			case 8:
				$arrPermisos = quitarPermisos('abmViajesDelivery');
				$keys = array_keys($arrNavBar);
				$keys[array_search($arrMenu[3],$keys) ] = 'abmViajes';
				$arrNavBar = array_combine($keys,$arrNavBar);
				$arrPermisos = asignarPermisos($arrNavBar);
			break;
		}
		
		if($_SESSION['idPerfil'] != 5){
			$paginaDefecto = $arrExcepciones[6];
		}
	break;
	case 2272://KCC-Perú
	case 156://KCC-Arg BIS 
		$arrExcepciones = array();
        //$arrExcepciones[4] = 'abmViajesDisponibilidadTransportistas';
        unset($arrNavBar[$arrMenu[26]]); //-- Se quita Alertas
            
        if($arrNavBar['logistica']){
            $arrNavBarSolapas['viajes'] = array();
            foreach($arrNavBar['logistica'] as $k => $item){
                if($k != 'abmViajesDeliveryArribosPartidas'){
                    array_push($arrNavBarSolapas['viajes'], $k);
                }
            }
        }
		
		switch($_SESSION['idPerfil']){
			case 5:
				array_push($arrPermisos,'abmViajesDeliveryAltaMasiva');

				array_push($arrPermisos,$arrMenu[63]); // 17102018
				array_push($arrNavBarSolapas['viajes'], $arrMenu[63]); // 17102018

				array_push($arrPermisos,$arrMenu[40]);
				array_push($arrNavBarSolapas['viajes'], $arrMenu[40]);

				unset($arrNavBar[$arrMenu[8]]);
				$arrNavBar['intermil'] = array(
					'abmIntermill' => ''
					,'abmIntermillAP' => ''
					//,'abmCruces' => ''
				);
				$arrNavBar[$arrMenu[8]] = '';
				
				$arrPermisos = array_merge($arrPermisos, asignarPermisos($arrNavBar['logistica']));
				$arrPermisos = array_merge($arrPermisos, asignarPermisos($arrNavBar['intermil']));
			break;
			case 6:		
			case 7:
				array_push($arrPermisos,$arrExcepciones[1]);
				array_push($arrPermisos,$arrExcepciones[2]);

				$arrNavBar['logistica'][$arrExcepciones[1]] = '';
				$arrNavBar['logistica'][$arrExcepciones[2]] = '';

				array_push($arrNavBarSolapas['viajes'], $arrExcepciones[1]);
				array_push($arrNavBarSolapas['viajes'], $arrExcepciones[2]);

				//--Ini. Se quita reastreo e informes para los perfiles que no sean Administrador --//
					if($_SESSION['idPerfil'] == 7){//--Se quita solo para Transportista...
					unset($arrNavBar[$arrMenu[0]]);
					unset($arrPermisos[array_search($arrMenu[0],$arrPermisos)]);
					unset($arrNavBar[$arrMenu[1]]);
					unset($arrPermisos[array_search($arrMenu[1],$arrPermisos)]);
					}
				//--Fin.
			break;
		}
		
        $arrAux = array();
        foreach($arrNavBar as $k => $item){
        	if($k == 'logistica'){
                $k = 'abmViajesDelivery';
			}
			$arrAux[$k] = $arrNavBar[$k]; 
		}
		unset($arrNavBar);
		$arrNavBar = $arrAux;
	break;	
	case 4835://Arauco
		$arrExcepciones = array();
        //$arrExcepciones[4] = 'abmViajesDisponibilidadTransportistas';
  //      unset($arrNavBar[$arrMenu[26]]); //-- Se quita Alertas
        unset($arrNavBar[$arrMenu[61]]); //-- Se quita Alertas

		$arrNavBarSolapas['viajes'] = array($arrMenu[3]);
        /*    
        if($arrNavBar['logistica']){
			$arrNavBarSolapas['viajes'] = array();
			foreach($arrNavBar['logistica'] as $k => $item){
                if($k != 'abmViajesDeliveryArribosPartidas'){
                    array_push($arrNavBarSolapas['viajes'], $k);
                }
            }
		}*/
		
		//--ini. Agregar Playa Virtual
		$auxBar = $arrNavBar;
		$arrNavBar = array();
		$arrNavBar['logistica'] = $auxBar['logistica'];
		unset($auxBar['logistica']);
		//--fin.
		
		switch($_SESSION['idPerfil']){
			case 5:
				$arrNavBar['abmViajesDeliveryPlayaVirtual'] = null;
				array_push($arrPermisos,'abmViajesDeliveryPlayaVirtual');
		
				array_push($arrPermisos,'abmViajesDeliveryAltaMasiva');

				/*array_push($arrPermisos,$arrMenu[63]); // 17102018
				array_push($arrNavBarSolapas['viajes'], $arrMenu[63]); // 17102018

				array_push($arrPermisos,$arrMenu[40]);
				array_push($arrNavBarSolapas['viajes'], $arrMenu[40]);*/

				$arrPermisos = array_merge($arrPermisos, asignarPermisos($arrNavBar['logistica']));
			break;
			case 6:	
				$arrNavBar['abmViajesDeliveryPlayaVirtual'] = null;
				array_push($arrPermisos,'abmViajesDeliveryPlayaVirtual');

			case 7:
				$arrNavBar['abmViajesDeliveryPlayaVirtual'] = null;
				array_push($arrPermisos,'abmViajesDeliveryPlayaVirtual');

				//--Ini. Se quita lugares para los perfiles que no sean Administrador --//
				if($_SESSION['idPerfil'] == 7){//--Se quita solo para Transportista...
					unset($arrNavBar[$arrMenu[2]]);
					unset($arrPermisos[array_search($arrMenu[2],$arrPermisos)]);
				}
				//--Fin.
			break;
			case 8:		
				$arrNavBar['abmViajesDeliveryPlayaVirtual'] = null;
				array_push($arrPermisos,'abmViajesDeliveryPlayaVirtual');

				array_push($arrPermisos,$arrMenu[52]);	//cuenta_conductores

				if(ES_MOBILE){
					unset($arrNavBar);
					$auxBar = array();
					$arrPermisos = array();
					$paginaDefecto = $arrMenu[69];
				}	

				$arrNavBar[$arrMenu[69]] = null;
				array_push($arrPermisos,$arrMenu[69]);

			break;	
		}

		foreach($auxBar as $k => $item){
			$arrNavBar[$k] = $item;
		}
		unset($auxBar);
		
		$arrAux = array();
        foreach($arrNavBar as $k => $item){
        	if($k == 'logistica'){
                $k = 'abmViajesDelivery';
			}
			$arrAux[$k] = $arrNavBar[$k]; 
		}
		unset($arrNavBar);
		$arrNavBar = $arrAux;		

	break;
	case 12481://MSC -> Prod
	//case 17925://MSC -> Testing
		$arrExcepciones = array();
        //$arrExcepciones[4] = 'abmViajesDisponibilidadTransportistas';
		unset($arrNavBar[$arrMenu[26]]); //-- Se quita Alertas
		unset($arrPermisos[array_keys($arrPermisos, $arrMenu[26])[0]]);

		unset($arrNavBar['logistica'][$arrMenu[61]]);//-- Se quita abmViajesDeliveryArribosPartidasPlanta
		unset($arrPermisos[array_keys($arrPermisos, $arrMenu[61])[0]]);
		unset($arrNavBar['logistica'][$arrMenu[62]]);//-- Se quita abmViajesDeliveryArribosPartidasCliente
		unset($arrPermisos[array_keys($arrPermisos, $arrMenu[62])[0]]);
		unset($arrNavBar['logistica'][$arrMenu[44]]);//-- Se quita abmReferenciasAjustar
		unset($arrPermisos[array_keys($arrPermisos, $arrMenu[44])[0]]);
		//unset($arrNavBar['logistica'][$arrMenu[63]]);//-- Se quita abmViajes	
		//unset($arrPermisos[array_keys($arrPermisos, $arrMenu[63])[0]]);
		unset($arrNavBar['logistica']['abmViajesDelivery']);//-- Se quita abmViajesDelivery
		unset($arrPermisos[array_keys($arrPermisos, 'abmViajesDelivery')[0]]);

		if($arrNavBar['logistica']){
            $arrNavBarSolapas['viajes'] = array();
            foreach($arrNavBar['logistica'] as $k => $item){
                if($k != 'abmViajesDeliveryArribosPartidas'){
                    array_push($arrNavBarSolapas['viajes'], $k);
                }
            }
        }
		
		switch($_SESSION['idPerfil']){
			case 5:
				array_push($arrPermisos,'abmViajesDeliveryAltaMasiva');

				array_push($arrPermisos,$arrMenu[40]);
				array_push($arrNavBarSolapas['viajes'], $arrMenu[40]);

				array_push($arrPermisos,$arrMenu[63]);
				array_push($arrNavBarSolapas['viajes'], $arrMenu[63]); 

				$arrPermisos = array_merge($arrPermisos, asignarPermisos($arrNavBar['logistica']));
			break;
			case 6:		
			case 7:
				array_push($arrPermisos,$arrExcepciones[1]);
				array_push($arrPermisos,$arrExcepciones[2]);

				$arrNavBar['logistica'][$arrExcepciones[1]] = '';
				$arrNavBar['logistica'][$arrExcepciones[2]] = '';

				array_push($arrNavBarSolapas['viajes'], $arrExcepciones[1]);
				array_push($arrNavBarSolapas['viajes'], $arrExcepciones[2]);

				//--Ini. Se quita reastreo e informes para los perfiles que no sean Administrador --//
				if($_SESSION['idPerfil'] == 7){//--Se quita solo para Transportista...
					unset($arrNavBar[$arrMenu[0]]);
					unset($arrPermisos[array_search($arrMenu[0],$arrPermisos)]);
					unset($arrNavBar[$arrMenu[1]]);
					unset($arrPermisos[array_search($arrMenu[1],$arrPermisos)]);
				}
				//--Fin.
			break;

			case 8:
				$arrPermisos = quitarPermisos('abmViajesDelivery');
				$keys = array_keys($arrNavBar);
				$keys[array_search($arrMenu[3],$keys) ] = 'abmViajes';
				$arrNavBar = array_combine($keys,$arrNavBar);
				$arrPermisos = asignarPermisos($arrNavBar);
			break;



		}
		
		$arrAux = array();
		foreach($arrNavBar as $k => $item){
        	if($k == 'logistica'){
				//$k = 'abmViajesDelivery';
				$k = $arrMenu[63];
			}
			$arrAux[$k] = $arrNavBar[$k]; 
		}
		unset($arrNavBar);
		$arrNavBar = $arrAux;
		
		$paginaDefecto = $arrMenu[63];
	break;	
	case 100://ACSA
		array_push($arrPermisos,'informes_km_recorridos');
		array_push($arrPermisos,'informes_viajes');
		array_push($arrPermisos,'informes_alertas');
	break;
	case 9048://FiberCorp
		unset($arrPermisos[4]);
		unset($arrPermisos[5]);
		unset($arrPermisos[6]);
		unset ($arrNavBar['opciones']);
		
		array_push($arrPermisos,$arrMenu[41]);######-NO VISIBLE EN EL MENU-(abmAllInOneMoviles)#####	
		$arrNavBar[$arrMenu[41]] = '';
	break;
	case 14121://Forza
		unset ($arrNavBar['opciones']);
		array_push($arrPermisos,$arrMenu[65]);//Notificaciones	
		$arrNavBar[$arrMenu[65]] = '';
		$arrNavBar[$arrMenu[8]] = '';
	break;
}
#####---  ---####


function asignarPermisos($array){
	$arrAux = array();
	foreach($array as $k => $item){
		if(is_array($item)){
			$arrAux = array_merge($arrAux, asignarPermisos($item));
		}
		array_push($arrAux,$k);
	}	
	return $arrAux;
}

function quitarPermisos($array){
	global $arrPermisos;
	
	if(is_array($array)){
		foreach($array as $k => $item){
			if(in_array($k, $arrPermisos)){
				$key = array_search($k, $arrPermisos);
				unset($arrPermisos[$key]);
			}
		}
	}
	else{
		if(in_array($array, $arrPermisos)){
			$key = array_search($array, $arrPermisos);
			unset($arrPermisos[$key]);
		}
	}
	sort($arrPermisos); 
	return $arrPermisos;
}
?>