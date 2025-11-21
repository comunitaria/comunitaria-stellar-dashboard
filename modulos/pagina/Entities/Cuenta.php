<?php
namespace Modulos\Pagina\Entities;
use Modulos\Pagina\Libraries\Stellar;

use CodeIgniter\Entity\Entity;

class Cuenta extends Entity
{
    public function actualizaEstado(){
        $st=new Stellar();
        $balances=$st->balances($this->clave);
        if (!$balances['existe']){
            $this->creada=0;
        }
        else{
            $this->balanceXLM=$balances['XLM'];
            $this->balanceILLA=$balances['cripto'];
            $this->trustline=$balances['trustline']?1:0;
            $this->autorizada=$balances['autorizada']?1:0;
            $this->creada=1;
        }
        
        try{
            model('Modulos\Pagina\Models\Cls_cuentas')->save($this);
        }
        catch(\Exception $e){}
    }
    public function descripcion(){
        return [
            'publica'=>'G'.$this->clave,
            'estado'=>$this->creada==0?'inexistente':($this->trustline==0?'creada':($this->autorizada==0?($this->bloqueada==0?'trustline':'bloqueada'):'autorizada')),
            'balanceCripto'=>number_format($this->balanceILLA,7),
            'balanceXLM'=>number_format($this->balanceXLM,7),
            'nivelDeCarga'=>round($this->balanceXLM*100/floatval(getenv('moneda.XLM.maximo')),2)
        ];
    }
    public function create(){
        $st=new Stellar();
        $ok=$st->crearCuenta('emisora',$this->clave,floatval(getenv('moneda.XLM.maximo')));
        if (!$ok['exito']){
            $errorMsg = 'Failed to create account on Stellar. ';
            if (isset($ok['mensaje']['causa'])) {
                $errorDetails = $ok['mensaje']['causa'];
                if (is_object($errorDetails)) {
                    $txCode = method_exists($errorDetails, 'getResultCodesTransaction') ? $errorDetails->getResultCodesTransaction() : 'unknown';
                    $opCodes = method_exists($errorDetails, 'getResultCodesOperation') ? $errorDetails->getResultCodesOperation() : [];
                    $errorMsg .= 'Transaction: ' . $txCode . ', Operations: ' . json_encode($opCodes);
                } else {
                    $errorMsg .= 'Causa: ' . $errorDetails;
                }
            }
            log_message('error', $errorMsg);
        } else {
            log_message('info', 'Successfully created account on Stellar for key: '.substr($this->clave, 0, 10).'...');
        }
        $this->actualizaEstado();
    }
    public function autorizate(){
        $st=new Stellar();
        $ok=$st->autorizarCuenta($this->clave);
        $this->actualizaEstado();
        return $ok;
    }
    public function bloqueate(){
        $st=new Stellar();
        $ok=$st->desautorizarCuenta($this->clave);
        $this->actualizaEstado();
        return $ok;
    }
    public function aseguraXLM(){
        $ok=false;
        $this->actualizaEstado();
        if ($this->balanceXLM<getenv('moneda.XLM.minimo')){
            $st=new Stellar();
            $ok=$st->transfiereXLM('distribuidora',$this->clave,floatval(getenv('moneda.XLM.maximo'))-floatval($this->balanceXLM));
            if ($ok){
                $this->actualizaEstado();
                return true;
            }
        }
        else{
            return true;
        }
    }
    public function transferirCripto($cantidad){
        $respuesta=['exito'=>false,'mensaje'=>'Transferencia imposible'];
        $st=new Stellar();
        $respuesta=$st->transfiereCripto('distribuidora',$this->clave,floatval($cantidad));
        if ($respuesta['exito']){
            $this->actualizaEstado();
            return $respuesta;
        }
        return $respuesta;
    }
    public function getSituacion(){
        return $this->creada?($this->trustline?($this->autorizada?\Modulos\Pagina\ESTADO_AUTORIZADA:\Modulos\Pagina\ESTADO_TRUSTLINE):\Modulos\Pagina\ESTADO_CREADA):\Modulos\Pagina\ESTADO_NO_CREADA;
    }
}
?>