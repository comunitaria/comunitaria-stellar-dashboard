<?php
namespace Modulos\Pagina\Controllers;
use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use Modulos\Pagina\Libraries\Custodia;
use Modulos\Pagina\Entities\Cuenta;

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
    // Devuelve el monedero cifrado (keystore) del usuario autentificado, o '' si no tiene.
    // El blob es opaco para el servidor: se cifra/descifra en el cliente con la contraseña.
    public function consultaKeystore(){
        $respuesta=['codigo'=>400,'tipo'=>'Error','mensaje'=>'Error indeterminado'];
        $res=$this->autentifica();
        if ($res['tipo']=='exito'){
            $resUsuario=$this->usuarioCorrecto($res['usuario']);
            if ($resUsuario!='OK'){
                $respuesta['mensaje']=$resUsuario;
                return $this->respond($respuesta, 400);
            }
            $usuario=$this->esBeneficiario?$this->beneficiario:$this->comercio;
            return $this->respond(['keystore'=>$usuario->keystore??''], 200);
        }
        else{
            return $this->respond($res, 401);
        }
    }
    // Guarda el monedero cifrado (keystore) del usuario autentificado.
    public function guardarKeystore(){
        $respuesta=['codigo'=>400,'tipo'=>'Error','mensaje'=>'Error indeterminado'];
        $res=$this->autentifica();
        if ($res['tipo']=='exito'){
            $resUsuario=$this->usuarioCorrecto($res['usuario']);
            if ($resUsuario!='OK'){
                $respuesta['mensaje']=$resUsuario;
                return $this->respond($respuesta, 400);
            }
            $usuario=$this->esBeneficiario?$this->beneficiario:$this->comercio;
            $blob=$this->request->getPost('keystore');
            if (is_null($blob)){
                $cuerpo=$this->request->getJSON(true);
                $blob=is_array($cuerpo)?($cuerpo['keystore']??null):null;
            }
            if (is_null($blob)||$blob===''){
                $respuesta['mensaje']='Falta el keystore';
                return $this->respond($respuesta, 400);
            }
            if (strlen($blob)>20000){
                $respuesta['mensaje']='Keystore demasiado grande';
                return $this->respond($respuesta, 400);
            }
            $usuario->keystore=$blob;
            $usuario->miModelo()->update($usuario->id,['keystore'=>$blob]);
            return $this->respond(['tipo'=>'exito','mensaje'=>'Keystore guardado'], 200);
        }
        else{
            return $this->respond($res, 401);
        }
    }
    // Devuelve (creándolo si hace falta) el token de consulta de saldo del comercio.
    // Con ese token, los empleados acceden a /saldo/<token> (solo lectura), sin la
    // clave privada. Solo aplica a comercios.
    public function tokenConsulta(){
        $respuesta=['codigo'=>400,'tipo'=>'Error','mensaje'=>'Error indeterminado'];
        $res=$this->autentifica();
        if ($res['tipo']=='exito'){
            $resUsuario=$this->usuarioCorrecto($res['usuario']);
            if ($resUsuario!='OK'){
                $respuesta['mensaje']=$resUsuario;
                return $this->respond($respuesta, 400);
            }
            if ($this->esBeneficiario){
                $respuesta['mensaje']='Solo los comercios tienen consulta de saldo para empleados';
                return $this->respond($respuesta, 400);
            }
            $comercio=$this->comercio;
            $token=$comercio->tokenConsulta;
            if (is_null($token)||$token===''){
                $token=bin2hex(random_bytes(16));
                $comercio->tokenConsulta=$token;
                $comercio->miModelo()->update($comercio->id,['tokenConsulta'=>$token]);
            }
            return $this->respond([
                'tipo'=>'exito',
                'token'=>$token,
                'url'=>base_url('saldo/'.$token)
            ], 200);
        }
        else{
            return $this->respond($res, 401);
        }
    }
    // Lee un parámetro de POST (form) o del cuerpo JSON.
    private function param($nombre){
        $v=$this->request->getPost($nombre);
        if (is_null($v)){
            $j=$this->request->getJSON(true);
            $v=is_array($j)?($j[$nombre]??null):null;
        }
        return $v;
    }
    private function respMonedero($cuenta){
        return [
            'cripto'=>getenv('moneda.nombre'),
            'emisora'=>'G'.getenv('moneda.emisora.publica'),
            'distribuidora'=>'G'.getenv('moneda.distribuidora.publica'),
            'cuenta'=>$cuenta->descripcion(),
        ];
    }
    // ---- CUSTODIA: monedero, pago, canje y saldo del lado servidor ----

    // Crea (o devuelve) el monedero custodiado del usuario autentificado.
    // Si tenía una cuenta legacy con saldo, lo migra (reusa la lógica existente).
    public function crearMonedero(){
        $respuesta=['codigo'=>400,'tipo'=>'Error','mensaje'=>'Error indeterminado'];
        $res=$this->autentifica();
        if ($res['tipo']!='exito') return $this->respond($res,401);
        $resU=$this->usuarioCorrecto($res['usuario']);
        if ($resU!='OK'){ $respuesta['mensaje']=$resU; return $this->respond($respuesta,400); }
        $usuario=$this->esBeneficiario?$this->beneficiario:$this->comercio;

        // ¿Ya tiene monedero custodiado y creado? Lo devolvemos.
        $cuentaActual=$usuario->miCuenta();
        if (!is_null($cuentaActual) && $cuentaActual->tieneCustodia()){
            $cuentaActual->actualizaEstado();
            if ($cuentaActual->creada==1){
                return $this->respond($this->respMonedero($cuentaActual),200);
            }
        }

        // ADOPCIÓN (migración sin rotar): si el dispositivo manda el secreto de su
        // wallet actual y coincide con la cuenta legacy ya registrada, lo custodiamos
        // en esa MISMA cuenta. Misma dirección, sin re-emitir, historial intacto.
        $secretoAdoptar=$this->param('secreto');
        if (!is_null($secretoAdoptar) && $secretoAdoptar!=='' && !is_null($cuentaActual) && !$cuentaActual->tieneCustodia()){
            $custodia=new Custodia();
            $sec=$custodia->normalizaSecreto($secretoAdoptar);
            $pubDerivada='';
            try{ $pubDerivada=$custodia->publicaDeSecreto($sec); }catch(\Exception $e){ $pubDerivada=''; }
            if ($pubDerivada!=='' && $pubDerivada===$cuentaActual->clave){
                $cuentaActual->guardaSecreto($sec);
                $cuentaActual->actualizaEstado(); // persiste (incluye secretoCifrado) y refresca estado
                return $this->respond($this->respMonedero($cuentaActual),200);
            }
        }

        // Cuenta legacy con saldo: lo acumulamos para re-emitirlo a la nueva y bloqueamos la vieja.
        if (!is_null($cuentaActual)){
            $cuentaActual->actualizaEstado();
            if ($cuentaActual->creada==1 && floatval($cuentaActual->balanceILLA)>0){
                $usuario->transferirILLA+=$cuentaActual->balanceILLA;
                $usuario->miModelo()->save($usuario);
                $cuentaActual->bloqueate();
            }
        }

        // Generar el par custodiado y registrar la cuenta.
        $par=(new Custodia())->generarPar();
        $cuenta=new Cuenta();
        $cuenta->clave=$par['publica'];
        $cuenta->guardaSecreto($par['privada']);
        try{
            $cuenta->id=model('Modulos\Pagina\Models\Cls_cuentas')->insert($cuenta);
        }catch(\Exception $e){
            $respuesta['mensaje']='No se pudo crear el monedero';
            return $this->respond($respuesta,400);
        }
        $usuario->registraCuenta($cuenta);

        // Fondear (emisora) + trustline (firma del usuario) + autorizar.
        $cuenta->create();
        $cuenta->estableceTrustline();
        $cuenta->autorizate();

        // Si había saldo a migrar y la cuenta quedó autorizada, re-emitirlo desde distribuidora.
        if ($cuenta->autorizada==1 && $usuario->transferirILLA){
            $cuenta->transferirCripto($usuario->transferirILLA);
            $usuario->transferirILLA=0;
            $usuario->miModelo()->save($usuario);
        }

        return $this->respond($this->respMonedero($cuenta),200);
    }

    // Pago de un beneficiario/usuario a un comercio. El servidor firma con la clave custodiada.
    public function pagar(){
        $respuesta=['codigo'=>400,'tipo'=>'Error','mensaje'=>'Error indeterminado'];
        $res=$this->autentifica();
        if ($res['tipo']!='exito') return $this->respond($res,401);
        $resU=$this->usuarioCorrecto($res['usuario']);
        if ($resU!='OK'){ $respuesta['mensaje']=$resU; return $this->respond($respuesta,400); }
        $usuario=$this->esBeneficiario?$this->beneficiario:$this->comercio;

        $comercioId=$this->param('comercio');
        $cantidad=$this->param('cantidad');
        if (!is_numeric($cantidad)||floatval($cantidad)<=0){
            $respuesta['mensaje']='Importe inválido'; return $this->respond($respuesta,400);
        }
        $comercio=model('Modulos\Pagina\Models\Cls_comercios')->find($comercioId);
        if (is_null($comercio)){ $respuesta['mensaje']='Comercio no encontrado'; return $this->respond($respuesta,400); }
        $destino=$comercio->miCuenta();
        if (is_null($destino)){ $respuesta['mensaje']='El comercio no tiene monedero'; return $this->respond($respuesta,400); }
        $origen=$usuario->miCuenta();
        if (is_null($origen)||!$origen->tieneCustodia()){
            $respuesta['mensaje']='Tu monedero no está custodiado'; return $this->respond($respuesta,400);
        }
        $r=$origen->paga($destino->clave,floatval($cantidad),'Compra');
        if ($r['exito']){
            return $this->respond(['tipo'=>'exito','mensaje'=>'Pago realizado','cuenta'=>$origen->descripcion()],200);
        }
        $respuesta['mensaje']=$r['mensaje'];
        return $this->respond($respuesta,400);
    }

    // Canje: el comercio transfiere ILLA a la distribuidora. Firma server-side.
    public function canjear(){
        $respuesta=['codigo'=>400,'tipo'=>'Error','mensaje'=>'Error indeterminado'];
        $res=$this->autentifica();
        if ($res['tipo']!='exito') return $this->respond($res,401);
        $resU=$this->usuarioCorrecto($res['usuario']);
        if ($resU!='OK'){ $respuesta['mensaje']=$resU; return $this->respond($respuesta,400); }
        if ($this->esBeneficiario){ $respuesta['mensaje']='Solo los comercios pueden canjear'; return $this->respond($respuesta,400); }
        $comercio=$this->comercio;
        $cantidad=$this->param('cantidad');
        if (!is_numeric($cantidad)||floatval($cantidad)<=0){
            $respuesta['mensaje']='Importe inválido'; return $this->respond($respuesta,400);
        }
        $cuenta=$comercio->miCuenta();
        if (is_null($cuenta)||!$cuenta->tieneCustodia()){
            $respuesta['mensaje']='El monedero no está custodiado'; return $this->respond($respuesta,400);
        }
        $r=$cuenta->paga(getenv('moneda.distribuidora.publica'),floatval($cantidad),'Reintegro');
        if ($r['exito']){
            return $this->respond(['tipo'=>'exito','mensaje'=>'Canje realizado','cuenta'=>$cuenta->descripcion()],200);
        }
        $respuesta['mensaje']=$r['mensaje'];
        return $this->respond($respuesta,400);
    }

    // Saldo en vivo (Horizon) del monedero del usuario autentificado.
    public function saldoUsuario(){
        $respuesta=['codigo'=>400,'tipo'=>'Error','mensaje'=>'Error indeterminado'];
        $res=$this->autentifica();
        if ($res['tipo']!='exito') return $this->respond($res,401);
        $resU=$this->usuarioCorrecto($res['usuario']);
        if ($resU!='OK'){ $respuesta['mensaje']=$resU; return $this->respond($respuesta,400); }
        $usuario=$this->esBeneficiario?$this->beneficiario:$this->comercio;
        $cuenta=$usuario->miCuenta();
        if (is_null($cuenta)){
            return $this->respond(['tipo'=>'exito','cuenta'=>null],200);
        }
        $cuenta->actualizaEstado();
        return $this->respond(['tipo'=>'exito','cuenta'=>$cuenta->descripcion()],200);
    }

    // Resuelve claves públicas (con G) al nombre de su titular, para mostrar la
    // contraparte en la lista de movimientos de la app. Beneficiarios: solo nombre.
    // Cuentas del sistema: 'ONG' (distribuidora) / 'Comunitaria' (emisora).
    public function nombres(){
        $res=$this->autentifica();
        if ($res['tipo']!='exito') return $this->respond($res,401);
        $claves=$this->param('claves');
        if (!is_array($claves)) $claves=[];
        $distrib=getenv('moneda.distribuidora.publica');
        $emis=getenv('moneda.emisora.publica');
        $resp=[];
        $i=0;
        foreach($claves as $claveConG){
            if (++$i>200) break;
            if (!is_string($claveConG)) continue;
            $sinG=(strlen($claveConG)==56)?substr($claveConG,1):$claveConG;
            if ($sinG===$distrib){ $resp[$claveConG]='ONG'; continue; }
            if ($sinG===$emis){ $resp[$claveConG]='Comunitaria'; continue; }
            $nombre='';
            $cuenta=model('Modulos\Pagina\Models\Cls_cuentas')->where('clave',$sinG)->first();
            if (!is_null($cuenta)){
                $b=model('Modulos\Pagina\Models\Cls_beneficiarios')->where('cuenta',$cuenta->id)->first();
                if (!is_null($b)){
                    $nombre=$b->nombre;
                }
                else{
                    $c=model('Modulos\Pagina\Models\Cls_comercios')->where('cuenta',$cuenta->id)->first();
                    if (!is_null($c)){ $nombre=$c->nombre; }
                }
            }
            $resp[$claveConG]=$nombre;
        }
        return $this->respond($resp,200);
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