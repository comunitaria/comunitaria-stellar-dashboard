<?php
/**
 * Apartado admin: enlaces/QR de consulta de saldo por comercio activo.
 * Variables: $comercios (array de ['nombre','cif','url','qr']), $usuario, $VPConf
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Enlaces de saldo para empleados</title>
    <style>
        * { box-sizing: border-box; }
        body { margin:0; font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;
               background:#f5f6f8; color:#222; }
        header { background:#fff; border-bottom:1px solid #e0e0e0; padding:16px 20px; display:flex;
                 align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px; }
        header h1 { font-size:1.2rem; margin:0; }
        header .sub { font-size:.8rem; color:#666; margin:2px 0 0; }
        .wrap { max-width:980px; margin:0 auto; padding:20px 16px 48px; }
        .grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:16px; }
        .card { background:#fff; border:1px solid #e0e0e0; border-radius:14px; padding:18px; text-align:center; }
        .card h2 { font-size:1.05rem; margin:0 0 2px; }
        .cif { font-size:.75rem; color:#888; margin:0 0 10px; }
        .card img { width:180px; height:180px; }
        .url { font-family:ui-monospace,Menlo,monospace; font-size:.7rem; word-break:break-all;
               color:#555; background:#f0f1f3; border-radius:8px; padding:8px; margin-top:8px; }
        button, .btn { font:inherit; font-size:.85rem; border:1px solid #e0e0e0; background:#fff; color:#333;
                 border-radius:9px; padding:8px 14px; cursor:pointer; text-decoration:none; display:inline-block; }
        button:active { background:#f0f1f3; }
        .vacio { text-align:center; color:#888; padding:40px; }
        @media print { header .noprint, .card .noprint { display:none !important; }
                       body { background:#fff; } .card { border:1px dashed #bbb; break-inside:avoid; } }
    </style>
</head>
<body>
<header>
    <div>
        <h1>Enlaces de saldo para empleados</h1>
        <p class="sub">Cada QR abre el saldo del comercio en modo solo lectura (sin acceso a fondos).</p>
    </div>
    <div class="noprint">
        <a class="btn" href="<?= esc(base_url('comercios'), 'attr') ?>">&larr; Comercios</a>
        <button onclick="window.print()">Imprimir todo</button>
    </div>
</header>

<div class="wrap">
    <?php if (empty($comercios)): ?>
        <p class="vacio">No hay comercios activos.</p>
    <?php else: ?>
        <div class="grid">
            <?php foreach ($comercios as $i => $c): ?>
                <div class="card">
                    <h2><?= esc($c['nombre']) ?></h2>
                    <p class="cif"><?= $c['cif'] !== '' ? 'CIF ' . esc($c['cif']) : '&nbsp;' ?></p>
                    <img src="<?= esc($c['qr'], 'attr') ?>" alt="QR de <?= esc($c['nombre'], 'attr') ?>">
                    <div class="url"><?= esc($c['url']) ?></div>
                    <div class="noprint" style="margin-top:8px">
                        <button onclick="copiar('<?= esc($c['url'], 'js') ?>')">Copiar enlace</button>
                        <a class="btn" href="<?= esc($c['url'], 'attr') ?>" target="_blank" rel="noopener">Abrir</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    function copiar(txt) {
        if (navigator.clipboard) { navigator.clipboard.writeText(txt); }
    }
</script>
</body>
</html>
