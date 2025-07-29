<?php
namespace Modulos\Pagina\Controllers;
use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class Api extends \Modulos\Vpbasicos\Controllers\Apibase
{
    private $jwt_secreto;
    private $expiracion;
    private $emisor;
    private $audiencia;
    private $objeto;
    private $beneficiario;
    private $comercio;
    private $esBeneficiario;
  
    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ){
        // Do Not Edit This Line
        
        parent::initController($request, $response, $logger);
        $this->jwt_secreto=getenv('api.JWT_secreto');
        $this->expiracion=getenv('api.expiracion_s');
        $this->emisor=getenv('api.emisor');
        $this->audiencia=getenv('api.audiencia');
        $this->objeto=getenv('api.objeto');
        $this->esteUsuario='';
        $this->esBeneficiario=false;
    }
    function contrasenaCorrecta($usuario,$contrasena){
        $respuesta=false;
        if (!is_null($this->beneficiario)){
            $respuesta=($this->beneficiario->activo=='1')&&(md5($contrasena)==$this->beneficiario->contrasena);
        }
        if (!is_null($this->comercio)){
            $respuesta=($this->comercio->activo=='1')&&(md5($contrasena)==$this->comercio->contrasena);
        }
        return $respuesta;
    }
    function usuarioCorrecto($usuario){
        $this->esteUsuario=$usuario;
        $this->beneficiario=model('Modulos\Pagina\Models\Cls_beneficiarios')->where('usuario="'.$usuario.'"')->first();
        $this->comercio=model('Modulos\Pagina\Models\Cls_comercios')->where('usuario="'.$usuario.'"')->first();
        $respuesta='Ningun usuario tiene ese nombre';
        if (!is_null($this->beneficiario)){
            $this->esBeneficiario=true;
            if ($this->beneficiario->activo!='1'){
                $respuesta='La cuenta de ese beneficiario ha sido desactivada';
            }
            else{
                $respuesta='OK';
            }
        }
        if (!is_null($this->comercio)){
            $this->esBeneficiario=false;
            if ($this->comercio->activo!='1'){
                $respuesta='La cuenta de ese beneficiario ha sido desactivada';
            }
            else{
                $respuesta='OK';
            }
        }
        return $respuesta;
    }
    public function infoUsuario($usuario){
        if ($this->esteUsuario!=$usuario){
            $this->usuarioCorrecto($usuario);
        }
        if ($this->esteUsuario==$usuario){
            return ['clase'=>$this->esBeneficiario?'beneficiario':'comercio'];
        }
        return [];
    }
  
    public function index()
    {
       return $this->respond(['mensaje'=>'API Comunitaria V1.0'], 200);
    }

    public function leerCuenta($clavePublica){
        $respuesta=['codigo'=>400,'tipo'=>'Error','mensaje'=>'Error indeterminado'];
        $res=$this->autentifica();
        if ($res['tipo']=='exito'){
            //Si viene con 'G', se la quito: GBOAKWTFL7IL4EORXRAMIVNCCK3ECZOKIJS7Y7WO53FHPAD44PGSRX3P
            if (strlen($clavePublica)==56) $clavePublica=substr($clavePublica,1);
            $clavePublica=strtoupper($clavePublica);
            if (!preg_match('/^[0-9A-Z]{55}$/',$clavePublica)){
                $respuesta['mensaje']='Debe especificar una clave pública como parámetro';
                return $this->respond($respuesta, 400);        
            }
            else{
                $resUsuario=$this->usuarioCorrecto($res['usuario']);
                if ($resUsuario!='OK'){
                    return $this->respond($resUsuario, 400);        
                }
                else{
                    $cuenta=null;
                    $usuario=null;
                    if ($this->esBeneficiario){
                        $usuario=$this->beneficiario;
                    }
                    else{
                        $usuario=$this->comercio;
                    }
                    $cuenta=$usuario->miCuenta();
                    if (is_null($cuenta)){
                        $respuesta['mensaje']='El usuario no tiene cuenta asignada';
                        return $this->respond($respuesta, 400);            
                    }
                    if (($cuenta->clave!=$clavePublica)){
                        $respuesta['mensaje']='El usuario solo puede consultar su cuenta';
                        return $this->respond($respuesta, 400);            
                    }
                    $usuario=($this->esBeneficiario?$this->beneficiario:$this->comercio)->info;
                    $usuario['clase']=$this->esBeneficiario?'beneficiario':'comercio';
                    $cuenta->actualizaEstado();
                    return $this->respond([
                            'cripto'=>getenv('moneda.nombre'),
                            'emisora'=>'G'.getenv('moneda.emisora.publica'),
                            'distribuidora'=>'G'.getenv('moneda.distribuidora.publica'),
                            'cuenta'=>$cuenta->descripcion(),
                            'usuario'=>$usuario
                        ], 200);
                }
            }
        }
        else{
            return $this->respond($res, 401);
        }
    } 
    public function registrarCuenta($clavePublica){
        $respuesta=['codigo'=>400,'tipo'=>'Error','mensaje'=>'Error indeterminado'];
        $res=$this->autentifica();
        if ($res['tipo']=='exito'){
            //Si viene con 'G', se la quito: GBOAKWTFL7IL4EORXRAMIVNCCK3ECZOKIJS7Y7WO53FHPAD44PGSRX3P
            if (strlen($clavePublica)==56) $clavePublica=substr($clavePublica,1);
            $clavePublica=strtoupper($clavePublica);
            if (!preg_match('/^[0-9A-Z]{55}$/',$clavePublica)){
                $respuesta['mensaje']='Debe especificar una clave pública como parámetro';
                return $this->respond($respuesta, 400);        
            }
            else{
                $resUsuario=$this->usuarioCorrecto($res['usuario']);
                if ($resUsuario!='OK'){
                    return $this->respond($resUsuario, 400);        
                }
                else{
                    $cuenta=null;
                    $usuario=null;
                    if ($this->esBeneficiario){
                        $usuario=$this->beneficiario;
                    }
                    else{
                        $usuario=$this->comercio;
                    }
                    $cuenta=$usuario->miCuenta();
                    $reintegrarILLA=0;
                    if (!is_null($cuenta)&&($cuenta->clave!=$clavePublica)){
                        $cuenta->actualizaEstado();
                        if ($cuenta->creada==1){
                            $usuario->transferirILLA+=$cuenta->balanceILLA;
                            $usuario->miModelo()->save($usuario);
                            $cuenta->bloqueate();
                        }
                    }
                    
                    $cuentaIndicada=model('Modulos\Pagina\Models\Cls_cuentas')->where('clave="'.$clavePublica.'"')->first();
                    if (is_null($cuentaIndicada)){
                        $cuentaIndicada=new \Modulos\Pagina\Entities\Cuenta();
                        $cuentaIndicada->clave=$clavePublica;
                        try{
                            $cuentaIndicada->id=model('Modulos\Pagina\Models\Cls_cuentas')->insert($cuentaIndicada);
                        }
                        catch(\Exception $e){};
                    }
                    else{
                        $cuentaIndicada->actualizaEstado();
                    }
                    
                    $usuario->registraCuenta($cuentaIndicada);
                    if ($cuentaIndicada->creada==0){
                        $cuentaIndicada->create();
                    }
                    else{
                        $cuentaIndicada->aseguraXLM();
                    }
                    return $this->respond([
                            'cripto'=>getenv('moneda.nombre'),
                            'emisora'=>'G'.getenv('moneda.emisora.publica'),
                            'distribuidora'=>'G'.getenv('moneda.distribuidora.publica'),
                            'cuenta'=>$cuentaIndicada->descripcion()
                        ], 200);
                }
            }
        }
        else{
            return $this->respond($res, 401);
        }
    } 
    public function autorizarCuenta($clavePublica){
        $respuesta=['codigo'=>400,'tipo'=>'Error','mensaje'=>'Error indeterminado'];
        $res=$this->autentifica();
        if ($res['tipo']=='exito'){
            //Si viene con 'G', se la quito: GBOAKWTFL7IL4EORXRAMIVNCCK3ECZOKIJS7Y7WO53FHPAD44PGSRX3P
            if (strlen($clavePublica)==56) $clavePublica=substr($clavePublica,1);
            $clavePublica=strtoupper($clavePublica);
            if (!preg_match('/^[0-9A-Z]{55}$/',$clavePublica)){
                $respuesta['mensaje']='Debe especificar una clave pública como parámetro';
                return $this->respond($respuesta, 400);        
            }
            else{
                $resUsuario=$this->usuarioCorrecto($res['usuario']);
                if ($resUsuario!='OK'){
                    return $this->respond($resUsuario, 400);        
                }
                else{
                    $cuenta=null;
                    $usuario=null;
                    $activo=false;
                    if ($this->esBeneficiario){
                        $usuario=$this->beneficiario;
                    }
                    else{
                        $usuario=$this->comercio;
                    }
                    $cuenta=$usuario->miCuenta();
                    $activo=($usuario->activo==1)&&($usuario->bloqueado==0);
                    if (!is_null($cuenta)){
                        if ($activo){
                            if($cuenta->clave==$clavePublica){                    
                                $cuenta->actualizaEstado();
                                if ($cuenta->creada==1){
                                    $res=$cuenta->autorizate();                           
                                    if ($cuenta->autorizada==1){
                                        if ($usuario->transferirILLA){
                                            $cuenta->transferirCripto($usuario->transferirILLA);
                                            $usuario->transferirILLA=0;
                                            $usuario->miModelo()->save($usuario);
                                        }
                                        return $this->respond([
                                                    'cripto'=>getenv('moneda.nombre'),
                                                    'emisora'=>'G'.getenv('moneda.emisora.publica'),
                                                    'distribuidora'=>'G'.getenv('moneda.distribuidora.publica'),
                                                    'cuenta'=>$cuenta->descripcion()
                                                ], 200);
                                    }
                                    else{
                                        $respuesta['mensaje']=$res['mensaje'];
                                        return $this->respond($respuesta, 400);        
                                    }
                                }
                                else{
                                    $respuesta['mensaje']='La cuenta no existe';
                                    return $this->respond($respuesta, 400);        
                                }
                            }
                            else{
                                $respuesta['mensaje']='Solo el propietario puede solicitar autorización';
                                return $this->respond($respuesta, 400);        
                            }
                        }
                        else{
                            $respuesta['mensaje']='El usuario esta bloqueado';
                            return $this->respond($respuesta, 400);        
                        }
                    }
                    else{
                        $respuesta['mensaje']='La cuenta no existe';
                        return $this->respond($respuesta, 400);        
                    }
                    
                }
            }
        }
        else{
            return $this->respond($res, 401);
        }
    } 
    public function infoComercio($id){
        $respuesta=['codigo'=>400,'tipo'=>'Error','mensaje'=>'Error indeterminado'];
        $res=$this->autentifica();
        if ($res['tipo']=='exito'){
            $this->comercio=model('Modulos\Pagina\Models\Cls_comercios')->find($id);
            if (!is_null($this->comercio)){
                return $this->respond($this->comercio->info,200);
            }
            else{
                $respuesta['mensaje']='El comercio no existe';
                return $this->respond($respuesta, 400);        
            }
        }
        else{
            return $this->respond($res, 401);
        }
    }
    public function comercios(){
        $respuesta=['codigo'=>400,'tipo'=>'Error','mensaje'=>'Error indeterminado'];
        $res=$this->autentifica();
        if ($res['tipo']=='exito'){
            $this->usuarioCorrecto($res['usuario']);
            $clase=$this->esBeneficiario?($this->beneficiario->autorizado):['clase'=>0, 'texto'=>\Modulos\Pagina\TODOS_LOS_COMERCIOS];
            $lista=model('Modulos\Pagina\Models\Cls_comercios')->deClase($clase['clase']);
            $respuesta=['clase'=>$clase['texto'],'listado'=>[]];
            foreach($lista as $unComercio){
               $respuesta['listado'][]=['id'=>$unComercio->id, 'nombre'=>$unComercio->nombre,'clave'=>$unComercio->clave,'hash'=>$unComercio->hashDatos,'info'=>str_replace('comercios','comercio/'.$unComercio->id,current_url())]; 
            }
            return $this->respond($respuesta,200);
        }
        else{
            return $this->respond($res, 401);
        }
          
    }
    public function consultaUsuario(){
        $respuesta=['codigo'=>400,'tipo'=>'Error','mensaje'=>'Error indeterminado'];
        $res=$this->autentifica();
        if ($res['tipo']=='exito'){
            $this->usuarioCorrecto($res['usuario']);
            $respuesta=($this->esBeneficiario?$this->beneficiario:$this->comercio)->info;
            $respuesta['clase']=($this->esBeneficiario?'beneficiario':'comercio');
            $lista=model('Modulos\Pagina\Models\Cls_comercios')->deClase($this->esBeneficiario?($this->beneficiario->clase??0):0);
            $respuesta['comercios']=[];
            foreach($lista as $unComercio){
               $respuesta['comercios'][]=['id'=>$unComercio->id, 'nombre'=>$unComercio->nombre,'clave'=>$unComercio->clave,'hash'=>$unComercio->hashDatos,'info'=>str_replace('usuario','comercio/'.$unComercio->id,current_url())]; 
            }
            $respuesta['moneda']=[
                'red'=>getenv('moneda.red'),
                'nodo'=>getenv('moneda.nodo.'.getenv('moneda.red')),
                'cripto'=>getenv('moneda.nombre'),
                'emisora'=>'G'.getenv('moneda.emisora.publica'),
                'distribuidora'=>'G'.getenv('moneda.distribuidora.publica'),
                ];
            return $this->respond($respuesta,200);
        }
        else{
            return $this->respond($res, 401);
        }
          
    }
}
?>