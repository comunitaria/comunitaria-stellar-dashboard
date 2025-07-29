<?php 

namespace Modulos\Pagina\Models;

use CodeIgniter\Model;
// Use App\Models;

class Cls_grupos_p extends App\Models\Cls_grupos
{

    protected $table      = 'grupos';
    protected $primaryKey = 'id_grupo';
    protected $useAutoIncrement = true;

    protected $returnType     = \Modulos\Pagina\Entities\Grupo::class; 
    protected $allowedFields = [ 'desc_grupo', 'id_perfil', 'id_oficio'];

    
    protected function initialize()
    {
      }

    public function findAllDeUsuario($id_usuario){
        $db=db_connect();
        $listaGrupos=$db->query('SELECT * FROM usuarios_grupos WHERE id_usr='.$id_usuario)->getResultArray();
        $grupos=[];
        foreach($listaGrupos as $grupo){
            $grupos[]=$grupo['id_grupo'];
        }
        return $this->where('id_grupo IN ('.implode(',',$grupos).')')->findAll();
    }
    function getGrupos(){
        $db = db_connect();
        $query = $db->query("SELECT id_grupo, desc_grupo, id_perfil, grupos.id_oficio, IF(id_perfil=4,oficios.desc_oficio,perfiles.desc_prf) 
        AS nombre_perfil FROM `grupos` LEFT JOIN perfiles ON grupos.id_perfil = perfiles.id_prf LEFT JOIN oficios ON grupos.id_oficio = oficios.id_oficio");
        return $query->getResultArray();
    }   
}
?>