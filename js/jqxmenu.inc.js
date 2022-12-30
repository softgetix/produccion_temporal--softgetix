// Requiere:
// - jQuery
// - jQuery Tools.

// var menu1 = new jqxmenu( "infogpsContextMenu", { "handlers": {""} } );

jqxmenu = function(menuid, menuconfig)
{
    this.menuid = $.trim(menuid);
    
    this.updateGUI = function(mainMenuID)
    {
        $(mainMenuID + " *").css( { "font-weight": "inherit" } );
        $(mainMenuID + " li[active='true']").css( { "font-weight": "bold" } );
        $(mainMenuID + " li[active='false']").css( { "font-weight": "normal" } );
    }
    
    var _this = this;
    
    if ( typeof this.menuid == "string" && this.menuid != "" )
    {
        var $menu = $("#" + this.menuid);
        
        $menu.menu();
        this.updateGUI("#" + this.menuid);

        if ( typeof menuconfig != "undefined" )
        {
            if ( typeof menuconfig.handlers != "undefined" )
            {
                $("#" + menuid + " li").bind( "click", function(ev)
                {
                    var id = $(this).attr("id");
                    //debug.log("ID elem menu: " + id);
                    //
                    // Si es una opcion grupal, selecciono SOLO la que corresponde
                    sOptgroup = $(this).attr("group");
                    if ( sOptgroup )
                    {
                        //debug.warn("grupo '" + sOptgroup + "'");
                        $("li[group='" + sOptgroup + "']").attr("active", "false");
                        $(this).attr("active", true);
                    }

                    if ( menuconfig.handlers[id] )
                    {
                        //debug.log("CALLED MENU '" + id + "'");
                        menuconfig.handlers[id]();
                    }

                    $("#" + menuid + "Container").hide();

                    _this.updateGUI("#" + menuid);
                });
            }
        }
    }
}