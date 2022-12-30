<?php 
$vd = (int)$_GET['vd'];
if(!$vd){ exit;} 

include_once 'includes/validarSesion.php';
include_once 'includes/funciones.php';
include_once 'includes/conn.php';

//error_reporting(E_ALL);

require_once 'clases/clsClientes.php';
$objCliente = new Cliente($objSQLServer);

include_once 'clases/clsReferencias.php';
$objReferencia = new Referencia($objSQLServer);

include_once 'clases/clsConductores.php';
$objConductor = new Conductor($objSQLServer);

$query = " SELECT vi_dador, vi_codigo, vd_ini, vd_re_id, vi_transportista, vi_co_id, vd_stock "
        ." FROM tbl_viajes_destinos WITH(NOLOCK) "
        ." INNER JOIN tbl_viajes  WITH(NOLOCK) ON vd_vi_id = vi_id "
        ." WHERE vd_id = {$vd}";
$viaje = $objSQLServer->dbGetRow($objSQLServer->dbQuery($query),0,3);
if(!$viaje){exit;}      

$dbdador = $objCliente->obtenerRegistros($viaje['vi_dador']);
$dbdador = $dbdador[0];

$dbreferencia = $objReferencia->obtenerRegistros($viaje['vd_re_id']);
$dbreferencia = $dbreferencia[0];

$dbtransportista = $objCliente->obtenerRegistros($viaje['vi_transportista']);
$dbtransportista = $dbtransportista[0];

$dbconductor = $objConductor->obtenerRegistros($viaje['vi_co_id']);
$dbconductor = $dbconductor[0];


$fabricante = array(
    'nombre' => $dbdador['cl_abbr']
    ,'direccion' => $dbdador['cl_direccion']
    ,'cuit' => $dbdador['cl_cuit']
);

$comprobante = array(
    'numero' => $viaje['vi_codigo']
    ,'fecha' => date('d/m/Y',strtotime($viaje['vd_ini']))
);

$cliente = array(
    'nombre' => $dbreferencia['re_nombre']
    ,'cuit' => $dbreferencia['re_identificador']
);

$sucursal = array(
    'codigo' => $dbreferencia['re_numboca']
    ,'direccion' => $dbreferencia['re_ubicacion']
);

$transporte = array(
    'nombre' => $dbtransportista['cl_razonSocial']
    ,'cuit' => $dbtransportista['cl_cuit']
);

$conductor = array(
    'nombre' => trim($dbconductor['co_nombre'].' '.$dbconductor['co_apellido'])
    ,'dni' => $dbconductor['co_dni']
);

$pallets = array(
    'descripcion' => 'Pallet tipo Arlog'
    ,'despachado' => $viaje['vd_stock']
    ,'aceptada' => NULL
);

include ('includes/FPDI/src/autoload.php');
include ('includes/FPDF/fpdf.php');

use setasign\Fpdi\Fpdi;
$pdf = new Fpdi('P', 'mm', 'A4');

$pdf->AddPage();
//$pdf->setSourceFile('/var/data/tmp/InformeTrazabilidad.pdf');
//$templateId = $pdf->importPage(1);
//$pdf->useTemplate($templateId);

//$pdf->SetTextColor(255,255,255); //--Color blanco
$pdf->SetTextColor(0,0,0); //--Color negro

//$pdf->SetFont('Arial');

//--Ini. Datos del Fabricante
$pdf->SetFont('Helvetica', 'B', 12);
$pdf->SetXY(20, 19);
$pdf->Write(0, 'Datos del Fabricante');
$pdf->SetFont('Helvetica', '', 10);
$pdf->SetXY(20, 25);
$pdf->Write(0, 'Nombre: '.$fabricante['nombre']);
$pdf->SetXY(20, 20);
$pdf->Write(20, iconv('UTF-8', 'windows-1252//TRANSLIT','Dirección').': '.iconv('UTF-8', 'windows-1252//TRANSLIT', utf8_encode($fabricante['direccion'])));
$pdf->SetXY(20, 35);
$pdf->Write(0, 'CUIT: '.$fabricante['cuit']);

//--Ini. Datos del Documento
$pdf->SetFont('Helvetica', '', 10);
$pdf->SetXY(130, 22);
$pdf->Write(0, iconv('UTF-8', 'windows-1252//TRANSLIT','Número de Documento:'));
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetXY(170, 22);
$pdf->Write(0, $comprobante['numero']);
$pdf->SetFont('Helvetica', '', 10);
$pdf->SetXY(130, 27);
$pdf->Write(0, 'Fecha de Comprobante: '.$comprobante['fecha']);

//--Ini. Datos del Cliente
$pdf->SetFont('Helvetica', 'B', 12);
$pdf->SetXY(20, 55);
$pdf->MultiCell(80, 4.5, 'Datos del Cliente', 0, 'R');
$pdf->SetFont('Helvetica', '', 10);
$pdf->SetXY(20, 61);
$pdf->MultiCell(80, 4.5, iconv('UTF-8', 'windows-1252//TRANSLIT', utf8_encode($cliente['nombre'])), 0, 'R');
$pdf->SetXY(20, 66);
$pdf->MultiCell(80, 4.5, 'CUIT: '.$cliente['cuit'], 0, 'R');

//--Ini. Datos de la Sucursal
$pdf->SetFont('Helvetica', 'B', 12);
$pdf->SetXY(110, 55);
$pdf->MultiCell(80, 4.5, 'Sucursal', 0, 'R');
$pdf->SetFont('Helvetica', '', 10);
$pdf->SetXY(110, 61);
$pdf->MultiCell(80, 4.5, iconv('UTF-8', 'windows-1252//TRANSLIT', 'Código de Sucursal').': '.$sucursal['codigo'], 0, 'R');
$pdf->SetXY(110, 66);
$pdf->MultiCell(80, 4.5, iconv('UTF-8', 'windows-1252//TRANSLIT','Dirección: '.utf8_encode($sucursal['direccion'])), 0, 'R');

//--Ini. Datos del Transporte
$pdf->SetFont('Helvetica', 'B', 12);
$pdf->SetXY(20, 88);
$pdf->MultiCell(80, 4.5, 'Transporte', 0, 'R');
$pdf->SetFont('Helvetica', '', 10);
$pdf->SetXY(20, 94);
$pdf->MultiCell(80, 4.5, iconv('UTF-8', 'windows-1252//TRANSLIT',utf8_encode($transporte['nombre'])), 0, 'R');
$pdf->SetXY(20, 99);
$pdf->MultiCell(80, 4.5, 'CUIT: '.$transporte['cuit'], 0, 'R');

//--Ini. Datos del Conductor
$pdf->SetFont('Helvetica', 'B', 12);
$pdf->SetXY(110, 88);
$pdf->MultiCell(80, 4.5, 'Conductor', 0, 'R');
$pdf->SetFont('Helvetica', '', 10);
$pdf->SetXY(110, 94);
$pdf->MultiCell(80, 4.5, 'Nombre: '.iconv('UTF-8', 'windows-1252//TRANSLIT', utf8_encode($conductor['nombre'])), 0, 'R');
$pdf->SetXY(110, 99);
$pdf->MultiCell(80, 4.5, 'DNI: '.$conductor['dni'], 0, 'R');

//--Ini. Info Pallets
$pdf->SetFont('Helvetica', 'B', 12);
$pdf->SetXY(60, 121);
$pdf->MultiCell(40, 4.5, iconv('UTF-8', 'windows-1252//TRANSLIT', 'Descripción de Pallet'), 0, 'R');
$pdf->SetFont('Helvetica', '', 10);
$pdf->SetXY(60, 132);
$pdf->MultiCell(40, 4.5, $pallets['descripcion'], 0, 'R');

$pdf->SetFont('Helvetica', 'B', 12);
$pdf->SetXY(110, 121);
$pdf->MultiCell(40, 4.5, 'Cantidad despachada', 0, 'R');
$pdf->SetFont('Helvetica', '', 10);
$pdf->SetXY(110, 132);
$pdf->MultiCell(40, 4.5, $pallets['despachado'], 0, 'R');

$pdf->SetFont('Helvetica', 'B', 12);
$pdf->SetXY(150, 121);
$pdf->MultiCell(40, 4.5, 'Cantidad aceptadda', 0, 'R');
$pdf->SetFont('Helvetica', '', 10);
$pdf->SetXY(150, 132);
$pdf->MultiCell(40, 4.5, $pallets['aceptada'], 0, 'R');

//--Ini. Firma
$pdf->SetFont('Helvetica', 'B', 12);
$pdf->SetXY(25, 180);
$pdf->Write(0, 'Fecha de Vencimiento del Vale');
$pdf->SetXY(120, 180);
$pdf->Write(0, 'Sello y Firma del Cliente');

//$pdf->Output('/var/data/tmp/roni.pdf', 'F'); //I
$pdf->Output($viaje['vi_codigo'].'.pdf', 'D'); //I
exit;




