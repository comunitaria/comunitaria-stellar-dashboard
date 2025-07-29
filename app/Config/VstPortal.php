<?php
/*
|--------------------------------------------------------------------------
| Fichero de cofiguración para la interfaz de usuario
|--------------------------------------------------------------------------
| Parámetros para adaptar la imagen visual de la control tower al cliente
| Para evitar colisión, todas las claves van precedidas de 'UI_'
*/
namespace Config;

use CodeIgniter\Config\BaseConfig;

class VstPortal extends BaseConfig
{

    public $UAT= false; 
    public $tituloWeb= ''; 
    public $nombreCliente= '';
    public $contenidoPie= '';
    public $tamanoTexto= '';
    public $menuLateral='';
    public $menuSuperior='';
    public $menuConfig='';
    public $permisos='';
    public $tonalidad='';
    public $lateralDark='';
    public $lateralDestacado='';
    public $superiorDark='';
    public $configuracionDark='';
  
}              
?>