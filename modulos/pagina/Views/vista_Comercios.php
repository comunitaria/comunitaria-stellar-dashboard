<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $VPConf->tituloWeb ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('public/assets/imagenes','https') ?>/confFavicon.png">
    <?= inserta_enlaces($enlaces) ?>
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
<!-- Vpconf< barra -->
<!-- Vpconf> aside (Zona sujeta a cambios auutomáticos)-->
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
            <?php 
                    $base_url=base_url();
                    echo tabla('tbComercios',[
                        'menu'=>[
                            'titulo'=>'Comercios adheridos',
                            'items' => [
                                [
                                    'texto' => '<i class="fas fa-plus pr-2"></i>Registrar comercio',
                                    'accion' => [
                                        'tipo' => 'enlace',
                                        'url' => base_url('/comercio/editar'),
                                        
                                                ]
                                    ],
                                    [
                                        'texto'=>'Eliminar',
                                        'accion'=>[
                                            'tipo' => 'eliminar',
                                            'url' => base_url('/comercios/eliminar'),
                                            'confirmacion'=>[
                                                'titulo'=>'Confirme eliminación de comercios',
                                                'cuerpo'=>'Todos los comercios marcados van a ser eliminados<br>Esta acción no puede deshacerse<br>¿Está completamente seguro?'
                                            ]
                                        ],
                                        
                                    ]
                                ],
                            ],
                            'componentes'=>'<"container-fluid mt-3"<"row"<"col"l><"col"f>>>rt<"container-fluid"<"row"<"col"i><"col"p>>>',
                            'menuLongitud'=>  [  [10, 25, 50, -1],  [10, 25, 50, "Todos"]],
                            'columnas'=>[
                                        [
                                            'titulo'=>'Id',
                                            'esIndice'=>true,
                                            'oculta'=>true,
                                            'entrada'=>[
                                            'tipo'=>'hidden',
                                            ],
                                        ],
                                        [
                                            'titulo'=>'CIF',
                                            'entrada'=>[
                                                'tipo'=>'text',
                                                'label'=> 'CIF'
                                            ],
                                            'orden'=>[0, 'asc'],
                                        ],
                                        [
                                            'titulo'=>'Nombre',
                                            'entrada'=>[
                                                'tipo'=>'text',
                                                'label'=> 'Nombre'
                                            ],
                                            
                                        ],
                                        [
                                            'titulo'=>'Usuario',
                                            'entrada'=>[
                                                'tipo'=>'text',
                                                'label'=> 'Usuario'
                                            ],
                                            
                                        ],
                                        [
                                            'titulo'=>'Contraseña',
                                            'oculta'=>true,
                                            'entrada'=>[
                                                'tipo'=>'password',
                                                'label'=> 'Contraseña'
                                            ]
                                        ],
                                        [
                                            'titulo'=>'Dirección',
                                            'entrada'=>[
                                                'tipo'=>'text',
                                                'label'=> 'Dirección'
                                            ],
                                            
                                        ],
                                        [
                                            'titulo'=>'Móvil',
                                            'entrada'=>[
                                                'tipo'=>'tel',
                                                'label'=> 'Móvil'
                                            ]
                                        ],
                                        [
                                            'titulo'=>'Correo',
                                            'entrada'=>[
                                                'tipo'=>'email',
                                                'label'=> 'Correo'
                                            ]
                                        ],
                                        [
                                            'titulo'=>'Activado',
                                            'entrada'=>[
                                                'tipo'=>'check',
                                                'label'=> 'Activado'
                                            ],
                                            'render'=>
<<<JS
                                                switch ( type){
                                                    case "sort": 
                                                        return data;
                                                        break;
                                                    case "filter": 
                                                        return data;
                                                        break;
                                                    default:
                                                        return '<i class="fas '+(data=='1'?'fa-check-square':'fa-times')+'"></i>';
                                                }
JS,
                                        ],
                                    ],
                                    'trClick'=>base_url('/comercio/editar').'/[Id]',
                                    'estilo'=>'hover striped col',
                                    'datos'=>$comercios,
                                ])
                    ?>
       
            </div>
        </section>
    </div>
<!-- Vpconf> pie (Zona sujeta a cambios automáticos)-->
    <?= pie($VPConf->contenidoPie,'',true) ?>
<!-- Vpconf< pie -->
 </div>
</body>
</html>