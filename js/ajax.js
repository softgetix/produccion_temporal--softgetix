function newReq(){
	var req;
	if(window.ActiveXObject){
		req = new ActiveXObject("Microsoft.XMLHTTP");
	}else if(window.XMLHttpRequest){
		req = new XMLHttpRequest();
	}
	return req;
}

function ajax(){
	var args=ajax.arguments,action=args[0],method=args[1],form=args[2],req,form_data,i,content_type;

	req=newReq();
	req.onreadystatechange = function (){
		if(req.readyState === 4){
			eval(req.responseText);
		}
	};

	form_data='';
	for(i=0;i<form.length;i++){
		if(form[i].type==="checkbox"){
			if(form[i].checked===true) {
				form_data+=form[i].name+'='+form[i].value+'&';
			}
		} else if(form[i].type==="radio"){
			if(form[i].checked===true) {
				form_data+=form[i].name+'='+form[i].value+'&';
			}
		} else if( form[i].type==="file" ) {
			continue;
		} else {
			form_data+=form[i].name+'='+form[i].value+'&';
		}
	}
	form_data+='form_name='+form.name;

	if(method==='POST'){
		req.open('POST', action, true);
		content_type = 'application/x-www-form-urlencoded';
//		var content_type = 'multipart/form-data'; AJAX no banca este tipo de codificacion
		if(args[4]){
			content_type = args[4];
		}
		req.setRequestHeader('Content-Type', content_type);

		req.send(form_data);
	}else if(method==='GET'){
		if(action.indexOf('?')===-1){
			action+='?'+form_data;
		} else {
			action+='&'+form_data;
		}

		req.open('GET', action, true);
		req.send(null);
	}
}

function simple_ajax(){
	var args = simple_ajax.arguments,action=dominio+args[0],method=args[1],values=args[2],req;
	req=newReq();

	req.onreadystatechange = function (){
		if(req.readyState === 4){
			
			if(req.responseText.substr(0,1) == '{'){
				var data = jQuery.parseJSON(req.responseText);
				validateSession(data);
			}
			else{	
				eval(req.responseText);
			}
		}
	};

	if( method==='POST' ){
		req.open('POST', action, true);
		req.send(values);
	}
	else{
		if (values){
			values='?'+values;
		}
		else{
			values='';
		}
		req.open('GET', action+values, true);
		req.send(null);
	}
}

function ajax_sincronico(urlInfoDatosGPS){
	$.ajax({
		async:false,
		cache:false,
		type: "POST",
		url: urlInfoDatosGPS,
		data:({}),
		success: function(req){
			eval(req);
		}
	});	
}