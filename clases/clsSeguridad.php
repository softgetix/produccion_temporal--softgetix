<?php
class Seguridad {

    protected $seccion;
    function __construct($objSQLServer, $seccion = NULL) {
        $this->objSQL = $objSQLServer;
        $this->seccion = $seccion;
    }

    function expireUser($id_usuario){
	  //$fecha=date('Y-d-m h:i:s');
	  $strSQL = "UPDATE tbl_usuarios SET us_expira=getdate()-2 WHERE us_id=".$id_usuario;
	  if($this->objSQL->dbQuery($strSQL))return true;
	  else return false;	
    }

    public function validar($idviaje = null){
        switch($this->seccion){
            case 'abmViajesDelivery':
            case 'abmViajes':
                $idAgente = $_SESSION['idAgente'];
                
                $strSQL = " SELECT COUNT(*) AS cant FROM tbl_viajes ";
                $strSQL.= " WHERE vi_id = ".intval($idviaje);
                if($idAgente == $_SESSION['idEmpresa']){
                    //--PARCHE: Para q conviva KCC Arg y KCC Arg Bis
                    $idAgente = ($idAgente == 10827)?('10827,156'):$idAgente;
                    //--
                    $strSQL.= " AND vi_transportista IN (SELECT cl_id FROM tbl_clientes WHERE cl_id_distribuidor in (".$idAgente.")) ";
                }
                else{
                    $strSQL.= " AND vi_transportista = ".$_SESSION['idEmpresa'];
                }

                $obj = $this->objSQL->dbQuery($strSQL);
                $result = $this->objSQL->dbGetRow($obj,0,3);	
                if($result['cant']){
                    return 	true;
                }
                return false;
            break;
        }
        return false;
    }
}
