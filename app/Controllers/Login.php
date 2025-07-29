<?php
namespace App\Controllers;
define('CLAVE_TOKEN','kas2iwsnxasakjslD@#Sakn3fbSDFcsa');
define('S_LIMITE_RESET_CONTRASENA',60*60);
class Login extends BaseController{
    protected $helpers=['autorizaciones', 'form'];
    // public function __construct()       {
    //     parent::__construct();
    //     $this->load->helper(array('form', 'url'));
    //     $this->load->library(array('session', 'unidades_lib', 'funciones'));   
    //     $this->load->database();     
	// 	$this->config->load('ui',TRUE);
		
    // }
    
    /**********************************************************/
    
    public function index()    {
      
        $validation = \Config\Services::validation();
		$login_error = '';           
        $result = '';
        $data=[];
        $data['VPConf']=config('VstPortal');
    
        if (strtolower($this->request->getMethod()) !== 'post') {
            $data['Login_error'] = '';
    
            return view('login', $data);    
        }
        $validation->setRules(
            [
                'txtUsuario' => [
                    'label'  => 'txtUsuario',
                    'rules'  => 'required|is_not_unique[usuarios.login_usr]',
                    'errors' => [
                        'required' => 'Debe especificar un nombre de usuario',
                        'is_not_unique' => 'El usuario no existe'
                    ],
                ],
                'txtClave' => [
                    'label'  => 'contraseña',
                    'rules'  => ['required',
                                static function($valor,$data){
                                    $MUsuario=model('App\Models\Cls_usuarios');
                                    $usuario=$MUsuario->where('login_usr',$data['txtUsuario'])->first();
                                    return is_null($usuario)||$usuario->login($valor);
                                }],
                    'errors' => [
                        'required' => 'Especifique una contraseña de, al menos, 8 caracteres',
                        1 => 'La contraseña no es correcta'
                                ]
                ],
            ]
        );
        $validation->withRequest($this->request)->run();
        $errors = $validation->getErrors();
        if (count($errors)>0 ) {
            $data['Login_error'] = 'fallo';
            return view('login', $data);    
        }

        return redirect('/');               

    }
    public function logout(){
        (new \App\Entities\Usuario())->logout();
        return redirect('/');
       }
    
    /************************************************************************************************/
    
    function olvido_clave () {
        $respuesta=['exito'=>false, 'mensaje'=>'Error indeterminado eliminando usuarios'];
        if (strtolower($this->request->getMethod()) == 'post') {
            $nombre=$this->request->getPost('nombre');
            $usuario=model('Cls_usuarios')->where('login_usr="'.$nombre.'"')->first();
            if (!is_null($usuario)){
                $correo=$usuario->correo??'';
                if ($correo!=''){
                    $encryption = new \CodeIgniter\Encryption\Encryption();
                    $config         = new \Config\Encryption();
                    $config->key    =CLAVE_TOKEN;
                    $config->driver = 'OpenSSL';
                    $encrypter = $encryption->initialize($config);
                    $token=rand(0,1000).','.strtotime('now');
                    $tokenEncriptado = $encrypter->encrypt($token);
                    $tokenEncriptado64 = base64_encode($tokenEncriptado);
                    $usuario->token=$token;
                    model('Cls_usuarios')->save($usuario);
                    $email = \Config\Services::email();

                    $email->setFrom(getenv('mail.fromUser'), getenv('mail.fromName'));
                    $email->setTo($correo);
                    
                    $email->setSubject('Cambio de contraseña');
                    $email->setMessage('
                            <h1>¿Olvidó su contraseña?</h1>
                            <p>Hemos recibido una petición de modificación de contraseña</p>
                            <p>Pulse el siguiente enlace para restablecer su contraseña</p>
                            <table><tr><td 
                            style="padding:15px;
                            background-color: #00bed6;
                            border: 1px solid #00bed6;
                            border-radius: 0.25em;">
                            <a style="
                            text-decoration: none;
                            font-weight: 400;
                            color: #fff;
                            text-align: center;
                            vertical-align: middle;
                            font-size: 20px;
                            line-height: 1.5;
                            " 
                            href="'.base_url('/login/acceso_token').'?usr='.urlencode($nombre).'&tk='.urlencode($tokenEncriptado64).'">Restablecer contraseña</a></td></tr></table>');
                    $email->send();
                    $respuesta['exito']=true;
                }
                else{
                    $respuesta['mensaje']='El usuario es desconocido.<br>Introduzca un nombre de usuario registrado en el sistema.';
                }
            }
            else{
                $respuesta['mensaje']='No disponemos de correo para enviarle un mensaje de recuperación de contraseña.<br>Por favor, solicite a su administrador que registre una dirección de correo en su perfil a la que usted tenga acceso.';
            }
        
        }
        return json_encode($respuesta);
  

    }
    function acceso_token () {
        $uri = current_url(true);
        $login_usr=urldecode(str_replace('usr=','',$uri->getQuery(['only'=>['usr']])));
        $tokenEncriptado64=urldecode(str_replace('tk=','',$uri->getQuery(['only'=>['tk']])));
        $encryption = new \CodeIgniter\Encryption\Encryption();
        $config         = new \Config\Encryption();
        $config->key    =CLAVE_TOKEN;
        $config->driver = 'OpenSSL';
        $encrypter = $encryption->initialize($config);
        $tokenEncriptado=base64_decode($tokenEncriptado64);
        $token='';
        try{
            $token = ($encrypter->decrypt($tokenEncriptado));
        }
        catch(\CodeIgniter\Encryption\Exceptions\EncryptionException $e){

        }
        echo $token.'<br>';
        if ($token!=''){
            $usuario=model('Cls_usuarios')->where('login_usr="'.$login_usr.'"')->first();
            if (!is_null($usuario)){
                echo $usuario->token.'<br>';
                if ($usuario->token==$token){
                    $parte=explode(',',$token);
                    echo 'Token correcto, de hace '.(strtotime('now')-$parte[1]).'s<br>';
                    if ((strtotime('now')-$parte[1])<=S_LIMITE_RESET_CONTRASENA){
                        registra_sesion_usuario($usuario);
                        return redirect()->to('usuarios/miPerfil/1');                            
                    }
                    else{

                    }
                }
            }
        }
        else{
            return redirect()->to('');                            
                    
        }
    }
}
?>