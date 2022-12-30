<!-- CONSTANTES -->
<script type="text/javascript" src="js/jqxmenu.inc.js"></script>

<!-- VARIABLES de CONFIGURACION -->
<script type="text/javascript">
    var g_iScrollReportes = 0; // Scroll en pixeles de la grilla de reportes
    var g_iZoomSpreadThreshold = 16; // Nivel de zoom en el cual ya se muestran TODOS los moviles
    var g_iReferenceZoomThreshold = 7;
    
    var g_canAddGeoFence = <?=$objPerfil->validarSeccion('abmReferencias')?'true':'false'; ?>;
    var g_bIsFirstLoad = true; // Indica si es la PRIMERA tanda de datos de la seccion izq o solo una actualizacion 
    var g_bIsDataUpdate = false; // Indica si se requieren todos los datos de la seccion izq o solo una actualizacion 
    var g_bConReferencias = permisos['conReferencias'];
	var g_bSearchIsActive = false; //Indica si el filtro de busqueda se encuentra activo
    var g_iSearchType = SEARCH_TYPES.FILTER_AND_CHASE; //Indica el tipo de filtro de busqueda
    
    var g_dialogInfoGPS = null;
    var g_bUserDragging = false;
    var g_bShowMovChecks = false; <?/* Indica si mostrar los checkboxes de cada movil o solo un PUNTO indicatorio del estado del mismo */?>
    var g_bEmbedGPSPanel = true; <?/* Indica si incrustar el panel de datos de GPS en la columna izq */?>
    var g_bGPSPanelActive = false; // automatico, no tocar
    var g_bUngroupedMovsExpanded = true;
    
    var g_bZoomPending = false;
    var g_sGroupedIcon = "grouped_32x32_purple.png";
    //var g_iOrderingCriteria = ORDERING_CRITERIA.GROUP;
    var g_iOrderingCriteria = ORDERING_CRITERIA.CLIENT;
	var g_iMovEnSeguimiento = SIN_SEGUIR_MOVIL; // Movil en seguimiento. -1 = ninguno */?>
    var g_iAlertVolume = 50 // 100; ?>;
    
    var g_dlgConfirmarAlerta = null; <?/* Seteado automaticamente en el onReady */?>
    var g_arrAlertas = {}; <?/* Rellenado automaticamente por ajax de alertas de rastreo */?>
    var g_iCantFilasAlertas = 0;
    
    var g_bShowLatLng = false;
    var g_bResetGroupInfoWhenCriteriaChanges = false;
    var g_bHoveringMap = false;
    var $g_oOverlayLatLng = null;
    var g_oMousePos = {
        "x": 0,
        "y": 0
    };
    
    // Extiendo el objeto String con la funcion Contains (mas intuitiva)
    String.prototype.contains = function( sTexto )
    {
        return this.indexOf( sTexto ) != -1;
    }
    
    var g_bDebugMode = true;
    var g_bCommandInProcess = false;
	var buscar_response = false;
</script>
<?

require_once('includes/newtracer/newtracer.menu.inc.php');

?>
<script type="text/javascript" src="js/newtracer.js"></script>
<script type="text/javascript" src="js/newalertas.js"></script>