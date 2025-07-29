<?php 

namespace Modulos\Pagina\Models;

use CodeIgniter\Model;

class Cls_parametros extends Model
{
    protected $table      = 'parametros';
    protected $primaryKey = 'clave';
    protected $useAutoIncrement = true;

    protected $returnType     = \Modulos\Pagina\Entities\Parametro::class; 
    protected $useSoftDeletes = false;

    protected $allowedFields = ['clave','valor'];

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
   public function valor($clave){
      $valor=$this->find($clave);
      if (is_null($valor)) return '';
      else return $valor->valor;
   }
   public function guarda($clave,$valor){
    $objValor=$this->find($clave);
    if (is_null($objValor)){
      $this->insert(['clave'=>$clave,'valor'=>$valor],false);
    }
    else{
      $objValor->valor=$valor;
      try{//Falla si no está modificado
        $this->save($objValor);
      }catch(\Exception $e) {}
    }
   }
}
?>
