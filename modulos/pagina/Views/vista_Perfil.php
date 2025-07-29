<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <title><?= $VPConf->tituloWeb ?></title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="shortcut icon" type="image/png" href="<?= base_url('public/assets/imagenes','https') ?>/confFavicon.png">
      <?= inserta_enlaces($enlaces) ?>
	  <script type="text/javascript" src="<?= base_url('public/assets/plugins', 'https') ?>/jquery-validation/jquery.validate.min.js"></script>
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

         <div class="content-wrapper ">
	<section class="content">
		<div class="container-fluid">
			<figure class="avatar mw-100 d-flex">
				<img src="<?= imagen_usr($usuario->id_usr) ?>" alt="Avatar" id="avatarGrande" class="avatarGrande rounded-circle mx-auto my-auto" style="padding-top:20px;width:15rem" />
				<img src="" alt="" id="btAvatar" role="button" class="edit-avatar" />
				<input type="file" id="ipFoto" name="ipFoto" style="display:none">
				
			</figure>
			<div class="row text-center" style="margin-top:-3rem">
		 		<div class="mx-auto text-right" style="width:13rem;">
					<a href="<?= base_url('usuarios/avatar') ?>" type="button" class="btn btn-info btn-rounded btn-icon" >
							<i class="fa fa-edit py-2"></i>
					</a>
				</div>
			</div>
			<div class="row">
				<form id="frmUsuario" class="col-md-5 mx-auto" >
					<input type="hidden" name="Id" value="<?= $usuario->id_usr ?>"/>
					<input type="hidden" class="campoUsuario" name="Contraseña" value=""/>
					<h5 class="form-group mt-2" >
						<label >Usuario: <?= $usuario->login_usr ?></label>
						<input type="hidden" name="Usuario" value="<?= $usuario->login_usr ?>"/>
					</h5>
					<div class="form-group mt-2">
						<label for="ipCorreo">Correo</label>
						<input type="email" id="ipCorreo" class="campoUsuario form-control" name="Correo" value="<?php echo $usuario->correo ?>" />
					</div>
					<div class="form-group" >
						<label for="ipNombre">Nombre y Apellidos</label>
						<input type="text" id="ipNombre"  class="campoUsuario form-control" name="Nombre" placeholder="Escriba su nombre y apellidos" value="<?= $usuario->nombre_usr ?>"/>
					</div>
					<div class="mb-2">
						<label>Pertenece a:</label><?php $nombresGrupos=[];foreach($grupos as $unGrupo){ $nombresGrupos[]=$unGrupo->desc_grupo; }?>
						<span><?= count($nombresGrupos)==0?'No pertenece a ningún grupo':implode(', ',$nombresGrupos) ?></span>
					</div>
					<div class="mb-2">
						<div class="form-group">
							<div class="custom-control custom-switch">
								<input type="checkbox" class="campoUsuario custom-control-input"  name="Suscripcion" id="ipSuscripcion" <?= (strpos($usuario->caracteristicas,'S')===false?'checked':'') ?>>
								<label class="custom-control-label" for="ipSuscripcion">Recibo notificaciones automáticas</label>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-3"></div>
						<div class="col-md-6">
							<div class="text-center">
								<button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" onclick="$('#frmCambioClave').modal('show')">Cambiar contraseña</button>
								<button type="button" class="btn btn-secondary btn-lg" data-bs-toggle="modal" onclick="location.href='<?=base_url() ?>'">Volver</button>
							</div>
						</div>
						<div class="col-md-3"></div>
					</div>
				</form>
			</div>
		</div>
	</section>
</div>
<?= formulario('frmCambioClave',[
      'titulo'=>'Modificar contraseña',
      'aceptar'=>
<<<JS
          function(){
			if ($("#frmCambioClave form").valid())
				$('input[name="Contraseña"]').val($("#ipNuevaClave1").val()).trigger('change');
                $('#frmCambioClave').modal('hide');
          }   
JS, 
      'campos'=>[
                  'ipNuevaClave1'=>[
                        'tipo'=>'password',
                        'label'=>'Nueva contraseña'
                  ],
                  'ipNuevaClave2'=>[
                        'tipo'=>'password',
                        'label'=>'Repite contraseña',
                  ],
      ]
    ]) ?>
<?= 'ACLAVE='.print_r($aClave,true) ?>	
	<script>
		$(document).ready(function(){

			if (<?= $aClave?'true':'false' ?>) {
				$('#frmCambioClave').modal('show');
			}
			jQuery.validator.setDefaults({
				errorElement: 'span',
				errorPlacement: function (error, element) {
					error.addClass('invalid-feedback');
					element.closest('.form-group').append(error);
				},
				highlight: function (element, errorClass, validClass) {
					$(element).addClass('is-invalid');
				},
				unhighlight: function (element, errorClass, validClass) {
					$(element).removeClass('is-invalid');
				}
			});
			$("#frmUsuario").validate({
					rules: {
						Nombre: {required: true, maxlength: 100},
					},
					messages:{
						Nombre: {required: 'Se requiere un nombre', maxlength: 'Máximo de 100 caracteres'},
					}
					
				});
			$("#frmCambioClave form").validate({
					rules: {
						ipNuevaClave1: {required: true, minlength: 4},
						ipNuevaClave2: {required: true, equalTo: "#ipNuevaClave1" },
					},
					messages: {
						ipNuevaClave1: {
							required: 'Cumplimente este campo',
							minlength: 'Al menos 4 caracteres'
						},
						ipNuevaClave2: {
							required: 'Cumplimente este campo',
							equalTo: 'Las contraseñas no coinciden'
						}
					
					}
				});
			$("#frmUsuario").submit( function () {    
              $.post(
               '<?= base_url('usuarios/modificar') ?>',
                $(this).serialize(),
                function(json_respuesta){
					const respuesta = JSON.parse(json_respuesta);
					if (respuesta.exito) {
						location.href='<?= base_url('usuarios/miPerfil') ?>';
					} else {
						alert('Error modificando usuario: '+respuesta.mensaje);
					}
                }
              );
              return false;   
            });   
			$('.campoUsuario').change(function(){
				if ($("#frmUsuario").valid())
					$("#frmUsuario").submit();
			});
		});

	</script>

         <!-- Vpconf> pie (Zona sujeta a cambios automáticos)-->
         <?= pie($VPConf->contenidoPie,'',true) ?>
         <!-- Vpconf< pie -->
      </div>
	  
   </body>
</html>