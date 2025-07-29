<?php 

namespace Modulos\Pagina\Models;

use CodeIgniter\Model;

class Cls_cuentas extends Model
{
    protected $table      = 'cuentas';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;

    protected $returnType     = \Modulos\Pagina\Entities\Cuenta::class; 
    protected $useSoftDeletes = false;

    protected $allowedFields = ['clave','balanceXLM','balanceILLA','creada','trustline','autorizada','bloqueada'];

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
}
?>
