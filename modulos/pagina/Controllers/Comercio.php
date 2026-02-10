<?php
namespace Modulos\Pagina\Controllers;
use App\Controllers\BaseController;

class Comercio extends BaseController
{
    protected $helpers=['autorizaciones', 'enlaces', 'form', 'Modulos\Vpbasicos\navegacion', 'Modulos\Vpbasicos\formularios'];
    
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
        public function editar($id=0)
        {
    //Vpconf> permiso editar
        if (!tienePermiso([2])) return redirect()->to('login');
    //Vpconf<

            if (strtolower($this->request->getMethod()) !== 'post') {
                $this->data['clasesBeneficiario']=model('Modulos\Pagina\Models\Cls_clases')->todas();
                if ($id==0) {
                    $id=rand(-1000,-1); //Un id temporal para varias creaciones simultáneas
                    $this->data['comercio']=[
                        'id'=>$id,
                        'CIF'=>'',
                        'usuario'=>'',
                        'contrasena'=>'',
                        'nombre'=>'',
                        'direccion'=>'',
                        'movil'=>'',
                        'correo'=>'',
                        'contacto'=>'',
                        'coordenadas'=>'',
                        'logo'=>'',
                        'activo'=>'',
                        'imgLogo'=>base_url('public/assets/imagenes/logos').'/logoDf.png',
                        'clases'=>[]
                    ];
                }
                else{
                    $elComercio=model('Modulos\Pagina\Models\Cls_comercios')->find(intval($id));
                    if (is_null($elComercio)){
                        return redirect('comercios');   
                    }
                    $this->data['comercio']=[
                        'id'=>$id,
                        'CIF'=>$elComercio->CIF,
                        'usuario'=>$elComercio->usuario,
                        'contrasena'=>$elComercio->contrasena,
                        'nombre'=>$elComercio->nombre,
                        'direccion'=>$elComercio->direccion,
                        'movil'=>$elComercio->movil,
                        'correo'=>$elComercio->correo,
                        'contacto'=>$elComercio->contacto,
                        'coordenadas'=>$elComercio->coordenadas,
                        'logo'=>$elComercio->logo,
                        'activo'=>$elComercio->activo,
                        'imgLogo'=>$elComercio->ficheroLogo(),
                        'clases'=>$elComercio->clases
                    ];
                }
            }
            else{
                
            }
                    $this->data['VPConf']=config('VstPortal');
                return view('\Modulos\Pagina\vista_EditarComercio',$this->data);
        }
    public function prelogo($id='0'){
        $target_dir = ROOTPATH . 'public/assets/imagenes/logos/';
        $target_file = $target_dir . 'prelogo'.$id.'.png';
        if (file_exists($target_file)) unlink($target_file);
        move_uploaded_file($_FILES["imagenCortada"]["tmp_name"], $target_file);
        return true;
    }
    public function bloquear($id,$bloquear){
        $resultado=['exito'=>false,'mensaje'=>'Error indeterminado'];
        if (!tienePermiso([3])) return $resultado;
        if (strtolower($this->request->getMethod()) == 'post') {
            $elComercio=model('Modulos\Pagina\Models\Cls_comercios')->find(intval($id));
            if (is_null($elComercio)){
                return json_encode($resultado);   
            }
            $resultado=$elComercio->bloquear($bloquear==1);
            return json_encode($resultado);
        }       
    }  
    public function pago($id){
        $resultado=['exito'=>false,'mensaje'=>'Error indeterminado'];
        if (!tienePermiso([3])) return $resultado;
        if (strtolower($this->request->getMethod()) == 'post') {
            $elComercio=model('Modulos\Pagina\Models\Cls_comercios')->find(intval($id));
            if (is_null($elComercio)){
                $resultado['mensaje']='El comercio no existe';
                return json_encode($resultado);   
            }
            $resultado=$elComercio->pago($this->request->getPost('mes'),$this->request->getPost('ano'),$this->request->getPost('importe'),$this->request->getPost('factura'),$this->request->getPost('notas'));
            return json_encode($resultado);
        }       
    }  
}
?>