<?php 
use CodeIgniter\Router\RouteCollection;

$routes->get('/usuarios', '\Modulos\Pagina\Controllers\Usuarios::index'); //Vpconf: ['usuarios','meBucP','2']

$routes->post('/usuarios/modificar', '\Modulos\Pagina\Controllers\Usuarios::modificar');

//usuarios modulo
$routes->post('/usuarios/crear_usuario', '\Modulos\Pagina\Controllers\Usuarios::crear_usuario');
$routes->get('/usuarios/buscar/(:any)', '\Modulos\Pagina\Controllers\Usuarios::buscar_usuario/$1');
$routes->get('/usuarios/buscar', '\Modulos\Pagina\Controllers\Usuarios::buscar_usuario');
$routes->get('/usuarios/mensajes_nuevos', '\Modulos\Pagina\Controllers\Usuarios::mensajes_nuevos');
//$routes->post('/usuarios/editar_usuario', '\Modulos\Pagina\Controllers\Usuarios::editar_usuario');
$routes->post('/usuarios/eliminar', '\Modulos\Pagina\Controllers\Usuarios::eliminar');

$routes->get('/usuarios/miPerfil', '\Modulos\Pagina\Controllers\Usuarios::miPerfil');
$routes->get('/usuarios/miPerfil/(:num)', '\Modulos\Pagina\Controllers\Usuarios::miPerfil/$1');
$routes->post('/usuarios/miPerfil', '\Modulos\Pagina\Controllers\Usuarios::miPerfil');

$routes->post('/configuracion/modificar', '\Modulos\Pagina\Controllers\Configuracion::modificar'); 


$routes->get('/usuarios/avatar', '\Modulos\Pagina\Controllers\Usuarios::avatar'); //Vpconf: ['ImagenAvatar','meBPu','']
$routes->post('/usuarios/avatar', '\Modulos\Pagina\Controllers\Usuarios::avatar'); //Vpconf: ['ImagenAvatar','meBPu','']







$routes->get('/', '\Modulos\Pagina\Controllers\Inicio::index'); //Vpconf: ['Bienvenida','meBPuc','']

$routes->get('/beneficiarios', '\Modulos\Pagina\Controllers\Beneficiarios::index'); //Vpconf: ['Beneficiarios','meBPuc','2']
$routes->post('/beneficiarios', '\Modulos\Pagina\Controllers\Beneficiarios::index'); //Vpconf: ['Beneficiarios','meBPuc','2']
$routes->post('/beneficiarios/crear', '\Modulos\Pagina\Controllers\Beneficiarios::crear'); 
$routes->post('/beneficiarios/modificar', '\Modulos\Pagina\Controllers\Beneficiarios::modificar'); 
$routes->post('/beneficiarios/eliminar', '\Modulos\Pagina\Controllers\Beneficiarios::eliminar'); 
$routes->get('/beneficiarios/(:num)/informacion', '\Modulos\Pagina\Controllers\Beneficiarios::informacion/$1'); 
$routes->post('/beneficiarios/(:num)/transferir/(:segment)', '\Modulos\Pagina\Controllers\Beneficiarios::transferir/$1/$2'); 
$routes->post('/beneficiario/(:num)/bloquear/(:num)', '\Modulos\Pagina\Controllers\Beneficiarios::bloquear/$1/$2'); 

$routes->get('/comercios', '\Modulos\Pagina\Controllers\Comercios::index'); //Vpconf: ['Comercios','meBPuc','2']
$routes->post('/comercios/crear', '\Modulos\Pagina\Controllers\Comercios::crear'); 
$routes->post('/comercios/modificar', '\Modulos\Pagina\Controllers\Comercios::modificar'); 
$routes->post('/comercios/eliminar', '\Modulos\Pagina\Controllers\Comercios::eliminar'); 


$routes->group('/api/v1.0',['filter' => 'cors'], static function (RouteCollection $routes): void {
    $routes->get('', '\Modulos\Pagina\Controllers\Api::index');
    $routes->options('', '\Dummy');
    $routes->options('(:any)', '\Dummy');
    $routes->post('login', '\Modulos\Pagina\Controllers\Api::login');
    $routes->get('cuenta/(:segment)', '\Modulos\Pagina\Controllers\Api::leerCuenta/$1');
    $routes->post('cuenta/(:segment)', '\Modulos\Pagina\Controllers\Api::registrarCuenta/$1');
    $routes->post('cuenta/(:segment)/autorizacion', '\Modulos\Pagina\Controllers\Api::autorizarCuenta/$1');
    $routes->get('comercio/(:segment)', '\Modulos\Pagina\Controllers\Api::infoComercio/$1');
    $routes->get('comercios', '\Modulos\Pagina\Controllers\Api::comercios');
    $routes->get('usuario', '\Modulos\Pagina\Controllers\Api::consultaUsuario');
});





$routes->get('/comercio/editar', '\Modulos\Pagina\Controllers\Comercio::editar'); //Vpconf: ['EditarComercio','eBup','2']
$routes->get('/comercio/editar/(:num)', '\Modulos\Pagina\Controllers\Comercio::editar/$1'); //Vpconf: ['EditarComercio','eBup','2']
$routes->post('/comercio/editar', '\Modulos\Pagina\Controllers\Comercio::editar'); //Vpconf: ['EditarComercio','eBup','2']
$routes->post('/comercio/prelogo/(:segment)', '\Modulos\Pagina\Controllers\Comercio::prelogo/$1'); //Vpconf: ['EditarComercio','eBup','2']
$routes->post('/comercio/(:num)/bloquear/(:num)', '\Modulos\Pagina\Controllers\Comercio::bloquear/$1/$2'); 
$routes->post('/comercio/(:num)/pago', '\Modulos\Pagina\Controllers\Comercio::pago/$1'); 

$routes->get('/cron', '\Modulos\Pagina\Controllers\Cron::index'); 
$routes->cli("cron", '\Modulos\Pagina\Controllers\Cron::index');

$routes->get('/transacciones', '\Modulos\Pagina\Controllers\Transacciones::index'); //Vpconf: ['Transacciones','meBPuc','4']
$routes->post('/transacciones/listado', '\Modulos\Pagina\Controllers\Transacciones::listado'); 

$routes->get('/donaciones', '\Modulos\Pagina\Controllers\Donaciones::index'); //Vpconf: ['Donaciones','meBpuc','4']
$routes->post('/donaciones/listado', '\Modulos\Pagina\Controllers\Donaciones::listado'); 

$routes->get('/reintegros', '\Modulos\Pagina\Controllers\Reintegros::index'); //Vpconf: ['Reintegros','meBucp','3']
$routes->post('/reintegros/listado', '\Modulos\Pagina\Controllers\Reintegros::listado'); 

$routes->get('/configuraciongeneral', '\Modulos\Pagina\Controllers\Configuraciongeneral::index'); //Vpconf: ['ConfiguracionGeneral','eBup','1']

?>