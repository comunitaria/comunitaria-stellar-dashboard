<?php
function enlaces_chat(&$enlaces){
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
        ],
        'stylesheet'=>[
            'adminlte'=>[
                'link'=>base_url('public/assets/css','https').'/adminlte.css',
                'requerido'=>['datatables'],
            ],
            
            
        ],
    ]);
}
function chat($id,$descripcion,$mensajes){
    $html='';
    $titulo=$descripcion['titulo']??'';
    $cuerpo=$descripcion['cuerpo']??'';
    $botones=$descripcion['botones']??[['texto'=>'Cerrar','valor'=>'']];    
    $color=$descripcion['color']??'primary';
    $outline=($descripcion['outline']??false)?'card-outline':'';
    $numMensajes=count($mensajes);
    $colapsado=$descripcion['minimizado']?'collapsed-card':'';
    $masMenos=$descripcion['minimizado']?'plus':'minus';
    $estilo=$descripcion['estilo']??'';
    $JSONMensajes=json_encode($mensajes);
    $ajax=$descripcion['ajax']??false;
    $ajax_url=($descripcion['ajax']??[])['url']??'';
    $ajax_ms=($descripcion['ajax']??[])['ms']??10000;
    $mensajesEstaticos=!$ajax?'true':'false';
    $html.=
<<<HTML
  <div class="row">
  <div id="$id" class="card card-$color direct-chat direct-chat-$color $outline $colapsado $estilo">
  <div class="card-header">
    <h3 class="card-title">$titulo</h3>
    <div class="card-tools">
      <span data-toggle="tooltip" title="$numMensajes Mensajess" class="badge badge-light">$numMensajes</span>
      <button type="button" class="btn btn-tool" data-card-widget="collapse">
        <i class="fas fa-$masMenos"></i>
      </button>
      <!--<button type="button" class="btn btn-tool" data-toggle="tooltip" title="Contacts" data-widget="chat-pane-toggle">
        <i class="fas fa-comments"></i>
      </button> -->
      <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i>
      </button>
    </div>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <!-- Conversations are loaded here -->
    <div class="direct-chat-messages">
      <!-- Message. Default to the left -->
    <script>
    if (!jsonEscape){
      function jsonEscape(str)  {
        return str.replace(/\\n/g, "\\\\n").replace(/\\r/g, "\\\\r").replace(/\\t/g, "\\\\t");
      }
    }
    if (!chat_anadeComentario){
      function chat_anadeComentario(id,m){
        var id_usuario=chat_mensajes[id][m]['id_usr']??0;
        var usuario=chat_mensajes[id][m]['nombre_usr']??'Anónimo';
        var fecha=chat_mensajes[id][m]['fecha'];
        var imagen=chat_mensajes[id][m]['imagen_usr'];
        var texto=chat_mensajes[id][m]['comentario']??'';
        function ladoContrario(l){
          return l=='left'?'right':'left';
        }
        var lado='';
        for(var m1 in chat_mensajes[id]){
          if (chat_ladoUsr[id][chat_mensajes[id][m1].id_usr]){
            lado=chat_ladoUsr[id][chat_mensajes[id][m1].id_usr];
          }
          else{
            lado=ladoContrario(lado);
          }
          chat_ladoUsr[id][chat_mensajes[id][m1].id_usr]=lado;
        }
        const timeZone=0;//Locale ya corrije new Date().getTimezoneOffset();
        var dfecha=new Date();
        dfecha.setTime((fecha+timeZone)*1000);
        var txtFecha=dfecha.toLocaleDateString()+' '+dfecha.toLocaleTimeString();
        lado=chat_ladoUsr[id][id_usuario];
        $('#'+id+' .direct-chat-messages').append(`
          <div class="direct-chat-msg `+lado+`">
            <div class="direct-chat-infos clearfix">
              <span class="direct-chat-name float-`+lado+`">`+usuario+`</span>
              <span class="direct-chat-timestamp float-`+ladoContrario(lado)+`">`+txtFecha+`</span>
            </div>
          <!-- /.direct-chat-infos -->
            <img class="direct-chat-img" src="`+imagen+`" alt="imagen usuario">
          <!-- /.direct-chat-img -->
            <div class="direct-chat-text">
              `+ texto + `
            </div>
          <!-- /.direct-chat-text -->
          </div>  `);
      }
    }
    var chat_{$id}_refresco=0;
    if (!chat_mensajes) var chat_mensajes={};
    if (!chat_ladoUsr) var chat_ladoUsr={};
    if ({$mensajesEstaticos}){
      chat_mensajes['{$id}']=JSON.parse(jsonEscape('$JSONMensajes'));
      chat_ladoUsr['{$id}']={};
      for(var m in chat_mensajes['{$id}']){
          chat_anadeComentario('{$id}',m);
      }
    }
    else{
      chat_{$id}_refresco={$ajax_ms};
      if(!leeMensajes){
        function leeMensajes(id,url,ms){
          $.get(url)
          .done((json_mensajes)=>{
            chat_ladoUsr[id]={};
            chat_mensajes[id]=JSON.parse(json_mensajes);
            $('#'+id+' .direct-chat-messages').empty();
            for(var m in chat_mensajes[id]){
                chat_anadeComentario(id,m);
            }
          })
          .fail((e)=>console.log(e))
          .always(()=>{
            setTimeout(() => {
              leeMensajes(id,url,ms);
            }, ms);
          });
        }
      }
      leeMensajes('{$id}','{$ajax_url}',{$ajax_ms});
    }
    </script>
      </div>
    </div>
HTML;
      if ($descripcion['conEnvio']??false){
        $html.=
<<<HTML
    <div class="card-footer">
        <div class="input-group">
HTML.
        entradaChat("{$id}_ipMensaje",
                      [
                        'placeholder'=>"Escriba mensaje. Use @ para dirigirlo a alguien.",
                        'filas'=>1,
                        'buscarUsuario'=>$descripcion['buscarUsuario']??'',
                        'listaUsr'=>"#{$id}_padreListaUsr",
                      ]).
<<<HTML
          <!--<input type="text" placeholder="Escriba mensaje. Use @ para dirigirlo a alguien." class="ipMensaje form-control">-->
          <span class="input-group-append">
            <button id="{$id}_Enviar" type="button" class="btn btn-warning"><i class="fas fa-paper-plane"></i> Enviar</button>
          </span>
        </div>
    </div>
HTML;
      }
      $html.=
<<<HTML
    </div>
  </div>
  <div class="row" style="position:absolute" id="{$id}_padreListaUsr"></div>
  <script>
  $(document).ready(function(){
    $('$id').on('expanded.lte.cardwidget', function(){
      $ ('html, body') .animate ({
        scrollTop: $(this).find('.direct-chat-messages').offset().top + $(this).find('.direct-chat-messages')[0].scrollHeight
      }, 2000);
    });
    $('#{$id}_Enviar').click(function(e){
      {$descripcion['enviar']}
    });

  });
  </script> 
<!--/.direct-chat -->
HTML;  
    return $html;
}

?>