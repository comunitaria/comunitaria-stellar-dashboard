<?php
namespace Modulos\Pagina\Controllers;
use App\Controllers\BaseController;
use Modulos\Pagina\Libraries\Stellar;

class Configuraciongeneral extends BaseController
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
        if (!tienePermiso([1])) return view('no_autorizado');
//Vpconf<
        $st=new Stellar();
        $this->data['emisora']=['clave'=>getenv('moneda.emisora.publica'),'XLM'=>0, 'cripto'=>0];
        $balances=$st->balances($this->data['emisora']['clave']);
        if (!$balances['existe']){
            $this->data['emisora']['clave']='ERROR: la cuenta emisora no existe en esta red';
        }
        else{
            $this->data['emisora']['XLM']=$balances['XLM'];
            $this->data['emisora']['cripto']=$balances['cripto'];
        }
        $this->data['distribuidora']=['clave'=>getenv('moneda.distribuidora.publica'),'XLM'=>0, 'cripto'=>0];
        $balances=$st->balances($this->data['distribuidora']['clave']);
        if (!$balances['existe']){
            $this->data['distribuidora']['clave']='ERROR: la cuenta distribuidora no existe en esta red';
        }
        else{
            $this->data['distribuidora']['XLM']=$balances['XLM'];
            $this->data['distribuidora']['cripto']=$balances['cripto'];
        }
        $this->data['ejecucionCron']=model('Modulos\Pagina\Models\Cls_parametros')->valor('CRON_EJECUCION');
        $this->data['costeXLM']='No disponible';
        try{
            $client = service('curlrequest');
            $respuesta = json_decode($client->request('GET', 'https://api.coinbase.com/v2/exchange-rates?currency=XLM')->getBody());
            $this->data['costeXLM']=number_format(100*floatval($respuesta->data->rates->EUR),2).' cts&euro;';

        }
        catch(\Exception $e){}
        $this->data['VPConf']=config('VstPortal');
        return view('\Modulos\Pagina\vista_ConfiguracionGeneral',$this->data);
    }
}
?>