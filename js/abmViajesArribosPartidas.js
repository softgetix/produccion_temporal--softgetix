$(document).ready(function(){
	setTimeout("vistaArribos(solapa)",600000);


	$('div.traficoView').click(function(e){
		
		var $popup = '.topupTrafico .showPopup';
		var $msg = $(this).children('span').html();

		if($msg.length > 1500){
			var $dl = $($popup).children('div.contenedorPopup').children('dl');
			$dl.css('overflow-y','scroll');	
			$dl.css('width','auto');
			$dl.css('height','240px');	
		}
		
		$($popup+' p').html($(this).children('span').html());
		$($popup).show();
	});

	$('a.button.cancel').click(function(e){
        $(this).parent().parent().parent().css('display','none');
    });
});

function vistaArribos(solapa){
	enviar('filtrarCol');
}