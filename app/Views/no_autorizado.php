<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $VPConf->tituloWeb ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <scrk rel="shortcut icon" type="image/png" href="/favicon.ico">
    
    <link rel="stylesheet" href="<?= base_url('public/assets/css','https') ?>/adminlte.css">
    <lin
</head>
<body class="hold-transition sidebar-mini layout-fixed bg-dark">
  <div class="jumbotron jumbotron-fluid text-dark m-5">
    <div class="container">
      <h1>NO AUTORIZADO</h1>
        <p>Su acceso a esta página no está autorizado. Por favor, reingrese con otro usuario o solicite autorización a su adminsitrador</p>
        <a href='<?= base_url('login','https') ?>'>Cambiar de usuario</a>
    </div>
  </div>
</body>
</html>
