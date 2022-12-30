<style>
    .msg{    
        padding: 7px;
        margin-bottom: 10px;
        color: white;
    }
    .e-msg{ background: #7e1313c4;}
    .s-msg{ background: #287e13a3;}
    #route {margin-left: 0.5%;}
    a.button {margin: 10px;}
    .w-30{width: 30%;}
    .w-40{width: 40%;}
    /*.w-40 select{width: 38%;}*/
    .w-50{width: 50%;}
    #main_tbl  tbody {  
      display: block;
      max-height: 320px;
      overflow-y: scroll;
    }
    
    #detalle-modal table thead{
        background-color: #389dd8;
    }
    #detalle-modal table tbody{
        background-color: #d9e3e8;
    }
    #detalle-modal table thead td{
        border: 1px solid #389dd8;
        font-size: 15px;
        font-weight: bold;
        color: white;
        text-align: left;
        padding-left: 15px;
        border-left: 1px solid;
    }
    #detalle-modal table tbody td{
        border: 1px solid #d9e3e8;
        text-align: left;
        padding-left: 15px;
        padding-right: 5px;
        border-left: 1px solid white;
        border-top: 1px solid white;
    }
      #detalle-modal  table thead, #main_tbl thead, #main_tbl tbody tr { 
        display: table;
      width: 100%;
      table-layout: fixed;
    }
 

    @media only screen and (min-width : 320px) and (max-width : 480px) {
            .grupo{margin: 60px 0 !important;}
            .w-30 {width: 99% !important;}
            .w-40 {width: 100% !important;}
            .w-30 select{width: 100% !important;}

          #main_tbl table, #main_tbl thead, #main_tbl tbody, #main_tbl th, #main_tbl td, #main_tbl tr {
                display: block;
              }
          #main_tbl  tbody{background: #f7f7d5 !important;}    
          #main_tbl    thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
              }
          #main_tbl tr { border: 1px solid #ccc; }
          #main_tbl td {
                background: #ffffff;
                border: none;
                border-bottom: 1px solid #eee;
                position: relative;
                padding-left: 200px;
                margin-left: 150px;
              }
         #main_tbl td:before {
                background: #f7f7d5;
                position: absolute;
                top: 12px;
                left: 6px;
                width: 100px;
                padding-right: 40px;
                white-space: nowrap;
                margin-left: -150px;
              }
           #main_tbl td:nth-of-type(1):before { content: "Codigo de viage"; }
           #main_tbl td:nth-of-type(2):before { content: "Ubicación del viaje"; }
           #main_tbl td:nth-of-type(3):before { content: "Fecha de viaje"; }
           #main_tbl td:nth-of-type(4):before { content: "Detalle";}
           #main_tbl td:nth-of-type(5):before { content: "";}

              #detalle-modal table, #detalle-modal thead, #detalle-modal tbody, #detalle-modal th, #detalle-modal td, #detalle-modal tr {
                  display: block;
              }
            
              #detalle-modal table tbody tr {
                  border-bottom: 1px solid white ;
              }
              #detalle-modal  tbody{background: #389dd8 !important;}    
              #detalle-modal    thead tr {
                    position: absolute;
                    top: -9999px;
                    left: -9999px;
                  }
              #detalle-modal td {
                    background: #d9e3e8 !important ; 
                    border: none;
                    border-bottom: 1px solid #eee;
                    position: relative;
                    padding-left: 200px;
                    margin-left: 150px;
                  }
             #detalle-modal td:before {
                    background: #389dd8 !important;
                    font-size: 15px;
                    font-weight: bold;
                    color: white;
                    position: absolute;
                    top: 12px;
                    left: 6px;
                    width: 103px;
                    padding-right: 40px;
                    white-space: nowrap;
                    margin-left: -150px;
                  }

               #detalle-modal table tbody tr td:nth-of-type(1){display: none;}


               #detalle-modal td:nth-of-type(1):before { content: ""; }   
               #detalle-modal  table tbody tr:first-child td:nth-of-type(2):before { content: "Origen"; }
               #detalle-modal table tbody tr td:nth-of-type(2):before { content: "Destinos"; }
               #detalle-modal td:nth-of-type(3):before { content: "Dirección"; }
               #detalle-modal td:nth-of-type(4):before { content: "Fecha"; }
              .detalle_mdl_custom_cls{
                width: 94% !important;left: 10px !important;
              }
    }

    .detalle{
        color: #0066FF !important;
    }
    .location_name{
            color: black; font-size: 18px;
            font-weight: bold; margin-right: 10px; float: right;

    }

    .date{ width: 80px !important; margin-left: 4px;}
</style>
<div id="dialog-form" style="display:none;">
  <form method="post" id="form_assign_trip" action="?c=<?=$seccion?>">
    <fieldset>
        <input type="hidden" name="hidOperacion" id="#hidOperacion" value="assignTrip">
        <input type="hidden" name="trip_id" value="" id="trip_id">
         <select id="patente" onChange="patenteChange(this)" required name="vehicle_id" class="float_l">
            <option value="">Patente Tractor</option>
            <?php if($patente) {
                foreach ($patente as $key => $value) {
                    echo "<option  value ='".$value['vehicle_id']."'>".$value['vehicle']."</option>";
                 }
             }
            ?>
        </select>

        <select id="Semi" required name="second_vehicle_id" class="float_l">
            <option value="">Patente Semirremolque</option>
        </select>
        <select id="Configuracion" required name="configuration_id" class="float_l">
            <option value="">Configuración</option>
        </select>
        <select id="Cargabruta" required name="load_id" class="float_l">
            <option value="">Carga bruta</option>
        </select>

         <select id="Tara" required name="tara_id" class="float_l"> 
            <option value="">Tara</option>
            
        </select>    

         <select id="conductor" required name="driver_id" class="float_l"> 
            <option value="">Conductor</option>
             <?php if($conductor) {
                foreach ($conductor as $key => $value) {
                     echo "<option value = '".$value['driver_id']."'>".$value['driver']."</option>";
                  }
             }
            ?>
        </select>

        <select id="hour" required name="hour" class="float_l"> 

            <option value="">Hora estimada de arribo</option>
            <option value="0">00:00</option>
            <option value="1">01:00</option>
            <option value="2">02:00</option>
            <option value="3">03:00</option>
            <option value="4">04:00</option>
            <option value="5">05:00</option>
            <option value="6">06:00</option>
            <option value="7">07:00</option>
            <option value="8">08:00</option>
            <option value="9">09:00</option>
            <option value="10">10:00</option>
            <option value="11">11:00</option>
            <option value="12">12:00</option>
            <option value="13">13:00</option>
            <option value="14">14:00</option>
            <option value="15">15:00</option>
            <option value="16">16:00</option>
            <option value="17">17:00</option>
            <option value="18">18:00</option>
            <option value="19">19:00</option>
            <option value="20">20:00</option>
            <option value="21">21:00</option>
            <option value="22">22:00</option>
            <option value="23">23:00</option>

        </select> 

    <input type="submit" class="button colorin Tomar_viaje" tabindex="-1" value="Tomar viaje">
    </fieldset>
  </form>
</div>
<div id="dialog-table">
    <div class="solapas clear">
        <div class="contenido clear detalle_table"></div>
    </div>
</div>

<div id="main" class="sinColIzq">
    
  <div class="grupo clear" style=" margin:10px 0;">
    <h1>Cargas disponibles</h1>
     
 
    <div class="solapas clear" style="margin-bottom: 50px;"><!-- gum-->
        <?php //print_r($lang->system);?>
        <div class="contenido clear">
             <?php if(!empty($msg)){
                $class = !empty($msg['error'])?'e-msg':'s-msg';
                echo '<div class="msg '.$class.'">'.$msg['msg'].'</div>';
             }?>
            <form name="frm_<?=$seccion?>" id="frm_<?=$seccion?>" action="?c=<?=$seccion?>" method="post">
                 <div class="w-30">
                    <input type="text" name="s_trip_code" id="s_trip_code"  title=""  placeholder=" Ticket N°" value="<?=!empty($_POST['s_trip_code'])?$_POST['s_trip_code']:'';?>" autocomplete="off"/>
                </div>
                  <div>
                        <select id="origin" name="origin_id" class="float_l w-30">
                            <option value="">Todas las plantas</option>
                            <?php if($origen) {
                                foreach ($origen as $key => $value) {
                                    $o_value = !empty($_POST['origin_id'])?$_POST['origin_id']:'';
                                    $o_selected = ($o_value== $value['re_id'])?'selected':'';
                                    echo "<option $o_selected value ='".$value['re_id']."'>".$value['re_name']."</option>";
                                 }
                             }
                            ?>
                        </select>
                        <select id="route" name="route_id" class="float_l w-30"> 
                            <option value="">Destino</option>
                             <?php if($ruta) {
                                foreach ($ruta as $key => $value) {
                                    $r_value = !empty($_POST['route_id'])?$_POST['route_id']:'';
                                    $r_selected = ($r_value== $value['route_code'])?'selected':'';
                                    echo "<option $r_selected value = '".$value['route_code']."'>".$value['route_description']."</option>";
                                 }
                             }
                            ?>
                        </select> 
                        <input type="text" class="date float_l" name="fecha" value="<?=$_POST['fecha']?>" autocomplete="off" placeholder="Fecha de Carga" style="width: 100px !important;">

                        <a class="button buscar float_l" style="margin-top:0px" href="javascript:void('0');"><?=$lang->botonera->buscar?></a>
                      
                  </div> 
                  <span class="clear"></span>
            </form>
            <table id="main_tbl" width="100%" height="100%">
                <thead>
                    <tr>
                        <td><span class="campo1">Ticket N°</span></td>
                        <td><span class="campo1">Planta</span></td>
                        <td><span class="campo1">Fecha de carga</span></td>
                        <td><span class="campo1">Destino</span></td>
                        <td><span class="campo1">Volumen de carga</span></td>
                        <td><span class="campo1">Detalle</span></td>
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                <?php if($result){
                    foreach($result as $i => $item){ ?>
                        <tr class="<?=((count($result) - 1)==$i)?'tr-last':''?>">
                            <td><?php echo $item['trip_code'] ?></td>
                            <td data-th><?php echo $item['trip_location'] ?></td>
                            <td data-th><?php echo $item['trip_Date']; ?></td>
                            <td data-th><?php echo $item['route']; ?></td>
                            <td data-th><?php echo $item['type']; ?></td>
                            <td data-th><a href='javascript:void(0)' class="detalle" data-id ="<?=$item['trip_id']?>">ver detalle</a></td>
                            <td data-th> <a class="button colorin assign_trip" href="javascript:void('0');" data-id ="<?=$item['trip_id']?>">Tomar viaje</a></td>
                        </tr>
                    <?php } 
                    }
                    else{?>
                        <tr class="tr-last">
                            <td class="td-last" colspan="5"><center><?=$lang->message->sin_resultados?></center></td>
                        </tr>
                    <?php }?>
                </tbody>
            </table>
        </div>
    </div>
   </div>   

   <div id="detalle-modal" style="display:none;"></div>
  
   <div id="TakeTrip-modal" style="display:none;"></div>

</div>
<script type="text/javascript">
$('.buscar').click(function(){
    $('#frm_availableTrip').submit();
})
  function patenteChange(sel){ 
        var patente = sel.value;

        if(patente != ""){
            $.ajax({ 
                async:false,
                cache:false,
                url: 'boot.php?c=availableTrip', 
                type: "POST",
                dataType:"json",
                cache: false, 
                data:({
                        hidOperacion:'addComboboxValue',
                        patente:patente,
                }),
                success:function(result){ 
                    
                    if(result.Semi){

                        var html = "";
                        for(var i=0;i<result.Semi.length;i++){
                            $selected = result.Semi[i].second_vehicle_default ? 'selected' : '';
                            html += "<option value="+result.Semi[i].second_vehicle_id+" "+$selected+">"+result.Semi[i].second_vehicle+"</option>";
                        }

                        $(".taketrip_mdl_custom_cls #Semi").html(html);
                    }

                    if(result.Configuracion){

                        var html = "";

                        for(var i=0;i<result.Configuracion.length;i++){
                            $selected = result.Configuracion[i].configuration_default ? 'selected' : '';
                            html += "<option value="+result.Configuracion[i].configuration_id+" "+$selected+">"+result.Configuracion[i].configuration_description+"</option>";
                        }
                        console.log("html",html);
                        $(".taketrip_mdl_custom_cls #Configuracion").html(html);
                    }

                    if(result.Cargabruta){

                        var html = "";

                        for(var i=0;i<result.Cargabruta.length;i++){
                            $selected = result.Cargabruta[i].load_default ? 'selected' : '';
                            html += "<option value="+result.Cargabruta[i].load_id+" "+$selected+">"+result.Cargabruta[i].load_description+"</option>";
                        }

                        $(".taketrip_mdl_custom_cls #Cargabruta").html(html);
                    }


                    if(result.Tara){

                        var html = "";

                        for(var i=0;i<result.Tara.length;i++){
                            $selected = result.Tara[i].tara_default ? 'selected' : '';
                            html += "<option value="+result.Tara[i].tara_id+" "+$selected+">"+result.Tara[i].tara_description+"</option>";
                        }

                        $(".taketrip_mdl_custom_cls #Tara").html(html);
                    }
                }
            }); 
        }

    } 
$(document).ready(function(){

    if ( window.history.replaceState ) {
      window.history.replaceState( null, null, window.location.href );
    } 
    setTimeout(function() {
        $(".msg").fadeOut("slow");
    }, 3500);

    $('.detalle').click(function(){

        var trip_id = $(this).attr("data-id");
        var request = $.ajax({ 
            async:false,
            cache:false,
            url: 'boot.php?c=availableTrip', 
            type: "POST",
            dataType:"json",
            cache: false, 
            data:({
                    accion:'ver_detalle',
                    hidOperacion:'verDetalle',
                    trip_id:trip_id,
            })
        }); 
      request.done(function(msg) {
        if(msg == "Error"){
            var html = "Oops Something went wrong !";
        }else{
            var data = "";
            var n = 1;
            for(var i=0;i<msg.length;i++){

                if(msg[i].destination_name){ var d_name = msg[i].destination_name}else{var d_name = "";}
                if(msg[i].destination_address){ var d_address = msg[i].destination_address}else{var d_address = "";}
                if(msg[i].destination_Date){ var d_date = msg[i].destination_Date}else{var d_date = "";}

                if(n == 1){
                    var name = "Origen";
                }else{
                    var name = "Destinos";
                }

               

                data += "<tr style='height:0px;'>"
                            +"<td><span class='location_name'>"+name+":</span></td>"
                            +"<td><span class=''>"+d_name+"</span></td>"
                            +"<td><span class=''>"+d_address+"</span></td>"
                            +"<td><span class=''>"+d_date+"</span></td>"
                        +"</tr>";

                n++;        
            }

            var html = 
                "<table width='100%' height='100%'>"
                        +"<thead>"
                             +"<tr>"
                                +"<td><span class=''></span></td>"
                                +"<td><span class=''></span></td>"
                                +"<td><span class=''>Dirección</span></td>"
                                +"<td><span class=''>Fecha</span></td>"
                            +"</tr>"
                        +"</thead>"
                        +"<tbody>"
                        +data
                        +"</tbody>"
                +"</table>";
        }
                
            $("#detalle-modal").html(html);
            $("#detalle-modal").dialog("destroy");
            $("#detalle-modal").dialog({
                resizable: false,
                draggable: false,
                width: 740,
                title: "Detalle",
                modal: true,
                dialogClass: 'detalle_mdl_custom_cls'
          });    
        });

             

    });   

    $('.assign_trip').click(function(){
        $("#trip_id").val('');
        var trip_id = $(this).attr("data-id");
        $("#trip_id").val(trip_id);

        var html = $("#dialog-form").html();
          $("#TakeTrip-modal").html(html);
            $("#TakeTrip-modal").dialog("destroy");
            $("#TakeTrip-modal").dialog({
                resizable: false,
                draggable: false,
                width: 370,
                modal: true,
                dialogClass: 'taketrip_mdl_custom_cls'
          });    
    });

   	$(".date").live("focusin", function() { 
		$(this).datepicker({
			onSelect: function(objDatepicker){
				var fecha = $(this).val().replace('/','-');
				var fecha = fecha.replace('/','-');
				$(this).val(fecha);

                //$(this).datepicker({});
			}
		});
	});


})
</script>