<?php 
namespace Modulos\Pagina\Libraries;
use Soneso\StellarSDK\StellarSDK;
use Soneso\StellarSDK\Asset;
use Soneso\StellarSDK\Network;
use Soneso\StellarSDK\Memo;
use Soneso\StellarSDK\PaymentOperationBuilder;
use Soneso\StellarSDK\CreateAccountOperationBuilder;
use Soneso\StellarSDK\AllowTrustOperationBuilder;
use Soneso\StellarSDK\TransactionBuilder;
use Soneso\StellarSDK\AssetTypeCreditAlphanum4;
use Soneso\StellarSDK\Crypto\KeyPair;
use Soneso\StellarSDK\Crypto\StrKey;
use Soneso\StellarSDK\Exceptions\HorizonRequestException;
use Soneso\StellarSDK\Responses\Operations\PaymentOperationResponse;
use Soneso\StellarSDK\Responses\Operations\CreateAccountOperationResponse;
class Stellar
{

    private $sdk;
    private $red;
    public function __construct(){
        $this->sdk= new StellarSDK(getenv('moneda.nodo.'.getenv('moneda.red')));
        if (getenv('moneda.red')=='public'){
            $this->red= Network::public();
        }
        else{
            $this->red= Network::testnet();
        }

    }
    public function bloquea()
    {
    }
    public function crearCuenta($de,$a,$cantidad){
        // $de es clave publica o alias 'emisora', 'distribuidora'
        if (($de=='emisora')||($de=='distribuidora')){
            return $this->crearCuentaReal(getenv('moneda.'.$de.'.publica'),$a,$cantidad,getenv('moneda.'.$de.'.privada'));
        }
        else{
            return ['exito'=>false,'mensaje'=>'Solo gobernanza'];
        }
    }
    private function crearCuentaReal($de,$a,$cantidad,$privadaDe){
           
        $cuentaDe = $this->sdk->requestAccount('G'.$de);
        $pagadorPar = KeyPair::fromPrivateKey(StrKey::decodeSeed('S'.$privadaDe));
        try{
            $paymentOperation = (new CreateAccountOperationBuilder('G'.$a, number_format($cantidad,7)))->build();
            $transaction = (new TransactionBuilder($cuentaDe))->addOperation($paymentOperation)->build();
            
            /// Sign the transaction with the key pair of your existing account.
            $transaction->sign($pagadorPar, $this->red);

            /// Submit the transaction to the stellar network.
            $response = $this->sdk->submitTransaction($transaction);
            return ['exito'=>$response->isSuccessful(),'mensaje'=>$response->isSuccessful()?'':'Error transaccion='.print_r($response,true)];
         }
         catch(HorizonRequestException $e){
             return ['exito'=>false,'mensaje'=>$this->motivoErrorStellar($e->getHorizonErrorResponse())];
         }
         catch(\Exception $e){
            log_message('debug','Excepcion de tipo '.get_class($e).'. '.$e->getMessage());
            return ['exito'=>false,'mensaje'=>$e->getMessage()];
        }
        
    }
    public function balances($clave){
        $respuesta=['existe'=>false, 'trustline'=>false, 'autorizada'=>false, 'XLM'=>0,'cripto'=>0];
        try{
            $account = $this->sdk->requestAccount('G'.$clave);
            $respuesta['existe']=true;
            // You can check the `balance`, `sequence`, `flags`, `signers`, `data` etc.
            foreach ($account->getBalances() as $balance) {
                switch ($balance->getAssetType()) {
                    case Asset::TYPE_NATIVE:
                        $respuesta['XLM']=$balance->getBalance();
                        break;
                    default:
                        if ($balance->getAssetCode()==getenv('moneda.nombre')){
                            $respuesta['cripto']=$balance->getBalance();
                            $respuesta['trustline']=true;
                            $respuesta['autorizada']=$balance->getIsAuthorized();
                        }
                }
            }
        }
        catch(HorizonRequestException $e){
            $infoError=$this->motivoErrorStellar($e->getHorizonErrorResponse());
            log_message('debug','Excepcion Stellar '.$infoError['causa']);
        }
        catch(\Exception $e){
           log_message('debug','Excepcion de tipo '.get_class($e).'. '.$e->getMessage());
        }
        return $respuesta;
    }
    public function transfiereXLM($de,$a,$cantidad,$privadaDe=''){
        // $de es clave publica o alias 'emisora', 'distribuidora'
        if (($de=='emisora')||($de=='distribuidora')){
            return $this->transfiereXLMReal(getenv('moneda.'.$de.'.publica'),$a,number_format($cantidad,7),getenv('moneda.'.$de.'.privada'));
        }
        else{
            return ['exito'=>false,'mensaje'=>'Solo gobernanza'];
        }
    }
    private function transfiereXLMReal($de,$a,$cantidad,$privadaDe){
        $cuentaDe = $this->sdk->requestAccount('G'.$de);
        $pagadorPar = KeyPair::fromPrivateKey(StrKey::decodeSeed('S'.$privadaDe));
        try{
            $paymentOperation = (new PaymentOperationBuilder('G'.$a, Asset::native(), $cantidad))->build();
            $transaction = (new TransactionBuilder($cuentaDe))->addOperation($paymentOperation)->build();
            
            /// Sign the transaction with the key pair of your existing account.
            $transaction->sign($pagadorPar, $this->red);

            /// Submit the transaction to the stellar network.
            $response = $this->sdk->submitTransaction($transaction);
            return ['exito'=>$response->isSuccessful(),'mensaje'=>'Respuesta='.print_r($response,true)];
         }
         catch(HorizonRequestException $e){
             return ['exito'=>false,'mensaje'=>$this->motivoErrorStellar($e->getHorizonErrorResponse())];
         }
         catch(\Exception $e){
            log_message('debug','Excepcion de tipo '.get_class($e).'. '.$e->getMessage());
            return ['exito'=>false,'mensaje'=>$e->getMessage()];
        }
        
    }
    public function transfiereCripto($de,$a,$cantidad,$privadaDe=''){
        // $de es clave publica o alias 'emisora', 'distribuidora'
        $respuesta=['exito'=>false,'mensaje'=>'Transferencia imposible'];
        if (($de=='emisora')||($de=='distribuidora')){
            return $this->transfiereCriptoReal(getenv('moneda.'.$de.'.publica'),$a,number_format($cantidad,7),getenv('moneda.'.$de.'.privada'),'Donación');
        }
        else{
            $respuesta['mensaje']='Solo gobernanza';
            return $respuesta;
        }
    }
    private function transfiereCriptoReal($de,$a,$cantidad,$privadaDe,$mensaje){
        $respuesta=['exito'=>false,'mensaje'=>'Transferencia imposible'];
        $cuentaDe = $this->sdk->requestAccount('G'.$de);
        $pagadorPar = KeyPair::fromPrivateKey(StrKey::decodeSeed('S'.$privadaDe));
        try{
            $cripto = new AssetTypeCreditAlphanum4(getenv('moneda.nombre'), 'G'.getenv('moneda.emisora.publica'));
            $paymentOperation = (new PaymentOperationBuilder('G'.$a, $cripto, $cantidad))->build();
            $transaction = (new TransactionBuilder($cuentaDe))->addOperation($paymentOperation)->addMemo(Memo::text($mensaje))->build();
            
            /// Sign the transaction with the key pair of your existing account.
            $transaction->sign($pagadorPar, $this->red);

            /// Submit the transaction to the stellar network.
            $response = $this->sdk->submitTransaction($transaction);
            if (!$response->isSuccessful()){
                $respuesta['mensaje']='Transferencia rechazada por la red Stellar';
            }
            else{
                $respuesta['mensaje']=$cantidad;
                $respuesta['exito']=true;
            }
            return $respuesta;
         }
         catch(HorizonRequestException $e){
             return ['exito'=>false,'mensaje'=>$this->motivoErrorStellar($e->getHorizonErrorResponse())];
         }
         catch(\Exception $e){
            log_message('debug','Excepcion de tipo '.get_class($e).'. '.$e->getMessage());
            return ['exito'=>false,'mensaje'=>$e->getMessage()];
        }
        
    }
    public function desautorizarCuenta($a){
        return $this->autorizarCuenta($a,0);
    }
    public function autorizarCuenta($a,$autorizar=1){
        $cuentaDe = $this->sdk->requestAccount('G'.getenv('moneda.emisora.publica'));
        $pagadorPar = KeyPair::fromPrivateKey(StrKey::decodeSeed('S'.getenv('moneda.emisora.privada')));
        try{
            $aop = (new AllowTrustOperationBuilder('G'.$a, getenv('moneda.nombre'), $autorizar, 0))->build(); // authorize
            $transaction = (new TransactionBuilder($cuentaDe))->addOperation($aop)->build();

            /// Sign the transaction with the key pair of your existing account.
            $transaction->sign($pagadorPar, $this->red);

            /// Submit the transaction to the stellar network.
            $response = $this->sdk->submitTransaction($transaction);
            return ['exito'=>$response->isSuccessful(),'mensaje'=>'Respuesta='.print_r($response,true)];
         }
         catch(HorizonRequestException $e){
             return ['exito'=>false,'mensaje'=>$this->motivoErrorStellar($e->getHorizonErrorResponse())];
         }
         catch(\Exception $e){
            log_message('debug','Excepcion de tipo '.get_class($e).'. '.$e->getMessage());
            return ['exito'=>false,'mensaje'=>$e->getMessage()];
        }
        
    }
    public function transacciones($tipoUsuario,$idUsuario,$cuenta,$ultimoID,$cuentasSistema){
        $respuesta=[];
        try{
            $sigue=false;
            $operationsPage=$this->sdk->payments()->forAccount('G'.$cuenta)->cursor($ultimoID)->order('asc')->limit(200)->execute();
            $i=0;
            do{                    
                foreach ($operationsPage->getOperations() as $payment) {
                    if ((($payment instanceof PaymentOperationResponse)||($payment instanceof CreateAccountOperationResponse)) &&($payment->isTransactionSuccessful())) {
                        $moneda=-1;
                        $cantidad=0;
                        $pagador='';
                        $cobrador='';
                        if (($payment instanceof PaymentOperationResponse)){
                            $asset=$payment->getAsset();
                            if ($asset->getType()==Asset::TYPE_NATIVE){
                                $moneda=0;
                            }
                            switch($asset->getType()){
                                case Asset::TYPE_NATIVE:
                                    $moneda=0;
                                    break;
                                case Asset::TYPE_CREDIT_ALPHANUM_4:
                                    if ($asset->getCode()==getenv('moneda.nombre')){
                                        $moneda=1;
                                    }
                                    break;
                            }
                            $cantidad=$payment->getAmount();
                            $cobrador=substr($payment->getTo(),-55);
                            $pagador=substr($payment->getFrom(),-55);
                        }
                        if (($payment instanceof CreateAccountOperationResponse)){
                            $moneda=0;
                            $cantidad=$payment->getStartingBalance();
                            $cobrador=substr($payment->getAccount(),-55);
                            $pagador=substr($payment->getFunder(),-55);
                        }
                        if ($moneda>=0){
                            $de_a_tipoUsuario=-1;
                            $de_a_usuario=-1;
                            if ($cobrador==$cuenta){
                                foreach($cuentasSistema as $idSistema=>$cuentaSistema){
                                    if ($pagador==$cuentaSistema){
                                        $de_a_tipoUsuario=0;
                                        $de_a_usuario=$idSistema;
                                    }
                                }
                                if ($de_a_tipoUsuario!=0)
                                    $respuesta[]='(2,'.$idUsuario.','.$tipoUsuario.','.$moneda.',"'.$cantidad.'","'.$payment->getCreatedAt().'","'.$pagador.'",'.$de_a_tipoUsuario.','.$de_a_usuario.','.$payment->getOperationId().')';
    //                            if ($de_a_usuario>0){
                                 //   $respuesta[]='(1,'.$de_a_usuario.','.$de_a_tipoUsuario.','.$moneda.',"'.$cantidad.'","'.$payment->getCreatedAt().'","'.$cobrador.'",'.$tipoUsuario.','.$idUsuario.','.$payment->getOperationId().')';
      //                          }
                            }
                            if ($pagador==$cuenta){
                                foreach($cuentasSistema as $idSistema=>$cuentaSistema){
                                    if ($cobrador==$cuentaSistema){
                                        $de_a_tipoUsuario=0;
                                        $de_a_usuario=$idSistema;
                                    }
                                }
                                if (($de_a_tipoUsuario!=0)||($tipoUsuario==0)){
                                    $respuesta[]='(1,'.$idUsuario.','.$tipoUsuario.','.$moneda.',"'.$cantidad.'","'.$payment->getCreatedAt().'","'.$cobrador.'",'.$de_a_tipoUsuario.','.$de_a_usuario.','.$payment->getOperationId().')';
                                }
                                if ($de_a_usuario>0){
                                    //$respuesta[]='(2,'.$de_a_usuario.','.$de_a_tipoUsuario.','.$moneda.',"'.$cantidad.'","'.$payment->getCreatedAt().'","'.$pagador.'",'.$tipoUsuario.','.$idUsuario.','.$payment->getOperationId().')';
                                }
                            }    
                        }
                    }
                }
                $operationsPage=$operationsPage->getNextPage();

            }while($operationsPage->getOperations()->count()>0);
        }
        catch(HorizonRequestException $e){
            log_message('debug','Excepcion Horizon '.print_r($e->getHorizonErrorResponse(),true));
        }
        catch(\Exception $e){
           log_message('debug','Excepcion de tipo '.get_class($e).'. '.$e->getMessage());
       }
       return $respuesta;
    }
    private function motivoErrorStellar($dice){
        try{
                    
            switch($dice->getStatus()){
                case '404':
                    return ['causa'=>'no existe'];
                case '400':
                    return ['causa'=>$dice->getExtras()];
                default:
                    return ['causa'=>$dice->getStatus()];
                }
        }
        catch(\Exception $e){
            return ['causa'=>'desconocida'];
        }

    }
    
} 