///////////////////////////////////////////////////////////////////////////////////////////////
/// OCULTA MODAL
///////////////////////////////////////////////////////////////////////////////////////////////
function muestraModal(modal){
	$(modal).modal('show');
}

function cierraModal(modal){
	$(modal).modal('hide');
	$(modal).removeData('bs.modal');
	$(modal).removeData('modal');
	$(modal).removeData();
	$(modal).find('form').trigger('reset');
	$('body').removeClass('modal-open');
	$('.modal-backdrop').remove();
}

function ocultaModal(modal){
	$(modal).modal('hide');
	/*
	$(modal).on('hidden.bs.modal', function () {
		$('.modalclass').remove();
		$(modal).removeData('bs.modal');
		$(modal+' .modal-body').html('');
	});
	*/
}

///////////////////////////////////////////////////////////////////////////////////////////////
/// MAYUSCULAS Y MINUSCULAS
///////////////////////////////////////////////////////////////////////////////////////////////
function mayusculas(e, elemento) {
	tecla=(document.all) ? e.keyCode : e.which; 
	elemento.value = elemento.value.toUpperCase();
}

function minusculas(e, elemento) {
	tecla=(document.all) ? e.keyCode : e.which; 
	elemento.value = elemento.value.toLowerCase();
}

(function(a){a.fn.validar=function(b){a(this).on({keypress:function(a){var c=a.which,d=a.keyCode,e=String.fromCharCode(c).toLowerCase(),f=b;(-1!=f.indexOf(e)||9==d||37!=c&&37==d||39==d&&39!=c||8==d||46==d&&46!=c)&&161!=c||a.preventDefault()}})}})(jQuery);

$(function(){
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// DATEPICKER CONFIGURACION REGIONAL
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$.datepicker.regional['es'] = {
		closeText: 'Cerrar', 
		prevText: 'Previo', 
		nextText: 'PrÃ³ximo',

		monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
		monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
		monthStatus: 'Ver otro mes', yearStatus: 'Ver otro aÃ±o',
		dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
		dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','SÃ¡b'],
		dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sa'],
		dateFormat: 'dd/mm/yy', firstDay: 0, 
		initStatus: 'Selecciona la fecha', isRTL: false};
		$.datepicker.setDefaults($.datepicker.regional['es']
	);


	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// DATEPICKER FECHAS
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$(".fechas").datepicker({
		showButtonPanel: true,
		format: 'dd/mm/yyyy',
		altFormat: "DD, d MM, yy"
	});

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// TOOLTIPS
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$('[data-popup="tooltip"]').tooltip();

	$('.seleccion').select2();

});







