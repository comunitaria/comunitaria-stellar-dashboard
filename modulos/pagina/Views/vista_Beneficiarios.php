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
            <script>
                var clases=<?= json_encode($clases) ?>;
            </script>    
            <?php 
                    $base_url=base_url();
                    $avatarDefecto= base_url('public/assets/imagenes/avatares').'/avatar.jpg';
                    $opcionesClases=[];
                    foreach($clases as $unaClase){
                        $opcionesClases[$unaClase]=$unaClase;
                    }
                    echo tabla('tbBeneficiarios',[
                        'menu'=>[
                            'titulo'=>'Beneficiarios',
                            'items' => [
                                [
                                    'texto' => '<i class="fas fa-plus pr-2"></i>Registrar beneficiario',
                                    'accion' => [
                                        'tipo' => 'anadir',
                                        'formulario' => [
                                            'titulo' => 'Registro de beneficiario',
                                            'requerido' => true,
                                        ],
                                        'url' => base_url('/beneficiarios/crear'),
                                        
                                                ]
                                    ],
                                    [
                                        'texto'=>'Eliminar',
                                        'accion'=>[
                                            'tipo' => 'eliminar',
                                            'url' => base_url('/beneficiarios/eliminar'),
                                            'confirmacion'=>[
                                                'titulo'=>'Confirme eliminación de beneficiarios',
                                                'cuerpo'=>'Todos los beneficiarios marcados van a ser eliminados<br>Esta acción no puede deshacerse<br>¿Está completamente seguro?'
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
                                            'titulo'=>'Apellidos',
                                            'entrada'=>[
                                                'tipo'=>'text',
                                                'label'=> 'Apellidos'
                                            ],
                                            'orden'=>[0, 'asc'],
                                            'accion'=>[
                                                'tipo'=>'editar',
                                                'formulario'=>[
                                                    'titulo'=>'Editar beneficiario',
                                                    'requerido'=>true,
                                                ],
                                                'url'=>base_url('/beneficiarios/modificar'),
                                            ],
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
                                            'titulo'=>'Autorizado en',
                                            'entrada'=>[
                                                'tipo'=>'select2',
                                                'label'=> 'Autorizado a compra con ILLA en comercios',
                                                'opciones'=>$opcionesClases
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
                                    'estilo'=>'hover striped col',
                                    'trClick'=>'editar',
                                    'datos'=>$beneficiarios,
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