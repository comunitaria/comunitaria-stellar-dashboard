<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?= $VPConf->tituloWeb ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <script src="<?= base_url('public/assets/js','https')?>/jquery-3.6.3.min.js"></script>

  <link rel="stylesheet" href="<?= base_url('public/assets/plugins','https') ?>/fontawesome-free/css/all.min.css">
  <link href="<?= base_url('public/assets/css','https') ?>/adminlte.css" rel="stylesheet" >
  <link rel="icon" href="<?php echo base_url('img')?>/favicon.png">
  <link rel="stylesheet" href="<?= base_url('public/assets/plugins/toastr','https') ?>/toastr.min.css">
  <script src="<?= base_url('public/assets/plugins/toastr','https') ?>/toastr.min.js"></script>
  
  </head>
<body class="hold-transition login-page" style="<?= file_exists(ROOTPATH . 'public/assets/imagenes/bg_login.png')?'background-image: url(\''.base_url('public/assets/imagenes').'/bg_login.png\');background-position: center;background-repeat: no-repeat;background-size: cover':'' ?>">
<div class="login-box">
  <div class="login-logo">
    <img src="<?= base_url('public/assets/imagenes','https')?>/logo_vst.png" style="height: 3em" alt="<?= $VPConf->tituloWeb ?>">    
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Iniciar sesión</p>
      <?= form_open('login') ?>
        <div class="input-group mb-3">
          <input type="text" class="form-control" name="txtUsuario" id="ipUsuario" placeholder="Usuario" value="<?= set_value('lusuario') ?>">
          <div class="input-group-append">
            <div class="input-group-text">
            <i class="fas fa-user"></i>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password"  name="txtClave" class="form-control" placeholder="Contraseña" value="<?= set_value('lclave') ?>">
          <div class="input-group-append">
            <div class="input-group-text">
              <i class="fas fa-lock"></i>
            </div>
          </div>
        </div>
        
        <small class="text-danger"><?= validation_list_errors() ?></small>
  
        <div class="row m-3">
            <button type="submit" class="btn btn-primary btn-block">Iniciar sesión</button>
        </div>

        <div class="a link-primary" id="aOlvidada">He olvidado mi contraseña</div>
        <?= form_close() ?>

    </div>
  </div>
  <!-- /.login-box-body -->
</div>
<div class="modal fade" id="confirmacion" style="display: none;" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-$color" data-dismiss="modal" >Aceptar</button>
      </div>
    </div>
  </div>
</div>
<!-- /.login-box -->
<script>
  $(function () {
    $("#aOlvidada").click(function(){
      if ($("#ipUsuario").val()==''){
        toastr.error('Escriba su nombre de usuario.<br>Enviaremos un mensaje electrónico para que recupere su contraseña.');
      }
      else
        $.post('<?= base_url('login/olvido_clave') ?>', {
                      nombre: $("#ipUsuario").val()
                    })
                    .done((json_respuesta) => {
                      respuesta=JSON.parse(json_respuesta);
                      if (respuesta.exito){
                        toastr.success('Hemos enviado un mensaje a su correo electrónico.<br>Por favor, abra el mensaje y haga clic en el botón "Restablecer contraseña".');
                      }
                      else{
                        toastr.error(respuesta.mensaje);
                      }
                    })
                    .fail((xhr, status, error) => {
                        alert('Error en el envío de contraseña:<br>' + error);
                    });
      
    });
  });
</script>
</body>
</html>
