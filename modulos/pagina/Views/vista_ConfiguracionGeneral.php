<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $VPConf->tituloWeb ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('public/assets/imagenes','https') ?>/confFavicon.png">
    <?= inserta_enlaces($enlaces) ?>
    <link rel="stylesheet" href="<?= base_url('public/assets/css','https') ?>/monedas.css"/>

<style>
    .imagenConfig:hover{
        cursor: pointer;
        box-shadow: 0px 0px 15px yellow;
    }
</style>
</head>
<body class="hold-transition layout-top-nav layout-fixed text-<?= $VPConf->tamanoTexto ?> <?= $VPConf->tonalidad ?>">
 <div class="wrapper">
<!-- Vpconf> barra (Zona sujeta a cambios automáticos)-->
    <?= barraNavegacion(json_decode($VPConf->menuSuperior,true),[['tipo'=>'usuario']],'',encabezado(base_url(""),base_url("public/assets/imagenes")."/logotipo".($VPConf->UAT?"UAT":"").".png",$VPConf->nombreCliente),true) ?>
<!-- Vpconf< barra -->    <div class="content-wrapper">
        <div class="content-header">
        </div>
        <section class="content">
            <div class="container-fluid">
                <h4 class="text-center w-100">Configuración del Sistema</h4>
                <div class="card mx-5">
                    <div class="card-header">
                        <h3 class="card-title">
                            <span class="ILLA"></span>
                                Moneda Social
                        </h3>
                    </div>
                    <div class="card-body">
                        <dl class="row ">
                            <dt class="col-sm-4 text-right">Red</dt>
                            <dd class="col-sm-8">Stellar <?= getenv('moneda.red') ?></dd>
                            <dt class="col-sm-4 text-right">Designación de la moneda</dt>
                            <dd class="col-sm-8"><?= getenv('moneda.nombre') ?></dd>
                            <dt class="col-sm-4 text-right">Cuenta emisora</dt>
                            <dd class="col-sm-8">G<?= $emisora['clave'] ?></dd>
                            <dt class="col-sm-4 text-right">Saldo emisora</dt>
                            <dd class="col-sm-8 XLM"><?= $emisora['XLM'] ?></dd>
                            <dt class="col-sm-4 text-right">Cuenta distribuidora</dt>
                            <dd class="col-sm-8">G<?= $distribuidora['clave'] ?></dd>
                            <dt class="col-sm-4 text-right">Saldos distribuidora</dt>
                            <dd class="col-sm-8 XLM"><?= $distribuidora['XLM'] ?></dd>
                            <dd class="col-sm-8 offset-sm-4 ILLA"><?= $distribuidora['cripto'] ?></dd>
                            <dt class="col-sm-4 text-right">Revisión y recarga de cuentas</dt>
                            <dd class="col-sm-8">Recarga automática de lumens hasta <span class="XLM"><?= number_format(floatval(getenv('moneda.XLM.maximo')),2) ?></span></dd>
                            <dd class="col-sm-8 offset-sm-4 ">en caso de saldo inferior a <span class="XLM"><?= number_format(floatval(getenv('moneda.XLM.minimo')),2) ?></span></dd>
                            <dd class="col-sm-8 offset-sm-4 ">Última revisión realizada <?= $ejecucionCron ?></dd>
                            <dt class="col-sm-4 text-right">Precio actual <span class="XLM">1</span></dt>
                            <dd class="col-sm-8"><?= $costeXLM ?></dd>
                            
                        </dl>
                    </div>
                </div>
            </div>
        </section>
    </div>
<!-- Vpconf> pie (Zona sujeta a cambios automáticos)-->
    <?= pie($VPConf->contenidoPie,'',false) ?>
<!-- Vpconf< pie -->
 </div>
</body>
</html>