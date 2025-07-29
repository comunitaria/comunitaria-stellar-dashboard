<?php
function sidebar($menu){
    $html=
<<<HTML
    <div class="sidebar">    
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
HTML;
    foreach($menu as $itemMenu){
        $html+=
<<<HTML
                <li class="nav-item">
                    <a href="$itemMenu['href']" class="nav-link {($itemMenu['activo']?'active':'')}">
                        <i class="nav-icon $itemMenu['icono']"></i>
                        <p>$itemMenu['texto']</p>
                    </a>
                </li>
HTML;
    }
    $html+=
    <<<HTML
                </ul>
            </nav>
        </div>    
    HTML;    
    return $html;
}
?>
