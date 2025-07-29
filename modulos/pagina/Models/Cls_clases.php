<?php 

namespace Modulos\Pagina\Models;

use CodeIgniter\Model;

class Cls_clases extends Model
{
    protected $table      = 'clases';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array'; 
    protected $useSoftDeletes = false;

    protected $allowedFields = ['clase'];

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
    public function idDe($clase){
      $db=db_connect();
      $lista=$db->query('SELECT id FROM clases WHERE clase="'.$clase.'"')->getResult();
      if (count($lista)>0) return $lista[0]->id;
      else return 0;
    }
    public function todas(){
      $respuesta=[0=>\Modulos\Pagina\TODOS_LOS_COMERCIOS];
      $db=db_connect();
      $lista=$db->query('SELECT * FROM clases ORDER BY clase')->getResult();
      foreach ($lista as $unaClase) {
          $respuesta[$unaClase->id]=$unaClase->clase;
      }
      return $respuesta;
    }
    public function creaYLista($arr){
      $listaId=[];
      if (!is_null($arr)&&count($arr)>0){
        $db=db_connect();
        $lista=$db->query('SELECT * FROM clases')->getResult();
        foreach($arr as $unaClase){
          if ($unaClase==\Modulos\Pagina\TODOS_LOS_COMERCIOS) continue;
          $id=-1;
          foreach($lista as $unaClaseRegistrada){
            if ($unaClaseRegistrada->clase==$unaClase){
              $id=$unaClaseRegistrada->id;
              break;
            }
          }
          if ($id>0){
            $listaId[]=$id;
          }
          else{
            if ($db->query('INSERT INTO clases SET clase="'.$unaClase.'"')){
              $listaId[]=$db->insertID();
            }
          }
        }
      }
      return $listaId;
    }
}
?>
