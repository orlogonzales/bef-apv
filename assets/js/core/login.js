$(document).ready(function(){
	$('#login').click(function(){
		var usuario    =$("#usuario").val();
		var clave      =$("#clave").val();
		var datos = 'usuario='+usuario+'&clave='+clave;
		if($.trim(usuario).length>0 && $.trim(clave).length>0){
			$.ajax({
				type: "POST",
				url: "php/acceso.php",
				data: datos,
				cache: false,
				dataType:'json',
				beforeSend: function(){
					$("#procesandoLogin").fadeIn('slow').html('<div class="form-group"><div class="row"><div class="col-xs-12 text-center"><img src="assets/images/loader.gif"></div></div></div>');
					$("#login").attr("disabled", "disabled");
				},
				success: function(respuesta){
					$("#procesandoLogin").fadeOut('slow');
					if(respuesta.login=="ADM"){ window.location.replace("administrador/"); }else{ window.location.replace("usuario/"); }

					if(respuesta.login=="USUARIOINACTIVO"){
						$("#login").removeAttr("disabled");
						new PNotify({ text: 'Usuario inactivo...', addclass: 'bg-warning alert-styled-right', type: 'warning' });
					}
					if(respuesta.login=="NOEXISTE"){
						$("#login").removeAttr("disabled");
						new PNotify({ text: 'Usuario no existe...', addclass: 'bg-warning alert-styled-right', type: 'warning' });
					}
				}
			});
		}else{
			$('#usuario').focus();
			new PNotify({ text: 'Ingrese Usuario y Clave', addclass: 'bg-danger alert-styled-right', type: 'error' });
		}
		return false;
	});
});
