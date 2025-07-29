<?php
namespace Modulos\Pagina\Controllers;
use App\Controllers\BaseController;

class Usuarios extends BaseController
{
    protected $helpers=['autorizaciones', 'enlaces', 'form', 'Modulos\Vpbasicos\navegacion', 'Modulos\Vpbasicos\tabla', 'Modulos\Vpbasicos\formularios'];
    
    //Vpconf> declaraciones
    private $data;    
    //Vpconf<

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        
        $this->data['enlaces']=[];
        enlaces($this->helpers,$this->data['enlaces']);
        if (!($this->data['usuario']=datos_usuario()))
            return redirect('login');
        
        
    }

    public function index()
    {
        //Vpconf> permiso
        if (!tienePermiso([2])) return view('no_autorizado');
//Vpconf<
            if (strtolower($this->request->getMethod()) !== 'post') {

                
                $this->data['usuario']=datos_usuario();

                $usuario = new \Modulos\Pagina\Entities\Usuario();
                $grupos = $usuario->grupos;
                $this->data['gruposEntidad']=$grupos;

                $MUsuarios = model('Modulos\Pagina\Models\Cls_usuarios_p');
                $this->data['usuarios']=[];
                foreach($MUsuarios->listadoCompleto() as $unUsuario){
                    $grupos = '';
                    $desc_grupos = [];
                    foreach($unUsuario->grupos as $grupo){
                        $desc_grupos[] = $grupo->desc_grupo;
                    }
                    $this->data['usuarios'][]=[
                        $unUsuario->id_usr,
                        $unUsuario->login_usr,
                        $unUsuario->nombre_usr,
                        $unUsuario->pwd_usr,
                        $unUsuario->correo,
                        imagen_usr($unUsuario->id_usr,true),
                        $unUsuario->grupos,
                    ];
                }
                
                $this->data['VPConf']=config('VstPortal');
                return view('\Modulos\Pagina\vista_usuarios',$this->data);
            }
            else{
        
            }
    }

    //métodos de crear, editar y eliminar usuario
    //el método de crear debe de grabar: en la tabla user sus atributos, ahora, el id que haya asignado a usuario debe de
    //usarlo para usuarios_grupos y unirlo al grupo o grupos seleccionados
    public function crear_usuario(){
        $respuesta=['exito'=>false, 'mensaje'=>'Error indeterminado creando usuario', 'id'=>''];
        $db = \Config\Database::connect();
        if (!$db->table('usuarios')->ignore(true)->insert([
            'nombre_usr'=>$this->request->getPost('Nombre'),
            'login_usr'=>$this->request->getPost('Usuario'),
            'correo'=>$this->request->getPost('Correo'),
            'pwd_usr'=>md5($this->request->getPost('Contraseña')),
        ])){
            $respuesta['mensaje']=json_encode($db->error());
        }
        else{
            $db->table('usuarios_grupos')->ignore(true)->insert([
                'id_usr'=>$db->insertID(),
                'id_grupo'=>1]);
            $respuesta['exito']=true;
            $respuesta['mensaje']='';
            $respuesta['id']=$db->insertID();
        }
        return json_encode($respuesta);
    }

    public function modificar(){
        if (!tienePermiso([2])&&($this->request->getPost('Id')!=$this->data['usuario']['idUsuario'])) return false;
        $respuesta=['exito'=>false, 'mensaje'=>'Error indeterminado modificando usuario'];
        $db = \Config\Database::connect();
        $valores=[
            'nombre_usr'=>$this->request->getPost('Nombre'),
            'login_usr'=>$this->request->getPost('Usuario'),
            'correo'=>$this->request->getPost('Correo'),
            'caracteristicas'=>($this->request->getPost('Suscripcion')?'s':'S')
        ];
        if ($this->request->getPost('Contraseña')!=''){
            $valores['pwd_usr']=md5($this->request->getPost('Contraseña'));
        }
        if(!$db->table('usuarios')->where('id_usr = '. $this->request->getPost('Id'))->update($valores)){
            $respuesta['mensaje']=json_encode($db->error());
        }
        else{
            $respuesta['exito']=true;
            $respuesta['mensaje']='';
            registra_sesion_usuario(model('Modulos\Pagina\Models\Cls_usuarios_p')->find($this->request->getPost('Id')));
        }
        return json_encode($respuesta);
    }

    public function eliminar(){
            $respuesta=['exito'=>false, 'mensaje'=>'Error indeterminado eliminando usuarios'];
            $lista=json_decode($this->request->getPost('lista'),true);
            if (count($lista)>0){
                $db = \Config\Database::connect();
                if (!$db->table('usuarios')->where('id_usr IN ('.implode(',',$lista).')')->delete()){
                    $respuesta['mensaje']=json_encode($db->error());
                }
                else{
                    if (!$db->table('usuarios_grupos')->where('id_usr IN ('.implode(',',$lista).')')->delete()){
                        $respuesta['mensaje']=json_encode($db->error());
                    }
                    else{
                        $respuesta['exito']=true;
                        $respuesta['mensaje']='';
                    }
                }
            }
            $respuesta['exito']=true;
            return json_encode($respuesta);
      
        }


        public function miPerfil($aClave=0){
            if(strtolower($this->request->getMethod()) !== 'post'){
                $uri = current_url(true);
                $this->data['aClave']=($aClave==1);
                if (!$this->data['usuario'])
                    return redirect('login');
                $this->data['usuario'] = model('Modulos\Pagina\Models\Cls_usuarios_p')->find($this->data['usuario']['idUsuario']);
                $this->data['grupos']=$this->data['usuario']->grupos();
                $this->data['VPConf']=config('VstPortal');
                return view('\Modulos\Pagina\vista_Perfil',$this->data);
            }
        }
        public function avatar()
        {
            //Vpconf> permiso avatar
        if (!tienePermiso(0)) return view('no_autorizado');
//Vpconf<

            if (strtolower($this->request->getMethod()) !== 'post') {
                $this->data['VPConf']=config('VstPortal');
                return view('\Modulos\Pagina\vista_ImagenAvatar',$this->data);
            }
            else{
                $target_dir = ROOTPATH . 'public/assets/imagenes/avatares/';
                $target_file = $target_dir . 'avt'.$this->data['usuario']['idUsuario'].'.png';
                if (file_exists($target_file)) unlink($target_file);
                move_uploaded_file($_FILES["imagenCortada"]["tmp_name"], $target_file);
                registra_sesion_usuario(model('Modulos\Pagina\Models\Cls_usuarios_p')->find($this->data['usuario']['idUsuario']));
                return true;
            }
        }

    public function cambiar_contrasena()
    {
        // recuperamos el usuario mediante su id
        $datosUsuario=datos_usuario();
        $MUsuarios = model('Modulos\Pagina\Models\Cls_usuarios_p');
        $id = $datosUsuario['idUsuario'];
        //obtenemos la nueva contraseña
        $new_password = $this->request->getPost('clave');
        //recuperamos de bbdd toda la info del usuario
        $usuario = $MUsuarios->find($id);
        // Verificar que el usuario existe
        if (!$usuario) {
            return $this->fail('No se pudo encontrar al usuario');
        }
        $hashed_password = md5($new_password);
        // Actualizar la contraseña del usuario
        $usuario->pwd_usr = $hashed_password;
        $MUsuarios->save($usuario);

        return $this->response->setJSON(['message' => 'La contraseña ha sido actualizada correctamente']);

    }
    public function buscar_usuario($inicio="")
    {
        // recuperamos el usuario mediante su id
        if (strtolower($this->request->getMethod()) == 'get') {
            $ini=strtolower(urldecode($inicio));
            $lista=[];
            foreach(model('Modulos\Pagina\Models\Cls_usuarios_p')->where('LOWER(nombre_usr) LIKE "'.$ini.'%" OR LOWER(login_usr) LIKE "'.$ini.'%"')->findAll() as $unUsuario){
                $lista[]=['id_usr'=>$unUsuario->id_usr, 'avatar'=>imagen_usr($unUsuario->id_usr), 'login_usr'=>$unUsuario->login_usr, 'nombre_usr'=>$unUsuario->nombre_usr];
            }
           return $this->response->setJSON($lista);
        }
    }
    public function mensajes_nuevos()
    {
        $nuevos = [];
        $mensajes=model('Modulos\Pagina\Models\Cls_mensajes')->findPara(session()->get('usuario')['idUsuario'],true);
        $remitentes=[];
        foreach($mensajes as $unMensaje){
            if (!array_key_exists($unMensaje->autor,$remitentes)) $remitentes[$unMensaje->autor]=model('Modulos\Pagina\Models\Cls_usuarios_p')->find($unMensaje->autor);
        }
        foreach($mensajes as $unMensaje){
            $nombreAutor=\Modulos\Pagina\USUARIO_SISTEMA;
            if ($unMensaje->autor!=0) $nombreAutor=$remitentes[$unMensaje->autor]->nombre_usr;
                    $nuevos[]=[ 
                        'titulo'=>'Expediente '.$unMensaje->expediente['titulo'], 
                        'texto'=>$unMensaje->texto, 
                        'autor_nombre'=>$nombreAutor,
                        'autor_avatar'=>imagen_usr($unMensaje->autor),
                        'hace'=>strtotime('now')-strtotime($unMensaje->fecha),
                        'link'=>base_url('expediente').'?exp='.$unMensaje->expediente['id'].($unMensaje->historico?'&h=1':'').'&b=1'
                        ];
        }
        return $this->response->setJSON($nuevos);
    }    
}
?>