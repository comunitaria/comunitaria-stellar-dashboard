<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $VPConf->tituloWeb ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('public/assets/imagenes','https') ?>/confFavicon.png">
    <?= inserta_enlaces($enlaces) ?>
    <script src="<?= base_url('public/assets/plugins','https') ?>/cropper/cropper.min.js"></script>
    <script src="<?= base_url('public/assets/plugins','https') ?>/cropper/jquery-cropper.min.js"></script>
    <link rel="stylesheet" href="<?= base_url('public/assets/plugins','https') ?>/cropper/cropper.min.css">
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
    <?= barraNavegacion(json_decode($VPConf->menuSuperior,true),[['tipo'=>'usuario']],'','menu',true) ?>
<!-- Vpconf< barra -->
<!-- Vpconf> aside (Zona sujeta a cambios automáticos)-->
    <aside class="main-sidebar sidebar-<?= $VPConf->lateralDark ?>-<?= $VPConf->lateralDark ?> elevation-4">
                <?= encabezado(base_url(''),base_url('public/assets/imagenes').'/logotipo'.($VPConf->UAT?"UAT":"").'.png',$VPConf->nombreCliente) ?>
                <?= sidebar(json_decode($VPConf->menuLateral,true)) ?>

    </aside>
<!-- Vpconf< aside -->

    <div class="content-wrapper">
        <div class="content-header">
        <div class="container-fluid">
                <div class="row mt-3 mb-md-5 ml-3">
                    <div class="col">
                       <h4>Imagen personal</h4>
                    </div>
                </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <div >
                    <input id="ipFichero" type="file" style="display:none" accept="image/jpeg, image/png, image/jpg">
                    <div class="mx-auto d-block" style="height:20rem;width:20rem">
                    <img id="imAvatar" id="image" style="height:20rem;width:20rem" src="<?php 
                        echo $usuario['Avatar'];
                    ?>">
                    </div>
                    <div class="row text-center" style="margin-top:-2rem">
                        <div class="mx-auto d-flex" style="width:23rem;">
                            <button id="btUpload" type="button" class="btn btn-info btn-rounded btn-icon" style="z-index:99" >
                                    <i class="fa fa-upload py-2"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row text-center mt-4">
                        <div class="mx-auto d-flex" style="width:20rem;">
                            <button type="button" onclick="history.back()" class="btn btn-secondary ml-auto">Volver</button>
                            <button type="button" id="btAceptar" class="btn btn-success ml-4">Aceptar</button>
                        </div>
                    </div>
                    
                </div>

            </div>
        </section>
    </div>
<!-- Vpconf> pie (Zona sujeta a cambios automáticos)-->
    <?= pie($VPConf->contenidoPie,'',true) ?>
<!-- Vpconf< pie -->
 </div>
 <script>
    var cropper;
    function creaCropper(){
        $("#imAvatar").cropper({
            viewMode: 2,
            initialAspectRatio: 1,
            aspectRatio: 1,
            maxCanvasWidth: 200,
            crop: function(event) {
                console.log(event.detail.x);
                console.log(event.detail.y);
                console.log(event.detail.width);
                console.log(event.detail.height);
                console.log(event.detail.rotate);
                console.log(event.detail.scaleX);
                console.log(event.detail.scaleY);
            }
        });
        cropper= $("#imAvatar").data('cropper');
    }
    $(document).ready(function(){
        $("#btUpload").click(function(){
            $('#ipFichero').trigger('click');
        });
        $('#ipFichero').change(function(evt){
            var tgt = evt.target || window.event.srcElement,
            files = tgt.files;
            
            // FileReader support
            if (FileReader && files && files.length) {
                var fr = new FileReader();
                fr.onload = function () {
                    $("#imAvatar").cropper('destroy');
                    $("#imAvatar").attr('src', fr.result);
                    creaCropper();
                }
                fr.readAsDataURL(files[0]);
            }
        });
       // creaCropper();
       $("#btAceptar").click(function(){
            var irAPagina=document.referrer;
            if (document.referrer.indexOf('?')<0) irAPagina+='?_='+(new Date()).getTime();
            else  irAPagina+='&_='+(new Date()).getTime();
            if (cropper){
                $("#imAvatar").cropper('getCroppedCanvas',{width:200, height:200}).toBlob((blob) => {
                        const formData = new FormData();

                        // Pass the image file name as the third parameter if necessary.
                        formData.append('imagenCortada', blob/*, 'example.png' */);

                        // Use `jQuery.ajax` method for example
                        $.ajax('<?= base_url('usuarios/avatar') ?>', {
                            method: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success() {
                                window.location.replace(irAPagina);
                            },
                            error() {
                                console.log('Upload error');
                            },
                        });
                });
            }
            else{
                window.location.replace(irAPagina);

            }
        });
    });
 </script>
</body>
</html>