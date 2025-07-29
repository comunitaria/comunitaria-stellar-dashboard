<?php
namespace Modulos\Pagina\Entities;

class Beneficiario extends UsuarioILLA
{
    public function getAutorizado(){
        $respuesta=['clase'=>0, 'texto'=>''];
        $respuesta['clase']=$this->clase??0;
        $clase=model('Modulos\Pagina\Models\Cls_clases')->find($respuesta['clase']);
        $respuesta['texto']=\Modulos\Pagina\TODOS_LOS_COMERCIOS;
        if (!is_null($clase)){
            $respuesta['texto']=$clase['clase'];
        }
        return $respuesta;
    }
    public function getInfo(){
        return [
            'id'=>$this->id,
            'usuario'=>$this->usuario,
            'nombre'=>$this->nombre??'',
            'apellidos'=>$this->apellidos??'',
            'autorizado_en'=>$this->autorizado,
            'correo'=>$this->correo??'',
            'direccion'=>$this->direccion??'',
            'movil'=>$this->movil??''];
    }
}
?>