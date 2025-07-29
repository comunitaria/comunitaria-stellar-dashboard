<?php
function enlaces_formularios(&$enlaces){
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
            'select2'=>[
                'link'=>base_url('public/assets/plugins','https').'/select2/js/select2.min.js',
                'requerido'=>['jquery'],
            ],
            'select2ES'=>[
                'link'=>base_url('public/assets/plugins','https').'/select2/js/i18n/es.js',
                'requerido'=>['select2'],
            ],
            'datatable'=>[
                    'link'=>base_url('public/assets/plugins/datatables/','https').'/jquery.dataTables.js',
                    'requerido'=>['jquery'],
            ],
            'datatable_bootstrap'=>[
                'link'=>base_url('public/assets/plugins/datatables-bs4/js','https').'/dataTables.bootstrap4.min.js',
                'requerido'=>['datatable'],
            ],
            'datatable_responsive'=>[
                'link'=>base_url('public/assets/plugins/datatables-responsive/js','https').'/dataTables.responsive.min.js',
                'requerido'=>['datatable'],
            ],
            'datatable_responsive_boot'=>[
                'link'=>base_url('public/assets/plugins/datatables-responsive/js','https').'/responsive.bootstrap4.min.js',
                'requerido'=>['datatable_responsive'],
            ],
            'datatable_buttons'=>[
                'link'=>base_url('public/assets/plugins/datatables-buttons/js','https').'/dataTables.buttons.min.js',
                'requerido'=>['datatable'],
            ],
            'datatable_buttons_boot'=>[
                'link'=>base_url('public/assets/plugins/datatables-buttons/js','https').'/buttons.bootstrap4.min.js',
                'requerido'=>['datatable_buttons'],
            ]
        ],
        'stylesheet'=>[
            'datatables'=>[
                'link'=>base_url('public/assets/plugins/datatables-bs4/css','https').'/dataTables.bootstrap4.css',
                'requerido'=>[]
            ],
            'datatables_responsive'=>[
                'link'=>base_url('public/assets/plugins/datatables-responsive/css','https').'/responsive.bootstrap4.min.css',
                'requerido'=>['datatables']
            ],
            'datatables_buttons'=>[
                'link'=>base_url('public/assets/plugins/datatables-buttons/css','https').'/buttons.bootstrap4.min.css',
                'requerido'=>['datatables']
            ],
            'adminlte'=>[
                'link'=>base_url('public/assets/css','https').'/adminlte.css',
                'requerido'=>['datatables'],
            ],
            'select2css'=>[
                'link'=>base_url('public/assets/plugins','https').'/select2/css/select2.min.css',
                'requerido'=>[''],
            ],
            'select2bootstrap'=>[
                'link'=>base_url('public/assets/plugins','https').'/select2-bootstrap4-theme/select2-bootstrap4.min.css',
                'requerido'=>['select2css'],
            ],
            
            
        ],
    ]);
}
function entradaChat($id,$descripcion){
    $placeholder=$descripcion['placeholder']??'';
    $filas=$descripcion['filas']??1;
    $URLUsuarios=$descripcion['buscarUsuario']??'';
    $padreListaUsuarios=$descripcion['listaUsr']??'';
    $html=
  <<<HTML
    <style>
          #{$id}_listaUsr .list-group-item.active{
            background-color: lightgrey;
            border:none;
          }
    
    </style>
          <textarea class="form-control" id="{$id}" name="{$id}" placeholder="$placeholder" rows="$filas"></textarea>  
    <script>
    var {$id}_modoArroba=false;
    var {$id}_inicialUsuario='';
    var {$id}_activo=0;
    $(document).ready(function(){
      $("$padreListaUsuarios").html('<ul style="display:none;z-index:99" class="list-group" id="{$id}_listaUsr"></ul>');
      $('body').on("keydown","#$id",function(e){
        var releer=false;
        if ({$id}_modoArroba){
          switch(e.which){
            case 8:
              {$id}_inicialUsuario={$id}_inicialUsuario.slice(0,-1);
              releer=true;
              e.preventDefault();
              break;
            case 38:
              {$id}_activo--;
              if ({$id}_activo<0) {$id}_activo=0;
              $('#{$id}_listaUsr .list-group-item').removeClass('active');
              $('#{$id}_listaUsr li:nth-child('+({$id}_activo+1)+')').addClass('active');
              e.preventDefault();
              break;
            case 40:
              {$id}_activo++;
              if ({$id}_activo>=$('#{$id}_listaUsr .list-group-item').length) {$id}_activo=$('#{$id}_listaUsr .list-group-item').length-1;
              $('#{$id}_listaUsr .list-group-item').removeClass('active');
              $('#{$id}_listaUsr li:nth-child('+({$id}_activo+1)+')').addClass('active');
              e.preventDefault();
              break;
            case 13:
              {$id}_modoArroba=false;
              if ($('#{$id}_listaUsr .list-group-item').length>0){
                var texto=$('#$id').val();
                texto=texto.substr(0,texto.lastIndexOf('@'))+'@'+$('#{$id}_listaUsr li:nth-child('+({$id}_activo+1)+')').data('login');
                $('#$id').val(texto);
              }
              $('#{$id}_listaUsr').empty().hide();
              e.preventDefault();
              break;
            default:
              if ((e.which==32)||(e.which>=48)){
                {$id}_inicialUsuario+=e.key;
                releer=true;
              }
              else{
                if ((e.which!=17)&&(e.which!=18)){
                  e.preventDefault();
                  {$id}_modoArroba=false;
                  $('#{$id}_listaUsr').empty().hide();
                }
              }
          }
          if (releer){
            const URL='$URLUsuarios';
            if ((URL!='')&&(({$id}_inicialUsuario!=''))){
              $.get(URL+'/'+{$id}_inicialUsuario)
              .done((lista)=>{
                $('#{$id}_listaUsr').css('margin-top','-10.3rem').css('margin-left',$('#$id').width()/4+'px').empty().show();
                {$id}_activo=0;
                for(var l in lista){
                  $('#{$id}_listaUsr').append('<li class="list-group-item '+(l==0?'active':'')+'" style="width: 20em" data-login="'+lista[l].login_usr+'"><img class="direct-chat-img" src="'+lista[l].avatar+'"><div style="margin-left:1em"><div>'+lista[l].nombre_usr+'</div><div>'+lista[l].login_usr+'</div></div></li>');
                }
                //$('#{$id}_listaUsr')[0].scrollIntoView();
                $('#{$id}_listaUsr .list-group-item').unbind('click').click(function(){
                  {$id}_modoArroba=false;
                  var texto=$('#$id').val();
                  texto=texto.substr(0,texto.lastIndexOf('@'))+'@'+$(this).data('login');
                  $('#$id').val(texto);
                  $('#{$id}_listaUsr').empty().hide();
  
                });
              });
            }
          }
        }
        if (e.key=='@'){
          {$id}_modoArroba=true;
          {$id}_inicialUsuario='';
        }
      });
    });
  
    </script> 
  HTML;
    return $html;
  }
  
function dialogo($id,$descripcion){
    $html='';
    $titulo=$descripcion['titulo']??'';
    $cuerpo=$descripcion['cuerpo']??'';
    $botones=$descripcion['botones']??[['texto'=>'Cerrar','valor'=>'']];    
    $estilos=$descripcion['estilos']??'';
    $html.=
<<<HTML
<div class="modal fade" id="$id" style="display: none;" aria-hidden="true">
<input type="hidden" class="resultado">
<div class="modal-dialog $estilos">
<div class="modal-content">
<div class="modal-header">
<h4 class="modal-title">$titulo</h4>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
 <span aria-hidden="true">×</span>
</button>
</div>
<div class="modal-body">
$cuerpo
</div>
<div class="modal-footer justify-content-between">
HTML;  
    foreach($botones as $i=>$unBoton){
        $texto=$unBoton['texto']??'Cerrar';
        $valor=$unBoton['valor']??'';
        $color=$unBoton['color']??'default';
        $html.=
<<<HTML
    <button type="button" class="btn btn-$color" data-dismiss="modal" onclick="$(&quot;#$id input.resultado&quot;).val(&quot;$valor&quot;)">$texto</button>
HTML;  
    }
    $html.=
<<<HTML
</div>
</div>
</div>
</div>
<script>
    function dialogo_$id(){
        return new Promise((resolve,reject)=>{
            $("#$id").modal('show')
            .on('hidden.bs.modal', function (e) {
                resolve($("#$id input.resultado").val());
            });
        });
    }
</script>
HTML;  
    return $html;
}
function formulario($id,$descripcion){
    $html='';
    $introduccion=$descripcion['introduccion']??'';
    $titulo=$descripcion['titulo']??'';
    $campos=$descripcion['campos']??[];
    $activarOK=$descripcion['requerido']??'true';
    $disabled=($activarOK=='true'?'':'disabled');
    $textoAceptar=$descripcion['botones']['aceptar']['texto']??'Guardar cambios';
    
    if ($descripcion['botones']['aceptar']['spinner']??false){
        $textoAceptar='<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true" style="display:none"></span>'.$textoAceptar;
    }
    $estilos=$descripcion['estilos']??'';
    $html.=
<<<HTML
    <div class="modal text-dark fade" id="$id">
        <div class="modal-dialog $estilos">
          <form class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="$id--titulo">$titulo</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="max-height: calc(100vh - 212px);overflow:auto">
                <div class="box-body">
                    $introduccion
HTML;
    foreach($campos as $idCampo=>$unCampo){
        $idCampo=$unCampo['id']??$idCampo;
        $tipo=$unCampo['tipo']??'text';
        $label=$unCampo['label']??'';
        $opciones=$unCampo['opciones']??[];
        $atributos=$unCampo['atributos']??'';
        $valor=$unCampo['valor']??'';
        switch($tipo){
            case 'select':
                $html.=
<<<HTML
                    <div class="form-group" style="margin: 10px 10px 0px 10px">
                        <label for="$idCampo" class="control-label">$label</label>
                        <select class="form-control" id="$idCampo" name="$idCampo"  data-inicial="$valor">
HTML;
                foreach($opciones as $unValor=>$unaOpcion){
                    $seleccionado=$valor==$unValor?'selected':'';
                    $html.=
<<<HTML
                        <option value="$unValor" $seleccionado>$unaOpcion</option>
HTML;
                }
                $html.=
<<<HTML
                        </select>
                    </div>
HTML;
                break;
            case 'select2':
                    $html.=
    <<<HTML
                    <div class="form-group" style="margin: 10px 10px 0px 10px">
                        <label for="$idCampo" class="control-label">$label</label>
                        <select  id="$idCampo" class="form-control select2 "  name="$idCampo"   data-inicial="$valor">
                        
HTML;
                    foreach($opciones as $unValor=>$unaOpcion){
                        $seleccionado=$valor==$unValor?'selected':'';
                        $html.=
<<<HTML
                            <option value="$unValor" $seleccionado>$unaOpcion</option>
HTML;
}
$html.=
<<<HTML

                       </select>
                        <script>
                            $(document).ready(function(){\$('#$idCampo').select2({'theme':'bootstrap4','placeholder':'Seleccione una opción',language: "es"});});
                        </script>
                    </div>
    HTML;
                    break;
            case 'check':
                $checked=($valor=='1'?'checked':'');
                $valor01=$valor;
                $estilo=($unCampo['color']??''!='')?('custom-control-input-'+$unCampo['color']):'';
                $html.=
<<<HTML
                    <div class="custom-control custom-switch my-2">
                        <input class="custom-control-input $estilo" type="checkbox" id="_$idCampo" $checked>
                        <input type="hidden" class="check_oculto" id="$idCampo" name="$idCampo" value="$valor01">
                        <label for="_$idCampo" class="custom-control-label">$label</label>
                    </div>
HTML;
                break;
            case 'textarea':
                $filas=$unCampo['filas']??3;
                $html.=
<<<HTML
                    <div class="form-group" style="margin: 10px 10px 0px 10px">
                        <label for="$idCampo" class="control-label">$label</label>
                        <textarea class="form-control" id="$idCampo" name="$idCampo" data-inicial="$valor"  rows="$filas" $atributos>$valor</textarea>
                    </div>
HTML;
                                break;
            case 'textarea_usuarios':
                $filas=$unCampo['filas']??3;
                $html.=
<<<HTML
                    <div class="form-group" style="margin: 10px 10px 0px 10px">
                        <label for="$idCampo" class="control-label">$label</label>
HTML.
        entradaChat("$idCampo",
                      [
                        'placeholder'=>"Escriba mensaje. Use @ para dirigirlo a alguien.",
                        'filas'=>$filas,
                        'buscarUsuario'=>$unCampo['buscarUsuario']??'',
                        'listaUsr'=>"#{$idCampo}_posLista",
                      ]).
<<<HTML
                    </div>
                    <div class="row" id="{$idCampo}_posLista"></div>
HTML;
                                break;
            case 'multicheck':
                $checks=$unCampo['checks']??[];
                $html.=
<<<HTML
                    <div class="form-group" style="margin: 10px 10px 0px 10px">
                        <label for="$idCampo" class="control-label">$label</label>
                        <input type="hidden" class="multicheck_oculto" id="$idCampo" name="$idCampo" value="$valor" data-inicial="$valor">
HTML;
                foreach($checks as $letra=>$unCheck){
                    $html.='<div class="form-check">
                                <input type="checkbox" class="form-check-input multicheckbox" id="'.$idCampo.'_'.$letra.'" '.(strpos($valor,$letra)===false?'':'checked').' >
                                <label class="form-check-label pt-1" for="'.$idCampo.'_'.$letra.'">
                                    '.$unCheck.'
                                </label>
                            </div>';
                }
                $html.=
<<<HTML
                    </div>
HTML;
                break;
            default:
                if ($tipo!='hidden'){
                    $html.=
<<<HTML
                    <div class="form-group" style="margin: 10px 10px 0px 10px">
                        <label for="$idCampo" class="control-label">$label</label>
HTML;
                }
                $html.=
<<<HTML
                        <input type="$tipo" class="form-control" id="$idCampo" name="$idCampo" value="$valor" data-inicial="$valor"  $atributos>
HTML;
                if ($tipo!='hidden'){
                    $html.=
<<<HTML
                    </div>
HTML;
            }

        }
    }
    $html.=
<<<HTML
                </div>                              
            </div>
            <div class="modal-footer">
                <button type="button"  id="$id--Cancelar"  class="btn btn-default pull-left" data-dismiss="modal">Cancelar</button>
                <button type="button" id="$id--Aceptar" class="btn btn-primary" $disabled>$textoAceptar</button>
            </div>
            <script>
                if (!___formularios_campo){
                    function ___formularios_campo(formulario, nombre){
                        return $("#"+formulario+" #ip"+nombre).val();
                    }
                }
                $(document).on('click','#$id--Aceptar',
HTML;
                $html.=$descripcion['aceptar']??'function(){$("#'.$id.'").modal("hide");}';
                $html.=
<<<HTML
                );
                
                $(document).on('keyup','#$id .form-control',function(){ 
                    $('#$id--Aceptar').prop('disabled',!($activarOK));
                });
                $(document).on('change','#$id .form-control',function(){ 
                    $('#$id--Aceptar').prop('disabled',!($activarOK));
                });
                $(document).on('click','#$id input[type="checkbox"]',function(){ 
                    $('#'+$(this).attr('id').substr(1)).val($(this).prop('checked')?'1':'0');
                    $('#$id--Aceptar').prop('disabled',!($activarOK));
                });
                $(document).on('click','#$id .multicheckbox',function(){ 
                    var campoOculto=$(this).attr('id').substr(0,$(this).attr('id').indexOf('_'));
                    var letra=$(this).attr('id').substr($(this).attr('id').indexOf('_')+1);
                    var valor=$("#"+campoOculto).val();
                    if ($(this).prop('checked')){
                        valor=valor.replace(letra.toLowerCase(),'')+letra;
                    }
                    else{
                        valor=valor.replace(letra,'')+letra.toLowerCase();
                    }
                    $('#'+campoOculto).val(valor);
                    $('#$id--Aceptar').prop('disabled',!($activarOK));
                });
                $(document).on('show.bs.modal','#$id',function(){ 
                    $("#$id .multicheck_oculto").each(function(){
                        var valor=$(this).val();
                        $("#$id input[type='checkbox'][id^="+$(this).attr('id')+"_]").each(function(){
                            var letra=$(this).attr('id').substr($(this).attr('id').indexOf('_')+1);
                            $(this).prop('checked',valor.indexOf(letra)>=0); 
                        });
                    });
                    $("#$id .check_oculto").each(function(){
                        $("#$id input[type='checkbox'][id^=_"+$(this).attr('id')+"]").prop('checked',$(this).val()=='1'); 
                    });

                });
  
           //# sourceURL=vp_{$id}_form.js 
           </script>
          </form>
        </div>
      </div>
HTML;
    return $html;
}
?>