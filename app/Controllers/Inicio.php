<?php

namespace App\Controllers;

class Inicio extends BaseController
{
    protected $helpers=['autorizaciones', 'Modulos\Vpbasicos\navegacion'];
   
    public function index()
    {
        /* INICIADOR  
            Aquí vemos si se ha iniciado ya el framework, en cuyo caso vamos a la página de bienvenida.
            Si no, vamos a la zona de configuración con la orden de inicializar
        */
        if ($_ENV['database.default.database']==''){
        //Valor por defecto: ir a inicialización   
            return $this->vpconf();
        }
        else{
        //Ya está configurado, mostramos pantalla de login o bienvenida:
            if (!($data['usuario']=datos_usuario()))
                return redirect()->to('login');

            $data['VPConf']=config('VstPortal');
            return view('bienvenida',$data);
        }
    }
    public function vpconf(){
        if ($_ENV['database.default.database']==''){
            //Valor por defecto: ir a inicialización   
            return view('vpconf_inicial');
        }
        else{
            //Zona de configuración
            
        }    
    }
}
