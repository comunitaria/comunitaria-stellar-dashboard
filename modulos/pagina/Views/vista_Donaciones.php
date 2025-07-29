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
    <script src="<?= base_url('public/assets/plugins/jszip','https') ?>/jszip.js" ></script>
    <script src="<?= base_url('public/assets/plugins/pdfmake','https') ?>/pdfmake.min.js" ></script>
    <script src="<?= base_url('public/assets/plugins/pdfmake','https') ?>/vfs_fonts.js" ></script>
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
    <aside class="main-sidebar sidebar-<?= $VPConf->lateralDark ?>-<?= $VPConf->lateralDark ?> elevation-4">
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
                    function imagenBloqueo(bloqueado){
                        $("#estadoCuenta").html(bloqueado?'<i class="text-danger iconoBloqueo fas fa-lock"></i><span class="pl-2">Cuenta bloqueada</span>':'<i class="text-success  iconoBloqueo fas fa-lock-open"></i><span class="pl-2">Se permiten transacciones</span>');
                        $("#accionCuenta").data('bloquear',bloqueado?0:1);
                        $("#accionCuenta").text(bloqueado?'Desbloquear cuenta':'Bloquear cuenta');
                        $("#btTransferencia").prop("disabled",bloqueado);
                    }
                                                        
                </script>    
 <?php
    $base_url=base_url();
?>
                        <h4 class="mt-1 mb-0 text-primary">Situación de beneficiarios</h4>
                         
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
                                    'titulo'=>'Nombre',
                                    'orden'=>'asc',
                                    'claseHd'=>'text-olive',
                                    'type'=> 'string',
                                ],
                                [
                                    'titulo'=>'IdClase',
                                    'oculta'=>true,
                                  ],
                                    [
                                    'titulo'=>'Compra autorizada',
                                    'claseHd'=>'text-olive',
                                    'type'=> 'string',
                                ],
                                [
                                    'titulo'=>'Móvil',
                                    'claseHd'=>'text-olive',
                                    'type'=> 'string',
                                ],
                                [
                                    'titulo'=>'Dirección',
                                    'claseHd'=>'text-olive',
                                    'type'=> 'string',
                                ],
                                [
                                    'titulo'=>'Monedero',
                                    'claseHd'=>'text-olive',
                                    'type'=> 'string',
                                    'render'=>
<<<JS
                                    if ( type === "sort" || type === 'type'|| type === 'filter' ) {
                                        return  (data==$cuentaAutorizada?'Monedero operativo':
                                                (row['Bloqueo']?'Monedero bloqueado':
                                                (data==$cuentaNoCreada?'Esperando que el usuario abra monedero':
                                                (data==$cuentaCreada?'Esperando que el usuario solicite autorización':
                                                (data==$cuentaTrustline?'En proceso de autorización de monedero':
                                                                        'Monedero bloqueado')))));   
                                    }
                                    else{
                                        return (data==$cuentaAutorizada?'<i class="text-primary fas fa-wallet"></i><span class="pl-2">Monedero operativo</span>':
                                                '<i class="text-danger fas fa-wallet"></i><span class="pl-2">'+(row['Bloqueo']?'Monedero bloqueado':(data==$cuentaNoCreada?'Esperando que el usuario abra monedero':
                                                                                                                                                                                                                                (data==$cuentaCreada?'Esperando que el usuario solicite autorización':
                                                                                                                                                                                                                                (data==$cuentaTrustline?'En proceso de autorización de monedero':
                                                                                                                                                                                                                                        ('Monedero bloqueado')))))+'</span>');   
                                    }
JS
                                ],
                                [
                                    'titulo'=>'Saldo',
                                    'claseHd'=>'text-olive',
                                    'type'=> 'num',
                                    'render'=>
<<<JS
                                    if ( type === "sort" || type === 'type'|| type === 'filter' ) {
                                        return data;
                                    }
                                    else{
                                        return '<span class="ILLA">'+data+'</span>';
                                    }
JS
                                ], [
                                    'titulo'=>'Bloqueo',
                                    'claseHd'=>'text-olive',
                                    'type'=> 'bool',
                                    'render'=>
<<<JS
                                    if ( type === "sort" || type === 'type'|| type === 'filter' ) {
                                        return data;
                                    }
                                    else{
                                        return data?'<i class="text-danger fas fa-lock"></i>':'<i class="text-success fas fa-lock-open"></i>';
                                    }
JS
                                ],
                                [
                                    'titulo'=>'Carga',
                                    'claseHd'=>'text-olive',
                                    'type'=> 'number',
                                    'render'=>
<<<JS
                                    if ( type === "sort" || type === 'type'|| type === 'filter' ) {
                                        return data;
                                    }
                                    else{
                                        var titulo='title="'+Math.round(data*100)+'%"';
                                        return  (data<=0?'<i class="fas fa-battery-empty text-danger" '+titulo+'></i>':
                                                (data<=0.25?'<i class="fas fa-battery-quarter text-danger" '+titulo+'></i>':
                                                (data<=0.50?'<i class="fas fa-battery-half text-warning" '+titulo+'></i>':
                                                (data<=0.75?'<i class="fas fa-battery-three-quarters text-success" '+titulo+'></i>':
                                                        '<i class="fas fa-battery-full text-success" '+titulo+'></i>'
                                                ))));
                                    }
JS
                                ],
                                [
                                    'titulo'=>'Usuario',
                                    'claseHd'=>'text-olive',
                                    'type'=> 'string',
                                ],
                        ];
                        
                        ?>
                        <?php
                                $urlBeneficiarios=base_url('beneficiarios');
                                echo tabla('tbDonaciones',[
                                     'sinCheckboxes'=>true,
                                    'componentes'=>'<"container-fluid mt-3"<"row"<"col"l><"col"f>>>rt<"container-fluid"<"row"<"col"i><"col"p>>>',
                                    'menuLongitud'=>  [  [50, 200, 1000, -1],  [50, 200, 1000, "Todos"]],
                                    'botones'=>[
                                        ["extend"=>'pdf', "orientation"=>"landscape", 'className'=>'btn btn-primary'],
                                        ["extend"=> "excelHtml5","text"=> "Excel","exportOptions"=> [ "orthogonal"=> "export"]]
                                    ],
                                    'trClick'=>["accion"=>"ejecutar",
                                                "funcion"=>
<<<JS
                                                function (id,fila){
                                                    $.get('$urlBeneficiarios/'+fila['Id']+'/informacion')
                                                    .done((json_info)=>{
                                                        infoBeneficiario=JSON.parse(json_info);
                                                        $("#dgNombre").text(infoBeneficiario['nombre']+' '+infoBeneficiario['apellidos']);
                                                        $("#dgClase").text(infoBeneficiario['clase']['nombre']);
                                                        $("#dgBalanceILLA").text(parseFloat(infoBeneficiario['balances']['ILLA']).toFixed(2));
                                                        $("#dgTotalILLA").text(parseFloat(infoBeneficiario['balances']['total']).toFixed(2));
                                                        $("#dgAlta").text(moment.unix(infoBeneficiario['alta']).format("DD/MM/YYYY"));
                                                        $("#dgUsuario").text(infoBeneficiario['usuario']);
                                                        $("#dgCuenta").text(infoBeneficiario['cuenta']);
                                                        $("#btEstadoCuenta").data('id',infoBeneficiario['id']);
                                                        $("#accionCuenta").data('id',infoBeneficiario['id']);
                                                        imagenBloqueo(infoBeneficiario['bloqueado']);
                                                        $("#ipTransferencia").val('');
                                                        $("#dgErrorTransfer").hide();
                                                        $("#dgBeneficiario").modal('show')
                                                        .on('hidden.bs.modal', function (e) {
                                                            //resolve($("#id input.resultado").val());
                                                        });
                                                        
                                                        $("#btTransferencia").prop("disabled",infoBeneficiario['bloqueado']||infoBeneficiario['estadoMonedero']!=$cuentaAutorizada).click(function(){
                                                            var transferir=parseFloat($("#ipTransferencia").val());
                                                            if (transferir>0){
                                                                $.post('$urlBeneficiarios/'+infoBeneficiario['id']+'/transferir/'+transferir)
                                                                .done((json_respuesta)=>{
                                                                    respuesta=JSON.parse(json_respuesta);
                                                                    if (!respuesta.exito){
                                                                        clearInterval(animacion.interval);
                                                                        $("#dgBalanceILLA").text(animacion.finBalance.toFixed(2));
                                                                        $("#dgTotalILLA").text(animacion.finTotal.toFixed(2));
                                                                        $("#dgErrorTransfer").text(respuesta.mensaje).show();
                                                        
                                                                    }
                                                                    else{
                                                                        refrescaTabla();
                                                                        animacion.confirmado=true;
                                                                    }

                                                                });
                                                                $("#dgBalanceILLA").addClass("text-danger");
                                                                $("#dgTotalILLA").addClass("text-danger");
                                                                animacion.confirmado=false;
                                                                animacion.pasos=50;
                                                                animacion.aumento=0.01;
                                                                if (transferir<animacion.pasos*0.01){
                                                                    animacion.pasos=transferir/animacion.aumento;
                                                                }
                                                                else{
                                                                    animacion.aumento=Math.floor(transferir/animacion.pasos*100)/100;
                                                                }
                                                                animacion.finBalance=parseFloat($("#dgBalanceILLA").text())+transferir;
                                                                animacion.finTotal=parseFloat($("#dgTotalILLA").text())+transferir;
                                                                animacion.interval=setInterval(()=>{
                                                                    var nuevoBalance=Math.min(animacion.finBalance,parseFloat($("#dgBalanceILLA").text())+animacion.aumento);
                                                                    var nuevoTotal=Math.min(animacion.finTotal,parseFloat($("#dgTotalILLA").text())+animacion.aumento);
                                                                    $("#dgBalanceILLA").text(nuevoBalance.toFixed(2));
                                                                    $("#dgTotalILLA").text(nuevoTotal.toFixed(2));
                                                                    if (animacion.confirmado&&(nuevoTotal==animacion.finTotal)&&(nuevoBalance==animacion.finBalance)){
                                                                        clearInterval(animacion.interval);
                                                                        setTimeout(() => {
                                                                              $("#dgBalanceILLA").removeClass("text-danger");
                                                                              $("#dgTotalILLA").removeClass("text-danger");
                                                                        }, 1000);
                                                                    }
                                                                },Math.ceil(4000/animacion.pasos));
                                                           }
                                                        });
                                                        $("#btTransferencia").parent().attr('title','');
                                                        if (infoBeneficiario['estadoMonedero']!=$cuentaAutorizada){
                                                            $("#btTransferencia").parent().attr('title','Monedero no activado');
                                                        }
                                                        if (infoBeneficiario['bloqueado']){
                                                            $("#btTransferencia").parent().attr('title','El beneficiario ha sido bloqueado');
                                                        }
                                                        $('[data-toggle="tooltip"]').tooltip();
   
                                                    });
                                                }
JS
                                        ],
                                    'columnas'=>$columnas,
                                    'estilo'=>'col-12 hover striped row-#f0f4ff-white rowhover-#e5edff  ',
                                    'ajax'=>[
                                        'url'=>base_url('donaciones/listado'),
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
        }
JS,
                         
                                    ]
                            ]);

            ?>
            
                    </div>
                </div>
            </div>
         </section>
    </div>
    <div class="modal fade" id="dgBeneficiario" style="display: none;" aria-hidden="true">
        <div class="modal-dialog $estilos">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="card card-widget widget-user m-0">
                        <div class="widget-user-header bg-primary">
                            <h3 class="widget-user-username" id="dgNombre"></h3>
                            <h5 class="widget-user-desc" id="dgClase"></h5>
                        </div>
                        <div class="widget-user-image">
                            <img class="img-circle elevation-2" src="<?= base_url('public/assets/imagenes/avatares').'/avatar.jpg' ?>" alt="User Avatar">
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-sm-4 border-right">
                                    <div class="description-block">
                                        <h5 class="description-header ILLA" id="dgBalanceILLA"></h5>
                                        <span class="description-text">SALDO</span>
                                    </div>
                                </div>

                                <div class="col-sm-4 border-right">
                                    <div class="description-block">
                                        <h5 class="description-header ILLA" id="dgTotalILLA"></h5>
                                        <span class="description-text">RECIBIDO</span>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="description-block">
                                        <h5 class="description-header" ID="dgAlta"></h5>
                                        <span class="description-text">ALTA</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4 ">
                                    <div class=" mx-auto btn-group">
                                        <button id="btEstadoCuenta" type="button" class="btn btn-default" data-id="" >
                                            <div class="spinner-grow spinner-grow-sm bloqueando" role="status" style="display:none">
                                                <span class="sr-only">Accediendo a la red...</span>
                                            </div>
                                            <span id="estadoCuenta"></span>
                                        </button>
                                        <button type="button" class="btn btn-default dropdown-toggle dropdown-icon" data-toggle="dropdown" aria-expanded="false">
                                            <span class="sr-only">Cambiar</span>
                                        </button>
                                        <div class="dropdown-menu" role="menu" style="">
                                            <a class="dropdown-item"  id="accionCuenta" data-bloquear="" data-id="">Bloquear cuenta</a>
                                        </div>
                                    </div>
                                </div>
                              <div class="row mt-4">
                                <div class="col-3"></div>
                                <div class="col-6">
                                    <strong>Transferir fondos</strong>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text ILLA pt-0" style="font-size:1.5em">
                                            </span>
                                        </div>
                                        <input type="number" min="0" max="1000" step=".01" id="ipTransferencia" class="form-control rounded-0">
                                        <span class="input-group-append" data-toggle="tooltip"  data-placement="top">
                                            <button type="button" class="btn btn-danger btn-flat" id="btTransferencia">Validar!</button>
                                        </span>
                                    </div>
                                    <div class="text-danger" style="display:none" id="dgErrorTransfer"></div>
                                    <div class="col-3"></div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <dl>
                                  <dt>Usuario</dt>
                                    <dd><small id="dgUsuario"></small></dd>
                                  <dt>Cuenta</dt>
                                    <dd><small id="dgCuenta"></small><i class="far fa-copy pl-2" role="button" onclick="copyToClipboard('#dgCuenta')"></i></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
        $('body').on('click','#accionCuenta',function(){
            $('#btEstadoCuenta .bloqueando').show();
            $('#btEstadoCuenta .iconoBloqueo').hide();
            $.post('<?= base_url('beneficiario') ?>/'+$(this).data('id')+'/bloquear/'+$(this).data('bloquear'),function(respuestaJson){
                var respuesta=JSON.parse(respuestaJson);
                if (respuesta.exito){
                    $('#btEstadoCuenta .bloqueando').hide();
                    imagenBloqueo($('#accionCuenta').data('bloquear')=='1');
                    refrescaTabla();
                }
            });
       }); 
               
       
    });
 </script>
</body>
</html>