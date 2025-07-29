<?php
namespace Modulos\Pagina\Entities;


class Comercio extends UsuarioILLA
{
    public function ficheroLogo(){
        if ($this->logo)
            return base_url('public/assets/imagenes/logos').(file_exists(ROOTPATH . 'public/assets/imagenes/logos/logo'.$this->id.'.png')? '/logo'.$this->id.'.png?'.strtotime('now') : '/logoDf.png');
        else
            return base_url('public/assets/imagenes/logos').'/logoDf.png';
    }
    public function getClases(){
        $db=db_connect();
        $clases=[];
        $lista=$db->query('SELECT clases.id, clases.clase FROM clases_comercio LEFT JOIN clases ON clases.id=clases_comercio.clase WHERE clases_comercio.comercio='.$this->id)->getResult();
        foreach ($lista as $unaClase) {
            $clases[$unaClase->id]=$unaClase->clase;
        }
        return $clases;
    }
    public function insert($valores){
        $modelo=model('Modulos\Pagina\Models\Cls_comercios');
        if ($modelo->insert($valores,false)){
            $this->id=$modelo->getInsertID();
            model('Modulos\Pagina\Models\Cls_comercios')->update($this->id,['hashDatos'=>md5(json_encode(model('Modulos\Pagina\Models\Cls_comercios')->find($this->id)->info))]);
            $this->grabaClases($valores['clases']??[]);
            return $modelo->getInsertID();
        }
        return -1;
    }
    public function update($valores){
        $res=model('Modulos\Pagina\Models\Cls_comercios')->update($this->id,$valores);
        if ($res){
            model('Modulos\Pagina\Models\Cls_comercios')->update($this->id,['hashDatos'=>md5(json_encode(model('Modulos\Pagina\Models\Cls_comercios')->find($this->id)->info))]);
            $this->grabaClases($valores['clases']??[]);
        }
    }
    private function grabaClases($lista){
        $db=db_connect();
        $db->query('DELETE FROM clases_comercio WHERE comercio='.$this->id);
        foreach($lista as $unaClase){
            $db->query('INSERT INTO clases_comercio SET clase='.$unaClase.',comercio='.$this->id);
        }
        $db->query('DELETE FROM clases WHERE id NOT IN (SELECT clase FROM clases_comercio UNION SELECT beneficiarios.clase FROM beneficiarios)');        
    }
    public function getInfo(){
        $miCuenta=model('Modulos\Pagina\Models\Cls_cuentas')->find($this->cuenta);
        return [
            "id"=> $this->id,
            "nombre"=> $this->nombre,
            "cuenta"=> is_null($miCuenta)?'':'G'.$miCuenta->clave,
            "CIF"=> $this->CIF??'',
            "contacto"=> $this->contacto??'',
            "direccion"=> $this->direccion??'',
            "movil"=> $this->movil??'',
            "correo"=> $this->correo??'',
            "coordenadas"=> $this->coordenadas??'',
            "logo"=> 'data:image/png;base64,' . base64_encode(file_get_contents($this->ficheroLogo())) ,
        ];
    }
    public function transaccionesMensuales($meses){
        $db=db_connect();
        $diaInicial=strtotime($meses.' months ago');
        $anomesInicial=date('m',$diaInicial)+date('y',$diaInicial)*12;
        $resTransacciones=$db->query("SELECT MONTH(momento)-1+(YEAR(momento)-2000)*12 as anomes, SUM(cantidad) as mandado  
                                        FROM transacciones 
                                        WHERE tipoUsuario=2 AND usuario=".$this->id." AND moneda=1 AND de_a_tipoUsuario=0 AND momento>=LAST_DAY(NOW() - INTERVAL ".$meses." MONTH) + INTERVAL 1 DAY
                                        GROUP BY MONTH(momento), YEAR(momento)
                                        ORDER BY anomes")->getResult();
        $resPagos=$db->query("SELECT de_mes-1+(de_ano-2000)*12 AS anomes, SUM(euros) as pagado  
                                        FROM pagos 
                                        WHERE usuario=".$this->id." AND de_mes+(de_ano-2000)*12>=".$anomesInicial."
                                        GROUP BY de_mes, de_ano
                                        ORDER BY anomes")->getResult();
        
        $lista=[];
        for($mes=0;$mes<$meses;$mes++){
            $lista[$anomesInicial+$mes]=['t'=>0,'p'=>0];
        }
        foreach($resTransacciones as $unaTransaccion){
            $lista[$unaTransaccion->anomes]['t']=floatval($unaTransaccion->mandado);
        }
        foreach($resPagos as $unPago){
            $lista[$unPago->anomes]['p']=floatval($unPago->pagado);
        }
        return $lista;
    }
    public function pago($mes,$ano,$importe,$factura,$notas){
        $db=db_connect();
        if ($db->query("INSERT INTO pagos (usuario,euros,de_mes,de_ano,factura,notas) VALUES (".$this->id.",".floatval($importe).",".intval($mes+1).",".intval($ano).",'".$factura."','".$notas."')")){
            $this->pagadoILLA+=floatval($importe);
            try{
                model('Modulos\Pagina\Models\Cls_comercios')->save($this);
            }
            catch(\Exception $e){
                log_message('debug','error registrando pago '.$this->pagadoILLA);
            }
            return ['exito'=>true, 'mensaje'=>'Registrado'];
        }
        else{
            log_message('debug','error registrando pago '.print_r($db->error(),true));
            return ['exito'=>false, 'mensaje'=>'Insercion imposible'];
        }  
              
    }
}
?>