<?php

namespace Modulos\Pagina\Models;

use CodeIgniter\Model;

class Cls_usuarios_p extends \App\Models\Cls_usuarios
{

    protected $returnType     = \Modulos\Pagina\Entities\Usuario::class; 
    
    
    protected function initialize()
    {
      }
    public function listadoCompleto(){
        $listado=$this->findAll();
        foreach($listado as $usuario){
          $usuario->grupos=$usuario->grupos();
          $usuario->grupos=$usuario->grupos();
        }
        return $listado;
    }
     
    function getGruposUsuario($id){
        $db = db_connect();

        $query = $db->query("SELECT usuarios.id_usr, usuarios.login_usr, usuarios.nombre_usr, grupos.desc_grupo, 
          IF(grupos.id_perfil=4, oficios.desc_oficio, perfiles.desc_prf) AS nombre_perfil 
          FROM grupos 
          LEFT JOIN perfiles ON perfiles.id_prf=grupos.id_perfil 
          LEFT JOIN oficios ON oficios.id_oficio=grupos.id_oficio 
          INNER JOIN (SELECT usuarios.id_usr, GROUP_CONCAT(usuarios_grupos.id_grupo) AS gruposs 
             FROM usuarios 
             LEFT JOIN usuarios_grupos ON usuarios_grupos.id_usr = usuarios.id_usr 
             WHERE usuarios.id_usr=$id) AS u 
            ON FIND_IN_SET(grupos.id_grupo, u.gruposs) > 0 
            INNER JOIN usuarios ON usuarios.id_usr = u.id_usr;");


        return $query->getResultArray();
    }

    function getGrupos(){
      $db = db_connect();
      $query = $db->query("SELECT id_grupo, desc_grupo, id_perfil, grupos.id_oficio, IF(id_perfil=4,oficios.desc_oficio,perfiles.desc_prf) 
      AS nombre_perfil FROM `grupos` LEFT JOIN perfiles ON grupos.id_perfil = perfiles.id_prf LEFT JOIN oficios ON grupos.id_oficio = oficios.id_oficio");
      return $query->getResultArray();
  }  

    function getUsuariosGrupos(){
      $db = db_connect();
      $query = $db->query("SELECT * FROM usuarios_grupos");
      return $query->getResultArray();
    }

    
    function listaDeOficio($id_oficio){
      $db = db_connect();
      $res = $db->query("SELECT id_usr FROM usuarios_grupos LEFT JOIN grupos ON grupos.id_grupo=usuarios_grupos.id_grupo WHERE grupos.id_oficio=$id_oficio")->getResult();    
      $idUsuarios=[];
      foreach($res as $unId) $idUsuarios[]=$unId->id_usr;
      if (count($idUsuarios)==0)
        return [];
      else{
        return $this->find($idUsuarios);
      }
    }

}
?>