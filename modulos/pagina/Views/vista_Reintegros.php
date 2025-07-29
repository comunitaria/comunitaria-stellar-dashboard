<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $VPConf->tituloWeb ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('public/assets/imagenes','https') ?>/confFavicon.png">
    <?= inserta_enlaces($enlaces) ?>
    <script type="text/javascript" src="<?= base_url('public/assets/plugins/moment','https') ?>/moment.min.js"></script>
    <script src="<?= base_url('public/assets/plugins/daterangepicker','https') ?>/daterangepicker.js" ></script>
    <script src="<?= base_url('public/assets/plugins/chart.js','https') ?>/Chart.min.js"></script>
    <script src="<?= base_url('public/assets/js','https') ?>/biblio.js" ></script>
    <link rel="stylesheet" href="<?= base_url('public/assets/plugins/daterangepicker','https') ?>/daterangepicker.css"/>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('public/assets/imagenes','https') ?>/confFavicon.png">
    <link rel="stylesheet" href="<?= base_url('public/assets/css','https') ?>/monedas.css"/>
<style>
    .imagenConfig:hover{
        cursor: pointer;
        box-shadow: 0px 0px 15px yellow;
    }
</style>
</head>
<body class="hold-transition sidebar-mini layout-fixed text-<?= $VPConf->tamanoTexto ?> <?= $VPConf->tonalidad ?>">
 <div class="wrapper">
<!-- Vpconf> barra (Zona sujeta a cambios automáticos)-->
    <?= barraNavegacion(json_decode($VPConf->menuSuperior,true),[['tipo'=>'usuario'],['tipo'=>'configuracion','titulo'=>'Configuracion', 'menu'=>$VPConf->menuConfig]],'','menu',true) ?>
<!-- Vpconf< barra --><!-- Vpconf> aside (Zona sujeta a cambios auutomáticos)-->
    <aside class="main-sidebar  sidebar-<?= $VPConf->lateralDark ?>-<?= $VPConf->lateralDark ?> elevation-4">
                <?= encabezado(base_url(''),base_url('public/assets/imagenes').'/logotipo'.($VPConf->UAT?"UAT":"").'.png',$VPConf->nombreCliente) ?>
                <?= sidebar(json_decode($VPConf->menuLateral,true)) ?>

    </aside>
<!-- Vpconf< aside -->
    <div class="content-wrapper">
        <div class="content-header">
        </div>
        <section class="content">
        <div class="container-fluid">
            <div class="row mb-2">
                    <div class="col-12 mx-auto">
                    <script>
                    var tablaTr;
                    var infoBeneficiario;
                    var animacion={pasos:0, aumento:0, finBalance:0,finTotal:0,interval:0,confirmado:false};
                    var listaMeses=['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
                </script>    
 <?php
    $base_url=base_url();
?>
                        <h4 class="mt-1 mb-4 text-primary">Reintegros realizados</h4>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="card card-dark sticky-top" style="margin-top: 8.7em">
                                    <div class="card-header">
                                        <h3 class="card-title">Reintegros y abonos</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                                            <canvas id="stackedBarChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%; display: block; width: 572px;" width="715" height="312" class="chartjs-render-monitor"></canvas>
                                        </div>
                                    </div>
                                </div>
                           </div>
                           <div class="col-lg-6">
                        <?php 
                        $cuentaAutorizada=\Modulos\Pagina\ESTADO_AUTORIZADA;
                        $cuentaNoCreada=\Modulos\Pagina\ESTADO_NO_CREADA;
                        $cuentaCreada=\Modulos\Pagina\ESTADO_CREADA;
                        $cuentaTrustline=\Modulos\Pagina\ESTADO_TRUSTLINE;
                        $cuentaBloqueada=\Modulos\Pagina\ESTADO_BLOQUEADA;
                        
                        $iconoUsuario="(data.tipo=='0'?'<img style=\"height:1.5em\" class=\"pr-1\" src=\"".base_url('public/assets/imagenes')."/logotipo.png\">':'<i class=\"text-primary pr-1 fas '+(data.tipo=='1'?'fa-user':(data.tipo=='2'?'fa-store-alt':'fa-question-circle'))+'\"></i>')";
                        $columnas=[
                            [
                                'titulo'=>'Id',
                                'esIndice'=>true,
                                'oculta'=>true,
                              ],
                                [
                                    'titulo'=>'Comercios colaboradores',
                                    'orden'=>'asc',
                                    'responsive'=>'all',
                                    'type'=> 'string',
                                    'render'=>
<<<JS
                                    if ( type === "sort" || type === 'type'|| type === 'filter' ) {
                                        return data;
                                    }
                                    else{
                                        var tablaReintegros='';
                                        var anosmes=Object.keys(row['Transacciones']).reverse();
                                        for(var fila in anosmes){
                                            var anomes=anosmes[fila];
                                            var ano=Math.floor(anomes / 12);
                                            var mes=listaMeses[(anomes % 12)];
                                            var reintegrado=(parseFloat(row['Transacciones'][anomes].t)>0);
                                            var pendiente=reintegrado&&(parseFloat(row['Transacciones'][anomes].t)>parseFloat(row['Transacciones'][anomes].p));
                                            tablaReintegros+=`
                                            <tr `+(fila>=2?'style="display:none"':'')+` class="reintegro `+(fila>=2?'adicional':'')+`"  data-fila="`+meta.row+`" data-anomes="`+anomes+`">
                                                <td>`+mes+'-'+ano+`</td>
                                                <td><span class="ILLA">`+parseFloat(row['Transacciones'][anomes].t).toFixed(2)+`</span>
                                                    <div class="progress progress-xs">
                                                        <div class="progress-bar bg-info" style="width: `+(parseFloat(row['Transacciones'][anomes].t)==0?'0':Math.round(100*(parseFloat(row['Transacciones'][anomes].p))/parseFloat(row['Transacciones'][anomes].t)))+`%"></div>
                                                    </div>
                                                </td>
                                                <td><div class="EURO">`+parseFloat(row['Transacciones'][anomes].p).toFixed(2)+`</div>
                                                </td>
                                                <td><span class="badge bg-`+(reintegrado?(pendiente?'danger':'success'):'gray')+`">`+(reintegrado?(pendiente?'ABONAR':'SALDADO'):'SIN REINTEGROS')+`</span></td>
                                            </tr>`;
                                        }
                                        return `
                        <div class="card card-widget widget-user-2">
                            <div class="widget-user-header bg-success">
                                <div class="widget-user-image">
                                    <img class="img-circle elevation-2" src="`+row['Logo']+`" alt="Logo comercio">
                                </div>
                                <h3 class="widget-user-username">`+row['Comercios colaboradores']+`</h3>
                                <h5 class="widget-user-desc">`+(row['Clases']?row['Clases'].replaceAll('#-#', ', '):'')+`</h5>
                            </div>
                            <div class="card-footer py-0">
                                <div class="row mt-4">
                                    <div class="col-lg-6 btn-group">
                                        <button type="button" class="btn btn-default" data-id="`+row['Id']+`" >
                                            <div class="spinner-grow spinner-grow-sm bloqueando" role="status" style="display:none">
                                                <span class="sr-only">Accediendo a la red...</span>
                                            </div>`+
                                            (row['Bloqueo']?'<i class="text-danger iconoBloqueo fas fa-lock"></i><span class="pl-2">Cuenta bloqueada</span>':'<i class="text-success  iconoBloqueo fas fa-lock-open"></i><span class="pl-2">Se permiten transacciones</span>')+`
                                        </button>
                                        <button type="button" class="btn btn-default dropdown-toggle dropdown-icon" data-toggle="dropdown" aria-expanded="false">
                                            <span class="sr-only">Cambiar</span>
                                        </button>
                                        <div class="dropdown-menu" role="menu" style="">
                                            <a class="dropdown-item bloquear" data-bloquear="`+(row['Bloqueo']?0:1)+`" data-id="`+row['Id']+`">`+(row['Bloqueo']?'Desbloquear cuenta':'Bloquear cuenta')+`</a>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 my-2">`+                                                                                                                                                                   
                                            (row['Monedero']==$cuentaAutorizada?'<i class="text-primary fas fa-wallet"></i><span class="pl-2">Monedero operativo</span>':'<i class="text-danger fas fa-wallet"></i><span class="pl-2">'+(row['Bloqueo']?'Monedero bloqueado':(row['Monedero']==$cuentaNoCreada?'Esperando que el usuario abra monedero':
                                                                                                                                                                                                                                        (row['Monedero']==$cuentaCreada?'Esperando que el usuario solicite autorización':
                                                                                                                                                                                                                                        (row['Monedero']==$cuentaTrustline?'En proceso de autorización de monedero':
                                                                                                                                                                                                                                        ('Monedero bloqueado')))))+'</span>')+`
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-sm-4 border-right">
                                        <div class="description-block">
                                            <h5 class="description-header ILLA" >`+row['Saldo']+`</h5>
                                            <span class="description-text">SALDO</span>
                                        </div>
                                    </div>

                                    <div class="col-sm-4 border-right">
                                        <div class="description-block">
                                            <h5 class="`+(row['Deuda']>0?'text-danger ':'')+`description-header ILLA" id="dgTotalILLA">`+row['Deuda']+`</h5>
                                            <span class="description-text">ADEUDADO</span>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="description-block">
                                            <h5 class="description-header" ID="dgAlta">`+moment.unix(row['Alta']).format("DD/MM/YYYY")+`</h5>
                                            <span class="description-text">ALTA</span>
                                        </div>
                                    </div>
                            
                                </div>
                                <div class="row mt-4">
                                    <dl class="col-lg-6">
                                        <dt>Dirección</dt>
                                        <dd><small id="dgDireccion">`+(row['Dirección']==''?'No registrada':row['Dirección'])+`</small></dd>
                                    </dl>
                                    <dl class="col-lg-6">
                                        <dt>Contacto</dt>
                                        <dd class="my-0"><small id="dgContacto">`+(row['Contacto']==''?'No registrado':row['Contacto'])+`</small></dd>
                                        <dd class="my-0"><small id="dgMovil"><i class="fas fa-phone"></i> `+(row['Movil']==''?'No registrado':row['Movil'])+`</small></dd>
                                        <dd><small id="dgCorreo"><i class="fas fa-envelope"></i> `+(row['Correo']==''?'No registrado':row['Correo'])+`</small></dd>
                                        <dt>Usuario</dt>
                                        <dd><small id="dgUsuario">`+row['Usuario']+`</small></dd>
                                    </dl>
                                </div>
                                <div class="row">
                                    <div class="card col-12">
                                        <div class="card-header">
                                            <h3 class="card-title">Últimos reintegros</h3>
                                            <div class="card-tools">
                                                <ul class="pagination pagination-sm float-right">
                                                    <li class="page-item"><a class="page-link" data-comercio=`+row['Id']+`>»</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card-body p-0">
                                            <table class="table" data-comercio="`+row['Id']+`">
                                                <thead>
                                                    <tr>
                                                        <th >Mes</th>
                                                        <th >Reintegrado</th>
                                                        <th >Abonado</th>
                                                        <th >Acción</th>
                                                    </tr>
                                                </thead>
                                                <tbody>`+tablaReintegros+`
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" >
                                    <dl>
                                        <dt>Cuenta</dt>
                                        <dd><small id="dgCuenta" class="text-truncate" style="display:inline-block;max-width:80vw">`+(row['Clave']==''?'No creada':row['Clave'])+`</small><i class="far fa-copy pl-2 d-inline" role="button" onclick="copyToClipboard('#dgCuenta')"></i></dd>
                                        </dl>
                                </div>
                            </div>
                        </div>
                                        `;
                                    }
JS                                                              
                                ],
                                [
                                    'titulo'=>'IdsClase',
                                    'oculta'=>true,
                                ]
                        ];
                        
                        ?>
                        <?php
                                $cuentaAutorizada=\Modulos\Pagina\ESTADO_AUTORIZADA;
                                $cuentaNoCreada=\Modulos\Pagina\ESTADO_NO_CREADA;
                                $cuentaCreada=\Modulos\Pagina\ESTADO_CREADA;
                                $cuentaTrustline=\Modulos\Pagina\ESTADO_TRUSTLINE;
                                $cuentaBloqueada=\Modulos\Pagina\ESTADO_BLOQUEADA;
                                $urlBeneficiarios=base_url('beneficiarios');
                                echo tabla('tbReintegros',[
                                     'sinCheckboxes'=>true,
                                    'componentes'=>'<"container-fluid mt-3"<"row"<"col"l><"col"f>>>rt<"container-fluid"<"row"<"col"i><"col"p>>>',
                                    'menuLongitud'=>  [  [50, 200, 1000, -1],  [50, 200, 1000, "Todos"]],
                                    'trClick'=>["accion"=>"ejecutar",
                                                "funcion"=>
<<<JS
                                                function (id,fila){
                                                }
JS
                                        ],
                                    'columnas'=>$columnas,
                                    'estilo'=>'col-12 hover ',
                                    'ajax'=>[
                                        'url'=>base_url('reintegros/listado'),
                                        'data function(d)'=>
<<<JS
                                            d.clases=JSON.stringify([0]);
JS
                                    ],
                                    'al'=>[
                                        'crear_tabla'=>
<<<JS
        function (tabla){
            tablaTr=tabla;
            tabla.buttons().container().appendTo($("#dvBotones"));
            tablaTr.on('draw', refrescaGraficaPagos);
            refrescaGraficaPagos();
        }
JS,
                         
                                    ]
                            ]);

            ?>
                            </div>
                            <div class="col-lg-3">
                                <div class="card card-info sticky-top" style="margin-top: 8.7em">
                                    <div class="card-header">
                                        <h3 class="card-title">Filtro de clases</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                    <?php
                                        foreach($clases as $unaClase){
                                            echo '
                                            <div class="custom-control custom-checkbox">
                                                <input class="custom-control-input filtroClase" type="checkbox" id="ipClase'.$unaClase['id'].'" data-clase="'.$unaClase['id'].'" >
                                                <label for="ipClase'.$unaClase['id'].'" class="custom-control-label">'.$unaClase['clase'].'</label>
                                            </div>';
                                        }
                                    ?>
                                    </div>
                                </div>                         
                            </div>
                        </div>
                    </div>
                </div>
            </div>
         </section>
    </div>
<!-- Vpconf> pie (Zona sujeta a cambios automáticos)-->
    <?= pie($VPConf->contenidoPie,'',false) ?>
<!-- Vpconf< pie -->
 </div>
 <script>
    function refrescaTabla(){
        tablaTr.ajax.reload( null, false );
       }
    $(document).ready(function(){
       $('body').on('click','.bloquear',function(){
        $('button[data-id="'+$(this).data('id')+'"] .bloqueando').show();
        $('button[data-id="'+$(this).data('id')+'"] .iconoBloqueo').hide();
        $.post('<?= base_url('comercio') ?>/'+$(this).data('id')+'/bloquear/'+$(this).data('bloquear'),function(respuestaJson){
            var respuesta=JSON.parse(respuestaJson);
            if (respuesta.exito){
                refrescaTabla();
            }
        });
       }); 
       $('body').on('click','.page-link',function(){
        $('table[data-comercio="'+$(this).data('comercio')+'"] tr.adicional').toggle();
       });
       $('body').on('click','.reintegro',function(){
        var comercio=tablaTr.rows($(this).data('fila')).data()[0];
        var transaccion=comercio['Transacciones'][$(this).data('anomes')];
        var mes=$(this).data('anomes') % 12;
        var ano=Math.floor($(this).data('anomes') / 12)+2000;
        $("#laTienda").html('<strong>'+comercio['Comercios colaboradores']+'</strong>');
        $("#elMes").text(['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'][mes]+' de '+ano);
        if (transaccion.p<transaccion.t){
            $("#ipComercio").val(comercio['Id']);
            $("#ipMes").val(mes);
            $("#ipAno").val(ano);
            $("#ipImporte").val(0);
            $("#ipFactura").val('');
            $("#ipObservaciones").val('');
            $('#frmRegistroPago').modal('show');
        }
       });
       $(".filtroClase").click(function(){
            var filtro=[];
            $(".filtroClase:checked").each(function(){filtro.push($(this).data('clase'))});
            var regFiltro='';
            if (filtro.length>0){
                tablaTr.columns(3).search("(?=.*"+filtro.join('\/)(?=.*')+"\/)",true).draw();
            }
            else{
                tablaTr.columns(3).search("").draw();
            }
            
       });
    });
    //---------------------
    //- STACKED BAR CHART -
    //---------------------
    function refrescaGraficaPagos(){
        var areaChartData = {
        labels  : [],
        datasets: [
            {
            label               : 'Abonado',
            backgroundColor     : 'rgba(23,162,184,0.9)',
            borderColor         : 'rgba(23,162,184,0.8)',
            pointRadius          : false,
            pointColor          : '#3b8bba',
            pointStrokeColor    : 'rgba(60,141,188,1)',
            pointHighlightFill  : '#fff',
            pointHighlightStroke: 'rgba(60,141,188,1)',
        //    data                : [28, 48, 40, 19, 86, 27, 90]
            },
            {
            label               : 'Pendiente',
            backgroundColor     : 'rgba(210, 214, 222, 1)',
            borderColor         : 'rgba(210, 214, 222, 1)',
            pointRadius         : false,
            pointColor          : 'rgba(210, 214, 222, 1)',
            pointStrokeColor    : '#c1c7d1',
            pointHighlightFill  : '#fff',
            pointHighlightStroke: 'rgba(220,220,220,1)',
        //    data                : [65, 59, 80, 81, 56, 55, 40]
            },
        ]
        };
        var hoy=new Date();
        var anomes=hoy.getMonth()+1+(hoy.getYear()-100)*12;
        var meses=12;
        var anosmeses=[];
        for(i=0;i<meses;i++){
            var elAnomes=anomes-meses+i;
            anosmeses.push(elAnomes);
            areaChartData.labels.push(listaMeses[elAnomes % 12]+'-'+Math.floor(elAnomes/12));
        }
        var comercios=tablaTr.rows({ filter : 'applied'}).data();
        areaChartData.datasets[0].data=[];
        areaChartData.datasets[1].data=[];
        for(var c in comercios){
            for(var anomes in comercios[c].Transacciones){
                var i=anosmeses.indexOf(parseInt(anomes));
                if (i>=0){
                    areaChartData.datasets[0].data[i]=comercios[c].Transacciones[anomes].p+(areaChartData.datasets[0].data[i]||0);
                    areaChartData.datasets[1].data[i]=(comercios[c].Transacciones[anomes].t-comercios[c].Transacciones[anomes].p)+(areaChartData.datasets[1].data[i]||0);
                }
            }
        }
        var barChartData = $.extend(true, {}, areaChartData);
        grafico.data=$.extend(true, {}, barChartData);
        grafico.update();
        
    }
    
    var stackedBarChartCanvas = $('#stackedBarChart').get(0).getContext('2d')
    
    var stackedBarChartOptions = {
      responsive              : true,
      maintainAspectRatio     : false,
      scales: {
        xAxes: [{
          stacked: true,
        }],
        yAxes: [{
          stacked: true,
          ticks: {
              callback: value => `${value} €`,
                beginAtZero: true,
          
            }
        }]
      }
    }

    var grafico=new Chart(stackedBarChartCanvas, {
      type: 'bar',
      options: stackedBarChartOptions
    }) 
    
 </script>
 <?php
    $baseURL=base_url('comercio');
    echo formulario('frmRegistroPago',[
         'titulo'=>'Registrar pago',
         'introduccion'=>'
         <div class="text-body">Va a registrar el abono de los reintegros efectuados por <span id="laTienda"></span> en el mes de <span id="elMes"></span>.</div>
         ',
         'aceptar'=>
   <<<JS
             function(){
                if (parseFloat($("#ipImporte").val())>0){
                $.post('$baseURL/'+$("#ipComercio").val()+'/pago',{
                    'importe': parseFloat($("#ipImporte").val()),
                    'mes': $("#ipMes").val(),
                    'ano': $("#ipAno").val(),
                    'factura': $("#ipFactura").val(),
                    'notas': $("#ipObservaciones").val(),
                }, function(){
                        refrescaTabla();
                        $('#frmRegistroPago').modal('hide');
                    });
                }
                else
                    $('#frmRegistroPago').modal('hide');
             }   
   JS, 
         'campos'=>[
                    'ipComercio'=>[
                            'tipo'=>'hidden'
                    ],
                    'ipMes'=>[
                        'tipo'=>'hidden'
                    ],
                    'ipAno'=>[
                        'tipo'=>'hidden'
                    ],
                    'ipImporte'=>[
                           'tipo'=>'number',
                           'label'=>'Importe abonado (€)',
                            'atributos'=>'min="0" step="0.01"'
                     ],
                     'ipFactura'=>[
                           'tipo'=>'text',
                           'label'=>'Factura (referencia)',
                     ],
                     'ipObservaciones'=>[
                        'tipo'=>'textarea',
                        'label'=>'Observaciones',
                  ],
     ]
        ]); ?> 
</body>
</html>