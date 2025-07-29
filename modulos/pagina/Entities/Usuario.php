<?php
namespace Modulos\Pagina\Entities;

use CodeIgniter\Entity\Entity;

class Usuario extends \App\Entities\Usuario
{
    function oficios(){
        $idOficios=[];
        foreach($this->grupos() as $unGrupo){
            if (($unGrupo->id_oficio==0)&&($unGrupo->id_perfil==\Modulos\Pagina\PERFIL_SUPERVISOR)){
                return model('\Modulos\Pagina\Models\Cls_oficios')->findAll();
            }
            if (!in_array($unGrupo->id_oficio,$idOficios))
                $idOficios[]=$unGrupo->id_oficio;
        }
        if (count($idOficios)>0){
            return model('\Modulos\Pagina\Models\Cls_oficios')->find($idOficios);
        }
        else    
            return [];
    }
    function grupos(){
        return model('\Modulos\Pagina\Models\Cls_grupos')->join('usuarios_grupos','usuarios_grupos.id_grupo=grupos.id_grupo')->where('usuarios_grupos.id_usr='.$this->id_usr)->findAll();
    }
    function colas(){
        $grupos=$this->grupos();
        $idGrupos=[0];
        $idOficios=[0];
        $supervisa=0;
        foreach($grupos as $unGrupo){
            if (!in_array($unGrupo->id_grupo,$idGrupos)) $idGrupos[]=$unGrupo->id_grupo;
            if (!in_array($unGrupo->id_oficio,$idOficios)) $idOficios[]=$unGrupo->id_oficio;
            if ($unGrupo->id_perfil==\Modulos\Pagina\PERFIL_SUPERVISOR){
                if ($unGrupo->id_oficio==0){
                    return model('\Modulos\Pagina\Models\Cls_colas')->findAll();
                }
                else{
                    $supervisa=$unGrupo->id_oficio;
                }
            }
        }
        if ($supervisa!=0){
            return model('\Modulos\Pagina\Models\Cls_colas')->where('id_oficio = '.$supervisa)->findAll();
        }
        else{
            return model('\Modulos\Pagina\Models\Cls_colas')->where('(id_usuario IN (0,'.$this->id_usr.')) AND (id_grupo IN ('.implode(',',$idGrupos).')) AND (id_oficio IN ('.implode(',',$idOficios).'))')->findAll();
        }
    }
}
?>