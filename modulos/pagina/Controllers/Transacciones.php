<?php
namespace Modulos\Pagina\Controllers;
use App\Controllers\BaseController;

class Transacciones extends BaseController
{
    protected $helpers=['autorizaciones', 'enlaces', 'Modulos\Vpbasicos\navegacion', 'Modulos\Vpbasicos\tabla'];
    
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
        if (!tienePermiso([4])) return view('no_autorizado');
//Vpconf<

                    $this->data['VPConf']=config('VstPortal');
                return view('\Modulos\Pagina\vista_Transacciones',$this->data);
        }
        public function listado(){
            if (strtolower($this->request->getMethod()) == 'post') {
                if (tienePermiso([4])){
                    $fechas=explode(' - ',$this->request->getPost('periodo'));
                    $fechaInicio=date_create_from_format('d/m/Y', $fechas[0]);
                    $fechaFin=date_create_from_format('d/m/Y', $fechas[1]);
                    return json_encode(model('Modulos\Pagina\Models\Cls_transacciones')->intervaloTemporal($fechaInicio,$fechaFin));
                }
                return 'No autorizado';
            }

        }

}
?>