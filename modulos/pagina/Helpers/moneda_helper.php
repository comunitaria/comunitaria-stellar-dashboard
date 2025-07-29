<?php
  function formatoMoneda($numero){
    $valor=floatval($numero);
    $decimales=strlen(strrchr($valor,"."))-1;
    if ($decimales<2) $decimales=2;
    return number_format($valor,$decimales);

}     
?>