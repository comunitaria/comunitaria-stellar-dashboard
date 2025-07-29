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
    #ipImgLogo:hover{
        cursor: pointer;
        box-shadow: 0px 0px 15px #A38000;
    }
</style>
</head>
<body class="hold-transition layout-top-nav layout-fixed text-<?= $VPConf->tamanoTexto ?> <?= $VPConf->tonalidad ?>">
 <div class="wrapper">
<!-- Vpconf> barra (Zona sujeta a cambios automáticos)-->
    <?= barraNavegacion(json_decode($VPConf->menuSuperior,true),[['tipo'=>'usuario']],'',encabezado(base_url(""),base_url("public/assets/imagenes")."/logotipo".($VPConf->UAT?"UAT":"").".png",$VPConf->nombreCliente),true) ?>
<!-- Vpconf< barra -->    
    <div class="content-wrapper ">
        <div class="content-header mx-4">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-12 mt-3">
                        <h3>Comercio adherido</h3>
                        <small>Introduzca la información de que disponga sobre el comercio adherido. Podrá modificarla más adelante.</small>
                    </div>
                </div>
            </div>
        </div>
        <section class="content mx-4">
            <div class="container-fluid">
            <?php $base_url=base_url(); ?>
            <?= form_open_multipart('comercios/modificar') ?>
                <input type="hidden" name="ipId" id="ipId" value="<?= $comercio['id'] ?>">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex p-0">
                                <h3 class="card-title p-3">Datos de registro y contacto</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="ipActivo" name="ipActivo" <?= ($comercio['activo']=='1')?'checked':'' ?> >
                                        <label class="custom-control-label" for="ipActivo">Comercio activado<div class="text-sm font-weight-normal">Desactive para que el comercio no participe en el sistema</div></label>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="ipCIF" class="mb-0 form-label">CIF*
                                            <div class="text-xs">Código CIF de la empresa</div>
                                        </label>
                                        <input type="text" class="form-control" id="ipCIF" name="ipCIF" value="<?= $comercio['CIF'] ?>">
                                    </div>
                                    <div class="col-md-9">
                                        <label for="ipNombre" class="mb-0 form-label">Nombre comercial*
                                            <div class="text-xs">Nombre que verán los usuarios en su app</div>
                                        </label>
                                        <input type="text" class="form-control" id="ipNombre" name="ipNombre" value="<?= $comercio['nombre'] ?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="ipUsuario" class="mb-0 form-label">Usuario*
                                            <div class="text-xs">Nombre que usará el comercio para acceder a la app</div>
                                        </label>
                                        <input type="text" class="form-control" id="ipUsuario" name="ipUsuario" value="<?= $comercio['usuario'] ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="ipContrasena" class="mb-0 form-label">Contraseña*
                                            <div class="text-xs">Contraseña que usará el comercio para acceder a la app</div>
                                        </label>
                                        <input type="password" class="form-control" id="ipContrasena" name="ipContrasena" value="<?= $comercio['contrasena'] ?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="ipContacto" class="mb-0 form-label">Nombre de contacto
                                            <div class="text-xs">Nombre del propietario o interlocutor</div>
                                        </label>
                                        <input type="text" class="form-control" id="ipContacto" name="ipContacto" value="<?= $comercio['contacto'] ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="ipCorreo" class="mb-0 form-label">Email
                                            <div class="text-xs">Correo electrónico de contacto</div>
                                        </label>
                                        <input type="email" class="form-control" id="ipCorreo" name="ipCorreo" value="<?= $comercio['correo'] ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="ipTelefono" class="mb-0 form-label">Teléfono
                                            <div class="text-xs">Número de teléfono del contacto</div>
                                        </label>
                                        <input type="number" class="form-control" id="ipTelefono" name="ipTelefono" value="<?= $comercio['movil'] ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex p-0">
                                <h3 class="card-title p-3">Información para beneficiarios</h3>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="ipLogo" class="mb-0 form-label">Logotipo
                                            <div class="text-xs">Imagen comercial que se mostrará en la aplicación</div>
                                        </label>
                                        <figure class="mw-100 mb-0 d-flex">
                                            <img src="<?= $comercio['imgLogo'] ?>" alt="Avatar" id="ipImgLogo" class="border mx-auto my-auto" style="margin-top:20px;width:15rem" />
                                            <input id="ipFichero" type="file" style="display:none" accept="image/jpeg, image/png, image/jpg">
                                        </figure>
                                        <input type="hidden" id="ipHayPrelogo" name="ipHayPrelogo" value="0">
                                        <input type="hidden" id="ipLogo" name="ipLogo" value="<?= $comercio['logo'] ?>">
                                        <button class="btn btn-secondary cerrado mt-0 ml-5" type="button" id="btLogo">Cambiar</button>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="row">
                                            <div class="col">
                                                <label for="ipClases" class="mb-0 form-label">Clases de beneficiarios
                                                    <div class="text-xs">Indique o cree grupos objetivo de beneficiarios. Los beneficiarios pertenecientes a estas clases podrán comprar con ILLA en este comercio.</div>
                                                </label>
                                                <select id="ipClases" name="ipClases[]" multiple="multiple" class="form-control">
                                                    <?php
                                                    foreach($clasesBeneficiario as $idClase=>$cadaClase){
                                                        echo '<option '.(in_array($cadaClase,$comercio['clases'])?'selected="selected"':'').'>'.$cadaClase.'</option>';
                                                    }
                                                    ?>
                                                </select>
                                                <script>
                                                    $("#ipClases").select2({
                                                            theme: "bootstrap4",
                                                            tags: true
                                                    });
                                                </script>    
                                            </div>
                                        </div>    
                                        <div class="row">
                                            <div class="col">
                                                <label for="ipDireccion" class="mb-0 form-label">Dirección
                                                    <div class="text-xs">Dirección que se proporcionará a los beneficiarios en la app</div>
                                                </label>
                                                <textarea class="form-control" id="ipDireccion" name="ipDireccion" value="<?= $comercio['direccion'] ?>"></textarea>
                                            </div>
                                            <div class="col">
                                                <label for="ipCoordenadas" class="mb-0 form-label">Coordenadas
                                                <div class="text-xs">Coordenadas en un mapa: latitud y longitud separadas por coma</div>
                                                </label>
                                                <input type="text" class="form-control" id="ipCoordenadas" name="ipCoordenadas" value="<?= $comercio['coordenadas'] ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row pb-3">
                        <a type="button" href="<?=base_url("comercios") ?>" class="btn btn-secondary ml-auto mr-3">Cancelar</a>
                        <button type="submit" class="btn btn-primary mr-5">Registrar los cambios</button>
                </div>
            </div>
            <?= form_close() ?>
        </section>
    </div>
<!-- Vpconf> pie (Zona sujeta a cambios automáticos)-->
    <?= pie($VPConf->contenidoPie,'',false) ?>
<!-- Vpconf< pie -->
 </div>
 <script>
    var cropper;
    function creaCropper(){
        $("#ipImgLogo").cropper({
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
        cropper= $("#ipImgLogo").data('cropper');
    }
    var imgPrelogo='';
    $(document).ready(function(){
        imgPrelogo=$("#ipImgLogo").attr('src').replace(/([^\/]+)$/,'prelogo'+$("#ipId").val()+'.png');
        $("#ipImgLogo").click(function(){
            $('#ipFichero').trigger('click');
        });
        $('body').on('click',"#btLogo.cerrado",function(){
            $('#ipFichero').trigger('click');
        });
        
        $('#ipFichero').change(function(evt){
            $("#btLogo").removeClass('btn-secondary').removeClass('cerrado').addClass('abierto').addClass('btn-success').text('Aceptar');
            var tgt = evt.target || window.event.srcElement,
            files = tgt.files;
            
            // FileReader support
            if (FileReader && files && files.length) {
                var fr = new FileReader();
                fr.onload = function () {
                    $("#ipImgLogo").cropper('destroy');
                    $("#ipImgLogo").attr('src', fr.result);
                    creaCropper();
                }
                fr.readAsDataURL(files[0]);
            }
        });
       // creaCropper();
       $('body').on('click',"#btLogo.abierto",function(){
            $("#btLogo").addClass('btn-secondary').addClass('cerrado').removeClass('abierto').removeClass('btn-success').text('Cambiar');
            if (cropper){
                $("#ipImgLogo").cropper('getCroppedCanvas',{width:200, height:200}).toBlob((blob) => {
                        const formData = new FormData();

                        formData.append('imagenCortada', blob);

                        $.ajax('<?= base_url('comercio/prelogo') ?>/'+$("#ipId").val(), {
                            method: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success() {
                                $("#ipImgLogo").cropper('destroy').attr('src',imgPrelogo);
                                $("#ipLogo").val(1);
                                $("#ipHayPrelogo").val('1');
                            },
                            error() {
                                console.log('Upload error');
                            },
                        });
                });
            }
        });
    });
 </script>
</body>
</html>