<?php
 
 namespace Modulos\Vpbasicos\Controllers;
 use App\Controllers\BaseController;
 
 
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use DomainException;
use InvalidArgumentException;
use UnexpectedValueException;


class Apibase extends BaseController
{
    use ResponseTrait;

    private $expiracion=3600;
    private $emisor="VST";
    private $audiencia="App movil";
    private $objeto="Autentificacion acceso a datos";
    private $jwt_secreto='secreto base';
    
    public function contrasenaCorrecta($usuario,$contrasena){
        //Sobreescribir esta funcion
        return true;
    }
    public function usuarioCorrecto($usuario){
        //Sobreescribir esta funcion
        return 'OK';
    }
    public function infoUsuario($usuario){
        //Sobreescribir esta funcion
        return [];
    }
    public function index()
    {
        return $this->respond(['mensaje'=>'API Base'], 200);
    }
    public function login(){
        $usuario = $this->request->getPost('username');
        $contrasena = $this->request->getPost('password');
        if(is_null($usuario)) {
            return $this->respond(['error' => 'Debe especificar usuario y contrasena'], 401);
        }
        $falloUsuario=$this->usuarioCorrecto($usuario);
        if($falloUsuario!='OK') {
            return $this->respond(['error' => $falloUsuario], 401);
        }
        if(!$this->contrasenaCorrecta($usuario,$contrasena)){
            return $this->respond(['error' => 'Contraseña incorrecta'], 401);
        }
        $key = $this->jwt_secreto;
        $iat = time(); // current timestamp value
        $exp = $iat + $this->expiracion;
 
        $payload = array(
            "iss" => $this->emisor,
            "aud" => $this->audiencia,
            "sub" => $this->objeto,
            "iat" => $iat, //Time the JWT issued at
            "exp" => $exp, // Expiration time of token
            "usuario" => $usuario,
        );
         
        $token = JWT::encode($payload, $key, 'HS256');
        $response = array_merge([
                'mensaje' => 'Login exitoso',
                'access_token' => $token
            ],
            $this->infoUsuario($usuario)
        );
         
        return $this->respond($response, 200);
      }
      public function autentifica(){
        $resultado=['codigo'=>1000,'tipo'=>'error','mensaje'=>'Indeterminado'];
        $key = $this->jwt_secreto;
        $header = $this->request->getHeader("Authorization");
        $token = null;
  
        // extract the token from the header
        if(!empty($header)) {
            if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
                $token = $matches[1];
            }
        }
  
        // check if token is null or empty
        if(is_null($token) || empty($token)) {
            $resultado['codigo']=1;
            $resultado['mensaje']='Acceso denegado';
            return $resultado;
        }
  
        try {
            $resultado['jwt']= JWT::decode($token, new Key($key, 'HS256'));
        } catch (InvalidArgumentException $e) {
            $resultado['codigo']=2;
            $resultado['mensaje']='Acceso denegado';
            return $resultado;
        } catch (DomainException $e) {
            $resultado['codigo']=3;
            $resultado['mensaje']='Acceso denegado';
            return $resultado;
        } catch (SignatureInvalidException $e) {
            $resultado['codigo']=4;
            $resultado['mensaje']='Acceso denegado';
            return $resultado;
        } catch (BeforeValidException $e) {
            $resultado['codigo']=5;
            $resultado['mensaje']='Acceso denegado';
            return $resultado;
        } catch (ExpiredException $e) {
            $resultado['codigo']=6;
            $resultado['mensaje']='Token expirado';
            return $resultado;
        } catch (UnexpectedValueException $e) {
            $resultado['codigo']=7;
            $resultado['mensaje']='Acceso denegado';
            return $resultado;
        }
        $resultado['tipo']='exito';
        $resultado['codigo']=0;
        $resultado['usuario']=$resultado['jwt']->usuario;
        return $resultado;
      }
}