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
            <div class="container-fluid">
            <?php $base_url=base_url(); ?>
            <?php
                if (tienePermiso(1)) {
                    echo tabla('tbOficios',[ 'menu'=>[
                                            'titulo'=>'Perfiles de usuarios',
                                            'items' =>[
                                                [
                                                    'texto'=>'<i class="fas fa-plus pr-2"></i>Nuevo perfil',
                                                    'accion'=>[
                                                        'tipo'=>'anadir',
                                                        'formulario'=>[
                                                            'titulo'=>'Nuevo perfil',
                                                            'requerido'=>
                                                    <<<JS
                                                            (___formularios_campo("Designación")!='')
                                                    JS,
                                                        ],
                                                        'url'=>base_url('/grupos/crear_oficio'),
                                                        'al'=>[
                                                            'actualizar'=>
<<<JS
                (valores,tabla)=>{
                    actualizar_select(tabla);
                }
JS
                                                        ]           
                                                    ]
                                                ],
                                                [
                                                    'texto'=>'Eliminar',
                                                    'accion'=>[
                                                        'tipo'=>'eliminar',
                                                        'url'=>base_url('/grupos/eliminar_oficio'),
                                                        'al'=>['actualizar'=>
<<<JS
                                                        (valores,tabla)=>{
                                                            actualizar_select(tabla);
                                                        }
JS
                                                            ]      
                                                    ]
                                                ],
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
                                                    'label'=>'Id del perfil:'
                                                ],
                                                
                                            ],
                                            [
                                                'titulo'=>'Designación',
                                                'entrada'=>[
                                                    'tipo'=>'text',
                                                    'label'=>'Designación del perfil:'
                                                ],
                                                'accion'=>[
                                                    'tipo'=>'editar',
                                                    'formulario'=>[
                                                        'titulo'=>'Modificar perfil',
                                                        'requerido'=>
                                                    <<<JS
                                                        (___formularios_campo("Designación")!='')
                                                    JS,
                                                    ],
                                                    'url'=>base_url('/grupos/modificar_oficio'), 
                                                    'al'=>['actualizar'=>
<<<JS
                                                        (valores,tabla)=>{
                                                            actualizar_select(tabla);
                                                        }  
JS
                                                        ]        
                                                ],
                                                
                                            ],
                                            [
                                                'titulo'=>'Características',
                                                'entrada'=>[
                                                    'tipo'=>'multicheck',
                                                    'label'=>'Características:',
                                                    'checks'=>[
                                                        'U'=>'Último perfil (no tiene "En curso")'
                                                    ]
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
                                                        var html='';
                                                        if (data.indexOf('U')>=0)
                                                            html+='<p><i class="fas fa-check-square"></i> Último perfil (no tiene expedientes "En curso")</p>';
                                                        else
                                                            html+='<p><i class="fas fa-times"></i> <span class="text-secondary">Último perfil (no tiene expedientes "En curso")</span></p>';
                                                        return html;
                                                }
JS
                                            ]
                                            
                                        ],
                                        'estilo'=>'hover striped col-sm-6',
                                        'datos'=>$oficios,
                                        'al'=>[
                                            'crear_tabla'=>
                                                <<<JS
                                                    (tabla)=>{
                                                            actualizar_select(tabla);
                                                    }
                                                JS
                                        ]
                                                ]);
            }                    
                                        ?>
       <div class="my-5"></div>
       <!-- GRUPOS -->
       <?= tabla('tbGrupos',[ 'menu'=>[
                                            'titulo'=>'Grupos',
                                            'items' =>[
                                                [
                                                    'texto'=>'<i class="fas fa-plus pr-2"></i>Nuevo grupo',
                                                    'accion'=>[
                                                        'tipo'=>'anadir',
                                                        'formulario'=>[
                                                            'titulo'=>'Nuevo grupo',
                                                            'requerido'=>
                                                    <<<JS
                                                            (___formularios_campo("Nombre_de_grupo")!='')
                                                    JS,
                                                        ],
                                                        'url'=>base_url('/grupos/crear'),
//                                                         'al'=>[
//                                                             'actualizar'=>
// <<<JS
//                 (valores,tabla)=>{
//                     actualizar_select(tabla);
//                 }
// JS
//                                                         ]
                                                    ]
                                                ],
                                                [
                                                    'texto'=>'Eliminar',
                                                    'accion'=>[
                                                        'tipo'=>'eliminar',
                                                        'url'=>base_url('/grupos/eliminar')           
                                                    ]
                                                ],
                                            ]
                                        ],
                                        'componentes'=>'rtp',
                                        'columnas'=>[
                                            [
                                                'titulo'=>'Id',
                                                'esIndice'=>true,
                                                'oculta'=>true,
                                                'entrada'=>[
                                                    'tipo'=>'hidden'
                                                ],
                                                
                                            ],
                                            [
                                                'titulo'=>'Nombre del grupo',
                                                'orden'=>[0, 'asc'],
                                                'entrada'=>[
                                                    'tipo'=>'text',
                                                    'label'=>'Nombre'
                                                ],
                                                'accion'=>[
                                                    'tipo'=>'editar',
                                                    'formulario'=>[
                                                        'titulo'=>'Modificar grupo',
                                                        'requerido'=>
                                                    <<<JS
                                                        (___formularios_campo('Nombre del grupo')!='')
                                                    JS,
                                                    ],
                                                    'url'=>base_url('/grupos/modificar'), 
                                                ]   
                                            ],
                                            [
                                                'titulo'=>'id_perfil',
                                                'oculta'=>true,
                                                'entrada'=>[
                                                    'tipo'=>'hidden'
                                                ],
                                                
                                            ],
                                            [
                                                'titulo'=>'oficio',
                                                'oculta'=>true,
                                                'entrada'=>[
                                                    'tipo'=>'hidden'
                                                ],
                                                
                                            ],
                                            [
                                                'titulo'=>'Perfil',
                                                'render'=>
<<<JS
                                                    return listaPerfiles[data];
JS,
                                                'entrada'=>[
                                                    'tipo'=>'select',
                                                    'label'=>'Perfil:',
                                                ]
                                            ],
                                            [
                                                'titulo'=>'Miembros del grupo',
                                                'render' => 
<<<JS
                                                return data+' <a class="btn btn-info btn-sm mt-0 ml-2" href="$base_url/grupos/modificar_grupo?grp='+row.Id+'"><i class="fas fa-plus"></i> Agregar</a>';
JS,
                                                'entrada'=>[
                                                    'tipo'=>'hidden',
                                                    'valor'=>0
                                                ]
                                            ],
                                        ],
                                        'estilo'=>'hover striped col',
                                        'datos'=>$grupos]) ?>
       
            </div>
        </section>
    </div>
    <script>
        var listaPerfiles;
        var listaPerfilesBase={ 
                                <?= tienePermiso(1)?'"1|0":"Superusuarios",':'' ?>
                                "2|0":'Administradores de usuarios',
                                "3|0":'Supervisores' 
                            };
        listaPerfiles=listaPerfilesBase;
        var oficios=JSON.parse('<?= json_encode(array_values($oficios)) ?>');
        for(var i=0;i<oficios.length; i++){
                listaPerfiles['3|'+oficios[i]['id_oficio']]='Supervisor '+oficios[i]['desc_oficio'];
                listaPerfiles['4|'+oficios[i]['id_oficio']]=oficios[i]['desc_oficio'];
        }
          
        function actualizar_select(){
            if (typeof tabla_tbOficios !== 'undefined'){
                listaPerfiles=listaPerfilesBase;
                oficios=[];
                tabla_tbOficios.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
                     oficios.push({id_oficio:this.data().Id,desc_oficio:this.data().Designación,caracteristicas:this.data().Características});
                });
                for(var i=0;i<oficios.length; i++){
                    listaPerfiles['4|'+oficios[i]['id_oficio']]=oficios[i]['desc_oficio'];
                }
            }
            var html = '';
            for(var l in listaPerfiles){
                html += '<option value="'+l+'">'+listaPerfiles[l]+'</option>';

            }
            $("#frmAnadir--tbGrupos #ipPerfil").html(html);
            $("#frmEditar--tbGrupos #ipPerfil").html(html);

        }
     
        $(document).ready(function(){
            actualizar_select();
            $("#frmEditar--tbGrupos #ipPerfil").change(function(){
                var datos = $(this).val().split('|');
                $("#frmEditar--tbGrupos #ipid_perfil").val(datos[0]);
                $("#frmEditar--tbGrupos #ipoficio").val(datos[1]);
            });
            $("#frmAnadir--tbGrupos #ipPerfil").change(function(){
                var datos = $(this).val().split('|');
                $("#frmAnadir--tbGrupos #ipid_perfil").val(datos[0]);
                $("#frmAnadir--tbGrupos #ipoficio").val(datos[1]);
            });
        });
    </script>
<!-- Vpconf> pie (Zona sujeta a cambios automáticos)-->
    <?= pie($VPConf->contenidoPie,'',true) ?>
<!-- Vpconf< pie -->
 </div>
</body>
</html>