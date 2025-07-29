<?php
namespace Modulos\Pagina\Entities;

use CodeIgniter\Entity\Entity;

class UsuarioILLA extends Entity
{
    public function getEsBeneficiario(){
        return preg_match('/\\Beneficiario$/',get_class($this));
    }
    public function miCuenta(){
        return model('Modulos\Pagina\Models\Cls_cuentas')->find($this->cuenta);
    }
    public function miModelo(){
        if ($this->esBeneficiario){
            return model('Modulos\Pagina\Models\Cls_beneficiarios');
        }
        else{
            return model('Modulos\Pagina\Models\Cls_comercios');
        }
    }
    public function registraCuenta($cuenta){
        $this->cuenta=$cuenta->id;
        try{
            $this->miModelo()->save($this);
            if (!$this->esBeneficiario){
                $this->miModelo()->update($this->id,['hashDatos'=>md5(json_encode($this->info))]);
            }
        }
        catch(\Exception $e){
            log_message('debug','Error registrando cuenta '.print_r($e,true));
        }
    }
    public function bloquear($bloquear){
        $resultado=['exito'=>false,'mensaje'=>'Error indeterminado'];
        $miCuenta=model('Modulos\Pagina\Models\Cls_cuentas')->find($this->cuenta);
        if ($bloquear){
            if ($this->bloqueado==1){
                return ['exito'=>true,'mensaje'=>'Ya bloqueado'];
            }
            else{
                $this->bloqueado=1;
                $this->miModelo()->save($this);
                if (!is_null($miCuenta)){                
                    $miCuenta->bloqueate();
                }
                return ['exito'=>true,'mensaje'=>'Bloqueado'];
            }
        }
        else{
            if ($this->bloqueado==0){
                return ['exito'=>true,'mensaje'=>'No bloqueado'];
            }
            else{
                $this->bloqueado=0;
                $this->miModelo()->save($this);
                if (!is_null($miCuenta)){
                    $miCuenta->autorizate();
                }
                return ['exito'=>true,'mensaje'=>'Desbloqueado'];
            }
        }
    }

}
?>