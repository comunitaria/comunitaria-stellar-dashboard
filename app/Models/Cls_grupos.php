<?php 

namespace App\Models;

use CodeIgniter\Model;

class Cls_grupos extends Model
{
    protected $table      = 'grupos';
    protected $primaryKey = 'id_grupo';
    protected $useAutoIncrement = true;

    protected $returnType     = \App\Entities\Grupo::class; 
    protected $useSoftDeletes = false;

    protected $allowedFields = ['desc_grupo', 'id_perfil'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

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