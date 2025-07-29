<?php 

namespace Modulos\Pagina\Models;

use CodeIgniter\Model;

class Cls_grupos_p extends \App\Models\Cls_grupos
{

    protected $table      = 'grupos';
    protected $primaryKey = 'id_grupo';
    protected $useAutoIncrement = true;

    protected $returnType     = \Modulos\Pagina\Entities\Grupo::class; 
    protected $allowedFields = [ 'desc_grupo', 'id_perfil', 'id_oficio'];

    
    protected function initialize()
    {
      }

    function getGrupos(){
        $db = db_connect();
        // $query = $db->query("SELECT grupos.id_grupo, grupos.desc_grupo, grupos.id_perfil, grupos.id_oficio, 
        // IF(grupos.id_perfil=4,oficios.desc_oficio,perfiles.desc_prf) AS nombre_perfil, 
        // COUNT(usuarios_grupos.id_usr) AS num_usuarios
        //     FROM `grupos`
        //     LEFT JOIN perfiles ON grupos.id_perfil = perfiles.id_prf 
        //     LEFT JOIN oficios ON grupos.id_oficio = oficios.id_oficio 
        //     LEFT JOIN usuarios_grupos ON grupos.id_grupo = usuarios_grupos.id_grupo
        //     LEFT JOIN usuarios ON usuarios_grupos.id_usr = usuarios.id_usr
        //     GROUP BY grupos.id_grupo");
        $query = $db->query("SELECT grupos.id_grupo, grupos.desc_grupo, grupos.id_perfil, grupos.id_oficio, 
        CONCAT(grupos.id_perfil,'|', grupos.id_oficio) AS perfil_oficio, 
        COUNT(usuarios_grupos.id_usr) AS num_usuarios
            FROM `grupos`
            LEFT JOIN perfiles ON grupos.id_perfil = perfiles.id_prf 
            LEFT JOIN oficios ON grupos.id_oficio = oficios.id_oficio 
            LEFT JOIN usuarios_grupos ON grupos.id_grupo = usuarios_grupos.id_grupo
            LEFT JOIN usuarios ON usuarios_grupos.id_usr = usuarios.id_usr
            GROUP BY grupos.id_grupo");
        return $query->getResultArray();
    }

    //recuperamos los datos de los usuarios del grupo con id = $id_grupo para la vista de modificar grupo
    function getUsuariosDeGrupo($id_grupo){
        $db = db_connect();
        $query = $db->query("SELECT id, usuarios.id_usr, usuarios.login_usr, usuarios.nombre_usr, usuarios.correo 
            FROM usuarios_grupos 
            LEFT JOIN usuarios ON usuarios_grupos.id_usr = usuarios.id_usr 
            WHERE usuarios_grupos.id_grupo = $id_grupo");
        return $query->getResultArray();
    }

    // para recuperar usuarios que no están en el grupo dado
    function getUsuariosNoEnGrupo($id_grupo){
        $db = db_connect();
        $consulta = $db->query("SELECT DISTINCT id_usr FROM usuarios WHERE NOT id_usr IN (SELECT id_usr FROM usuarios_grupos WHERE id_grupo=$id_grupo)")->getResult();
        $lista=[];
        foreach($consulta as $unId){
            $lista[]=$unId->id_usr;
        }
        return model('Modulos\Pagina\Models\Cls_usuarios_p')->find($lista);
    }
    
    function getGrupo($id){
        $db = db_connect();
        $query = $db->query("SELECT * FROM grupos WHERE id_grupo = $id");
        return $query->getResultArray();
    }

    function anadirUsuarioAlGrupo($id_grupo, $usuario){
        $db = db_connect();
        foreach($usuario as $id_usuario){
            $query = $db->query("INSERT INTO usuarios_grupos (id_grupo, id_usr) VALUES ($id_grupo, $id_usuario)");
        }
        return true;
    }
}
?>