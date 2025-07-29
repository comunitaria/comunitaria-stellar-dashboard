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
<body class="hold-transition sidebar-mini layout-fixed">
<?= barraNavegacion($menuNav,$componentesNav) ?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">

    <?= encabezado('/',base_url('public/assets/imagenes').'/logotipo'.($VPConf->UAT?"UAT":"").'.png',$VPConf->nombreCliente) ?>
    <?= sidebar([]) ?>

</aside>
<div class="content-wrapper">
    <section class="content mt-5">
        <div class="container-fluid">
        <!-- <button class="btn btn-primary">Prueba boton primary</button> -->
        <!-- Luego habría que moverlo a su correspondiente vista -->
        <!-- < ?= tabla_no_checkbox('tbPaginas',['columnas'=>[['titulo'=>'Petición'],['titulo'=>'Estado'],['titulo'=>'Módulo'],['titulo'=>'Posición'],['titulo'=>'Fecha de modificación']]]) ?> -->
        <table id="tbPeticiones" class="table editable table-bordered table-striped" style=" text-align:center;">
            <thead style="background-color: black; color: white;">
                <tr>
                    <th scope="col">ID petición</th>
                    <th scope="col">Creada por</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Departamento encargado</th>
                    <th scope="col">Pendientes</th>
                    <th scope="col">Fecha de creación</th>
                    <th scope="col">Última modificación</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($peticiones as $peticion): ?>
                <tr style="background-color: white">
                    <td> <?= $peticion->id_peticion ?> </td>
                    <td> Nico </td>
                    <td><?= $peticion->estado ?> </td>
                    <td> (propietario) Comercial </td>
                    <td>0/1</td>
                    <td><?= $peticion->fecha_creacion ?> </td>
                    <td><?= $peticion->fecha_ultima_modificacion ?> </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
</section>
</div>

<script>
    $(document).ready(function(){
        
    });
</script>
</body>
</html>
