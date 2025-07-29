<?php
function enlaces_tabla(&$enlaces){
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
            ],
            'moment'=>[
                'link'=>base_url('public/assets/plugins/moment','https').'/moment-with-locales.min.js',
                'requerido'=>['datatable'],
            ],
            'datepicker'=>[
                'link'=>base_url('public/assets/plugins/bootstrap-datepicker/js','https').'/bootstrap-datepicker.min.js',
                'requerido'=>['moment'],
            ],
            'datepicker_es'=>[
                'link'=>base_url('public/assets/plugins/bootstrap-datepicker/locales','https').'/bootstrap-datepicker.es.min.js',
                'requerido'=>['moment','datepicker'],
            ],
            'daterangepicker'=>[
                'link'=>base_url('public/assets/plugins/bootstrap-daterangepicker','https').'/daterangepicker.js',
                'requerido'=>['moment'],
            ],
            'datatable_filtrada'=>[
                'link'=>base_url('public/assets/plugins/datatable-filtro','https').'/datatablefiltro.js',
                'requerido'=>['datatable','moment','datepicker_es'],
            ],
            'datatable_buttons_html5'=>[
                'link'=>base_url('public/assets/plugins/datatables-buttons/js','https').'/buttons.html5.min.js',
                'requerido'=>['datatable_buttons']
            ],
            'toastr'=>[
                'link'=>base_url('public/assets/plugins/toastr','https').'/toastr.min.js',
                'requerido'=>['jquery']
            ],
  
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
            'datepicker'=>[
                'link'=>base_url('public/assets/plugins/bootstrap-datepicker/css','https').'/bootstrap-datepicker.min.css',
                'requerido'=>['datatables']
            ],
            'daterangepicker'=>[
                'link'=>base_url('public/assets/plugins/bootstrap-daterangepicker','https').'/daterangepicker.css',
                'requerido'=>['datatables']
            ],
            'datatables_filtrada'=>[
                'link'=>base_url('public/assets/plugins/datatable-filtro','https').'/datatablefiltro.css',
                'requerido'=>['datatables','datepicker']
            ],
            'toastr'=>[
                'link'=>base_url('public/assets/plugins/toastr','https').'/toastr.min.css',
                'requerido'=>[],
            ],
            'adminlte'=>[
                'link'=>base_url('public/assets/css','https').'/adminlte.css',
                'requerido'=>['datatables'],
            ],
            
            
        ],
    ]);
}

function tabla($id,$descripcion){
    $html='';
    $indice=-1;
    $tituloIndice='0';
    $dom=$descripcion['componentes']??'lBfrtip';
    $buttons=json_encode($descripcion['botones']??[]);
    $columnas=$descripcion['columnas']??[];
    foreach($columnas as $i=>$unaColumna){
        if ($unaColumna['esIndice']??false) {
            $indice=$i+1;
            $tituloIndice=$unaColumna['titulo']??$i;
            break;
        }
    }
    $arDatos=[];
    if (isset($descripcion['datos'])){
        foreach($descripcion['datos'] as $ident=>$unDato){
            $fila=['__chk'=>'<input class="tbCheck" data-id="'.$ident.'" type="checkbox">'];
            $i=0;
            foreach($unDato as $clave=>$valor){
                $laClave=$clave;
                if (isset($columnas[$i])) $laClave=$columnas[$i]['titulo']??$clave;
                $fila[$laClave]=$valor;
                $i++;
            }
            array_push($arDatos,$fila);
        }
    }
    $estilos=explode(' ',$descripcion['estilo']??'');
    $menuLongitud=json_encode($descripcion['menuLongitud']??[]);
    $tabledark=in_array('dark',$estilos)?'table-dark':'bg-light';
    $navdark=in_array('dark',$estilos)?'navbar-dark':'navbar-light';
    $text=in_array('dark',$estilos)?'text-light':'text-dark';
    $hover=in_array('hover',$estilos)?'table-hover':'';
    $striped=in_array('striped',$estilos)?'table-striped':'';
    $cellborder=in_array('cellborder',$estilos)?'table-bordered':'';
    $trClick=$descripcion['trClick']??'';
    $col=0;
    $colTamano='';
    $claseCol='';
    $colorFilaOdd='';
    $colorFilaEven='';
    $colorFilaHover='';
    foreach ($estilos as $unEstilo) {
        if (preg_match('/col-([a-z]{2}-)?([0-9]+)/',$unEstilo,$matches)>0){
            $col=$matches[2];
            $colTamano=$matches[1];
            $claseCol=$matches[0];
        }
        if (preg_match('/row-([^-]+)(-.+)?/',$unEstilo,$matches)>0){
            $colorFilaOdd=$matches[1];
            if (count($matches)==3) $colorFilaEven=substr($matches[2],1);
        }
        if (preg_match('/rowhover-(.+)/',$unEstilo,$matches)>0){
            $colorFilaHover=$matches[1];
        }
    }
    unset($estilos[array_search('dark',$estilos)]);
    unset($estilos[array_search('hover',$estilos)]);
    unset($estilos[array_search('striped',$estilos)]);
    unset($estilos[array_search($claseCol,$estilos)]);
    $txEstilosTabla=implode(' ',$estilos);
    $datos=json_encode($arDatos);
    $camposEntrada=['A'=>[],'E'=>[]];
    $camposEntrada['A']['ipa___id']=['tipo'=>'hidden'];
    $camposEntrada['E']['ipe___id']=['tipo'=>'hidden'];
    $orden=array_fill(0,count($columnas),['','asc']);
    foreach($columnas as $i=>$unaColumna){
        $camposEntrada['A']['ipa'.str_replace(' ','_',$unaColumna['titulo']??$i)]=
                    $unaColumna['entrada']??[
                                'tipo'=>'text',
                                'label'=>$unaColumna['titulo']??$i,
                                'valor'=>$unaColumna['valor']??''
                            ];
        $camposEntrada['E']['ipe'.str_replace(' ','_',$unaColumna['titulo']??$i)]=
                    $unaColumna['entrada']??[
                                'tipo'=>'text',
                                'label'=>$unaColumna['titulo']??$i,
                                'valor'=>$unaColumna['valor']??''
                            ];
                if (isset($unaColumna['orden'])){
            $orden[$unaColumna['orden'][0]??0]=[$i+1,$unaColumna['orden'][1]??'asc'];
        }
    }
    $txtOrden=[];
    //log_message('debug','ORDEN:'.print_r($orden,true));
    foreach($orden as $unOrden){
        if ($unOrden[0]!='') $txtOrden[]="[".$unOrden[0].",'".$unOrden[1]."']";
    }
    $txtOrden=implode(",",$txtOrden);
    if (isset($descripcion['menu'])){
        $titulo=$descripcion['menu']['titulo']??'';
        $html.=
<<<HTML
    <nav class="navbar navbar-expand-sm $navdark border-top border-secondary border-bottom py-0">
        <ul class="navbar-nav">
HTML;
        foreach($descripcion['menu']['items']??[] as $ident=>$unItem){
            $tituloItem=$unItem['texto']??'';
            $html.=
<<<HTML
            <li class="nav-item">
                <div class="a nav-link" id="$id--$ident">$tituloItem</div>
            </li>
HTML;
            if (isset($unItem['accion'])){
                $form=$unItem['accion']['formulario']??[];
                switch($unItem['accion']['tipo']??''){
                    case 'anadir':
                        $funcion=
<<<JS
                    function(){
                        let valores={'__chk':'<input class="tbCheck" data-id="'+tabla_$id.rows().count()+'" type="checkbox">'};
JS;
                        foreach($columnas as $i=>$unaColumna){
                            $nombreCampo=$unaColumna['titulo']??$i;
                            $nombreColumna=str_replace(' ','_',$unaColumna['titulo']??$i);
                            $funcion.=
<<<JS
                        valores['$nombreCampo']=($('#frmAnadir--$id #ipa$nombreColumna').val());
JS;
                        }
                        if (isset($unItem['accion']['url'])){
                            $columnaIndice=str_replace(' ','_',$tituloIndice);

                            $funcion.=
<<<JS
                        $.post('{$unItem['accion']['url']}',valores)
                        .done((json_respuesta)=>{
                            const respuesta=JSON.parse(json_respuesta);
                            if (respuesta.exito){
                                if (respuesta.id)
                                    valores['$columnaIndice']=respuesta.id;
                                tabla_$id.row.add(valores).draw(false);
JS;
                            if (isset($unItem['accion']['al'])){
                                if (isset($unItem['accion']['al']['actualizar'])){
                                    $hook=$unItem['accion']['al']['actualizar'];
                                    $funcion.=
<<<JS
                                    ($hook)(valores,tabla_$id);
JS;
                                }   
                            }
                        $funcion.=
<<<JS
                                $('#frmAnadir--$id').modal('hide');                        
                            }
                            else{
                                toastr.error(respuesta.mensaje);;
                            }
                        })
                        .fail(()=>{
                            alert('Error insertando elemento');
                        });
                    }
JS;
                        }
                        else{
                            $funcion.=
<<<JS
                        tabla_$id.row.add(valores).draw(false);
JS;
                            if (isset($unItem['accion']['al'])){
                                if (isset($unItem['accion']['al']['actualizar'])){
                                    $hook=$unItem['accion']['al']['actualizar'];
                                    $funcion.=
<<<JS
                                    ($hook)(valores,tabla_$id);
JS;
                                }   
                            }
                        $funcion.=
<<<JS
                        $('#frmAnadir--$id').modal('hide');
                    }   
JS; 
                        }
                        $html.=formulario('frmAnadir--'.$id,[
                            'titulo'=>$form['titulo']??'Nuevo',
                            'campos'=>$camposEntrada['A'],
                            'requerido'=>$form['requerido']??'true',
                            'aceptar'=>$funcion
                            ]);
                        $html.=
<<<HTML
                            <script> 
                                $('body').on('click','#$id--$ident',function(){
                                    $('#frmAnadir--$id input').each((i,e)=>{
                                        $(e).val($(e).data('inicial'));
                                    });
                                    $('#frmAnadir--$id select').each((i,e)=>{
                                        $(e).val($(e).data('inicial'));
                                    });
                                    $("#frmAnadir--$id #ipa___id").val(tabla_$id.rows.count);
                                    $('#frmAnadir--$id').modal('show');
                                    });
                             //# sourceURL=vp_tabla_{$id}_menu_$ident.js 
                            </script>
HTML;
                        break;
                    case 'eliminar':
                        $envio='';
                        if (isset($unItem['accion']['url'])){
                            $envio.=
<<<JS
                        var postear={'lista': JSON.stringify(lista)};
                        $.post('{$unItem['accion']['url']}',postear)
                        .done((json_respuesta)=>{
                            const respuesta=JSON.parse(json_respuesta);
                            if (respuesta.exito){                           
                                tabla_$id.rows(JSON.parse(postear.lista).map((e)=>'#'+e)).remove().draw(false);
JS;
                            if (isset($unItem['accion']['al'])){
                                if (isset($unItem['accion']['al']['actualizar'])){
                                    $hook=$unItem['accion']['al']['actualizar'];
                                    $envio.=
<<<JS
                                    ($hook)(valores,tabla_$id);
JS;
                                }   
                            }
                            $envio.=
<<<JS
                             }
                            else{
                                alert('Error borrando elementos: '+respuesta.mensaje);
                            }
                        })
                        .fail(()=>{
                            alert('Error borrando elementos');
                        });
JS;
                        }
                        else{
                            $envio.=
<<<JS
                        var postear={'lista': JSON.stringify(lista)};
                        tabla_$id.rows(JSON.parse(postear.lista).map((e)=>'#'+e)).remove().draw(false);
JS;
                            if (isset($unItem['accion']['al'])){
                                if (isset($unItem['accion']['al']['actualizar'])){
                                    $hook=$unItem['accion']['al']['actualizar'];
                                    $envio.=
<<<JS
                        ($hook)(valores,tabla_$id);
JS;
                                }   
                            }
                        }
                        $realizacion=
<<<JS
                                    var lista=[];
                                    $("#$id .tbCheck:checked").each((i,e)=>{
                                        lista.push(tabla_$id.row($(e).closest('tr')).data()['$tituloIndice']);
                                    });
                                    if (lista.length>0){
                                    $envio;
                                    }
JS;
                        if (isset($unItem['accion']['confirmacion'])){
                            $html.=dialogo($id.'__'.$ident.'__conf',
                            [
                                'titulo'=>$unItem['accion']['confirmacion']['titulo']??'Confirmación de eliminación',
                                'cuerpo'=>$unItem['accion']['confirmacion']['cuerpo']??'¿Está seguro?',
                                'botones'=>[['texto'=>'No', 'valor'=>0],['texto'=>'Sí', 'valor'=>1, 'color'=>'danger']]
                            ]);
                            $html.=
<<<HTML
                            <script> 
                                $('body').on('click','#$id--$ident',function(){
                                    $("#{$id}__{$ident}__conf").modal('show')
                                    .on('hidden.bs.modal', function (e) {
                                        if ($("#{$id}__{$ident}__conf input.resultado").val()=='1'){
                                            $realizacion;
                                        }
                                    })
                                });
                             //# sourceURL=vp_tabla_{$id}_menu_$ident.js 
                            </script>
HTML;

                        }
                        else{
                            $html.=
<<<HTML
                            <script> 
                                $('body').on('click','#$id--$ident',function(){
                                    $realizacion
                                });
                             //# sourceURL=vp_tabla_{$id}_menu_$ident.js 
                            </script>
HTML;
                        }
                        break;
                    case 'enlace':
                        $html.=
<<<HTML
                            <script> 
                                $('body').on('click','#$id--$ident',function(){
                                    window.location.href = '{$unItem['accion']['url']}';
                                });
                             //# sourceURL=vp_tabla_{$id}_menu_$ident.js 
                            </script>
HTML;
                        break;
                    case 'funcion':
                        $html.=
<<<HTML
                            <script> 
                                $('body').on('click','#$id--$ident',function(){
                                    {$unItem['accion']['script']}
                                });
                             //# sourceURL=vp_tabla_{$id}_menu_$ident.js 
                            </script>
HTML;
                        break;
                }
            }
        }
        $html.=
<<<HTML
        </ul>
    </nav>
    <h3 class="mt-3 ml-1 mb-1" >$titulo</h3>
HTML;
    }
    if ($col>0){
        $html.=
<<<HTML
    <div class="row">
    <div class="$claseCol">
HTML;
    }
    $checkOculto=($descripcion['sinCheckboxes']??false)?'style="display:none"':'';
    $html.=
<<<HTML
    <style>
        table a{
            color: inherit;
        }
        table a:hover{
            color: inherit;
            text-decoration: underline;
        }
HTML;
    if ($colorFilaOdd!=''){
        $html.=
<<<HTML

        #$id tbody tr.odd{
            background-color: $colorFilaOdd;
        }
HTML;
    }
    if ($colorFilaEven!=''){
        $html.=
<<<HTML

        #$id tbody tr.even{
            background-color: $colorFilaEven;
        }
HTML;
    }
    if ($colorFilaHover!=''){
        $html.=
<<<HTML

        #$id tbody tr:hover{
            background-color: $colorFilaHover;
        }
HTML;
    }
    if ($trClick!=''){
        $html.=
<<<HTML

        #$id tbody tr:hover{
            cursor:pointer;
        }
HTML;
    }
    $html.=
<<<HTML
    </style>
    <table id="$id" class=" $text table editable  $striped $hover $tabledark $txEstilosTabla">
        <thead>
            <tr>
                <th $checkOculto ><input type="checkbox" class="chkTodos"></th>
HTML;
    foreach($columnas as $unaColumna){
        $columnaOculta=($unaColumna['oculta']??false)?'style="display:none"':'';
        $columnaResponsive=($unaColumna['responsive']??''!='')?'class="'.$unaColumna['responsive'].'"':'';
        $html.=
<<<HTML
                <th $columnaOculta $columnaResponsive>{$unaColumna['titulo']}</th>
HTML;
    }
    $html.=
<<<HTML
    </thead>
        <tbody>
        </tbody>
    </table>
HTML;
    if ($col>0){
        $resto='col-';
        if ($colTamano!='') $resto.=$colTamano.'-';
        $resto.=(12-$col);
        $html.=
<<<HTML
    </div>
    <div class="$resto"></div>
    </div>
HTML;
    }

    $html.=
<<<HTML
    <script>
    let tabla_$id;
    $(document).ready(function() {
HTML;
        $html.=
<<<HTML
    tabla_$id = $('#$id').DataTableFiltrada({
      
HTML;
        if ($descripcion['datos']??false){
            $html.=
<<<HTML
        'data':$datos,
HTML;
        }
        if ($descripcion['ajax']??false){
            $funcion=$descripcion['ajax']['data function(d)']??'';
            $html.=
<<<HTML
        'ajax':{
            'url': '{$descripcion['ajax']['url']}',
            'type' : 'post',
            'data': function(d){{$funcion}},
            'dataSrc': function ( json ) {
                for(var i in json){
                    json[i].__chk='<input class="tbCheck" data-id="'+i+'" type="checkbox">';
                }
                    return json;
                    }
        },
HTML;
        }
        if (isset($descripcion['al'])){
            if (isset($descripcion['al']['crear_fila'])){
                $funcion=$descripcion['al']['crear_fila'];
                $html.=
<<<HTML
        'createdRow': $funcion,
HTML;
            }
        }
        
        $html.=
<<<HTML
        'columns':[
        {data: '__chk'},
HTML;
        $i=0;
        foreach($columnas as $unaColumna){
            $titulo=$unaColumna['titulo']??$i;
            $render='';
            $className='';
            $claseHd=$unaColumna['claseHd']??'';
            if (isset($unaColumna['accion'])){
                switch($unaColumna['accion']['tipo']??''){
                    case 'editar':
                        $render=
<<<JS
            ,'render': function(data, type, row, meta){
                return '<div class="a editar_tb_$id" data-id="'+meta.row+'" >'+data+'</div>';
            }
JS;
                         break;
                }
            }
            if (isset($unaColumna['render'])){
                $render=
<<<JS
            ,'render': function(data, type, row, meta){
                {$unaColumna['render']};
            }
JS;
            }
            if (isset($unaColumna['clase'])){
                $className=',className: "'.$unaColumna['clase'].'"';
            }
            $html.=
<<<HTML
        {data: '$titulo', title: '<div class="$claseHd">$titulo</div>' $render $className},
HTML;
           
           
            $i++;
        }
        $html.=
<<<HTML
      ],
HTML;
        if ($indice>-1){
            $html.=
<<<HTML
    'rowId': '$tituloIndice',
HTML;
        }
        
        $html.=
<<<HTML
      'dom': '$dom',
      'buttons': $buttons,
      'paging'      : true,
      'searching'   : true,
      'ordering'    : true,
      'select'      : true,
      'order': [$txtOrden],
      'info'        : true,
      'lengthMenu'  : $menuLongitud,
      'autoWidth'   : false,
       "responsive": true,
       'columnDefs': [
HTML;
        if ($descripcion['sinCheckboxes']??false){
            $html.=
<<<HTML
            {
                targets: 0,
                visible: false,
            },
HTML;
        }
        else{
            $html.=
<<<HTML
            {
                targets: 0,
                type: 'check',
            },
HTML;
        }
        
        foreach($columnas as $indiceColumna=>$unaColumna){
            $target=$indiceColumna+1;
            $html.=
<<<HTML
            {
                targets: $target,
HTML;
            if ($unaColumna['oculta']??false){
                $html.=
<<<HTML
                visible: false,
HTML;
            }
            if ($unaColumna['type']??false){
                $html.='type: "'.$unaColumna['type'].'",';
            }
            $html.=
<<<HTML
            },
HTML;
        }
        $html.=
<<<HTML
      ],
      'language': {
        'url':
HTML;
        $html.="'".base_url('public/assets/js/vp').'/Spanish.json'."'";
        $html.=
<<<HTML
      },
      "initComplete": function(settings) {
            //$(".dataTables_info").hide();
            //$(".dataTables_empty").hide();

            $("#$id .chkTodos").click(function(){
                $("#$id .tbCheck").prop("checked",$(this).prop("checked"));
            });
        
HTML;
        $leerValores='';
        foreach($columnas as $i=>$unaColumna){
                $nombreCampo=$unaColumna['titulo']??$i;
                $nombreColumna=str_replace(' ','_',$unaColumna['titulo']??$i);
                $leerValores.=
<<<JS
            $('#frmEditar--$id #ipe$nombreColumna').val((typeof fila.data()[0]['$nombreCampo']=='object')?JSON.stringify(fila.data()[0]['$nombreCampo']):fila.data()[0]['$nombreCampo']);
JS;
        }

        if ($trClick!=''){
            if (is_array($trClick)){
                switch($trClick['accion']??''){
                    case 'ejecutar':
                        if ($trClick['funcion']??''!=''){
                            $funcion=$trClick['funcion'];
                            $html.=
                        <<<HTML
            $("#$id tbody").on("click","tr",function(e){
                if (!$(e.target).hasClass('dtr-control')&&!$(e.target).parent().hasClass('dtr-control')&&!$(e.target).hasClass('dataTables_empty')){
                    if ($(this).attr('id')){
                        var idClicado=tabla_$id.rows(this).indexes()[0];
                        var fila=tabla_$id.rows(this).data()[0];   
                        ($funcion)(idClicado,fila);
                    }
                }
            });
HTML;
                        }
                        break;
                }
            }
            else{
                preg_match_all('/\[(.+?)\]/',$trClick,$matches);
                $href=$trClick;
                if (count($matches)==2){
                    foreach($matches[1] as $unMatch){
                        $href=str_replace('['.$unMatch.']','"+tabla_'.$id.'.row(this).data().'.$unMatch.'+"',$href);
                    }
                }
                $html.=
<<<HTML
            $("#$id tbody").on("click","tr",function(e){
                if (!$(e.target).hasClass('dtr-control')&&!$(e.target).parent().hasClass('dtr-control')&&!$(e.target).hasClass('dataTables_empty')){
                    if ("$href"=="editar"){
                        if ($(this).find('.a.editar_tb_$id').length>0){
                            var idClicado=$(this).find('.a.editar_tb_$id').data('id');
                            var fila=tabla_$id.rows(idClicado);   
                            $leerValores
                            $("#frmEditar--$id #ipe___id").val(idClicado);
                            $('#frmEditar--$id').modal('show');
                        }
                    }
                    else{
                        location.href="$href";
                    }
                }
            });
HTML;
            }
        }
        if (isset($descripcion['al'])){
            if (isset($descripcion['al']['crear_tabla'])){
                $hook=$descripcion['al']['crear_tabla'];
                $html.=
<<<JS
                        ($hook)(tabla_$id);
JS;
            }
        }
        $htmlPendiente='';
        foreach($columnas as $unaColumna){
            if (isset($unaColumna['accion'])){
                if (($unaColumna['accion']['tipo']??'')=='editar'){
                    $form=$unaColumna['accion']['formulario']??[];
                    if (isset($unaColumna['accion']['url'])){
                        $funcion=
<<<JS
                    function(){
                        let valores={'__chk':'<input class="tbCheck" data-id="'+tabla_$id.rows().count()+'" type="checkbox">'};
JS;
                        foreach($columnas as $i=>$otraColumna){
                            $nombreCampo=$otraColumna['titulo']??$i;
                            $nombreColumna=str_replace(' ','_',$otraColumna['titulo']??$i);
                            $funcion.=
<<<JS
                        var valor=$('#frmEditar--$id #ipe$nombreColumna').val();
                        try {
                            valor=JSON.parse(valor);
                        } catch (e) {
                        }
                        valores['$nombreCampo']=(valor);
JS;
                        }
                        $funcion.=
<<<JS
                        $.post('{$unaColumna['accion']['url']}',valores)
                        .done((json_respuesta)=>{
                            const respuesta=JSON.parse(json_respuesta);
                            if (respuesta.exito){
                                let actual=tabla_$id.rows($("#frmEditar--$id #ipe___id").val()).data()[0];
                                for(var k in actual){
                                    actual[k]=valores[k];
                                }
                                tabla_$id.rows($("#frmEditar--$id #ipe___id").val()).data(actual);
                                tabla_$id.rows($("#frmEditar--$id #ipe___id").val()).invalidate().draw();
JS;
                        if (isset($unaColumna['accion']['al'])){
                            if (isset($unaColumna['accion']['al']['actualizar'])){
                                $hook=$unaColumna['accion']['al']['actualizar'];
                                $funcion.=
<<<JS
                        ($hook)(valores,tabla_$id);
JS;
                            }   
                        }
                        $funcion.=
<<<JS
                                $('#frmEditar--$id').modal('hide');                        
                            }
                            else{
                                toastr.error(respuesta.mensaje);
                            }
                        })
                        .fail(()=>{
                            alert('Error editando elemento');
                        });
                    }
JS;
                    }
                    else{
                        $funcion.=
<<<JS
                        tabla_$id.rows($("#frmEditar--$id #ipe___id").val()).data(valores).draw();
JS;
                        if (isset($unaColumna['accion']['al'])){
                            if (isset($unaColumna['accion']['al']['actualizar'])){
                                $hook=$unaColumna['accion']['al']['actualizar'];
                                $funcion.=
<<<JS
                        ($hook)(valores,tabla_$id);
JS;
                            }   
                        }
                        $funcion.=
<<<JS

                        $('#frmEditar--$id').modal('hide');
                    }
JS; 
                    }
                    
                    $htmlPendiente.=formulario('frmEditar--'.$id,[
                        'titulo'=>$form['titulo']??'Modificar',
                        'campos'=>$camposEntrada['E'],
                        'requerido'=>$form['requerido']??'true',
                        'aceptar'=>$funcion
                        ]);

                    $html.=
<<<HTML
            $('body').on('click',".a.editar_tb_$id",function(e){
                var fila=tabla_$id.rows($(this).data('id'));   
                $leerValores
                $("#frmEditar--$id #ipe___id").val($(this).data('id'));
                $('#frmEditar--$id').modal('show');
            });
HTML;
                }
            }
        }
    $html.=
<<<HTML
        }
    });
    });
     //# sourceURL=vpa_tabla_$id.js 
    </script>
    $htmlPendiente
HTML; 
    return $html;
}

?>
