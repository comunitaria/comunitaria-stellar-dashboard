<?php 

namespace Modulos\Pagina\Models;

use CodeIgniter\Model;

class Cls_comercios extends Model
{
    protected $table      = 'comercios';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;

    protected $returnType     = \Modulos\Pagina\Entities\Comercio::class; 
    protected $useSoftDeletes = true;

    protected $allowedFields = ['usuario','contrasena','nombre','CIF','direccion','movil','correo','contacto','coordenadas','logo','cuenta','hashDatos','bloqueado','activo','transferirILLA','recibidoILLA','pagadoILLA','canjeadoILLA'];

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
        return $this->where('activo=1')->findAll();
    }
    public function deClase($idClase){
            $db=db_connect();
            $listaComercios=$db->query('SELECT comercio, IFNULL(cuentas.clave,"") AS clave FROM clases_comercio LEFT JOIN comercios ON comercios.id=clases_comercio.comercio LEFT JOIN cuentas ON cuentas.id=comercios.cuenta WHERE comercios.activo=1 AND NOT ISNULL(cuentas.clave) AND cuentas.autorizada=1 AND cuentas.bloqueada=0 '.($idClase!=0?'AND clase='.$idClase:''))->getResult();
            $listaIds=[];
            $cuentas=[];
            foreach ($listaComercios as $unComercio) {
                $listaIds[]=$unComercio->comercio;
                $cuentas[$unComercio->comercio]='G'.$unComercio->clave;
            }
            if (count($listaIds)>0){
                $lista=$this->find($listaIds);
                foreach($lista as $i=>$unComercio){
                    $lista[$i]->clave=$cuentas[$unComercio->id]??'';
                }
                return $lista;
            }
            else return [];
        
    }
    public function deClases($clases=[0]){
        helper(['Modulos\Pagina\formatos']);
        $db=db_connect();
        $todos=in_array(0,$clases);
        $res=$db->query("SELECT comercios.id , CONCAT(GROUP_CONCAT(clases.id SEPARATOR '/'),'/') AS idsClase, GROUP_CONCAT(clases.clase SEPARATOR '#-#') as clases,
                                cuentas.id AS idCuenta, cuentas.clave, cuentas.creada, cuentas.trustline, cuentas.autorizada,cuentas.bloqueada, cuentas.balanceILLA, cuentas.balanceXLM
                                FROM comercios 
                            LEFT JOIN cuentas  ON cuentas.id=comercios.cuenta
                            LEFT JOIN clases_comercio ON clases_comercio.comercio=comercios.id
                            LEFT JOIN clases ON clases.id=clases_comercio.clase
                            WHERE comercios.activo=1 ".(!$todos?("AND clases.id IN (".implode(',',$clases).")"):"")."
                            GROUP BY comercios.id")->getResult();
        $lista=[];
        foreach($res as $unIdComercio){
          $unComercio=$this->find($unIdComercio->id);
          $lista[]=[
            'Id'=> $unComercio->id,
            'Comercios colaboradores'=> $unComercio->nombre,
            'IdsClase'=>$unIdComercio->idsClase,
            'Clases'=>$unIdComercio->clases,
            'Usuario'=>$unComercio->usuario,
            'Dirección'=>$unComercio->direccion,
            'Contacto'=>$unComercio->contacto,
            'Movil'=>$unComercio->movil,
            'Correo'=>$unComercio->correo,
            'Monedero'=>is_null($unIdComercio->idCuenta)?\Modulos\Pagina\ESTADO_NO_CREADA:($unIdComercio->creada?($unIdComercio->trustline?($unIdComercio->autorizada?\Modulos\Pagina\ESTADO_AUTORIZADA:\Modulos\Pagina\ESTADO_TRUSTLINE):\Modulos\Pagina\ESTADO_CREADA):\Modulos\Pagina\ESTADO_NO_CREADA),
            'Saldo'=>formatoMoneda(is_null($unIdComercio->idCuenta)?0:$unIdComercio->balanceILLA),
            'Clave'=>is_null($unIdComercio->idCuenta)?'':'G'.$unIdComercio->clave,
            'Bloqueo'=>$unComercio->bloqueado=='1',
            'Logo'=> ($unComercio->logo=='1')?(base_url('public/assets/imagenes/logos').(file_exists(ROOTPATH . 'public/assets/imagenes/logos/logo'.$unComercio->id.'.png')? '/logo'.$unComercio->id.'.png?'.strtotime('now') : '/logoDf.png')):
                                             (base_url('public/assets/imagenes/logos').'/logoDf.png'),
            'Alta'=> strtotime($unComercio->creado),
            'Transacciones'=>$unComercio->transaccionesMensuales(12),
            'Deuda'=>formatoMoneda($unComercio->canjeadoILLA-$unComercio->pagadoILLA)
          ];
        }
        return $lista;
    }
}
?>
