/**
* jQuery.fn.emiSlider
* Fecha: Mayo 2011
*
* Autor Emilio Arcioni
* Version 1.0
* Basado en DualSlider by Rob Phillips
*
* Requerimientos:
* jquery.1.3.2.js - http://jquery.com/
* jquery.easing.1.3.js - http://gsgd.co.uk/sandbox/jquery/easing/
*
**/


(function($) {

    $.fn.emiSlider = function(options) {
		
        var defaults = {
            easing: 'easeOutBack',
            duration: 1000,
			next: ".next",
			previous: ".previous",
			carousel: ".backgrounds",
			galeria: ".details",
			auto: false
        };

        var options = $.extend(defaults, options);
		var interval ="";
        this.each(function() {
			
            var obj = $(this);
			var n;
			var dist;
			var accion;
            var carousel;
			var cant;
			var itemsCarousel = $(options.carousel,obj);
			var itemsGaleria = $(options.galeria, obj);
			
            var carouselTotal = $(options.carousel, obj).children().length;
            var carouselwidth = $(itemsCarousel).children(":first-child").outerWidth();
			var carouselheight = $(itemsCarousel).children(":first-child").outerHeight();
            var detailWidth = $(itemsGaleria).children(":first-child").outerWidth();
			var detailHeight = $(itemsGaleria).children(":first-child").outerHeight();
            var locked = false;
		
			$(itemsCarousel).wrap('<div style="overflow:hidden; width:'+carouselwidth+'px; height:'+carouselheight+'px;"></div>');
			
			$(itemsGaleria).children().wrap('<a style="cursor:pointer"></a>');
			
            $(itemsCarousel).css("width", ((carouselTotal*2) * carouselwidth) + 100 + "px");
            

			//$(itemsCarousel).find("img").show();
			/*
			$(itemsCarousel).find("img").each(function(){
				$(this).show();
				//$('.contenido_slider').show();
			});
			*/
			
			$(itemsCarousel).find("img").each(function(){
				$(this).show();
				$('.contenido_slider').show();
			});
						
			$("img",$(itemsGaleria).children()).show();
            $(".contenido_slider",$(itemsGaleria).children()).show();
            
			
            $(options.next, obj).click(function() {
				sig();
				automatico();
            });
            
            $(options.previous, obj).click(function() {
                ant();
				automatico();
            });
			
			$(options.display, obj).click(function() {
				if(options.auto){
					options.auto = false;
					options.times = 0;
					clearInterval(interval);
					$('.nav.play').addClass("on");
				}
				else{
					options.auto = true;
					options.times = 7000;
					sig();
					automatico();
					$('.nav.play').removeClass("on");}
            })
			
			//actual=0;
			actual = new Array(options.slider_id);
			actual[options.slider_id]=0;
			$(itemsGaleria).children().click(function() {
						   
				n=$(this).index();
				
				//dist=n-actual;
				dist=n-actual[options.slider_id];
				if(dist<0){
					accion="prev";
				}else{
					accion="next";	
				}
				
				if(dist!=0){
					if (locked != true) {
						//actual=n;
						actual[options.slider_id] = n;
						//$(".bullet_carrousel.on").removeClass("on");
						$("."+options.bullet+".on").removeClass("on");
						$("span",this).addClass("on");
					}
					
					carouselPage(accion,Math.abs(dist));
					lock();
				}
			});

            function lock() {
                locked = true;
            }

            function unLock() {
                locked = false;
            }
			
			function automatico(){
				
				if(options.times){
					clearInterval(interval);
					interval=setInterval(sig,options.times);
				}
				else if(options.auto){
					clearInterval(interval);
					interval=setInterval(sig,5000);
				}
			}

			function ant(){
				carouselPage("prev",1);
                
				if(locked != true){
					/*
					if(actual>0){
						actual--;
					}else{
						actual=carouselTotal-1;
					}*/
					if(actual[options.slider_id]>0){
						actual[options.slider_id]--;
					}else{
						actual[options.slider_id]=carouselTotal-1;
					}
				}
				lock();
			}
			function sig(){
				
				carouselPage("next",1);
                if(locked != true){
					/*
					if(actual<carouselTotal-1){
						actual++;
					}else{
						actual=0;
					}*/
					if(actual[options.slider_id]<carouselTotal-1){
						actual[options.slider_id]++;
					}else{
						actual[options.slider_id]=0;
					}
				}
				//$(".bullet_carrousel.on").removeClass("on");
				//$(itemsGaleria).children(":nth-child("+(actual+1)+")").addClass("on");
				
				$("."+options.bullet+".on").removeClass("on");
				$(itemsGaleria).children(":nth-child("+(actual[options.slider_id]+1)+")").children().addClass("on");
				lock();
			}
			automatico();
			
			function adjust(accion,cant) {
				
				if(accion=="next"){
					for(i=0;i<cant;i++){
						$(itemsCarousel).children(":first-child").remove();
						}
				}
				
				if(accion=="prev"){
					for(i=0;i<cant;i++){
						$(itemsCarousel).children(":last-child").remove();
						
					}
				}
				
				$(itemsCarousel).css("margin-left","0px");


            }

            function carouselPage(accion,cant) {
				if (locked != true) {
                   
					if (accion=="next"){
						for(i=1;i<=cant;i++){
							$(itemsCarousel).append( $(itemsCarousel).children(":nth-child("+(i)+")").clone() );	
							
						}
						
						newPage = -carouselwidth * cant;
						newPageDetail = -detailWidth * cant;

					}
					if (accion=="prev"){
						
						for(i=carouselTotal;i>carouselTotal-cant;i--){
							$(itemsCarousel).prepend( $(itemsCarousel).children(":nth-child("+(carouselTotal)+")").clone() );
							
							
						}
						
						$(itemsCarousel).css("margin-left","-" + (carouselwidth*cant) + "px");

						newPage = 0;
						newPageDetail = 0;
					}
                    					
					
                    $(itemsCarousel).animate({
                        marginLeft: newPage
                    }, {
                        "duration": options.duration, "easing": options.easing,
                        complete: function() {
							adjust(accion,cant);
							unLock();
                        }
                    });
					
					
                }
            }

        });

    };

})(jQuery);