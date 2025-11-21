<?php
namespace Modulos\Pagina\Controllers;
use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use Modulos\Pagina\Libraries\Stellar;

class Cron extends  BaseController
{
    use ResponseTrait;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ){
        // Do Not Edit This Line
        
        parent::initController($request, $response, $logger);
    }
    public function index()
    {
        $parametros=model('Modulos\Pagina\Models\Cls_parametros');
        $ultimoID=0;
        $cuentasSistema=[
            1=>getenv('moneda.distribuidora.publica'),
            2=>getenv('moneda.emisora.publica'),
        ];
        $lista=[
            0=>$cuentasSistema,
            1=>[],
            2=>[]
        ];
        $lista=[
            0=>$cuentasSistema,
            1=>[],
            2=>[]
        ];

        $st=new Stellar();
        $db=db_connect();
        $maxID=$db->query("SELECT MAX(idTransaccion) AS maxID from transacciones")->getResult();
        $ultimoID=$maxID[0]->maxID??0;
        $parametros->guarda('CRON_ULTIMA_TRANSACCION',$ultimoID);
        $listaUsuariosCuenta=$db->query("
                            SELECT 2 as TIPO_USUARIO, comercios.id, cuentas.clave FROM comercios LEFT JOIN cuentas ON cuentas.id=comercios.cuenta WHERE cuentas.clave IS NOT NULL AND cuentas.clave<>'' AND comercios.activo=1")->getResult();
        foreach ($listaUsuariosCuenta as $usuarioCuenta) {
            $lista[$usuarioCuenta->TIPO_USUARIO][$usuarioCuenta->id]=$usuarioCuenta->clave;
        }
        $listaUsuariosCuenta=$db->query("
                            SELECT 1 as TIPO_USUARIO, beneficiarios.id, cuentas.clave FROM beneficiarios LEFT JOIN cuentas ON cuentas.id=beneficiarios.cuenta WHERE cuentas.clave IS NOT NULL AND cuentas.clave<>'' AND beneficiarios.activo=1")->getResult();
        foreach ($listaUsuariosCuenta as $usuarioCuenta) {
            $lista[$usuarioCuenta->TIPO_USUARIO][$usuarioCuenta->id]=$usuarioCuenta->clave;
        }

        $valores=[];
        foreach ($lista as $tipoUsuario=>$unTipoUsuario){
            if ($tipoUsuario!=1){
                foreach($unTipoUsuario as $idUsuario=>$usuarioCuenta) {
                    $masValores=$st->transacciones($tipoUsuario,$idUsuario,$usuarioCuenta,$ultimoID,$cuentasSistema);
                    $valores=array_merge($valores,$masValores);
                }
            }
        }
        $parametros->guarda('CRON_TRANSACCIONES',count($valores));

        if (count($valores)>0){
            $db->query("INSERT INTO transacciones (tipo, usuario, tipoUsuario, moneda, cantidad, momento, de_a_cuenta, de_a_tipoUsuario, de_a_usuario, idTransaccion) VALUES ".implode(',',$valores));
            $db->query("INSERT INTO transacciones
                                (tipo,usuario,tipoUsuario,moneda,cantidad,de_a_tipoUsuario,de_a_cuenta,de_a_usuario,momento,idTransaccion)
                                SELECT IF(transacciones.tipo=1,2,1) as tipo,
                                        if(beneficiarios.id IS NULL, comercios.id,beneficiarios.id) AS usuario,
                                        if(beneficiarios.id IS NULL, 2,1) AS tipoUsuario,
                                        transacciones.moneda,
                                        transacciones.cantidad,
                                        transacciones.tipoUsuario as de_a_tipoUsuario,
                                        '',
                                        transacciones.usuario as de_a_usuario,
                                        transacciones.momento,
                                        transacciones.idTransaccion 
                                    FROM transacciones RIGHT JOIN cuentas ON cuentas.clave=transacciones.de_a_cuenta LEFT JOIN beneficiarios ON beneficiarios.cuenta=cuentas.id LEFT JOIN comercios ON comercios.cuenta=cuentas.id 
                                    WHERE transacciones.de_a_usuario=-1");
            $db->query("UPDATE transacciones 
                                LEFT JOIN cuentas ON cuentas.clave=transacciones.de_a_cuenta 
                                LEFT JOIN beneficiarios ON beneficiarios.cuenta=cuentas.id 
                                LEFT JOIN comercios ON comercios.cuenta=cuentas.id 
                        SET de_a_tipoUsuario=if(beneficiarios.id IS NULL, 2,1), 
                            de_a_usuario=if(beneficiarios.id IS NULL, comercios.id,beneficiarios.id) 
                        WHERE transacciones.de_a_usuario=-1 AND (beneficiarios.id IS NOT NULL OR comercios.id IS NOT NULL)");
        }
        foreach($valores as $unApunte){
            //1 tipo, 2 usuario, 3 tipoUsuario, 4 moneda, 5 cantidad, 6 momento, 7 de_a_cuenta, 8 de_a_tipoUsuario, 9 de_a_usuario, 10 idTransaccion
            if (preg_match('/\(([^,]+),([^,]+),([^,]+),([^,]+),([^,]+),([^,]+),([^,]+),([^,]+),([^,]+),([^,]+)\)/i',$unApunte,$coincidencias)){
                //Un comercio cobra
                if (($coincidencias[1]==2)&&($coincidencias[3]==2)&&($coincidencias[4]==1)){
                    $db->query("UPDATE comercios SET recibidoILLA=recibidoILLA+".$coincidencias[5]." WHERE id=".$coincidencias[2]);
                }
                //Un comercio paga a Comunitaria
                if (($coincidencias[1]==2)&&($coincidencias[3]==0)&&($coincidencias[4]==1)){
                    $db->query("UPDATE comercios LEFT JOIN cuentas ON cuentas.id=comercios.cuenta SET canjeadoILLA=canjeadoILLA+".$coincidencias[5]." WHERE cuentas.clave=".$coincidencias[7]);
                }
            }
        }
        
        $cuentasAfectadas=[];
        $listaCuentasAfectadas=$db->query("SELECT IFNULL(B1.cuenta,C1.cuenta) as cuenta 
                                                FROM transacciones 
                                                LEFT JOIN beneficiarios B1 ON B1.id=transacciones.usuario AND transacciones.tipo=1
                                                LEFT JOIN comercios C1 ON C1.id=transacciones.usuario AND transacciones.tipo=2 
                                            WHERE idTransaccion>".$ultimoID." AND NOT (B1.cuenta IS NULL AND C1.cuenta IS NULL) 
                                            UNION 
                                            SELECT IFNULL(B2.cuenta,C2.cuenta) as usuario 
                                                FROM `transacciones` 
                                                LEFT JOIN beneficiarios B2 ON B2.id=transacciones.de_a_usuario AND transacciones.de_a_tipoUsuario=1 
                                                LEFT JOIN comercios C2 ON C2.id=transacciones.de_a_usuario AND transacciones.de_a_tipoUsuario=2 
                                            WHERE idTransaccion>".$ultimoID." AND NOT (B2.cuenta IS NULL AND C2.cuenta IS NULL)")->getResult();
        foreach ($listaCuentasAfectadas as $idCuenta) {
            $cuentasAfectadas[]=$idCuenta->cuenta;
        }
        $parametros->guarda('CRON_CUENTAS_REVISADAS',count($cuentasAfectadas));

        if (count($cuentasAfectadas)>0){    
            foreach(model('Modulos\Pagina\Models\Cls_cuentas')->find($cuentasAfectadas) as $unaCuenta){
                $unaCuenta->aseguraXLM();
            }
        }
        
        // Check wallets that are marked as "not created" to see if they've been created on Stellar network
        $cuentasPendientes=$db->query("SELECT id FROM cuentas WHERE creada=0 AND clave IS NOT NULL AND clave<>''")->getResult();
        $cuentasActualizadas=0;
        foreach ($cuentasPendientes as $cuentaPendiente) {
            $cuenta=model('Modulos\Pagina\Models\Cls_cuentas')->find($cuentaPendiente->id);
            if (!is_null($cuenta)){
                $cuenta->actualizaEstado();
                if ($cuenta->creada==1){
                    $cuentasActualizadas++;
                    // If the wallet was just created, ensure it has XLM
                    $cuenta->aseguraXLM();
                }
            }
        }
        $parametros->guarda('CRON_CUENTAS_ACTUALIZADAS',$cuentasActualizadas);
        
        date_default_timezone_set('Europe/Madrid');
        $parametros->guarda('CRON_EJECUCION',date('d/m/Y H:i:s'));
        return $this->respond(['mensaje'=>'CRON Comunitaria V1.0'], 200);
    }

}
?>