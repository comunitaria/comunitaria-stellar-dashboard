<?php 

namespace App\Models;

use CodeIgniter\Model;

class Cls_perfiles extends Model
{
    protected $table      = 'perfiles';
    protected $primaryKey = 'id_prf';
    protected $useAutoIncrement = true;

    protected $returnType     = \App\Entities\Perfil::class; 
    protected $useSoftDeletes = false;

    protected $allowedFields = ['desc_prf'];

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