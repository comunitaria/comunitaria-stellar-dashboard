<?php
namespace Modulos\Pagina\Controllers;

use App\Controllers\BaseController;
use Modulos\Pagina\Libraries\Stellar;

/**
 * Página pública de SOLO LECTURA del saldo de un comercio.
 *
 * Se accede con /saldo/<tokenConsulta>. No requiere login y no expone la clave
 * privada: muestra solo datos que ya son públicos en la blockchain (saldo y
 * dirección de cobro). Pensada para que los dependientes/empleados consulten
 * el saldo y muestren el QR de cobro, mientras el dueño conserva el control
 * total en su app.
 */
class Saldo extends BaseController
{
    private function buscaComercio($token)
    {
        if (!preg_match('/^[0-9a-f]{32}$/', $token)) {
            return null;
        }
        return model('Modulos\Pagina\Models\Cls_comercios')->where('tokenConsulta', $token)->first();
    }

    public function index($token)
    {
        $comercio = $this->buscaComercio($token);
        if (is_null($comercio) || $comercio->activo != '1') {
            return $this->response->setStatusCode(404)->setBody('Comercio no encontrado');
        }

        $cuenta = $comercio->miCuenta();
        $clave  = is_null($cuenta) ? '' : $cuenta->clave;

        $saldo = 0;
        $existe = false;
        $autorizada = false;
        if ($clave !== '') {
            $bal = (new Stellar())->balances($clave);
            $saldo = $bal['cripto'];
            $existe = $bal['existe'];
            $autorizada = $bal['autorizada'];
        }

        $movimientos = model('Modulos\Pagina\Models\Cls_transacciones')->ultimasDeComercio($comercio->id, 10);

        $data = [
            'nombre'       => $comercio->nombre,
            'clavePublica' => $clave === '' ? '' : 'G' . $clave,
            'saldo'        => $saldo,
            'existe'       => $existe,
            'autorizada'   => $autorizada,
            'moneda'       => getenv('moneda.nombre'),
            'emisora'      => 'G' . getenv('moneda.emisora.publica'),
            'nodo'         => getenv('moneda.nodo.' . getenv('moneda.red')),
            'urlSaldo'     => base_url('saldo/' . $token),
            'urlQr'        => base_url('saldo/' . $token . '/qr'),
            'movimientos'  => $movimientos,
        ];

        return view('\Modulos\Pagina\vista_Saldo', $data);
    }

    // Devuelve el QR (SVG) que apunta a esta misma página /saldo/<token>,
    // para imprimir y pegar en el mostrador.
    public function qr($token)
    {
        $comercio = $this->buscaComercio($token);
        if (is_null($comercio)) {
            return $this->response->setStatusCode(404)->setBody('No encontrado');
        }
        require_once ROOTPATH . 'vendor/tecnickcom/tcpdf/tcpdf_barcodes_2d.php';
        $barcode = new \TCPDF2DBarcode(base_url('saldo/' . $token), 'QRCODE,M');
        $svg = $barcode->getBarcodeSVGcode(6, 6, 'black');
        return $this->response
            ->setHeader('Content-Type', 'image/svg+xml')
            ->setHeader('Cache-Control', 'public, max-age=86400')
            ->setBody($svg);
    }
}
