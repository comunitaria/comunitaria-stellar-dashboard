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
<body class="hold-transition sidebar-mini layout-fixed text-<?= $VPConf->tamanoTexto ?> <?= $VPConf->tonalidad ?>">
 <div class="wrapper">
<!-- Vpconf> barra (Zona sujeta a cambios automáticos)-->
    <?= barraNavegacion(json_decode($VPConf->menuSuperior,true),[['tipo'=>'usuario'],['tipo'=>'configuracion','titulo'=>'Configuracion', 'menu'=>$VPConf->menuConfig]],'','menu',true) ?>
<!-- Vpconf< barra -->
<!-- Vpconf> aside (Zona sujeta a cambios auutomáticos)-->
    <aside class="main-sidebar sidebar-<?= $VPConf->lateralDark ?>-<?= $VPConf->lateralDark ?> elevation-4">
                <?= encabezado(base_url(''),base_url('public/assets/imagenes').'/logotipo'.($VPConf->UAT?"UAT":"").'.png',$VPConf->nombreCliente) ?>
                <?= sidebar(json_decode($VPConf->menuLateral,true)) ?>

    </aside>
<!-- Vpconf< aside -->

    <div class="content-wrapper">
        <div class="content-header">
        </div>
        <section class="content">
            <a type="button" href="<?= base_url('public/assets/repositorio').'/monederoIlla.apk' ?>">Descargar monedero Android</a>
            <div class="container-fluid">
                <h4 class="text-center w-100">Moneda solidaria Illa</h4>
                <div  class="text-center mb-5">
                    <img src="<?= base_url('public/assets/imagenes').'/Comunitaria.png' ?>" style="width:14em">
                </div>
                <div class="row mt-5">
                    <div class="col-lg-3"></div>
                    <div class="col-lg-3">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?= $beneficiarios ?><sup style="font-size: 20px"></sup></h3>
                                <p>Beneficiarios</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <a href="<?= base_url('donaciones') ?>" class="small-box-footer">Donaciones <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>

                    <div class="col-lg-3 ">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?= $beneficiarios ?></h3>
                                <p>Comercios colaboradores</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-store"></i>
                            </div>
                            <a href="<?= base_url('reintegros') ?>" class="small-box-footer">Información y abonos <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>

                    <div class="col-lg-3 ">

                    </div>
            </div>
            <div class="row mt-2 text-center mx-5">
                En esta plataforma puede realizar donaciones en ILLAs a beneficiarios y registrar abonos a los comercios colaboradores.<br>
                Si dispone de autorización, puede dar de alta tanto a beneficiarios como a comercios y, si es preciso, bloquear sus cuentas.<br>
                Los monederos (aplicaciones móviles para pago y cobro en comercios asociados) son autónomos. Una vez que de de alta a un participante, proporcionele el nombre de usuario y clave asignados. El usuario puede crear un monedero y solo él dispondrá de las claves de acceso a su cuenta de ILLAs.
            </div>
        </section>
    </div>
<!-- Vpconf> pie (Zona sujeta a cambios automáticos)-->
    <?= pie($VPConf->contenidoPie,'',true) ?>
<!-- Vpconf< pie -->
 </div>
</body>
</html>