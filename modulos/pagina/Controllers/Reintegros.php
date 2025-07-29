<?php
namespace Modulos\Pagina\Controllers;
use App\Controllers\BaseController;

class Reintegros extends BaseController
{
    protected $helpers=['autorizaciones', 'enlaces', 'Modulos\Vpbasicos\navegacion',  'Modulos\Vpbasicos\formularios', 'Modulos\Vpbasicos\tabla'];
    
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
        if (!tienePermiso([3])) return view('no_autorizado');
//Vpconf<
            $this->data['clases']=model('Modulos\Pagina\Models\Cls_clases')->findAll();
            $this->data['VPConf']=config('VstPortal');
            return view('\Modulos\Pagina\vista_Reintegros',$this->data);
        }
        public function listado(){
            if (strtolower($this->request->getMethod()) == 'post') {
                if (tienePermiso([3])){
                    $clases=json_decode($this->request->getPost('clases'));
                    return json_encode(model('Modulos\Pagina\Models\Cls_comercios')->deClases($clases));
                }
                return 'No autorizado';
            }

        }
}
?>