<?php
namespace Modulos\Pagina\Controllers;
use App\Controllers\BaseController;

class Beneficiarios extends BaseController
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

            if (strtolower($this->request->getMethod()) !== 'post') {
                $this->data['clases']=model('Modulos\Pagina\Models\Cls_clases')->todas();
                $this->data['beneficiarios']=[];
                foreach(model('Modulos\Pagina\Models\Cls_beneficiarios')->activos() as $unBeneficiario){
                    $this->data['beneficiarios'][]=[
                        $unBeneficiario->id,
                        $unBeneficiario->apellidos,
                        $unBeneficiario->nombre,
                        $unBeneficiario->usuario,
                        $unBeneficiario->contrasena,
                        $this->data['clases'][$unBeneficiario->clase],
                        $unBeneficiario->direccion,
                        $unBeneficiario->movil,
                        $unBeneficiario->correo,
                        $unBeneficiario->activo,
                    ];
                }
                
            }
            else{
    
            }
            $this->data['VPConf']=config('VstPortal');
            return view('\Modulos\Pagina\vista_Beneficiarios',$this->data);
        }

        public function crear(){
            $respuesta=['exito'=>false, 'mensaje'=>'Error indeterminado creando beneficiario', 'id'=>''];
            $db = \Config\Database::connect();
            $usuario=$this->request->getPost('Usuario');
            $hayBeneficiario=model('Modulos\Pagina\Models\Cls_beneficiarios')->where('usuario = "'. $usuario.'"')->first();
            $hayComercio=model('Modulos\Pagina\Models\Cls_comercios')->where('usuario = "'. $usuario.'"')->first();
            if (!is_null($hayBeneficiario)||!(is_null($hayComercio))){
                $respuesta['mensaje']="El usuario ya existe (".(!is_null($hayBeneficiario)?'como beneficiario':'como comercio').")";
            }
            else{
                if (!$db->table('beneficiarios')->ignore(true)->insert([
                    'nombre'=>$this->request->getPost('Nombre'),
                    'apellidos'=>$this->request->getPost('Apellidos'),
                    'usuario'=>$this->request->getPost('Usuario'),
                    'contrasena'=>md5($this->request->getPost('Contraseña')),
                    'direccion'=>$this->request->getPost('Dirección'),
                    'clase'=>model('Modulos\Pagina\Models\Cls_clases')->idDe($this->request->getPost('Autorizado_en')),
                    'movil'=>$this->request->getPost('Móvil'),
                    'correo'=>$this->request->getPost('Correo'),
                    'activo'=>$this->request->getPost('Activado'),
                ])){
                    $respuesta['mensaje']=json_encode($db->error());
                }
                else{
                    $respuesta['exito']=true;
                    $respuesta['mensaje']='';
                    $respuesta['id']=$db->insertID();
                }
            }
            return json_encode($respuesta);
        }
  
        public function modificar(){
            $respuesta=['exito'=>false, 'mensaje'=>'Error indeterminado modificando beneficiario'];
            $db = \Config\Database::connect();
            $usuario=$this->request->getPost('Usuario');
            $hayBeneficiario=model('Modulos\Pagina\Models\Cls_beneficiarios')->where('usuario = "'. $usuario.'" AND id <> '. $this->request->getPost('Id'))->first();
            $hayComercio=model('Modulos\Pagina\Models\Cls_comercios')->where('usuario = "'. $usuario.'" ')->first();
            if (!is_null($hayBeneficiario)||!(is_null($hayComercio))){
                $respuesta['mensaje']="El usuario ya existe (".(!is_null($hayBeneficiario)?'como beneficiario':'como comercio').")";
            }
            else{
                $unBeneficiario=model('Modulos\Pagina\Models\Cls_beneficiarios')->where('id = '. $this->request->getPost('Id'))->first();
                if (!is_null($unBeneficiario)){
                    $valores=[
                        'nombre'=>$this->request->getPost('Nombre'),
                        'apellidos'=>$this->request->getPost('Apellidos'),
                        'usuario'=>$this->request->getPost('Usuario'),
                        'direccion'=>$this->request->getPost('Dirección'),
                        'clase'=>model('Modulos\Pagina\Models\Cls_clases')->idDe($this->request->getPost('Autorizado_en')),
                        'movil'=>$this->request->getPost('Móvil'),
                        'correo'=>$this->request->getPost('Correo'),
                        'activo'=>$this->request->getPost('Activado'),
                    ];
                    if (($this->request->getPost('Contraseña')!='')&&($unBeneficiario->contrasena!=$this->request->getPost('Contraseña'))){
                        $valores['contrasena']=md5($this->request->getPost('Contraseña'));
                    }
                    if(!$db->table('beneficiarios')->where('id = '. $this->request->getPost('Id'))->update($valores)){
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
            return json_encode($respuesta);
        }
    
        public function eliminar(){
                $respuesta=['exito'=>false, 'mensaje'=>'Error indeterminado eliminando beneficiarios'];
                $lista=json_decode($this->request->getPost('lista'),true);
                if (count($lista)>0){
                    $db = \Config\Database::connect();
                    if (!$db->table('beneficiarios')->where('id IN ('.implode(',',$lista).')')->delete()){
                        $respuesta['mensaje']=json_encode($db->error());
                    }
                    else{
                       $respuesta['exito']=true;
                        $respuesta['mensaje']='';
                    }
                }
                return json_encode($respuesta);
          
            }
        public function informacion($id){
            if (tienePermiso([2,4])){
                $beneficiario=model('Modulos\Pagina\Models\Cls_beneficiarios')->find($id);
                if (!is_null($beneficiario)){
                    $clase=model('Modulos\Pagina\Models\Cls_clases')->find($beneficiario->clase);
                    $cuenta=model('Modulos\Pagina\Models\Cls_cuentas')->find($beneficiario->cuenta);
                    $tieneClase=!is_null($clase);
                    $tieneCuenta=!is_null($cuenta);
                    if ($tieneCuenta){
                        $cuenta->actualizaEstado();
                    }
                    return json_encode([
                        'id'=>$id,
                        'nombre'=>$beneficiario->nombre,
                        'apellidos'=>$beneficiario->apellidos,
                        'clase'=>$tieneClase?['id'=>$beneficiario->clase,'nombre'=>$clase['clase']]:['id'=>0,'nombre'=>\Modulos\Pagina\TODOS_LOS_COMERCIOS],
                        'balances'=>['ILLA'=>$tieneCuenta?$cuenta->balanceILLA:0,'XLM'=>$tieneCuenta?$cuenta->balanceXLM:0, 'total'=>$beneficiario->recibidoILLA],
                        'alta'=>strtotime($beneficiario->creado),
                        'estadoMonedero'=>$tieneCuenta?($cuenta->situacion):0,
                        'cuenta'=>!$tieneCuenta?'No creada':('G'.$cuenta->clave),
                        'bloqueado'=>$beneficiario->bloqueado=='1',
                        'usuario'=>$beneficiario->usuario,
                        'correo'=>$beneficiario->correo
                                        ]);
                }
            }
        }
        public function transferir($id,$cantidad){
            $respuesta=['exito'=>false,'mensaje'=>'Transferencia imposible'];
            if (tienePermiso([2,4])){
                $beneficiario=model('Modulos\Pagina\Models\Cls_beneficiarios')->find($id);
                if (!is_null($beneficiario)){
                    $cuenta=model('Modulos\Pagina\Models\Cls_cuentas')->find($beneficiario->cuenta);
                    $tieneCuenta=!is_null($cuenta);
                    if ($tieneCuenta){
                        $respuesta=$cuenta->transferirCripto($cantidad);
                        if ($respuesta['exito']){
                            $beneficiario->recibidoILLA+=$cantidad;
                            model('Modulos\Pagina\Models\Cls_beneficiarios')->save($beneficiario);
                        }
                    }
                    else {
                        $respuesta['mensaje']='Error: el monedero no está disponible';
                    }
                }
                $respuesta['mensaje']='Error: el usuario no existe';
            }
            return json_encode($respuesta);
        }
        public function bloquear($id,$bloquear){
            $resultado=['exito'=>false,'mensaje'=>'Error indeterminado'];
            if (!tienePermiso([3])) return $resultado;
            if (strtolower($this->request->getMethod()) == 'post') {
                $elBeneficiario=model('Modulos\Pagina\Models\Cls_beneficiarios')->find(intval($id));
                if (is_null($elBeneficiario)){
                    $resultado['mensaje']='El beneficiario no existe';
                    return json_encode($resultado);   
                }
                $resultado=$elBeneficiario->bloquear($bloquear==1);
                return json_encode($resultado);
            }       
        }  
        
}
?>