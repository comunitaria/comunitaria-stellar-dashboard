/**
 * This plug-in for DataTables represents the ultimate option in extensibility
 * for sorting date / time strings correctly. It uses
 * [Moment.js](http://momentjs.com) to create automatic type detection and
 * sorting plug-ins for DataTables based on a given format. This way, DataTables
 * will automatically detect your temporal information and sort it correctly.
 *
 * For usage instructions, please see the DataTables blog
 * post that [introduces it](//datatables.net/blog/2014-12-18).
 *
 * @name Ultimate Date / Time sorting
 * @summary Sort date and time in any format using Moment.js
 * @author [Allan Jardine](//datatables.net)
 * @depends DataTables 1.10+, Moment.js 1.7+
 *
 * @example
 *    $.fn.dataTable.moment( 'HH:mm MMM D, YY' );
 *    $.fn.dataTable.moment( 'dddd, MMMM Do, YYYY' );
 *
 *    $('#example').DataTable();
 */
function dateDeFecha(fechaNuestra){
	var parte=fechaNuestra.split(" ");
	var partesFecha=parte[0].split("/");
	var partesHora=[];
	if (parte.length>1){
		partesHora=parte[1].split(":");
	}
	var dia=parseInt(partesFecha[0]);
	var mes=0;
	if (partesFecha.length>1) mes=parseInt(partesFecha[1])-1;
	var ano=1900;
	if (partesFecha.length>2) ano=parseInt(partesFecha[2]);
	var hora=0;
	if (partesHora.length>0) hora=parseInt(partesHora[0]);
	var minuto=0;
	if (partesHora.length>1) minuto=parseInt(partesHora[1]);
	var segundo=0;
	if (partesHora.length>2) segundo=parseInt(partesHora[2]);
	return new Date(ano,mes,dia,hora,minuto,segundo);	
}

(function (factory) {
	if (typeof define === "function" && define.amd) {
		define(["jquery", "moment", "datatables.net"], factory);
	} else {
		factory(jQuery, moment);
	}
}(function ($, moment) {

$.fn.dataTable.moment = function ( format, locale ) {
	var types = $.fn.dataTable.ext.type;

	// Add type detection
	types.detect.unshift( function ( d ) {
		if ( d ) {
			// Strip HTML tags and newline characters if possible
			if ( d.replace ) {
				d = d.replace(/(<.*?>)|(\r?\n|\r)/g, '');
			}

			// Strip out surrounding white space
			d = $.trim( d );
		}

		// Null and empty values are acceptable
		if ( d === '' || d === null ) {
			return 'moment-'+format;
		}

		return moment( d, format, locale, true ).isValid() ?
			'moment-'+format :
			null;
	} );

	// Add sorting method - use an integer for the sorting
	types.order[ 'moment-'+format+'-pre' ] = function ( d ) {
		if ( d ) {
			// Strip HTML tags and newline characters if possible
			if ( d.replace ) {
				d = d.replace(/(<.*?>)|(\r?\n|\r)/g, '');
			}

			// Strip out surrounding white space
			d = $.trim( d );
		}
		
		return !moment(d, format, locale, true).isValid() ?
			Infinity :
			parseInt( moment( d, format, locale, true ).format( 'x' ), 10 );
	};
};

}));
$.fn.dataTable.moment( 'DD/MM/YYYY HH:mm:ss' );
	
$.fn.DataTableFiltrada = function(config) {

	var este=this;
	var initCompleteInicial=config.initComplete;
    var tabla;
	config.initComplete=function(settings,json){
		var filtroColumna=este.data('filtroColumna');
			
		for (var indice=0;indice<tabla.columns().header().length;indice++){
			filtroColumna.push({tipo:'string',filtro:'',valor1:'',valor2:'',funcion:null, visible:true});
			var tipo="string";
			var columnas=tabla.columns().header().length;
			if(config.columnDefs){
				for(var i in config.columnDefs){
					var esEsta=(config.columnDefs[i].targets=='_all')||
								(config.columnDefs[i].targets==indice)||
								(columnas+config.columnDefs[i].targets==indice);
					if (!esEsta){
						for(var t in config.columnDefs[i].targets){
							esEsta= (config.columnDefs[i].targets[t]==indice)||
									(columnas+config.columnDefs[i].targets[t]==indice);
							if (esEsta) break;
						}
					}
					if (esEsta){
						if (config.columnDefs[i].type){
							filtroColumna[indice].tipo=config.columnDefs[i].type;
						}
						filtroColumna[indice].visible=(config.columnDefs[i].visible!=false);
						break;
					}
				}
			}
			$.fn.dataTable.ext.search.push(function(settings, searchData, index){
				var vale=true;
				var filtroColumna=$("#"+settings.nTable.id).data('filtroColumna');
				for (var i=0;i<filtroColumna.length;i++){
					switch (filtroColumna[i].tipo){
						case 'fecha':
							var hoy=new Date();
							var diaDeHoy=hoy.getDate();
							var esteMes=hoy.getMonth();
							var esteAno=hoy.getFullYear();
							var dateCelda=dateDeFecha(searchData[i]);
							switch(filtroColumna[i].filtro){
								case 'Hoy': vale=vale&&(dateCelda.getDate()==diaDeHoy)&&(dateCelda.getMonth()==esteMes)&&(dateCelda.getFullYear()==esteAno); break;
								case 'Mes': vale=vale&&(dateCelda.getMonth()==esteMes)&&(dateCelda.getFullYear()==esteAno); break;
								case 'Año': vale=vale&&(dateCelda.getFullYear()==esteAno); break;
								case 'Desde': vale=vale&&dateDeFecha(searchData[i])>=filtroColumna[i].valor1; break;
								case 'Hasta': vale=vale&&dateDeFecha(searchData[i])<=filtroColumna[i].valor1; break;
								case 'Entre': vale=vale&&dateDeFecha(searchData[i])>=filtroColumna[i].valor1&&dateDeFecha(searchData[i])<=filtroColumna[i].valor2; break;
							}
							break;
						case 'string':
							switch(filtroColumna[i].filtro){
								case 'Igual': 
								case 'Lista': vale=vale&&searchData[i].toUpperCase()==filtroColumna[i].valor1; break;
								case 'Contiene': vale=vale&&searchData[i].toUpperCase().includes(filtroColumna[i].valor1); break;
							}
							break;
						case 'num':
							var txtCelda=searchData[i];
							if (txtCelda.indexOf('.')<txtCelda.indexOf(',')){
								txtCelda=txtCelda.replace('.','');
							}
							var valorCelda=parseFloat(txtCelda.replace(',','.'));
							if (!isNaN(valorCelda)){
								switch(filtroColumna[i].filtro){
									case 'Igual': vale=vale&&valorCelda==filtroColumna[i].valor1; break;
									case 'Menor': vale=vale&&valorCelda<filtroColumna[i].valor1; break;
									case 'Mayor': vale=vale&&valorCelda>filtroColumna[i].valor1; break;
									case 'Entre': vale=vale&&valorCelda>=filtroColumna[i].valor1&&valorCelda<=filtroColumna[i].valor2; break;
								}
							}
							else vale=false;
							break;
					}
				}
				if (!vale)
					vale=false;
				$("#"+settings.nTable.id).data('filtroColumna',filtroColumna);
				return vale;
			});;

		}
		este.find('th').append('<i class="fa fa-filter iconofiltro"></i>');
		este.find('th').off('click.DT').css('outline','none');
		//este.parents(".dataTables_wrapper").find(".dataTables_filter").hide();
		function borraDialogos(){
			$("#datatablefiltro_menu").remove();
			$("#datatablefiltro_info").remove();
			var offset=1;
			for (var i=0;i<tabla.columns().header().length;i++){
				if (este.data('filtroColumna')[i].filtro==''){
					este.find('th:nth-child('+(i+offset)+') i').hide();
				}
				else{
					este.find('th:nth-child('+(i+offset)+') i').show();	
				}
				if (!este.data('filtroColumna')[i].visible) offset--;
			}
		}
		este.on('click.DT',"tbody",function(e){
			borraDialogos();
		});
		este.on('click.DT',"thead th",function(e){
			borraDialogos();
			var indice=tabla.column(this).index();
			if (este.data('filtroColumna')[indice].tipo!='check'){				
				var activo=este.data('filtroColumna')[indice].filtro;
				var htmlCuadro='<div class="dropdown-menu" id="datatablefiltro_menu" style="position:absolute;display:block;left:'+this.offsetLeft+'px;top:'+(this.offsetTop+this.offsetHeight)+'px">'+
							'<div class="datatablefiltro_opcion" id="menuAsc"><small>Orden Ascendente</small></div>'+
							'<div class="datatablefiltro_opcion" id="menuDesc"><small>Orden Descendente</small></div>'+
							'<hr>'+(activo!=''?'<div class="datatablefiltro_opcion" id="menuSin"><small>sin filtro</small><img style="width:16px;color:red;float:right" src="data:image/png;base64,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"/></div>':'');
				switch(este.data('filtroColumna')[indice].tipo){
					case 'fecha':
		
						htmlCuadro+='<div class="datatablefiltro_opcion" id="menuHoy"><small '+(activo=="Hoy"?'class="activo"':'')+'>hoy</small></div>'+
									'<div class="datatablefiltro_opcion" id="menuMes"><small '+(activo=="Mes"?'class="activo"':'')+'>este mes</small></div>'+
									'<div class="datatablefiltro_opcion" id="menuAño"><small '+(activo=="Año"?'class="activo"':'')+'>este año</small></div>'+
									'<div class="datatablefiltro_opcion" id="menuDesde"><small '+(activo=="Desde"?'class="activo"':'')+'>desde ...</small></div>'+
									'<div class="datatablefiltro_opcion" id="menuHasta"><small '+(activo=="Hasta"?'class="activo"':'')+'>hasta ...</small></div>'+
									'<div class="datatablefiltro_opcion" id="menuEntre"><small '+(activo=="Entre"?'class="activo"':'')+'>entre ...</small></div>';
									break;
					case 'string':
						htmlCuadro+='<div class="datatablefiltro_opcion" id="menuIgual"><small '+(activo=="Igual"?'class="activo"':'')+'>igual a ...</small></div>'+
									'<div class="datatablefiltro_opcion" id="menuContiene"><small '+(activo=="Contiene"?'class="activo"':'')+'>contiene ...</small></div>'+
									'<div class="datatablefiltro_opcion" id="menuLista"><small '+(activo=="Lista"?'class="activo"':'')+'>selección ...</small></div>';
									break;
					case 'num':
						htmlCuadro+='<div class="datatablefiltro_opcion" id="menuIgualNum"><small '+(activo=="Igual"?'class="activo"':'')+'>igual a ...</small></div>'+
									'<div class="datatablefiltro_opcion" id="menuMenor"><small '+(activo=="Menor"?'class="activo"':'')+'>menor que ...</small></div>'+
									'<div class="datatablefiltro_opcion" id="menuMayor"><small '+(activo=="Mayor"?'class="activo"':'')+'>mayor que ...</small></div>'+
									'<div class="datatablefiltro_opcion" id="menuEntreNum"><small '+(activo=="Entre"?'class="activo"':'')+'>entre ...</small></div>';
									break;
				}			
				htmlCuadro+='</div>';
				este.parent().append(htmlCuadro);
				var htmlInfo='<div class="dropdown-menu" id="datatablefiltro_info" style="position:absolute;display:none;left:'+(this.offsetLeft+$("#datatablefiltro_menu").width())+'px;top:'+(this.offsetTop+this.offsetHeight+100)+'px"></div>';
				este.parent().append(htmlInfo)
					
				$("#menuAsc").click(function(){
					tabla.order([[indice,"asc"] ])
						.draw();
					borraDialogos();
				});
				$("#menuDesc").click(function(){
					tabla.order([[indice,"desc"]])
						.draw();
					borraDialogos();
				});
				$("#menuSin").click(function(){
					//$.fn.dataTable.ext.search.splice($.fn.dataTable.ext.search.indexOf(myFilterFunction, 1));
					este.data('filtroColumna')[indice].filtro="";	
					tabla
						//.column( indice )
						//.search( '')
						.draw();
					borraDialogos();
				});
				$("#menuHoy").click(function(){
					este.data('filtroColumna')[indice].filtro="Hoy";	
					tabla.draw();
					borraDialogos();
				});
				$("#menuMes").click(function(){
					este.data('filtroColumna')[indice].filtro="Mes";	
					tabla.draw();
					borraDialogos();
				});
				$("#menuAño").click(function(){
					este.data('filtroColumna')[indice].filtro="Año";	
					tabla.draw();
					borraDialogos();
				});
				$("#menuIgual").click(function(){
					$("#datatablefiltro_info").html('<input id="datatablefiltro_inputText"  class="form-control" type="text">').show();
					$("#datatablefiltro_inputText").focus();
					$("#datatablefiltro_inputText").keyup(function(e){
						if (e.keyCode==13){
							este.data('filtroColumna')[indice].filtro="Igual";	
							este.data('filtroColumna')[indice].valor1=$("#datatablefiltro_inputText").val().toUpperCase();	
							tabla.draw();
							borraDialogos();
						}
						if (e.keyCode==27){
							$("#datatablefiltro_info").hide();		
						}
					});
				});
				$("#menuContiene").click(function(){
					$("#datatablefiltro_info").html('<input id="datatablefiltro_inputText"  class="form-control" type="text">').show();
					$("#datatablefiltro_inputText").focus();
					$("#datatablefiltro_inputText").keyup(function(e){
						if (e.keyCode==13){
							este.data('filtroColumna')[indice].filtro="Contiene";	
							este.data('filtroColumna')[indice].valor1=$("#datatablefiltro_inputText").val().toUpperCase();	
							tabla.draw();
							borraDialogos();
						}
						if (e.keyCode==27){
							$("#datatablefiltro_info").hide();		
						}
					});
				});
				$("#menuLista").click(function(){
					var lista=tabla.cells('',indice).render('filter').toArray().filter(function(value, index, self){return self.indexOf(value) === index;}).sort();
					var html='<select class="form-control" id="datatablefiltro_select"><option disabled selected>Seleccione un valor...</option>';
					for(var i=0;i<lista.length;i++){
						html+='<option>'+lista[i]+'</option>';
					}
					html+='</select>';
					$("#datatablefiltro_info").html(html).show();
					$("#datatablefiltro_select").change(function(e){
							este.data('filtroColumna')[indice].filtro="Lista";	
							este.data('filtroColumna')[indice].valor1=$("#datatablefiltro_select").val().toUpperCase();	
							tabla.draw();
							borraDialogos();
						
					});
					$("#datatablefiltro_select").keyup(function(e){
						if (e.keyCode==27){
							$("#datatablefiltro_info").hide();		
						}
					});
				});
				$("#menuDesde").click(function(){
                    var valorActual='';
                    if (este.data('filtroColumna')[indice].filtro=="Desde")	
                        valorActual=este.data('filtroColumna')[indice].valor1.toLocaleDateString();	
                    $("#datatablefiltro_info").html('<input id="datatablefiltro_fecha" value="'+valorActual+'" class="form-control" style="width:auto" type="text">').show();
					$("#datatablefiltro_fecha").datepicker({
						autoclose: true,
						todayHighlight: true,
						language: 'es'
					});
                    if (valorActual!='') $("#datatablefiltro_fecha").datepicker('setDate',dateDeFecha(valorActual));
					$("#datatablefiltro_fecha").change(function(){
						este.data('filtroColumna')[indice].filtro="Desde";	
						este.data('filtroColumna')[indice].valor1=dateDeFecha($("#datatablefiltro_fecha").val());	
						
						tabla.draw();
						borraDialogos();
						
					});
					$("#datatablefiltro_fecha").keyup(function(e){
						if (e.keyCode==27){
							$("#datatablefiltro_info").hide();		
						}
					});
				});
				$("#menuHasta").click(function(){
					$("#datatablefiltro_info").html('<input id="datatablefiltro_fecha"  class="form-control" type="text">').show();
					$("#datatablefiltro_fecha").datepicker({
						autoclose: true,
						todayHighlight: true,
						language: 'es'
					});
					$("#datatablefiltro_fecha").change(function(){
						este.data('filtroColumna')[indice].filtro="Hasta";
						var fechaIndicada=dateDeFecha($("#datatablefiltro_fecha").val());
						este.data('filtroColumna')[indice].valor1=new Date(fechaIndicada);	
						este.data('filtroColumna')[indice].valor1.setDate(fechaIndicada.getDate()+1);
						tabla.draw();
						borraDialogos();
						
					});
					$("#datatablefiltro_fecha").keyup(function(e){
						if (e.keyCode==27){
							$("#datatablefiltro_info").hide();		
						}
					});
				});
				$("#menuEntre").click(function(){
					$("#datatablefiltro_info").html('<input id="datatablefiltro_fecha"  class="form-control" type="text">').show();
					$("#datatablefiltro_fecha").daterangepicker({
						autoclose: true,
						todayHighlight: true,
						autoApply: true,
						dateFormat: 'dd/mm/yy',
						locale: {"format":"DD/MM/YYYY","separator":" - ","applyLabel":"Guardar","cancelLabel":"Cancelar","fromLabel": "Desde","toLabel":"Hasta","customRangeLabel":"Personalizar","daysOfWeek":["Do","Lu","Ma","Mi","Ju","Vi","Sa"],"monthNames":["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre"],"firstDay":1}
					});
					$("#datatablefiltro_fecha").change(function(){
						este.data('filtroColumna')[indice].filtro="Entre";
						var textoFecha=$("#datatablefiltro_fecha").val().split(' - ');
						if (textoFecha.length==2){
							este.data('filtroColumna')[indice].valor1=dateDeFecha(textoFecha[0]);
							var fechaIndicadaHasta=dateDeFecha(textoFecha[1]);
							este.data('filtroColumna')[indice].valor2=new Date(fechaIndicadaHasta);	
							este.data('filtroColumna')[indice].valor2.setDate(fechaIndicadaHasta.getDate()+1);
							tabla.draw();
						}
						borraDialogos();
						
					});
					$("#datatablefiltro_fecha").keyup(function(e){
						if (e.keyCode==27){
							$("#datatablefiltro_info").hide();		
						}
					});
				});
				$("#menuIgualNum").click(function(){
					$("#datatablefiltro_info").html('<input id="datatablefiltro_inputText"  class="form-control" type="number">').show();
					$("#datatablefiltro_inputText").focus();
					$("#datatablefiltro_inputText").keyup(function(e){
						if (e.keyCode==13&&!isNaN(parseInt($("#datatablefiltro_inputText").val()))){
							este.data('filtroColumna')[indice].filtro="Igual";	
							este.data('filtroColumna')[indice].valor1=parseInt($("#datatablefiltro_inputText").val());	
							tabla.draw();
							borraDialogos();
						}
						if (e.keyCode==27){
							$("#datatablefiltro_info").hide();		
						}
					});
				});
				$("#menuMenor").click(function(){
					$("#datatablefiltro_info").html('<input id="datatablefiltro_inputText"  class="form-control" type="number">').show();
					$("#datatablefiltro_inputText").focus();
					$("#datatablefiltro_inputText").keyup(function(e){
						if (e.keyCode==13&&!isNaN(parseInt($("#datatablefiltro_inputText").val()))){
							este.data('filtroColumna')[indice].filtro="Menor";	
							este.data('filtroColumna')[indice].valor1=parseInt($("#datatablefiltro_inputText").val());	
							tabla.draw();
							borraDialogos();
						}
						if (e.keyCode==27){
							$("#datatablefiltro_info").hide();		
						}
					});
				});
				$("#menuMayor").click(function(){
					$("#datatablefiltro_info").html('<input id="datatablefiltro_inputText"  class="form-control" type="number">').show();
					$("#datatablefiltro_inputText").focus();
					$("#datatablefiltro_inputText").keyup(function(e){
						if (e.keyCode==13&&!isNaN(parseInt($("#datatablefiltro_inputText").val()))){
							este.data('filtroColumna')[indice].filtro="Mayor";	
							este.data('filtroColumna')[indice].valor1=parseInt($("#datatablefiltro_inputText").val());	
							tabla.draw();
							borraDialogos();
						}
						if (e.keyCode==27){
							$("#datatablefiltro_info").hide();		
						}
					});
				});
				$("#menuEntreNum").click(function(){
					$("#datatablefiltro_info").html('<input id="datatablefiltro_inputText"  class="form-control" type="number"><br/><input id="datatablefiltro_inputText2"  class="form-control" type="number">').show();
					$("#datatablefiltro_inputText").focus();
					function registraEntreNum(){
						if (!isNaN(parseInt($("#datatablefiltro_inputText").val()))&&
							!isNaN(parseInt($("#datatablefiltro_inputText2").val()))){
							este.data('filtroColumna')[indice].filtro="Entre";	
							este.data('filtroColumna')[indice].valor1=parseInt($("#datatablefiltro_inputText").val());	
							este.data('filtroColumna')[indice].valor2=parseInt($("#datatablefiltro_inputText2").val());	
							tabla.draw();
							borraDialogos();
						}
					}
					$("#datatablefiltro_inputText").keyup(function(e){
						if (e.keyCode==13){
							registraEntreNum();
						}
						if (e.keyCode==27){
							$("#datatablefiltro_info").hide();		
						}
					});
					$("#datatablefiltro_inputText2").keyup(function(e){
						if (e.keyCode==13){
							registraEntreNum();
						}
						if (e.keyCode==27){
							$("#datatablefiltro_info").hide();		
						}
					});
				});
			}
		});
		if (initCompleteInicial)
			initCompleteInicial(settings,json);
	};
	tabla=this.DataTable( config);
	este.data('filtroColumna',[]);
	return tabla;
};
