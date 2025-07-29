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
<!-- Vpconf> aside (Zona sujeta a cambios automáticos)-->
    <aside class="main-sidebar sidebar-<?= $VPConf->lateralDark ?>-<?= $VPConf->lateralDark ?> elevation-4">
                <?= encabezado(base_url(''),base_url('public/assets/imagenes').'/logotipo'.($VPConf->UAT?"UAT":"").'.png',$VPConf->nombreCliente) ?>
                <?= sidebar(json_decode($VPConf->menuLateral,true)) ?>

    </aside>
<!-- Vpconf< aside -->
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <?php 
                    $base_url=base_url();
                    $avatarDefecto= base_url('public/assets/imagenes/avatares').'/avatar.jpg';
                    echo tabla('tbUsuarios',['menu'=>[
                            'titulo'=>'Usuarios',
                            'items' => [
                                [
                                    'texto' => '<i class="fas fa-plus pr-2"></i>Nuevo usuario',
                                    'accion' => [
                                        'tipo' => 'anadir',
                                        'formulario' => [
                                            'titulo' => 'Nuevo usuario',
                                            'requerido' => true,
                                        ],
                                        'url' => base_url('/usuarios/crear_usuario'),
                                        
                                                ]
                                    ],
                                    [
                                        'texto'=>'Eliminar',
                                        'accion'=>[
                                            'tipo' => 'eliminar',
                                            'url' => base_url('/usuarios/eliminar'),
                                            ]
                                    ]
                                ],
                                ],
                                    'componentes'=>'rtp',
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
                                            'titulo'=>'Usuario',
                                            'entrada'=>[
                                                'tipo'=>'text',
                                                'label'=> 'Usuario'
                                            ],
                                            'orden'=>[0, 'asc'],
                                            'accion'=>[
                                                'tipo'=>'editar',
                                                'formulario'=>[
                                                    'titulo'=>'Editar usuario',
                                                    'requerido'=>true,
                                                ],
                                                'url'=>base_url('/usuarios/modificar'),
                                            ],
                                        ],
                                        [
                                            'titulo'=>'Nombre',
                                            'entrada'=>[
                                                'tipo'=>'text',
                                                'label'=> 'Nombre y Apellidos'
                                            ],
                                            'render'=>
<<<JS
                                                    return `
                                                    <div class="image d-inline">
                                                        <img src="`+(row.Avatar||'$avatarDefecto')+`" style="width:2em" class="img-circle elevation-2" alt="Avatar">
                                                    </div>
                                                    <span class="ml-2">`+data+`</span>`;
JS,

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
                                            'titulo'=>'Correo',
                                            'entrada'=>[
                                                'tipo'=>'email',
                                                'label'=> 'Correo'
                                            ]
                                        ],
                                        [
                                            'titulo'=>'Avatar',
                                            'oculta'=>true,
                                            'entrada'=>[
                                                'tipo'=>'hidden',
                                            ]
                                        ],

                                    ],
                                    'estilo'=>'hover striped col',
                                    'datos'=>$usuarios,
                                ])
                    ?>
            </div>
        </section>
    </div>
    <?= pie($VPConf->contenidoPie,'',true) ?>
 </div>
</body>
</html>