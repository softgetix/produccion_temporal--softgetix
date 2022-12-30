<table width="100%" height="100%">
	<thead>
    	<tr>
        	<td width="6%" style="text-align:center"><span class="campo1"><?=$lang->system->fecha?></span></td>
            <td width="7%" style="text-align:center"><span class="campo1">Ip</span></td>
            <?php if($_SESSION['idTipoEmpresa']!=2){ ?>	
            	<td width="12" ><span class="campo1"><?=$lang->system->usuario?></span></td>
            <?php }?>
            <td width="65%" ><span class="campo1"><?=$lang->system->navegador?></span></td>
            <td width="10%" style="text-align:center" class="td-last"><span class="campo1"><?=$lang->system->estado?></span></td>
		</tr>
	</thead>
    <tbody>
    	<?php if($result->numRows > 0){
		   	for($i = 0; $i < $result->numRows; $i++) {
        	    $class = ($i % 2 == 0)? 'filaPar' : 'filaImpar';?>
                <tr class="<?=$class?> <?=(($result->numRows-1) == $i)?'tr-last':''?>">
               		<td style="text-align:center"><?=formatearFecha($result->result[$i]['lg_date'])?></td>
                    <td style="text-align:center"><?=$result->result[$i]['lg_ip']?></td>
                    <?php if($_SESSION['idTipoEmpresa']!=2){ ?>	
                    	<td><?=$result->result[$i]['lg_userUsername']?></td>
                    <?php }?>
                    <td><?=$result->result[$i]['lg_userAgent']?></td>
                    <td style="text-align:center" class="td-last"><?=($result->result[$i]['lg_loginSuccess'] == 1)?$lang->system->ingreso_exitoso:$lang->system->ingreso_fallido?></td>
				</tr>
			<?php }?>
		<?php }?>
	</tbody>
</table>
