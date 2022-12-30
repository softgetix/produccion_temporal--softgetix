<script type="text/javascript">

var g_o_menucfg_MasOpciones =
{
    "handlers":
    {
        "mnu_SelectAllGroups": function()
        {
            newTracer.cambiarEstadoGrupos(true,false);
        },
		
		"mnu_SelectAllMoviles": function()
        {
            newTracer.cambiarEstadoGrupos(true,true);
        },

        "mnu_UnselectAllGroups": function()
        {
            newTracer.cambiarEstadoGrupos(false,false);
        },


        // Incrustar/flotar panel de datos GPS

        "mnu_EmbedGPSPanel": function()
        {
            newTracer.embedGPSPanel();
        },

        "mnu_MoveGPSPanel": function()
        {
            newTracer.moveGPSPanel();
        },

        // Mostrar/ocultar checkbox de moviles

        "mnu_ShowMovChecks": function()
        {
            newTracer.showMovChecks();
        },

        "mnu_HideMovChecks": function()
        {
            newTracer.hideMovChecks();
        },


        // Mostrar/ocultar checkbox de moviles

        "mnu_FilterMovOnly": function()
        {
            g_iSearchType = SEARCH_TYPES.FILTER_ONLY;
        },

        "mnu_FilterAndChase": function()
        {
            g_iSearchType = SEARCH_TYPES.FILTER_AND_CHASE;
        },


        // Si me cambian un criterio de ordenamiento tengo que 
        // traer los datos de nuevo para evitar desfasajes de informacion.

        "mnu_OrderByGroup": function(){
            g_iOrderingCriteria = ORDERING_CRITERIA.GROUP;
            g_bIsDataUpdate = false;
            if (g_bResetGroupInfoWhenCriteriaChanges) newTracer.resetGroupsInfo( actualizarArray );
        },

        "mnu_OrderByClient": function(){
            g_iOrderingCriteria = ORDERING_CRITERIA.CLIENT;
            g_bIsDataUpdate = false;
            if (g_bResetGroupInfoWhenCriteriaChanges) newTracer.resetGroupsInfo( actualizarArray );
        },

        "mnu_OrderByEquipmentModel": function(){
            g_iOrderingCriteria = ORDERING_CRITERIA.EQUIPMENT_MODEL;
            g_bIsDataUpdate = false;
            if (g_bResetGroupInfoWhenCriteriaChanges) newTracer.resetGroupsInfo( actualizarArray );
        },

        "mnu_OrderByCellCompany": function(){
            g_iOrderingCriteria = ORDERING_CRITERIA.CELL_COMPANY;
            g_bIsDataUpdate = false;
            if (g_bResetGroupInfoWhenCriteriaChanges) newTracer.resetGroupsInfo( actualizarArray );
        }
    }
};

var g_o_menucfg_InfoGPS =
{
    "handlers":
    {
        "mnu_InfoGPSOpenHere": function()
        {
            var movid = $(this).attr("data-mov-id");
            enviarHistorico(movid);
        },

        "mnu_InfoGPSOpenInNewWindow": function()
        {
            var movid = $(this).attr("data-mov-id");
            enviarHistorico(movid, undefined, true);
        }
    }
};

</script>