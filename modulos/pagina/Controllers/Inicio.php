<?php
namespace Modulos\Pagina\Controllers;
use App\Controllers\BaseController;

class Inicio extends BaseController
{
    protected $helpers=['autorizaciones', 'enlaces', 'Modulos\Vpbasicos\navegacion'];
    
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
        if (!tienePermiso([0])) return view('no_autorizado');
//Vpconf<
            $db=db_connect();
            $lista=$db->query('SELECT COUNT(id) as cuenta FROM beneficiarios WHERE activo=1')->getResult();
            $this->data['beneficiarios']=$lista[0]->cuenta;
            $lista=$db->query('SELECT COUNT(id) as cuenta FROM comercios WHERE activo=1')->getResult();
            $this->data['comercios']=$lista[0]->cuenta;
            $this->data['VPConf']=config('VstPortal');
            return view('\Modulos\Pagina\vista_Bienvenida',$this->data);
        }
}
?>