<?php
set_time_limit(0);
require_once "includes/funciones.php";
require_once "includes/conn.php";

$un_mostrarComo = $_POST['un_mostrarComo']?$_POST['un_mostrarComo']:NULL;
$un_mod_id = $_POST['un_mod_id']?$_POST['un_mod_id']:NULL;
$date = $_POST['date']?$_POST['date']:NULL;

if(!$_POST){
	$un_mostrarComo = $_GET['un_mostrarComo'];
	$date = date('Y-m-d H:i');
	
	//-- UN_MOD_ID --//
	$sql= " SELECT un_mod_id FROM tbl_unidad WHERE un_mostrarComo = '".$un_mostrarComo."' ";
	$rs = $objSQLServer->dbQuery($sql);
	$res = $objSQLServer->dbGetRow($rs);
	$un_mod_id = $res['un_mod_id'];
	//-- --//
	
	$whereMostrar = '';
	switch($un_mod_id){
		case 1:
		case 9:
			$un_mostrarComo = 'ID='.$un_mostrarComo;
		break;
		case 3:
		case 8:
			$un_mostrarComo = ','.$un_mostrarComo.',';
		break;	
		case 2:
			$un_mostrarComo = '>'.$un_mostrarComo;
		break;
		case 5:
		case 17:
			$un_mostrarComo = 'ID='.substr($un_mostrarComo, 1,strlen($un_mostrarComo) - 1);
		break;
		case 6:
			$un_mostrarComo = $un_mostrarComo.' ';
		break;
	}
}

$sql = " SELECT cl_paquete, cl_fechaRecibido FROM tbl_comm_log ";
$sql.= " WHERE cl_paquete LIKE '%".$un_mostrarComo."%' ";
$sql.= " AND cl_mod_id = ".$un_mod_id;
$sql.= " AND cl_fechaRecibido >= '".$date."'";
$sql.= " ORDER BY cl_fechaRecibido DESC ";
$rs = $objSQLServer->dbQuery($sql);
$res = $objSQLServer->dbGetAllRows($rs);
$i = 0;
?>
<link type="text/css" rel="stylesheet" href="css/estilosDefault.css"/>
<link type="text/css" rel="stylesheet" href="css/estilosABMDefault.css"/>
<style>
html{ overflow:auto;}
</style>
<div id="main" style="margin:0px; padding:0px;">
<table cellpadding="0" cellspacing="0" class="widefat">
<thead>
	<tr class="titulo">
    	<td width="20">&nbsp;</td>
        <td>M&oacute;vil</td>
        <td style="text-align:left">Paquete</td>
        <td width="150">Recibido</td>
    </tr>
</thead>
<tbody id="listado">
	<?php 
	if($res){
		foreach($res as $item){?>
		<tr class="<?=($i % 2 == 0)?'filaPar':'filaImpar'; $i++;?>">
			<td>&nbsp;</td>
			<td style="text-align:center"><?=$_GET['un_mostrarComo']?></td>
			<td><?=$item['cl_paquete']?></td>
			<td style="text-align:center"><?=date('d-m-Y H:i',strtotime($item['cl_fechaRecibido']))?></td>
		</tr>
		<?php }?>
    <?php }else{?>
    	<tr class="<?=($i % 2 == 0)?'filaPar':'filaImpar'; $i++;?>">
			<td colspan="4" style="text-align:center">Esperando paquetes</td>
		</tr>
    <?php }?>
</tbody>
</table>
<form name="logControl" id="logControl" method="post" action="<?=$_SERVER['PHP_SELF'].'?un_mostrarComo='.$_GET['un_mostrarComo']?>">
	<input type="hidden" name="un_mostrarComo" value="<?=$un_mostrarComo?>"?>
    <input type="hidden" name="un_mod_id" value="<?=$un_mod_id?>"?>
    <input type="hidden" name="date" value="<?=$date?>"?>
</form>
</div>

<script language="javascript">
	setTimeout('document.logControl.submit()',5000);
</script>

