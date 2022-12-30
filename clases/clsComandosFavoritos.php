<?php

class ComandoFavorito{
    var $objSQLServer;

    protected $iTicket;

    public function __construct($objSQLServer){
        $this->objSQL = $objSQLServer;
    }

    public function getTicket() { return $this->iTicket; }
    public function setTicket($iTicket) { $this->iTicket = $iTicket; }
    
    public function obtener_UG_ID($un_mostrarComo){
        $sSQL = "SELECT ug_id FROM tbl_unidad_gprs WITH(NOLOCK) WHERE ug_identificador = '".$un_mostrarComo."'";

        $objRes = $this->objSQL->dbQuery($sSQL);
        $retval = false;
        if($objRes !== false){
            $arrData = $this->objSQL->dbGetRow($objRes, 0, 3 );
            $retval = $arrData['ug_id'];
        }
        return $retval;
    }
    
    public function insertar( $sCmd, $idEquipo )
    {
        $params = array(
            'ce_ug_id' => $this->obtener_UG_ID($idEquipo),
            'ce_comando' => $sCmd,
            'ce_fechaCreado' => 'CURRENT_TIMESTAMP',
            'ce_fechaEnviado' => 'NULL',
            'ce_respuesta' => 'NULL',
            'ce_ticket' => 'DBO.PADLASTN( IDENT_CURRENT(\'tbl_comando_enviado\'), 4, \'0\' )',
            'ce_fechaRespuesta' => 'NULL',
            'ce_tomadoPorModulo' => 0,
            'ce_enviar' => 1,
        );

        $sSQLInsert = "
            INSERT INTO tbl_comando_enviado (
                ce_ug_id,
                ce_comando,
                ce_fechaCreado,
                ce_fechaEnviado,
                ce_respuesta,
                ce_ticket,
                ce_fechaRespuesta,
                ce_tomadoPorModulo,
                ce_enviar
            ) VALUES (
                 ".$params['ce_ug_id']." ,
                '".$params['ce_comando']."',
                 ".$params['ce_fechaCreado']." ,
                 ".$params['ce_fechaEnviado']." ,
                 ".$params['ce_respuesta']." ,
                 ".$params['ce_ticket']." ,
                 ".$params['ce_fechaRespuesta']." ,
                 ".$params['ce_tomadoPorModulo']." ,
                 ".$params['ce_enviar']."
            )
        ";

        // Falta chequear la insercion aca y obtener el ID del registro insertado
        // para ponerlo como CE_TICKET y devolverlo por AJAX

        $bSuccess = $this->objSQL->dbQuery( $sSQLInsert );

        $retval = false;

        if ( $bSuccess === true )
        {
            $sSQLSelect = "SELECT TOP 1 ce_ticket FROM tbl_comando_enviado WITH(NOLOCK) ORDER BY ce_id DESC";
            $objRes = $this->objSQL->dbQuery( $sSQLSelect );

            if ( $objRes !== false )
            {
                $arrTicketInfo = $this->objSQL->dbGetRow( $objRes, 0, 3 );
                $this->iTicket = $arrTicketInfo['ce_ticket'];
                $retval = true;
            }
        }

        return $retval;
    }


    public function enviarUDP($idEquipo){
        if ( $idEquipo ){
            $sSQL = "SELECT un_id FROM tbl_unidad WITH(NOLOCK) WHERE un_mostrarComo = '".$idEquipo."'";
            $objRes = $this->objSQL->dbQuery($sSQL);

            $retval = false;
            if ( $objRes !== false )
            {
                $arrData = $this->objSQL->dbGetRow($objRes, 0, 3);
                //die( "UN_ID: ".var_export($arrData, true) );

                require_once 'clases/clsEquipos.php';

                $objEquipo = new Equipo($this->objSQL);
                $arrEquipo = $objEquipo->obtenerEquipos($arrData['un_id']);

                if ( $arrEquipo[0]['mo_puerto'] )
                {
					$buf = ">CP<";
                    $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
                    socket_sendto($socket, $buf, strlen($buf), 0, $arrEquipo[0]['mo_ip'], $arrEquipo[0]['mo_puerto']);
                    //echo "socket_sendto(\$socket (".(print_r($socket,true))."), \$buf = (".$buf."), strlen(\$buf) = ".strlen($buf).", 0, ".$arrEquipo[0]['mo_ip'].", ".$arrEquipo[0]['mo_puerto'].");";
                    socket_close($socket);
                }
                
                $retval = $arrEquipo[0]['mo_puerto'];
            }

            return $retval;
        }
    }


    public function chequearEstadoTicket( $iTicket ){
        $sSQL = "SELECT ce_respuesta, ce_fechaRespuesta FROM tbl_comando_enviado WITH(NOLOCK) WHERE ce_ticket = '".$iTicket."'";
        
        $objRes = $this->objSQL->dbQuery($sSQL);

        $retval = false;
        if ( $objRes !== false ){
            $arrData = $this->objSQL->dbGetRow($objRes, 0, 3);
            if ( $arrData['ce_respuesta'] != null ){
                $retval = $arrData;
            }
        }

        return $retval;
    }
}