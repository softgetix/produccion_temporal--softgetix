<div id="colIzq">

</div>

<div id="main">
   <h1>Administraci&oacute;n de Grupos de Veh&iacute;culos</h1>
<?php
   //** Alta / Modificacion **
   if($_GET['ope'] == 1){
?>
   ALTA / MODIFICAR

<?php
   //** Listado **
   } else {
      require_once 'includes/botoneraABMs.php';
?>
   <div id="buscador">
      <h1>Buscador</h1>
      <span>Nombre de Grupo: </span><input type="text" /> <img src="imagenes/lupaChica" alt="Buscar" />
   </div>
   <div id="mainBox">
      <table>
      <tr class="titulo">
         <td></td>
         <td>Nombre de Grupo</td>
      </tr>
<?php
      for($i = 0; $i < 5; $i++){
         $class = ($i % 2 == 0)? 'filaPar' : 'filaImpar';
?>
      <tr class="<?=$class?>">
         <td class="checkColum"><input type="checkbox" /></td>
         <td>Grupo <?=$i?></td>
      </tr>
<?php    } ?>
      </table>
   </div>
<?php } ?>
</div>