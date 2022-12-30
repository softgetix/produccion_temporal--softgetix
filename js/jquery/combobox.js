(function( $ ) {
    $.widget( "ui.combobox", {
        _create: function() {
            var self = this,
            select = this.element.hide(),
            selected = select.children( ":selected" ),
            value = selected.val() ? selected.text() : "",
            input = this.input = $( "<input>" )
            .insertAfter( select )
            .val( value )
			.focus(function(){
				//$(this).val('');
				//$("#auto_"+select.attr('id')).keydown();
				$(this).autocomplete( "search", "" );
				$(this).keydown();
			})
            .attr('id','auto_'+select.attr('id'))
            .autocomplete({
                delay: 50,
                minLength: 1,
                source: function(request, response) {
					if (request.term == "") {
						try {
							response(select.children("option")[0]);
							return;
						}
						catch(ex) {}
					}
                    var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
                    response( select.children( "option" ).map(function() {
                        var text = $( this ).text();
                        if ( this.value && ( !request.term || matcher.test(text) ) ){
                            return {
                                label: text.replace(
                                    new RegExp(
                                        "(?![^&;]+;)(?!<[^<>]*)(" +
                                        $.ui.autocomplete.escapeRegex(request.term) +
                                        ")(?![^<>]*>)(?![^&;]+;)", "gi"
                                        ), "<strong>$1</strong>" ),
                                value: text,
                                option: this
                            };
                        }
                    }) );
                },
                
                select: function( event, ui ) {
					ui.item.option.selected = true;
                    self._trigger( "selected", event, {
                        item: ui.item.option
                    });
                    select.change();
                },
                
                change: function( event, ui ) {
                    if ( !ui.item ) {
                        var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" ),
                        valid = false;
                        select.children( "option" ).each(function() {
                            if ( $( this ).text().match( matcher ) ) {
                                this.selected = valid = true;
                                return false;
                            }
                            select.change();
                        });
                        if ( !valid ) {
                            // remove invalid value, as it didn't match anything
                            select.val( "0" );//dependo de que siempre exista el option 0 vacio
                            input.data( "autocomplete" ).term = "";
                            if (!$(this).hasClass('showTip')){
                                //$( this ).val( "" );
                                if ($(this).hasClass('conTip')){
                                    //$(this).blur();
                                }
                            }
                            
                            // Si tiene esta clase.
                            if (select.hasClass('clearOnError')){
                                $(this).val("");
                                if ($(this).hasClass('conTip')){
                                    $(this).blur();
                                }
                            }
                            return false;
                        }
                    }
                }
            }).focus(function() {
				$(this).autocomplete("search", "");
			});
            if (this.options.width){
                input.width(this.options.width);
            }
            input.data( "autocomplete" )._renderItem = function( ul, item ) {
                return $( "<li></li>" )
                .data( "item.autocomplete", item )
                .append( "<a>" + item.label + "</a>" )
                .appendTo( ul );
            };
        },
        destroy: function() {
            this.input.remove();
            this.element.show();
            $.Widget.prototype.destroy.call( this );
        }
    });

    $(document).ready(function(){
        $('select.combobox').each(
            function(){
                var $this=$(this),options={};
                if ($this.hasClass('txtFiltro')){
                    options.addClass('txtFiltro');
                };
                if ($this.hasClass('clearOnError')){
                    //options.addClass('clearOnError');
                };
                if($this.hasClass('width')){
                    options.width=$this.width();
                };
                $this.combobox(options);
            });
			

    });
}( jQuery ));