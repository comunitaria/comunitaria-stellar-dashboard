<?php
namespace Modulos\Pagina\Controllers;
use App\Controllers\BaseController;

class Comercios extends BaseController
{
    protected $helpers=['autorizaciones', 'enlaces', 'Modulos\Vpbasicos\navegacion', 'Modulos\Vpbasicos\formularios', 'Modulos\Vpbasicos\tabla'];
    
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
            //Vpconf> permiso index
        if (!tienePermiso([2])) return view('no_autorizado');
//Vpconf<

                $this->data['comercios']=[];
                foreach(model('Modulos\Pagina\Models\Cls_comercios')->findAll() as $unComercio){
                    $this->data['comercios'][]=[
                        $unComercio->id,
                        $unComercio->CIF,
                        $unComercio->nombre,
                        $unComercio->usuario,
                        $unComercio->contrasena,
                        $unComercio->direccion,
                        $unComercio->movil,
                        $unComercio->correo,
                        $unComercio->activo,
                    ];
                }

                    $this->data['VPConf']=config('VstPortal');
                return view('\Modulos\Pagina\vista_Comercios',$this->data);
        }
        public function crear(){
            if (!tienePermiso([2])) return view('no_autorizado');
            $respuesta=['exito'=>false, 'mensaje'=>'Error indeterminado creando comercio', 'id'=>''];
            $db = \Config\Database::connect();
            $usuario=$this->request->getPost('ipUsuario');
            $hayBeneficiario=model('Modulos\Pagina\Models\Cls_beneficiarios')->where('usuario = "'. $usuario.'"')->first();
            $hayComercio=model('Modulos\Pagina\Models\Cls_comercios')->where('usuario = "'. $usuario.'"')->first();
            if (!is_null($hayBeneficiario)||!(is_null($hayComercio))){
                $respuesta['mensaje']="El usuario ya existe (".(!is_null($hayBeneficiario)?'como beneficiario':'como comercio').")";
            }
            else{
                $insertado=(new \Modulos\Pagina\Entities\Comercio())->insert([
                    'nombre'=>$this->request->getPost('ipNombre'),
                    'CIF'=>$this->request->getPost('ipCIF'),
                    'usuario'=>$this->request->getPost('ipUsuario'),
                    'contrasena'=>md5($this->request->getPost('ipContrasena')),
                    'direccion'=>$this->request->getPost('ipDirección'),
                    'movil'=>$this->request->getPost('ipMovil'),
                    'correo'=>$this->request->getPost('ipCorreo'),
                    'contacto'=>$this->request->getPost('ipContacto'),
                    'coordenadas'=>$this->request->getPost('ipCoordenadas'),
                    'logo'=>$this->request->getPost('ipLogo'),
                    'activo'=>$this->request->getPost('ipActivo')=='on'?1:0,
                    'clases'=>model('Modulos\Pagina\Models\Cls_clases')->creaYLista($this->request->getPost('ipClases'))
                ]);
                if ($insertado<=0){
                    $respuesta['mensaje']=json_encode($db->error());
                }
                else{
                    $respuesta['exito']=true;
                    $respuesta['mensaje']='';
                    $respuesta['id']=$insertado;
                    if ($this->request->getPost('ipHayPrelogo')=='1'){
                        $prelogo = ROOTPATH . 'public/assets/imagenes/logos/prelogo'.$this->request->getPost('ipId').'.png';
                        $logo = ROOTPATH . 'public/assets/imagenes/logos/logo'.$respuesta['id'].'.png';
                        if (file_exists($prelogo)){
                            rename($prelogo,$logo);
                        }
                    }
                }
            }
            return redirect('comercios');
        }
  
        public function modificar(){
            if (!tienePermiso([2])) return view('no_autorizado');
            $id=intval($this->request->getPost('ipId')??'0');
            if ($id<=0){
                return $this->crear();
            }
            $respuesta=['exito'=>false, 'mensaje'=>'Error indeterminado modificando comercio'];
            $db = \Config\Database::connect();
            $usuario=$this->request->getPost('ipUsuario');
            $hayBeneficiario=model('Modulos\Pagina\Models\Cls_beneficiarios')->where('usuario = "'. $usuario.'"')->first();
            $hayComercio=model('Modulos\Pagina\Models\Cls_comercios')->where('usuario = "'. $usuario.'" AND id<>'.$id)->first();
            if (!is_null($hayBeneficiario)||!(is_null($hayComercio))){
                $respuesta['mensaje']="El usuario ya existe (".(!is_null($hayBeneficiario)?'como beneficiario':'como comercio').")";
            }
            else{
                $unComercio=model('Modulos\Pagina\Models\Cls_comercios')->find($id);
                if (!is_null($unComercio)){
                    $valores=[
                        'nombre'=>$this->request->getPost('ipNombre'),
                        'CIF'=>$this->request->getPost('ipCIF'),
                        'usuario'=>$this->request->getPost('ipUsuario'),
                        'contrasena'=>md5($this->request->getPost('ipContrasena')),
                        'direccion'=>$this->request->getPost('ipDirección'),
                        'movil'=>$this->request->getPost('ipMovil'),
                        'correo'=>$this->request->getPost('ipCorreo'),
                        'contacto'=>$this->request->getPost('ipContacto'),
                        'coordenadas'=>$this->request->getPost('ipCoordenadas'),
                        'activo'=>$this->request->getPost('ipActivo')=='on'?1:0,
                        'logo'=>$this->request->getPost('ipLogo'),
                        'clases'=>model('Modulos\Pagina\Models\Cls_clases')->creaYLista($this->request->getPost('ipClases'))
                    ];
                    if ($this->request->getPost('ipHayPrelogo')=='1'){
                        $prelogo = ROOTPATH . 'public/assets/imagenes/logos/prelogo'.$id.'.png';
                        $logo = ROOTPATH . 'public/assets/imagenes/logos/logo'.$id.'.png';
                        if (file_exists($prelogo)){
                            rename($prelogo,$logo);
                        }
                    }
                    if (($this->request->getPost('Contrasena')!='')&&($unComercio->contrasena!=$this->request->getPost('Contrasena'))){
                        $valores['contrasena']=md5($this->request->getPost('Contraseña'));
                    }
                    if(!model('Modulos\Pagina\Models\Cls_comercios')->find($id)->update($valores)){
                        $respuesta['mensaje']=json_encode($db->error());
                    }
                    else{
                        $respuesta['exito']=true;
                        $respuesta['mensaje']='';
                    }
                }
                else{
                    $respuesta['mensaje']='El usuario no existe';
                }
            }
            return redirect('comercios');
        }
    
        public function eliminar(){
                $respuesta=['exito'=>false, 'mensaje'=>'Error indeterminado eliminando comercios'];
                $lista=json_decode($this->request->getPost('lista'),true);
                if (count($lista)>0){
                    $db = \Config\Database::connect();
                    if (!$db->table('comercios')->where('id IN ('.implode(',',$lista).')')->delete()){
                        $respuesta['mensaje']=json_encode($db->error());
                    }
                    else{
                       $respuesta['exito']=true;
                        $respuesta['mensaje']='';
                    }
                }
                return json_encode($respuesta);
          
            }
      
}
?>