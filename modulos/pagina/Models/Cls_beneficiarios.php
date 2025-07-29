<?php 

namespace Modulos\Pagina\Models;

use CodeIgniter\Model;

class Cls_parametros extends Model
{
    protected $table      = 'beneficiarios';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;

    protected $returnType     = \Modulos\Pagina\Entities\Beneficiario::class; 
    protected $useSoftDeletes = true;

    protected $allowedFields = ['usuario','contrasena','clase','nombre','apellidos','direccion','movil','correo','cuenta','bloqueado','activo','transferirILLA','recibidoILLA'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'creado';
    protected $updatedField  = 'actualizado';
    protected $deletedField  = 'borrado';

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
    public function activos(){
        return $this->findAll();
    }
    public function deClases($clases=[0]){
        helper(['Modulos\Pagina\formatos']);
        $db=db_connect();
        $todos=in_array(0,$clases);
        $res=$db->query("SELECT beneficiarios.* , clases.id AS idClase, clases.clase as clase,
                                cuentas.id AS idCuenta, cuentas.creada, cuentas.trustline, cuentas.autorizada,cuentas.bloqueada, cuentas.balanceILLA, cuentas.balanceXLM
                                FROM beneficiarios 
                            LEFT JOIN cuentas  ON cuentas.id=beneficiarios.cuenta
                            LEFT JOIN clases ON clases.id=beneficiarios.clase
                            WHERE beneficiarios.activo=1 ".(!$todos?("AND beneficiarios.clase IN (".implode(',',$clases).")"):""))->getResult();
        $lista=[];
        foreach($res as $unBeneficiario){
          $lista[]=[
            'Id'=> $unBeneficiario->id,
            'Nombre'=> $unBeneficiario->nombre.' '.$unBeneficiario->apellidos,
            'IdClase'=>$unBeneficiario->idClase,
            'Compra autorizada'=>$unBeneficiario->clase,
            'Móvil'=>$unBeneficiario->movil,
            'Dirección'=>$unBeneficiario->direccion,
            'Monedero'=>is_null($unBeneficiario->idCuenta)?\Modulos\Pagina\ESTADO_NO_CREADA:($unBeneficiario->creada?($unBeneficiario->trustline?($unBeneficiario->autorizada?\Modulos\Pagina\ESTADO_AUTORIZADA:\Modulos\Pagina\ESTADO_TRUSTLINE):\Modulos\Pagina\ESTADO_CREADA):\Modulos\Pagina\ESTADO_NO_CREADA),
            'Saldo'=>formatoMoneda(is_null($unBeneficiario->idCuenta)?0:$unBeneficiario->balanceILLA),
            'Bloqueo'=>$unBeneficiario->bloqueado=='1',
            'Carga'=>is_null($unBeneficiario->idCuenta)?0:(floatval($unBeneficiario->balanceXLM)-floatval(getenv('moneda.XLM.minimo')))/(floatval(getenv('moneda.XLM.maximo'))-floatval(getenv('moneda.XLM.minimo'))),
            'Usuario'=> $unBeneficiario->usuario,
          ];
        }
        return $lista;
    }
}
?>
