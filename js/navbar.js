var arrowimages={
			//down:['downarrowclass', 'imagenes/rdsarrow-down.gif', 8], 
			down:['downarrowclass', null, 8], 
			right:['rightarrowclass', 'imagenes/rdsarrow-right.gif']
}

var superMenu={

	buildmenu:function(menuid, arrowsvar){
		jQuery(document).ready(function($){
			var $mainmenu=$("#"+menuid+">ul");
			var $headers=$mainmenu.find("ul").parent();
			$headers.each(function(i){
				var $curobj=$(this);
				var $subul=$(this).find('ul:eq(0)');
				this._dimensions={
					w: this.offsetWidth, 
					h: this.offsetHeight, 
					subulw: $subul.outerWidth(), 
					subulh: $subul.outerHeight()
				}
				this.istopheader = $curobj.parents("ul").length==1? true : false;			
				$subul.css({top:this.istopheader? this._dimensions.h+"px" : 0});
				$curobj.children("a:eq(0)").css(this.istopheader? {paddingRight: arrowsvar.down[2]} : {}).append(
					(!this.istopheader)?		
						'<img src="'+ (this.istopheader? arrowsvar.down[1] : arrowsvar.right[1])
						+'" class="' + (this.istopheader? arrowsvar.down[0] : arrowsvar.right[0])
						+ '" style="border:0;" />':''
				);
				
				$curobj.hover(
					function(e){
						var $targetul=$(this).children("ul:eq(0)")
						this._offsets={left:$(this).offset().left, top:$(this).offset().top}
						var menuleft=this.istopheader? 0 : this._dimensions.w;
						menuleft = (this._offsets.left+menuleft+this._dimensions.subulw>$(window).width())? (this.istopheader? -this._dimensions.subulw+this._dimensions.w : -this._dimensions.w) : menuleft;
						$targetul.css({left:menuleft+"px"}).show();
					},
					function(e){
						$(this).children("ul:eq(0)").hide();
					}
				); 
			});
			$mainmenu.find("ul").css({display:'none', visibility:'visible'});
		}); 
	}
}


superMenu.buildmenu("localizartMenu", arrowimages);
