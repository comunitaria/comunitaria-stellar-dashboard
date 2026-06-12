<?php
namespace Modulos\Pagina\Controllers;

use App\Controllers\BaseController;

/**
 * Apartado de administración: lista los comercios activos con su enlace y QR
 * de consulta de saldo (/saldo/<token>) para repartir a los empleados.
 *
 * Requiere login de administrador (mismo patrón que Inicio/Comercios). Genera
 * el token de consulta al vuelo para los comercios que aún no lo tengan.
 */
class Saldos extends BaseController
{
    protected $helpers = ['autorizaciones', 'enlaces', 'Modulos\Vpbasicos\navegacion'];
    private $data;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        $this->data['enlaces'] = [];
        enlaces($this->helpers, $this->data['enlaces']);
        if (!($this->data['usuario'] = datos_usuario())) {
            return redirect('login');
        }
    }

    public function index()
    {
        if (!tienePermiso([2])) {
            return redirect()->to('login');
        }

        $modelo = model('Modulos\Pagina\Models\Cls_comercios');
        $comercios = [];
        foreach ($modelo->where('activo', 1)->findAll() as $comercio) {
            $token = $comercio->tokenConsulta;
            if (is_null($token) || $token === '') {
                $token = bin2hex(random_bytes(16));
                $modelo->update($comercio->id, ['tokenConsulta' => $token]);
            }
            $comercios[] = [
                'nombre' => $comercio->nombre,
                'cif'    => $comercio->CIF ?? '',
                'url'    => base_url('saldo/' . $token),
                'qr'     => base_url('saldo/' . $token . '/qr'),
            ];
        }

        $this->data['comercios'] = $comercios;
        $this->data['VPConf']    = config('VstPortal');
        return view('\Modulos\Pagina\vista_Saldos', $this->data);
    }
}
