// JavaScript Document
function cargarNodo(nodo,wizard)
{

	if($('#opcion').length)
	{
		if((isNaN($('input[name=\'opcion\']:checked').val())))
		{
      		eval($('input[name=\'opcion\']:checked').val());
	  		exit();
		}
	}
	
	$("#nodo").val(nodo);
	$("#wizard").val(wizard);
	$("#formWizard").submit();
	
}

function cargarWizard(wizard,ruta)
{
  //alert(ruta);
  if(ruta!=undefined)
  {
	document.location.href="boot.php?c=wizards&wizard="+wizard+"&ruta="+ruta;  
	exit();
  }
  document.location.href="boot.php?c=wizards&wizard="+wizard;	
}

function goBack(wizard)
{
  
  var nodoAnt=$("#trace").val().split(",");
  $("#curso").val('back');
  //alert(nodoAnt[nodoAnt.length-2]);
  //alert(wizard);
  //alert(nodoAnt.length-2);
  cargarNodo(nodoAnt[nodoAnt.length-2], wizard);
  
}

function cargarURL(url)
{
   window.location=url;
}

function cargarURLParent(url)
{
  parent.window.location=url;
}