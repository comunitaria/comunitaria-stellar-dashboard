<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $VPConf->tituloWeb ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= inserta_enlaces($enlaces) ?>
    <script type="text/javascript" src="<?= base_url('public/assets/plugins/moment','https') ?>/moment.min.js"></script>
    <script src="<?= base_url('public/assets/plugins/daterangepicker','https') ?>/daterangepicker.js" ></script>
    <script src="<?= base_url('public/assets/plugins/jszip','https') ?>/jszip.js" ></script>
    <script src="<?= base_url('public/assets/plugins/pdfmake','https') ?>/pdfmake.min.js" ></script>
    <script src="<?= base_url('public/assets/plugins/pdfmake','https') ?>/vfs_fonts.js" ></script>
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
                    <script>var tablaTr;
                    
                </script>    
 <?php
    $base_url=base_url();
?>
                        <h4 class="mt-1 mb-0 text-primary">Pagos registrados en la red Stellar</h4>
                        <div class="mb-4"><small>Las transacciones pueden tardar hasta 5 minutos en registrarse en este listado</small></div>
                        <div class="row">
                                <div class="form-group col-lg-3" id="znPeriodo">
                                    <label>Indique periodo:</label>
                                    <div class="input-group">
                                    <div class="input-group-prepend">
                                    <span class="input-group-text">
                                    <i class="far fa-calendar-alt"></i>
                                    </span>
                                    </div>
                                    <?php
                                        $mesPasado=(new DateTime("first day of last month"))->format('d/m/Y').' - '.(new DateTime("last day of last month"))->format('d/m/Y');
                                        $esteMes=(new DateTime("first day of this month"))->format('d/m/Y').' - '.(new DateTime("today"))->format('d/m/Y');
                                        $unPeriodo=$esteMes;
                                    ?>
                                    <input type="text" class="form-control float-right  ipPeriodos" data-mesPasado="<?= $mesPasado ?>"  data-esteMes="<?= $esteMes ?>" data-unPeriodo="<?= $unPeriodo ?>" id="ipPeriodo"  
                                            value="<?= $esteMes ?>">
                                    </div>

                                </div>
                                <div class="form-group col-lg-3">
                                    <div class="form-check">
                                        <input class="form-check-input itPeriodo" type="radio" name="rdPeriodo" checked="" data-periodo="este">
                                        <label class="form-check-label">Este mes</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input itPeriodo" type="radio" name="rdPeriodo" data-periodo="pasado">
                                        <label class="form-check-label">Mes pasado</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input itPeriodo" type="radio" name="rdPeriodo" data-periodo="periodo">
                                        <label class="form-check-label">Periodo</label>
                                    </div>
                                </div>
                                <div id="dvBotones" class="col-lg-3"></div>
                        </div>
                       
                        <?php 
                        $urlStellar=(getenv('moneda.red')=='testnet'?'testnet.':'').'stellarchain.io';

                        $iconoUsuario="(data.tipo=='0'?'<img style=\"height:1.5em\" class=\"pr-1\" src=\"".base_url('public/assets/imagenes')."/logotipo.png\">':'<i class=\"text-primary pr-1 fas '+(data.tipo=='1'?'fa-user':(data.tipo=='2'?'fa-store-alt':'fa-question-circle'))+'\"></i>')";
                        $columnas=[
                            [
                                'titulo'=>'Id',
                                'esIndice'=>true,
                                'oculta'=>true,
                              ],
                            [
                                    'titulo'=>'Fecha',
                                    'claseHd'=>'text-olive',
                                    'type'=>'fecha',
                                    'render'=>
<<<JS
                                    if ( type === "sort" || type === 'type' ) {
                                        return data;
                                    }
                                    else{
                                        return new Date(data).toLocaleDateString();
                                    }
JS
                                ],
                                [
                                    'titulo'=>'Transacción',
                                    'orden'=>'desc',
                                    'claseHd'=>'text-olive',
                                    'type'=> 'num',
                                    'render'=>
<<<JS
                                    if ( type === "sort" || type === 'type'|| type === 'filter' ) {
                                        return data;
                                    }
                                    else{
                                        return '<a href="https://$urlStellar/operations/'+data+'" target="_blank">'+data+'</a>';
                                    }
JS
                                ],
                                [
                                    'titulo'=>'Movimiento',
                                    'claseHd'=>'text-olive',
                                    'type'=> 'string',
                                ],
                                [
                                    'titulo'=>'Importe',
                                    'claseHd'=>'text-olive',
                                    'type'=> 'num',
                                    'clase'=>'text-right',
                                    'render'=>
<<<JS
                                    if ( type === "sort" || type === 'type'|| type === 'filter' ) {
                                        return data.cantidad;
                                    }
                                    else{
                                        return '<span class="'+(data.moneda==0?'XLM':'ILLA')+'">'+data.cantidad+'</span>';
                                    }
JS
                                  ],
                                  [
                                    'titulo'=>'Pagado por',
                                    'claseHd'=>'text-olive',
                                    'render'=>
<<<JS
                                    if ( type === "sort" || type === 'type' ) {
                                        return data.nombre;
                                    }
                                    else{
                                        return $iconoUsuario+data.nombre;
                                    }
JS
                                ],
                                [
                                    'titulo'=>'Pagado a',
                                    'claseHd'=>'text-olive',
                                    'render'=>
<<<JS
                                    if ( type === "sort" || type === 'type' ) {
                                        return data.nombre;
                                    }
                                    else{
                                        return $iconoUsuario+data.nombre;
                                    }
JS
                                ],
                                    
                        ];
                        
                        ?>
                        <?= tabla('tbTransacciones',[
                                     'sinCheckboxes'=>true,
                                    'componentes'=>'<"container-fluid mt-3"<"row"<"col"l><"col"f>>>rt<"container-fluid"<"row"<"col"i><"col"p>>>',
                                    'menuLongitud'=>  [  [10, 25, 50, -1],  [10, 25, 50, "Todos"]],
                                    'botones'=>[
                                        ["extend"=>'pdf', "orientation"=>"landscape", 'className'=>'btn btn-primary'],
                                        ["extend"=> "excelHtml5","text"=> "Excel","exportOptions"=> [ "orthogonal"=> "export"]]
                                    ],
                                    'columnas'=>$columnas,
                                    'estilo'=>'col-12 hover striped row-#f0f4ff-white rowhover-#e5edff  ',
                                    'ajax'=>[
                                        'url'=>base_url('transacciones/listado'),
                                        'data function(d)'=>
<<<JS
                                            d.periodo=$("#ipPeriodo").val();
JS
                                    ],
                                    'al'=>[
                                        'crear_tabla'=>
<<<JS
        function (tabla){
            tablaTr=tabla;
            tabla.buttons().container().appendTo($("#dvBotones"));
        }
JS
                                    ]
                                    
                                ])
            ?>
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
    $(document).ready(function(){
        $('.ipPeriodos').daterangepicker({
    "locale": {
        "format": "DD/MM/YYYY",
        "separator": " - ",
        "applyLabel": "Aceptar",
        "cancelLabel": "Cancelar",
        "fromLabel": "Desde",
        "toLabel": "Hasta",
        "customRangeLabel": "Custom",
        "daysOfWeek": [
            "Do",
            "Lu",
            "Ma",
            "Mi",
            "Ju",
            "Vi",
            "Sa"
        ],
        "monthNames": [
            "Enero",
            "Febrero",
            "Marzo",
            "Abril",
            "Mayo",
            "Junio",
            "Julio",
            "Agosto",
            "Septiembre",
            "Octubre",
            "Noviembre",
            "Diciembre"
        ],
        "firstDay": 1
    }});
        $(".itPeriodo").change(function(){
            switch($(this).data('periodo')){     
                case 'periodo':
                    $("#ipPeriodo").val($("#ipPeriodo").data('unperiodo'));
                    refrescaTabla();
                    break;
                case 'este':
                    $("#ipPeriodo").val($("#ipPeriodo").data('estemes'));
                    refrescaTabla();
                    break;
                case 'pasado':
                    $("#ipPeriodo").val($("#ipPeriodo").data('mespasado'));
                    refrescaTabla();
                    break;
                case 'fecha':
                    refrescaTabla();
                    break;
            }
        });
       $(".ipPeriodos").change(function(){
            $("#ipPeriodo").data('unperiodo',$(this).val());
            $(".itPeriodo[data-periodo=periodo]").prop('checked',true);
            refrescaTabla();
        }); 
       function refrescaTabla(){
        tablaTr.ajax.reload( null, false );
       }
    });
 </script>
</body>
</html>