<div id="colIzq" style="position: relative;">
    <div id="leftcol-overlay" style="
        position: absolute; background-color: black;
        width: 100%; height: 100%;
        z-index: 1; opacity: .5;
        display: none;">
    </div>

    <div id="leftcol-overlay-loading" style="
        position: absolute;
        z-index: 2; display: none;
        margin-left: 25px; margin-top: 120px;
    ">
        <img src="imagenes/ajax-loader.gif" width="200" />
    </div>
 
    <div id="rastreo_colIzqTabs">
        <ul style="display:none">
            <li></li>
        </ul>
		
        <div id="infoListado" class="contenidoDesplegable">
            <div id="infoListado-inner">
                <div id="infoListado-upper">
                    <table width="100%" >
                        <tr>
                            <td>
                                <input type="text" id="txtBuscar" class="buscar" placeholder="<?=$lang->system->rastreo_buscador?>" onkeyup="javascript:newTracer.setBuscador(event);" 
                                style=" width:87%;"/>
                            </td>
                            
                            <?php /*if(!tienePerfil(16)){?>
                            <td align="right">
                                <button id="btn_tracerCtxMenu" onclick="newTracer.toggleContextMenu();" 
                                        style="width:30px;">
                                    <a href="#" style="text-decoration: none;">
                                        <img src="imagenes/raster/black/cog_16x16.png" />
                                    </a>
                                    
                                </button>
                                <!-- Menu "mas opciones" de la seccion izquierda -->
                                <div id="tracerCtxMenuContainer" class="jq-menu"style="position: absolute; z-index: 10; width: 200px; display: none;">
                                    <ul id="tracerCtxMenu" class="jq-menu">
                                        <li id="mnu_SelectAllGroups"><a href="javascript:;"><?=$lang->item_menu_rastreo->item_1?></a></li>
                                        <li id="mnu_SelectAllMoviles"><a href="javascript:;"><?=$lang->item_menu_rastreo->item_2?></a></li>
                                        <li id="mnu_UnselectAllGroups"><a href="javascript:;"><?=$lang->item_menu_rastreo->item_3?></a></li>
                                        
                                        <li class="menu-separator">&nbsp;</li>
                                        
                                        <li id="mnu_EmbedGPSPanel" active="true" group="gpspanelopt"><a href="javascript:;"><?=$lang->item_menu_rastreo->item_4?></a></li>
                                        <li id="mnu_MoveGPSPanel" group="gpspanelopt"><a href="javascript:;"><?=$lang->item_menu_rastreo->item_5?></a></li>
                                        
                                        <li class="menu-separator">&nbsp;</li>
                                        
                                        <li id="mnu_ShowMovChecks" group="enablechecksopt"><a href="javascript:;"><?=$lang->item_menu_rastreo->item_6?></a></li>
                                        <li id="mnu_HideMovChecks" active="true" group="enablechecksopt"><a href="javascript:;"><?=$lang->item_menu_rastreo->item_7?></a></li>
                                        
                                        <li class="menu-separator">&nbsp;</li>
                                        
                                        <li id="mnu_FilterMovOnly" group="filtermodeopt"><a href="javascript:;"><?=$lang->item_menu_rastreo->item_8?></a></li>
                                        <li id="mnu_FilterAndChase" active="true" group="filtermodeopt"><a href="javascript:;"><?=$lang->item_menu_rastreo->item_9?></a></li>
                                        
                                        <li class="menu-separator">&nbsp;</li>
                                        
                                        <li id="mnu_OrderByGroup" group="orderingcriteria" active="true"><a href="javascript:;"><?=$lang->item_menu_rastreo->item_10?></a></li>
                                        <li id="mnu_OrderByClient" group="orderingcriteria"><a href="javascript:;"><?=$lang->item_menu_rastreo->item_11?></a></li>
                                        <li id="mnu_OrderByEquipmentModel" group="orderingcriteria"><a href="javascript:;"><?=$lang->item_menu_rastreo->item_12?></a></li>
                                        <li class="menu-separator">&nbsp;</li>
                                        <li id="mnu_OrderByCellCompany" group="orderingcriteria"><a href="javascript:;"><?=$lang->item_menu_rastreo->item_13?></a></li>
                                        
                                        <li class="menu-separator">&nbsp;</li>
                                        
                                        <li id="mnu_Cancel">
                                            <a href="javascript:;"><?=$lang->botonera->cancelar?></a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                            <?php }*/
							if(tienePerfil(16)){?>
                                <input type="hidden" id="seleccionar-todos-los-grupos" value="1" />
                            <?php }?>
                        </tr>
                    </table>
                </div>
                
                <div id="divScrollUltimosReportes">
                    <img src="imagenes/ajax-loader.gif" border="0" style="margin:10px;"  />
                </div>
                <div id="divMSG" style="display:none">
                    <p style=" text-align:center"><?=$lang->botonera->active_visualizacion?>&nbsp;</p>
                </div>
                

                <div id="info" align="center" style="display:none;">
                    <button class="button" type="button" title="<?=$lang->botonera->volver?>" onclick="closeInfo();" style="margin:10px;width:200px;line-height: 20px;" >
                        <img style="float:left;margin-right:10px;" src="imagenes/raster/black/volver.png">
                        <span><?=$lang->botonera->volver?></span>
                    </button>
                    <div id="divDatosInfoGps"></div>
                    	<input type="hidden" name="hidIdMovilConf" id="hidIdMovilConf" value="<?=(isset($_POST['movil_id']))?$_POST['movil_id']:'0'?>"/>
                	</div>
	            </div>


            <form method="post" action="boot.php?c=informes" name="frmHistorico" id="frmHistorico">
                <input type="hidden" name="idMovil" id="idMovil" value="0" />
                <input type="hidden" name="rastreo" id="fromRastreo" value="1" />
            </form>
            
            
            <form method="post" action="boot.php?c=abmReferencias" name="frmRef" id="frmRef">
                <input type="hidden" name="idMovil" id="idMovil" value="0" />
                <input type="hidden" name="hidLat" id="hidLat" value="0" />
                <input type="hidden" name="hidLng" id="hidLng" value="0" />
            </form>
            
            <form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post">
				<input name="hidOperacion" id="hidOperacion" type="hidden" value="" />
    			<input type="hidden" name="hidSeccion" id="hidSeccion" value="<?=$seccion?>" />
    			<input type="hidden" name="hidId" id="hidId" />
            </form>

        </div>
    </div>
</div>
