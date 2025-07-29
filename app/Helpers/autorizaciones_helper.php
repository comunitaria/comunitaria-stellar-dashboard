<?php

function registra_sesion_usuario($usuario){

    $sesion=session();
    $sesion->remove('usuario');
    $sesion->set(['usuario'=>[  'idUsuario' => $usuario->id_usr,
                                'Nombre_Usuario' => $usuario->nombre_usr,
                                'Login_Usuario' => $usuario->login_usr,
                                'Avatar' => imagen_usr($usuario->id_usr),
                                'Caracteristicas'=>$usuario->caracteristicas]]);
}
function limpia_sesion_usuario(){

    $sesion = session();
    $sesion->remove('usuario');
}
function datos_usuario(){
    $sesion = session();
    return $sesion->has('usuario')?$sesion->get('usuario'):false;
}
function imagen_usr($id,$conRefresco=true){
        //strtotime fuerza el refresco de la imagen
        if ($id>0)
            return (base_url('public/assets/imagenes/avatares').(file_exists(ROOTPATH . 'public/assets/imagenes/avatares/avt'.$id.'.png')? '/avt'.$id.'.png'.($conRefresco?('?'.strtotime('now')):'') : '/avatar.jpg'));
        else
            return (base_url('public/assets/imagenes/avatares').'/'.\Modulos\Pagina\AVATAR_USUARIO_SISTEMA);
    }
function tienePermiso($permiso) {
    /* Esta función recibe un parámetro con un identificador de usuario y otro con el id del 
     * permiso. Si el usuario tiene habilitado ese permiso devuelve 1,  en caso
     * contrario 0
     */
    if ($permiso==0) return true;
    if ($permiso=='') return true;
    
    if (is_array($permiso)){
        if (intval($permiso[0]??0)==0) {
            return true;
        }
    }
    else{
        $permiso=[$permiso];
    }
    $sesion = session();
    if (!$sesion->has('usuario')) return false;
    $usuarios=model('App\Models\Cls_usuarios');
    $usuario=$usuarios->find($sesion->get('usuario')['idUsuario']);
    if (is_null($usuario)) return false;
    $permisos=$usuario->permisos();
    $valorret = in_array(1,$permisos);
    foreach($permiso as $unPermiso){
        if ($valorret) break;
        $valorret=$valorret||in_array($unPermiso,$permisos);
    }
    return $valorret;

}

