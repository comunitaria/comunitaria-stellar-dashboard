<?php
function enlaces($helpers,&$enlaces){
    foreach($helpers as $unHelper){
        $partes=explode('\\',$unHelper);
        $nombre=end($partes);
        if (function_exists('enlaces_'.$nombre)){
            call_user_func_array('enlaces_'.$nombre,[&$enlaces]);
        }
    }
}
function inserta_enlaces($enlaces){
    $html='';
    foreach($enlaces as $tipo=>$unTipoEnlace){
        foreach($unTipoEnlace as $unEnlace){
            switch($tipo){
                case 'script':
                    $html.=
<<<HTML
    <script src="{$unEnlace['link']}"></script>
HTML;
                    break;
                case 'stylesheet':
                    $html.=
<<<HTML
    <link rel="stylesheet" href="{$unEnlace['link']}"> 
HTML;
                    break;
                }
        }
    }
    return $html;
   
}
function anade_enlaces(&$enlaces,$listaEnlaces){
    foreach($listaEnlaces as $tipo=>$lista){
        foreach($lista as $id=>$unEnlace){
            anade_un_enlace($enlaces,$tipo,$id,$unEnlace);
        }
    }
}
function anade_un_enlace(&$enlaces,$tipo,$id,$unEnlace){
    if (!isset($enlaces[$tipo])){
        $enlaces[$tipo]=[$id=>$unEnlace];
        return;
    }
    $delanteDe=count($enlaces[$tipo]);
    $tras=-1;
    $yaEsta=false;
    $i=0;
    foreach($enlaces[$tipo] as $otroId=>$otroEnlace){
        if ($otroId==$id){
            $yaEsta=true;
            break;
        }
        if (in_array($id,$otroEnlace['requerido'])){
            $delanteDe=$i;
        }
        if (in_array($otroId,$unEnlace['requerido'])){
            $tras=$i;
        }
        $i++;
    }
    $pos=$delanteDe;
    if ($tras>$pos){
        $pos=$tras;
    }
    $enlaces[$tipo]=array_merge(array_slice($enlaces[$tipo], 0, $pos), [$id=>$unEnlace], array_slice($enlaces[$tipo], $pos));
 }
function ayuda($nombre){
    if (file_exists(ROOTPATH.'public/assets/ayuda/'.$nombre.'.md'))
        return (new \Parsedown())->text(file_get_contents(ROOTPATH.'public/assets/ayuda/'.$nombre.'.md'));
    else
        return (new \Parsedown())->text(file_get_contents(ROOTPATH.'public/assets/ayuda/sinAyuda.md'));
}
function seccionAyuda($ficheroAyuda){
    return 
<<<HTML
        <div id="ayuda" class="mx-3 my-2" style="display:none">
           <div class="row"><button type="button" id="btSalirAyuda" class="btn btn-info mr-3 mb-2"><i class="fas fa-chevron-left"></i></button></div> 
            $ficheroAyuda
        </div>
        <script>

        $(document).ready(function(){
            $("#aAyuda").click(function(){
                if ($("section").is(":hidden")){
                    $("#ayuda").hide();
                    $("section").show();
            }
                else{
                    $("#ayuda").show();
                    $("section").hide();
            }
            });
            $("#btSalirAyuda").click(function(){
                $("#ayuda").hide();
                $("section").show();
            });
        });
        </script>
HTML;    
}
?>
