<?php
function enlaces_dashboard(&$enlaces){
    anade_enlaces($enlaces,[
        //<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
        //<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    ]);
}
function dashboard_header($titulo,$tituloENG="",$dashboard_link,$dashboard_active){
    //titulo del dashboard
    //tengo que ver los enlaces que harían falta (dependencias)
    //a su vez toda esta página está dentro de una class=content-wrapper
<<<HTML
<!-- para que sea código HTML-->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">$titulo</h1>
                <h3 class="m-0">$tituloENG</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="$dashboard_link">Todos</a></li>
                <li class="breadcrumb-item active">$dashboard_active</li>
                </ol>
            </div>
        </div>
     </div>
</div>
HTML;
}
function dashboard_buttons(&$enlaces){
    //botones grandes que aparecen en el dashboard
    //p.e: peticiones en curso, peticiones borrador... de colores
    //green->success yellow->warning red->danger blue->info
<<<HTML
<section class="content">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
               <div class="inner">
                  <h3>$nTipo1</h3>
                  <p>$tipo1</p>
               </div>
               <div class="icon">
                  <i class="ion ion-bag"></i>
                  <!-- <ion-icon name="time-outline"></ion-icon> -->
               </div>
               <!-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
            </div>
         </div>
         <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
               <div class="inner">
                  <h3>$nTipo2</h3>
                  <p>$tipo2</p>
               </div>
               <div class="icon">
                  <i class="ion ion-stats-bars"></i>
                  <!-- <ion-icon name="warning-outline"></ion-icon> -->
               </div>
               <!-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
            </div>
         </div>
         <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
               <div class="inner">
                  <h3>$nTipo3</h3>
                  <p>$tipo3</p>
               </div>
               <div class="icon">
                  <i class="ion ion-person-add"></i>
                  <!-- <ion-icon name="warning-outline"></ion-icon> -->
               </div>
               <!-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
            </div>
         </div>
         <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
               <div class="inner">
                  <h3>$nTipo4</h3>
                  <p>$tipo4</p>
               </div>
               <div class="icon">
                  <i class="ion ion-pie-graph"></i>
                  <!-- <ion-icon name="timer-outline"></ion-icon> -->
               </div>
               <!-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
            </div>
         </div>
      </div>
   </div>
</section>
HTML;
}
?>