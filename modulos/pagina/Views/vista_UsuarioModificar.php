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
        <section class="content">
            <div class="container-fluid">
                <?= tabla('tbUsuarioGrupo',['menu'=>[
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
                                        ]
                                    ],
                                    [
                                        'texto'=>'Eliminar',
                                        'accion'=>[
                                            'tipo' => 'eliminar',
                                            'url' => base_url('/usuarios/eliminar_usuario'),
                                            ]
                                        ]
                                    ],
                                    'componentes'=>'rt',
                                    'columnas'=>[
                                        [
                                            'titulo'=>'Id',
                                            'esIndice'=>true,
                                            'oculta'=>true,
                                            'entrada'=>[
                                            'tipo'=>'hidden',
                                            'label'=>'Id usuario'
                                            ],
                                        ],
                                        [
                                            'titulo'=>'Nombre y Apellidos',
                                            'entrada'=>[
                                                'tipo'=>'text',
                                                'label'=> 'Nombre y Apellidos'
                                            ],
                                            'accion'=>[
                                                'tipo'=>'editar',
                                                'formulario'=>[
                                                    'titulo'=>'Editar usuario',
                                                    'requerido'=>true,
                                                ],
                                                'url'=>base_url('/usuarios/modificar/editar_usuario'),
                                            ],
                                        ],
                                        [
                                            'titulo'=>'Nombre de usuario',
                                            'entrada'=>[
                                                'tipo'=>'text',
                                                'label'=> 'Nombre de usuario'
                                            ]
                                        ],
                                        [
                                            'titulo'=>'Correo',
                                            'entrada'=>[
                                                'tipo'=>'email',
                                                'label'=> 'Correo'
                                            ]
                                        ]
                                    ],
                                    'estilo'=>'hover striped col',
                                    'datos'=> $datosUsuario,
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