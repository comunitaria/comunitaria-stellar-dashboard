<?php
//VPconf: variables globales para hooks y filtros
$vpconf_hooks=[];
$vpconf_filtros=[];
function anade_filtro($objeto,$funcion){
    global $vpconf_filtros;
    if (strpos($_SERVER['PHP_SELF'],'/vpconf/')===false){
        if (!isset($vpconf_filtros[$objeto])) $vpconf_filtros[$objeto]=[];
      $vpconf_filtros[$objeto][]=$funcion;  
    }
}
function procesa_filtro($objeto,$entrada=''){
    global $vpconf_filtros;
    $devolver=$entrada;
    if (strpos($_SERVER['PHP_SELF'],'/vpconf/')===false){
        if (isset($vpconf_filtros[$objeto])){
            foreach($vpconf_filtros[$objeto] as $unaFuncion){
                $devolver=$unaFuncion($devolver);
            }
        }
    }
    return $devolver;
}

?>