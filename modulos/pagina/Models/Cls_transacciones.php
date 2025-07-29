<?php 

namespace Modulos\Pagina\Models;

use CodeIgniter\Model;

class Cls_transacciones extends Model
{
    
    protected $table      = 'transacciones';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;

    protected $returnType     = \Modulos\Pagina\Entities\Transaccion::class; 
    protected $useSoftDeletes = false;

    protected $allowedFields = ['tipo','usuario','tipoUsuario','moneda','cantidad','de_a_cuenta','de_a_tipoUsuario','de_a_usuario','momento','idTransaccion'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = '';
    protected $updatedField  = '';
    protected $deletedField  = '';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    
    protected function initialize()
    {
    }    
    public function intervaloTemporal($fechaIni,$fechaFin){
        helper(['Modulos\Pagina\formatos']);
        $db=db_connect();
        $res=$db->query("SELECT transacciones.*, 
                                CASE 
                                    WHEN transacciones.tipoUsuario=0 THEN IF(transacciones.usuario=1,'Comunitaria (D)','Comunitaria (E)')
                                    WHEN transacciones.tipoUsuario=1 THEN CONCAT(B1.nombre,' ',B1.apellidos)
                                    WHEN transacciones.tipoUsuario=2 THEN C1.nombre
                                END AS nombreDe,
                                CASE 
                                    WHEN transacciones.de_a_tipoUsuario=0 THEN IF(transacciones.de_a_usuario=1,'Comunitaria (D)','Comunitaria (E)')
                                    WHEN transacciones.de_a_tipoUsuario=1 THEN CONCAT(B2.nombre,' ',B2.apellidos)
                                    WHEN transacciones.de_a_tipoUsuario=2 THEN C2.nombre
                                    ELSE transacciones.de_a_cuenta
                                END AS nombreA
                            FROM transacciones 
                            LEFT JOIN beneficiarios B1 ON B1.id=transacciones.usuario
                            LEFT JOIN beneficiarios B2 ON B2.id=transacciones.de_a_usuario
                            LEFT JOIN comercios C1 ON C1.id=transacciones.usuario
                            LEFT JOIN comercios C2 ON C2.id=transacciones.de_a_usuario
                            WHERE transacciones.tipo=1 AND DATE(momento) BETWEEN '".$fechaIni->format('Y-m-d')."' AND '".$fechaFin->format('Y-m-d')."'")->getResult();
        $lista=[];
        foreach($res as $unaTransaccion){
          if ((!is_null($unaTransaccion->nombreA))&&(!is_null($unaTransaccion->nombreDe))){
            $lista[]=[
              'Id'=> $unaTransaccion->id,
              'Fecha'=> $unaTransaccion->momento,
              'Transacción' => $unaTransaccion->idTransaccion,
              'Movimiento'=>$unaTransaccion->tipoUsuario==0?($unaTransaccion->moneda==0?'Recarga de lumens':($unaTransaccion->de_a_tipoUsuario==0?'Emisión de ILLAs':'Entrega a beneficiarios')):($unaTransaccion->de_a_tipoUsuario==2?'Compra en comercio':($unaTransaccion->de_a_tipoUsuario==0?'Conversión de ILLAs':'Transferencia no autorizada!')),
              'Importe'=>['cantidad'=>formatoMoneda($unaTransaccion->cantidad),'moneda'=>$unaTransaccion->moneda],
              'Pagado por'=> ['nombre'=>$unaTransaccion->nombreDe, 'tipo'=>$unaTransaccion->tipoUsuario],
              'Pagado a'=> ['nombre'=>$unaTransaccion->nombreA, 'tipo'=>$unaTransaccion->de_a_tipoUsuario],
            ];
          }
        }
        return $lista;
      }
    
}
?>
