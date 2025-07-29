<?php
namespace Modulos\Pagina\Controllers;
use App\Controllers\BaseController;

class Grupos extends BaseController
{
    protected $helpers=['autorizaciones', 'enlaces', 'Modulos\Vpbasicos\navegacion', 'Modulos\Vpbasicos\tabla', 'Modulos\Vpbasicos\formularios'];
    
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
        session()->set('conFiltroFormulario',false);    
        //filtraPorFormulario();
        
        
    }

    public function index()
    {
        //Vpconf> permiso
        if (!tienePermiso([2])) return view('no_autorizado');
//Vpconf<
            if (strtolower($this->request->getMethod()) !== 'post') {
                $MGrupos=model('Modulos\Pagina\Models\Cls_grupos_p');
                $this->data['grupos'] = $MGrupos->getGrupos();
                if (!tienePermiso(1)){
                    foreach($this->data['grupos'] as $i=>$unGrupo){
                        if ($unGrupo['id_perfil']==1) unset($this->data['grupos'][$i]);
                    }
                    $this->data['grupos']=array_values($this->data['grupos']);
                }

                $MOficios=model('Modulos\Pagina\Models\Cls_oficios');
                $this->data['oficios'] = $MOficios->getOficios();

                $this->data['VPConf']=config('VstPortal');
                return view('\Modulos\Pagina\vista_grupos',$this->data);
            }
            else{
                
            }
    }
    //OFICIOS
    public function crear_oficio(){
        $respuesta=['exito'=>false, 'mensaje'=>'Error indeterminado creando permiso', 'id'=>''];
        $db = \Config\Database::connect();
        if (!$db->table('oficios')->ignore(true)->insert([
            'desc_oficio'=>$this->request->getPost('Designación'),
            'caracteristicas'=>$this->request->getPost('Características')
        ])){
            $respuesta['mensaje']=json_encode($db->error());
        }
        else{
            $respuesta['exito']=true;
            $respuesta['mensaje']='';
            $respuesta['id']=$db->insertID();
        }
        return json_encode($respuesta);
    }

    public function eliminar_oficio(){
        $respuesta=['exito'=>false, 'mensaje'=>'Error indeterminado borrando oficio'];
        $lista=json_decode($this->request->getPost('lista'),true);
        if (count($lista)>0){
            $db = \Config\Database::connect();
            if (!$db->table('oficios')->where('id_oficio IN ('.implode(',',$lista).')')->delete()){
                $respuesta['mensaje']=json_encode($db->error());
            }
            else{
                $respuesta['exito']=true;
                $respuesta['mensaje']='';
            }
        }
        $respuesta['exito']=true;
        return json_encode($respuesta);
    }

    public function modificar_oficio(){
        $respuesta=['exito'=>false, 'mensaje'=>'Error indeterminado modificando oficio'];
        $db = \Config\Database::connect();
        if(!$db->table('oficios')->where('id_oficio = '. $this->request->getPost('Id'))->update([
            'desc_oficio'=>$this->request->getPost('Designación'),
            'caracteristicas'=>$this->request->getPost('Características')
        ])){
            $respuesta['mensaje']=json_encode($db->error());
        }
        else{
            $respuesta['exito']=true;
            $respuesta['mensaje']='';
        }
        return json_encode($respuesta);
    }

    public function crear(){
        $respuesta=['exito'=>false, 'mensaje'=>'Error indeterminado creando el grupo', 'id'=>''];
        $db = \Config\Database::connect();
        if (!$db->table('grupos')->ignore(true)->insert([
            'desc_grupo'=>$this->request->getPost('Nombre_del_grupo'),
            'id_perfil'=>$this->request->getPost('id_perfil'),
            'id_oficio'=>$this->request->getPost('oficio')
        ])){
            $respuesta['mensaje']=json_encode($db->error());
        }
        else{
            $debug=[$this->request->getPost('ipId_perfil')];
            $respuesta['exito']=true;
            $respuesta['mensaje']='';
            $respuesta['id']=$db->insertID();
        }
        return json_encode($respuesta);
    }

    public function eliminar(){
        $respuesta=['exito'=>false, 'mensaje'=>'Error indeterminado borrando oficio'];
        $lista=json_decode($this->request->getPost('lista'),true);
        if (count($lista)>0){
            $db = \Config\Database::connect();
            if (!$db->table('usuarios_grupos')->where('id_grupo IN ('.implode(',',$lista).')')->delete()){
                $respuesta['mensaje']=json_encode($db->error());
            }
            else{
                if (!$db->table('grupos')->where('id_grupo IN ('.implode(',',$lista).')')->delete()){
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

    // esta función pasará a ser la que rediriga a la vista de edición de usuarios en grupos
    public function modificar(){
        if (!tienePermiso([2])) return false;
        $respuesta=['exito'=>false, 'mensaje'=>'Error indeterminado modificando grupo'];
        $db = \Config\Database::connect();
        if(!$db->table('grupos')->where('id_grupo = '. $this->request->getPost('Id'))->update([
            'desc_grupo'=>$this->request->getPost('Nombre_del_grupo'),
            'id_perfil'=>$this->request->getPost('id_perfil'),
            'id_oficio'=>$this->request->getPost('oficio')
        ])){
            $respuesta['mensaje']=json_encode($db->error());
        }
        else{
            $respuesta['exito']=true;
            $respuesta['mensaje']='';
        }
        return json_encode($respuesta);
    }

    public function modificar_grupo(){
        //Vpconf> permiso modificar
        if (!tienePermiso([2])) return view('no_autorizado');
        //Vpconf<
            if (strtolower($this->request->getMethod()) !== 'post') {
                $uri = current_url(true);
                $id=str_replace('grp=','',$uri->getQuery(['only'=>['grp']]));
                if ($id=='') $id=0;
                $this->data['id']=$id;
                $this->data['usuarios']= model('Modulos\Pagina\Models\Cls_grupos_p')->getUsuariosDeGrupo($id);
                foreach($this->data['usuarios'] as $u=>$unUsuario) $this->data['usuarios'][$u][]=imagen_usr($unUsuario['id_usr'],false);                
                $this->data['usuariosNoEnGrupo']  = model('Modulos\Pagina\Models\Cls_grupos_p')->getUsuariosNoEnGrupo($id); // recuperamos y pasamos a la vista los usuarios que no estáne en el grupo
                
                
                $this->data['grupo']=model('Modulos\Pagina\Models\Cls_grupos_p')->find($id);
                $this->data['VPConf']=config('VstPortal');
                return view('\Modulos\Pagina\vista_GruposModificar',$this->data);
            }else{
            
            }
    }

    
    public function anadirUsuariosAlGrupo(){
        $usuarios = $this->request->getPost('usuarios'); // Obtener la lista de usuarios
        $grp = $this->request->getPost('grp'); // Obtener el ID del grupo
        
      
        $MGrupos = model('Modulos\Pagina\Models\Cls_grupos_p');
        // por cada usuario recibido en usuarios, se añade al grupo
        // foreach($usuarios as $usuario){
        //     $MGrupos->anadirUsuarioAlGrupo($grp, $usuario);
        // }

        $MGrupos->anadirUsuarioAlGrupo($grp, $usuarios);

        return $this->response->setJSON(['mensaje' => 'Los usuarios se agregaron correctamente al grupo.']);
    }
    public function eliminar_usuarios(){
        $respuesta=['exito'=>false, 'mensaje'=>'Error indeterminado retirando usuarios'];
        $lista=json_decode($this->request->getPost('lista'),true);
        if (count($lista)>0){
            $db = \Config\Database::connect();
            if (!$db->table('usuarios_grupos')->where('id IN ('.implode(',',$lista).')')->delete()){
                $respuesta['mensaje']=json_encode($db->error());
            }
            else{
                $respuesta['exito']=true;
                $respuesta['mensaje']='';
            }
        }
        $respuesta['exito']=true;
        return json_encode($respuesta);
    }

}
?>