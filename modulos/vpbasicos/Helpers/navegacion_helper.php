<?php
function enlaces_navegacion(&$enlaces){
    anade_enlaces($enlaces,[
        'script'=>[
            'adminltejs'=>[
                    'link'=>base_url('public/assets/js','https').'/adminlte/adminlte.js',
                    'requerido'=>['jquery'],
            ],
            'jquery'=>[
                'link'=>base_url('public/assets/plugins','https').'/jquery/jquery.min.js',
                'requerido'=>[],
            ],
            'bootstrapBundle'=>[
                    'link'=>base_url('public/assets/plugins','https').'/bootstrap/js/bootstrap.bundle.min.js',
                    'requerido'=>['jquery'],
            ]
        ],
        'stylesheet'=>[
            'adminlte'=>[
                'link'=>base_url('public/assets/css','https').'/adminlte.css',
                'requerido'=>[],
            ],
            'OverlayScrollbars'=>[
                'link'=>base_url('public/assets/plugins','https').'/overlayScrollbars/css/OverlayScrollbars.min.css',
                'requerido'=>[],
            ],
            'fontawesome'=>[
                'link'=>base_url('public/assets/plugins','https').'/fontawesome-free/css/all.min.css',
                'requerido'=>[],
            ],
        ],
    ]);
}
function sidebar($pmenu){
    $miURL=preg_replace('/(.*)index.php\//','',$_SERVER['PHP_SELF']);

    if (is_array($pmenu)){
        $menu=$pmenu;
    }
    else{
        $menu=json_decode($pmenu,true);
    }
    $html=
<<<HTML
    <div class="sidebar">    
        <nav class="mt-2  sticky-top">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            
HTML;
    foreach($menu as $itemMenu){
        $aut=explode(',',$itemMenu['aut']);
        if (tienePermiso($aut)){
            if (isset($itemMenu['menu'])){
                $href='#';
            }
            else{
                $href=$itemMenu['href'];
                if (substr($href,0,1)=='/') $href=base_url($href);
            }
            $open='';
            $activo='';
            if (substr($href,-strlen($miURL))==$miURL)
                $activo='active';     
            if (isset($itemMenu['menu'])){
                foreach($itemMenu['menu'] as $subItemMenu){
                    if (substr($subItemMenu['href']??'',-strlen($miURL))==$miURL){
                        $activo='active';     
                        $open='menu-open';
                        break;
                    }
                }
            }
            $html.=
<<<HTML
                <li class="nav-item $open">
                    <a href="$href" class="nav-link $activo">
                        <i class="nav-icon {$itemMenu['icono']}"></i>
                        <p>{$itemMenu['texto']}
HTML;
            if (isset($itemMenu['menu'])){
                $html.=
<<<HTML
                        <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
HTML;
                foreach($itemMenu['menu'] as $subItemMenu){
                    $aut=explode(',',$subItemMenu['aut']);
                    if (tienePermiso($aut)){
                        if (isset($subItemMenu['menu'])){
                            $href='#';
                        }
                        else{
                            $href=$subItemMenu['href'];
                            if (substr($href,0,1)=='/') $href=base_url($href);
                        }
                        $activo='';
                        if (substr($href,-strlen($miURL))==$miURL)
                            $activo='active';     
                        $html.=
<<<HTML
                        <li class="nav-item">
                            <a href="$href" class="nav-link $activo">
                                <i class="nav-icon {$subItemMenu['icono']}"></i>
                                <p>{$subItemMenu['texto']}</p>
                            </a>
                        </li>
HTML;
                    }
                }
                $html.=
<<<HTML
                    </ul>
HTML;
            }
            else{
                $html.=
<<<HTML
                        </p>
                    </a>
HTML;
            }
<<<HTML
                </li>
HTML;
        }
    }
    $html.=
    <<<HTML
                </ul>
            </nav>
        </div>    
    HTML;    
    return $html;
}
function sidebarConfig($titulo,$pmenu){
    $miURL=preg_replace('/(.*)index.php\//','',$_SERVER['PHP_SELF']);

    if (is_array($pmenu)){
        $menu=$pmenu;
    }
    else{
        if ($pmenu!='')
            $menu=json_decode($pmenu,true);
        else
            $menu=[];
    }
    
    $dark=env('Config\VstPortal.configuracionDark');
    if ($dark=='') $dark='dark';
    $todasLasAut=[];
    foreach($menu as $itemMenu){
        $todasLasAut=array_merge($todasLasAut,explode(",",$itemMenu['aut']));
    }
    $todasLasAut=array_unique($todasLasAut);
    if (!tienePermiso($todasLasAut)) return '';
    $html=
<<<HTML
    <aside class="control-sidebar control-sidebar-$dark">
        <div class="p-3 control-sidebar-content">
            <h4 class="">$titulo</h4>
          <nav class="mt-2">
            <ul class="nav nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
HTML;
    foreach($menu as $itemMenu){
        $aut=explode(',',$itemMenu['aut']);
        if (tienePermiso($aut)){
            if (isset($itemMenu['menu'])){
                $href='#';
            }
            else{
                $href=$itemMenu['href'];
                if (substr($href,0,1)=='/') $href=base_url($href);
            }
            $open='';
            $activo='';
            if (substr($href,-strlen($miURL))==$miURL)
                $activo='active';     
            if (isset($itemMenu['menu'])){
                foreach($itemMenu['menu'] as $subItemMenu){
                    if (substr($subItemMenu['href']??'',-strlen($miURL))==$miURL){
                        $activo='active';     
                        $open='menu-open';
                        break;
                    }
                }
            }
            $html.=
<<<HTML
                <li class="nav-item $open">
                    <a href="$href" class="nav-link $activo">
                        <i class="nav-icon {$itemMenu['icono']}"></i>
                        <p>{$itemMenu['texto']}
HTML;
            if (isset($itemMenu['menu'])){
                $html.=
<<<HTML
                        <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
HTML;
                foreach($itemMenu['menu'] as $subItemMenu){
                    $aut=explode(',',$subItemMenu['aut']);
                    if (tienePermiso($aut)){
                        if (isset($subItemMenu['menu'])){
                            $href='#';
                        }
                        else{
                            $href=$subItemMenu['href'];
                            if (substr($href,0,1)=='/') $href=base_url($href);
                        }
                        $activo='';
                        if (substr($href,-strlen($miURL))==$miURL)
                            $activo='active';     
                        $html.=
<<<HTML
                        <li class="nav-item">
                            <a href="$href" class="nav-link $activo" >
                                <i class="nav-icon {$subItemMenu['icono']}"></i>
                                <p>{$subItemMenu['texto']}</p>
                            </a>
                        </li>
HTML;
                    }
                }
                $html.=
<<<HTML
                    </ul>
HTML;
            }
            else{
                $html.=
<<<HTML
                        </p>
                    </a>
HTML;
            }
<<<HTML
                </li>
HTML;
        }
    }
    $html.=
    <<<HTML
                </ul>
              </nav>
            </div>
        </aside>    
    HTML;    
    return $html;
}

function activar_menu_item(&$menu,$itemActivo){
    foreach($menu as $clave=>$valor){
        $menu[$clave]['activo']=($clave==$itemActivo);
    }
}
function barraNavegacion($menu,$componentes,$clases='',$inicio='menu',$fijar=false){
    $usuario=datos_usuario();
    $fijo='';
    if ($fijar)
        $fijo='<script>document.body.classList.add("layout-navbar-fixed");</script>';
    $dark=env('Config\VstPortal.superiorDark');
    if (count($menu)>0){
        if (isset($menu[0]['vpconf'])){
            $dark='dark';
        } 
    }
    $lateral=($inicio=='menu')?
                '<div class="a nav-link" data-widget="pushmenu" role="button"><i class="fas fa-bars"></i></div>':$inicio;

    $html=
<<<HTML
<nav class="main-header navbar navbar-expand navbar-$dark $clases">
    <ul class="navbar-nav">
        <li class="nav-item">
            $lateral
        </li>
HTML;
    foreach($menu as $itemMenu){
        if (tienePermiso($itemMenu['aut'])){
            $href=$itemMenu['href'];
            if (substr($href,0,1)=='/') $href=base_url($href);
            $activo=(($itemMenu['activo']??'')?'active':'');
            $html.=
<<<HTML
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{$href}" class="nav-link menu-superior $activo" >{$itemMenu['texto']}</a>
        </li>
HTML;
        }
    }
    $html.=
<<<HTML
    </ul>
HTML;
    $aLaDerecha='ml-auto';
        
    foreach($componentes as $unComponente){
        $id='';
        if ($unComponente['id']??''!=''){
            $id=' id="'.$unComponente['id'].'"';
        }
        switch($unComponente['tipo']){
            case 'comentarios':
                $JSONMensajes=str_replace("'","\\'",json_encode($unComponente['mensajes']??[]));
                $URLAPI=$unComponente['url']??'';
                $refresco=$unComponente['refresco']??1000;
                $idComponente=$unComponente['id']??'___mensajes';
                $suscrito=strpos($usuario['Caracteristicas']??'','S')===false?'true':'false';
                $base_url=base_url();
                $html.=
<<<HTML
    <ul id="$idComponente" class="navbar-nav $aLaDerecha">
        <li class="nav-item dropdown">
            <div class="nav-link $aLaDerecha" data-toggle="dropdown" aria-expanded="true">
                <i class="text-lg far fa-comments"></i>
                <span class="badge badge-danger navbar-badge" style="display:none"></span>
            </div>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="left: inherit; right: 0px;max-height: 80vh;overflow-y: auto;">
            </div>
        </li>
    </ul>
    <script>
        var suscrito=$suscrito;
        function actualiza(___nuevos){
            var suscripcion=`<a id="{$idComponente}_sus" href="{$base_url}/usuarios/miPerfil" class="dropdown-item" ><i class="fa fa-cog text-secondary"></i> `+(suscrito?'Dejar de recibir notificaciones automáticas':'Activar notificaciones automáticas')+`</a>
                `;
            if (___nuevos.length>0){
                var hayNuevos=($("#$idComponente .badge").show().text()!=___nuevos.length);
                $("#$idComponente .badge").show().text(___nuevos.length);
                if (hayNuevos){
                    $("#$idComponente .badge").animate({
                        left: "+=2",fontSize:"+=10", top:"-=10",
                        height: "+=10", width:"+=10"
                    }, 300).animate({
                        left: "-=2",fontSize:"-=10", top:"+=10",
                        height: "-=10", width:"-=10"
                    }, 300);
                }
                var divMensajes='';
                var ahoraHay=$("#$idComponente .dropdown-menu a.dropdown-item").length;
                $("#{$idComponente}_sus").remove();
                for(var m in ___nuevos){
                    var hace='';
                    var valor=___nuevos[m].hace;
                    if (valor<60){
                        hace=valor+' segundo'+(((valor>0)||(valor>1))?'s':'');
                    }
                    else{
                        valor=Math.round(valor/60);
                        if (valor<60){
                            hace=valor+' minuto'+(((valor>0)||(valor>1))?'s':'');
                        }
                        else{
                            valor=Math.round(valor/60);
                            if (valor<24){
                                hace=valor+' hora'+(((valor>0)||(valor>1))?'s':'');
                            }
                            else{
                                valor=Math.round(valor/24);
                                hace=valor+' día'+(((valor>0)||(valor>1))?'s':'');
                            }
                        }
                    }
                    if (m<ahoraHay){
                        $("#$idComponente .dropdown-menu a.dropdown-item").eq(m).attr('href',___nuevos[m].link);
                        $("#$idComponente .dropdown-menu a.dropdown-item img").eq(m).attr('src',___nuevos[m].autor_avatar);
                        $("#$idComponente .dropdown-menu a.dropdown-item h3").eq(m).html(___nuevos[m].autor_nombre);
                        $("#$idComponente .dropdown-menu a.dropdown-item p").eq(m*3).html(___nuevos[m].titulo);
                        $("#$idComponente .dropdown-menu a.dropdown-item p").eq(m*3+1).html(___nuevos[m].texto);
                        $("#$idComponente .dropdown-menu a.dropdown-item p").eq(m*3+2).html(`<i class="far fa-clock mr-1"></i> hace `+hace);
                    }
                    else{
                        $("#$idComponente .dropdown-menu").append(`
                    <a class="dropdown-item" href="`+___nuevos[m].link+`">
                        <div class="media">
                            <img src="`+___nuevos[m].autor_avatar+`" alt="Avatar" class="img-size-50 mr-3 img-circle">
                            <div class="media-body">
                                <h3 class="dropdown-item-title">
                                `+___nuevos[m].autor_nombre+`
                                </h3>
                                <p class="text-sm text-primary">`+___nuevos[m].titulo+`</p>
                                <p class="text-sm">`+___nuevos[m].texto+`</p>
                                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> hace `+hace+`</p>
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-divider"></div>
                    `);    
                    }
                    $("#$idComponente .dropdown-menu a.dropdown-item").eq(m)
                }
                $("#$idComponente .dropdown-menu").append(suscripcion);
    
//                $("#$idComponente .dropdown-menu").html(divMensajes);
            }
            else{
                $("#$idComponente .badge").hide();
                $("#$idComponente .dropdown-menu").html(`
                    <div class="dropdown-item text-secondary" >No tiene nuevos mensajes</div>
                    <div class="dropdown-divider"></div>
                `+suscripcion);
            }
        }
HTML;
                if($URLAPI!=''){
                    $html.=
<<<HTML
                (function ___refresco() {
                        $.get('$URLAPI',function(respuesta){
                            actualiza(respuesta.filter((m)=>suscrito||!m.autor_avatar.includes('avatarSistema')));
                            setTimeout(___refresco, $refresco);
                        });    
                })();
HTML;
                }
                else{
                    $html.=
<<<HTML
                actualiza(JSON.parse('$JSONMensajes').filter((m)=>suscrito||!m.autor_avatar.includes('avatarSistema')));
HTML;        
                }
                $html.=
<<<HTML
     </script>
HTML;
            
                $aLaDerecha='';
                
                break;
            case 'icono':
                $html.=
<<<HTML
    <ul class="navbar-nav $aLaDerecha">
        <li class="nav-item">
            <a class="nav-link" $id href="{$unComponente['href']}">
                <i class="text-md   {$unComponente['icono']}"></i>
            </a>
        </li>
    </ul>
HTML;
                $aLaDerecha='';
                break;
            case 'usuario':
                $logout=base_url('logout','https');
                if (isset($usuario['Nombre_Usuario'])){
                    $imagen=$usuario['Avatar'];
                    $hrefMiPerfil=base_url('usuarios/miPerfil');
                    $html.=
<<<HTML
<ul class="navbar-nav $aLaDerecha">
        <li class="nav-item dropdown user-menu">
            <div class="nav-link dropdown-toggle" data-toggle="dropdown" role="button">
                <div class="image d-inline">
                    <img src="$imagen" style="width:2em" class="img-circle elevation-2" alt="Avatar">
                </div>
                <div class="info d-none d-sm-inline">
                {$usuario['Nombre_Usuario']}
                </div>
                </div>
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <li class="user-header">
                    <div class="text-center">
                    {$usuario['Nombre_Usuario']}
                    </div>
                    <div class="p-0 mt-2">
                    <img  src="$imagen" style="width:5em" class="img-circle elevation-2" alt="Avatar">
                    </div>
                    <a href="$hrefMiPerfil" class="btn btn-primary btn-outline mt-2 py-1">Mi perfil</a>
                </li>
                <li class="user-body d-none">
                    <div class="row">
                    </div>
                </li>
                <li class="user-footer">
                    <button type="button" onclick="$(this).closest('.dropdown-menu').removeClass('show');" class="btn btn-default btn-flat float-right">Volver</button>
                    <a href="$logout" class="btn btn-default btn-flat ml-auto">Cerrar sesión</a>
                </li>
            </ul>
        </li>
    </ul>
HTML;
                }
                else{
                    $href=base_url('/login');
                    $html.=
<<<HTML
    <a class="btn btn-primary $aLaDerecha" type="button" href="$href">Iniciar sesión</a>
HTML;
                }
                $aLaDerecha='';
                break;
            case 'configuracion':
                $sidebarConfiguracion=sidebarConfig($unComponente['titulo']??'Configuración',$unComponente['menu']);
                if ($sidebarConfiguracion!=''){
                    $fijo.=$sidebarConfiguracion;
                    $html.=
<<<HTML
    <ul class="navbar-nav $aLaDerecha">
        <li class="nav-item">
            <div class="nav-link" data-widget="control-sidebar" data-controlsidebar-slide="true" role="button">
                <i class="fas fa-cog"></i>
                </div>
        </li>
    </ul>
HTML;
                    $aLaDerecha='';
                }
                break;
        }
    }
    $html.=
<<<HTML
    </ul>
</nav>
$fijo
HTML;
    return $html;
}

function encabezado($href,$logo,$titulo){
    $html=
<<<HTML
  <a href="$href" class="brand-link">
        <img src="$logo" alt="$titulo Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">$titulo</span>
    </a>
HTML;
    return $html;
}

function pie($contenidoHTML,$clases='',$fijar=false){
    $izquierdo='';
    $derecho='';
    if (is_array($contenidoHTML)){
        $izquierdo=$contenidoHTML[0];
        if (count($contenidoHTML)>1){
            $derecho=$contenidoHTML[1];
        }
    }
    else{
        $izquierdo=$contenidoHTML;
    }
    $fijo='';
    if ($fijar)
        $fijo='<script>document.body.classList.add("layout-footer-fixed");</script>';
    $html=
<<<HTML
<footer class="main-footer $clases">
    <div class="float-right d-none d-sm-inline">
        $derecho
</div>
$izquierdo
</footer>
$fijo
HTML;
    return $html;
}

?>
