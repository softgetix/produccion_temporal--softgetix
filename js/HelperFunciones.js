// JavaScript Document
function cargarCapa(wizard,nodo,volver)
{
	if(nodo==undefined)
	{
	$("#capaframe").load("boot.php?c=wizards&wizard="+wizard+" #maincontent")
	}else
    {
	$("#capaframe").load("boot.php?c=wizards&wizard="+wizard+"&nodo="+nodo+" #maincontent")
    }
}