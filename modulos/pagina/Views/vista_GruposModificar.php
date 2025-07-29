<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <title><?= $VPConf->tituloWeb ?></title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="shortcut icon" type="image/png" href="<?= base_url('public/assets/imagenes','https') ?>/confFavicon.png">
      <?= inserta_enlaces($enlaces) ?>
      <script src="<?= base_url('public/assets/plugins/toastr', 'https') ?>/toastr.min.js"></script>
    <script src="<?= base_url('public/assets/plugins/moment', 'https') ?>/moment-with-locales.min.js"></script>
      <style>
         .imagenConfig:hover{
         cursor: pointer;
         box-shadow: 0px 0px 15px yellow;
         }
      </style>
   </head>
   <body class="hold-transition sidebar-mini layout-fixed  text-<?= $VPConf->tamanoTexto ?> <?= $VPConf->tonalidad ?>">
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
                <div class="container-fluid p-4">
                <?= tabla('tbUsuarios',['menu'=>[
                               'titulo'=>'<button type="button" onclick="history.back()" class="btn btn-info mr-3"><i class="fas fa-chevron-left"></i></button>'.$grupo->desc_grupo.'<small class="ml-3">Miembros</small>',
                               'items' => [
                                        [
                                            'texto' => '<i class="fas fa-plus pr-2"></i>Añadir usuario',
                                            'accion' => [
                                            'tipo' => 'funcion',
                                            'script' => <<<JS
                                                $('#modalElegirUsuario').modal('show');
JS
                                            ]
                                        ],   
                                        [
                                            'texto'=>'Eliminar',
                                            'accion'=>[
                                                'tipo' => 'eliminar',
                                                'url' => base_url('/grupos/eliminar_usuarios'), // eliminar usuario de grupo
                                                ]
                                        ]
                                    ],
                                    ],
                                    'componentes'=>'rt',
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
                                            'titulo'=>'id_usr',
                                            'oculta'=>true,
                                            'entrada'=>[
                                                'tipo'=>'hidden'
                                            ]
                                        ],
                                        [
                                            'titulo'=>'Usuario',
                                            'entrada'=>[
                                                'tipo'=>'text',
                                                'label'=> 'Usuario'
                                            ]
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
                                                        <img src="`+row.Avatar+`" style="width:2em" class="img-circle elevation-2" alt="Avatar">
                                                    </div>
                                                    <span class="ml-2">`+data+`</span>`;
JS,
                                            
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
                                                'tipo'=>'hidden'
                                            ]
                                        ],
                                        // [
                                        //     'titulo'=>'Grupos',
                                        //     'entrada'=>[
                                        //         'tipo'=>'text',
                                        //         'label'=> 'Grupos'
                                        //     ]
                                        // ],
                                    ],
                                    'estilo'=>'hover striped col',
                                    'datos'=>$usuarios,
                                ])
                    ?>

                    <?php
                        echo formulario("frmElegirUsuario", [
                            "titulo" => "Escoja los usuarios que desea añadir al grupo",
                            "requerido" => <<<JS
                            ___formularios_campo("Usuario")!=''
JS
                            ,
                            "campos" => [
                                "ipUsuario" => [
                                    "tipo" => "select",
                                    "label" => "Usuario",
                                    "opciones" => ["ar" => "Argentina","bo" => "Bolivia","cl" => "Chile","co" => "Colombia"], // se deben de añadir aquí los usuarios recuperados?
                                ],
                            ],
                            // "aceptar" => <<<JS

                        ]);

                    ?>

            

    <script>
        $(document).ready(function(){
            $('#select-usuarios').select2({
            data: [<?php
                    foreach ($usuariosNoEnGrupo as $unUsuario) {
                            ?>
                        {
                        'id':<?=$unUsuario->id_usr ?>,
                        'text':'<?= $unUsuario->login_usr ?>',
                        'htmlSel':`
                                    <div class="image d-inline">
                                        <img src="<?=imagen_usr($unUsuario->id_usr) ?>" style="width:2em" class="img-circle elevation-2" alt="Avatar">
                                    </div>
                                    <span class="ml-2"><?= $unUsuario->login_usr ?>: <?= $unUsuario->nombre_usr ?></span>
                                `,
                        'htmlRes':`
                                    <div class="image d-inline">
                                        <img src="<?=imagen_usr($unUsuario->id_usr) ?>" style="width:2em" class="img-circle elevation-2" alt="Avatar">
                                    </div>
                                    <span class="ml-2"><?= $unUsuario->login_usr ?></span>
                                `
                        },
                        <?php 
                            }
                             ?>
                        ],
            placeholder: 'Selecciona uno o varios usuarios',
            allowClear: false,
            templateSelection: function(option) {
                return $( option.htmlRes);
            },
            templateResult: function(option) {
                return $( option.htmlSel);
            }
        });

            $('#anadir-usuarios').click(function(){
                var usuarios = $('#select-usuarios').val();
    
                
                var datos = {
                    usuarios: usuarios,
                    grp: <?= $grupo->id_grupo ?>
                };
                
                $.ajax({
                    url: '<?php echo base_url() ?>/grupos/anadirUsuariosAlGrupo',
                    method: 'POST',
                    data: datos})
                .done(()=>{
                    location.reload();
                })
                .fail((xhr, status, error) => {
                    $("#modalElegirUsuario").modal('hide');
                    toastr.error('Error en la insercion de usuarios:<br>' + error);
                    });
            });


        });
    </script>
                </div>
            </section>
        </div>
    </div>

         <!-- Vpconf> pie (Zona sujeta a cambios automáticos)-->
         <?= pie($VPConf->contenidoPie,'',true) ?>
         <!-- Vpconf< pie -->
         <div class="modal fade" id="modalElegirUsuario" tabindex="-1" role="dialog" aria-labelledby="modalElegirUsuarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalElegirUsuarioLabel">Elija los usuarios que desea añadir</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <select id="select-usuarios" multiple>
                    </select>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" id="anadir-usuarios" class="btn btn-primary">Añadir usuarios</button>
                </div>
            </div>
        </div>
    </div>

   </body>
</html>