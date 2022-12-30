<?php if (false){ ?>
<?php //if (!isset($sinColIzq)){ ?>
<!--<div id="colIzq"> -->
	<?php //require_once('includes/datosColIzqAbm.php')?>
<!--</div> -->
<?php } ?>
<div id="main" class="sinColIzq">

    <form name="frm_<?= $seccion ?>" id="frm_<?= $seccion ?>" action="?c=<?= $seccion ?>" method="post">
        <input name="hidOperacion" id="hidOperacion" type="hidden" value="<?= $operacion; ?>" />
        <input name="hidFiltro" id="hidFiltro" type="hidden" value="" />
        <input name="hidSeccion" id="hidSeccion" type="hidden" value="<?= $seccion; ?>" />
        <?php
        switch ($operacion) {
            case 'listar':
                $sinBuscador = 1;
                require_once 'includes/botoneraABMs.php';
                require_once 'cruces/listar.php';
                break;
            case 'alta':
            case 'modificar': {
                    require_once 'cruces/am.php';
                    break;
                }
        }
        ?>
    </form>
</div>
