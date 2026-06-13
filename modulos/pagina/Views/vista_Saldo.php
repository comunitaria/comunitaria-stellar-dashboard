<?php
/**
 * Página pública de solo lectura del saldo de un comercio.
 * Variables: $nombre, $clavePublica, $saldo, $existe, $autorizada,
 *            $moneda, $emisora, $nodo, $urlSaldo, $urlQr
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Saldo · <?= esc($nombre) ?></title>
    <style>
        :root { --verde:#2e7d32; --gris:#666; --borde:#e0e0e0; }
        * { box-sizing: border-box; }
        body { margin:0; font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;
               background:#f5f6f8; color:#222; -webkit-font-smoothing:antialiased; }
        .wrap { max-width:480px; margin:0 auto; padding:24px 16px 48px; }
        .card { background:#fff; border:1px solid var(--borde); border-radius:16px; padding:24px;
                box-shadow:0 1px 3px rgba(0,0,0,.06); margin-bottom:16px; text-align:center; }
        .nombre { font-size:1.3rem; font-weight:700; margin:0 0 4px; }
        .etiqueta { font-size:.8rem; text-transform:uppercase; letter-spacing:.05em; color:var(--gris); margin:0; }
        .saldo { font-size:3rem; font-weight:800; color:var(--verde); margin:8px 0 0; line-height:1.1; }
        .moneda { font-size:1rem; font-weight:600; color:var(--gris); }
        .actualizado { font-size:.75rem; color:var(--gris); margin-top:8px; }
        .aviso { background:#fff3cd; border:1px solid #ffe69c; color:#7a5b00; border-radius:10px;
                 padding:12px; font-size:.85rem; margin-top:12px; }
        .qrbox { padding:8px; }
        .qrbox img { width:200px; height:200px; }
        .dir { font-family:ui-monospace,SFMono-Regular,Menlo,monospace; font-size:.72rem; word-break:break-all;
               color:var(--gris); background:#f0f1f3; border-radius:8px; padding:10px; margin-top:8px; }
        .acciones { display:flex; gap:8px; justify-content:center; margin-top:12px; flex-wrap:wrap; }
        button { font:inherit; font-size:.9rem; border:1px solid var(--borde); background:#fff; color:#333;
                 border-radius:10px; padding:10px 16px; cursor:pointer; }
        button:active { background:#f0f1f3; }
        .pie { text-align:center; font-size:.72rem; color:var(--gris); margin-top:8px; }
        @media print {
            body { background:#fff; }
            .noprint { display:none !important; }
            .card { box-shadow:none; border:none; }
        }
    </style>
</head>
<body>
<div class="wrap">

    <div class="card">
        <p class="etiqueta">Saldo del comercio</p>
        <h1 class="nombre"><?= esc($nombre) ?></h1>
        <?php if ($clavePublica === ''): ?>
            <div class="aviso">Este comercio todavía no tiene un monedero configurado.</div>
        <?php else: ?>
            <div class="saldo"><span id="saldo"><?= number_format((float)$saldo, 2) ?></span> <span class="moneda"><?= esc($moneda) ?></span></div>
            <div class="actualizado" id="actualizado">Saldo en vivo</div>
            <?php if (!$existe): ?>
                <div class="aviso">El monedero aún no está activo en la red.</div>
            <?php elseif (!$autorizada): ?>
                <div class="aviso">El monedero todavía no está autorizado para operar.</div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php if ($clavePublica !== ''): ?>
    <div class="card">
        <p class="etiqueta">Cartel para el mostrador</p>
        <div class="qrbox"><img src="<?= esc($urlQr, 'attr') ?>" alt="QR de consulta de saldo"></div>
        <p class="pie">Escanea este QR para ver el saldo en vivo</p>
        <div class="dir"><?= esc($clavePublica) ?></div>
        <div class="acciones noprint">
            <button onclick="window.print()">Imprimir</button>
            <button onclick="copiar('<?= esc($urlSaldo, 'js') ?>')">Copiar enlace</button>
        </div>
    </div>
    <?php endif; ?>

    <p class="pie noprint">Solo lectura · no permite mover fondos</p>
</div>

<script>
    var NODO   = <?= json_encode($nodo) ?>;
    var PUB    = <?= json_encode($clavePublica) ?>;
    var ASSET  = <?= json_encode($moneda) ?>;
    var ISSUER = <?= json_encode($emisora) ?>;

    function formatea(s) {
        var n = parseFloat(s);
        if (isNaN(n)) return s;
        return n.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    function copiar(txt) {
        if (navigator.clipboard) { navigator.clipboard.writeText(txt); }
    }
    async function refrescar() {
        if (!PUB) return;
        try {
            var r = await fetch(NODO + '/accounts/' + PUB);
            if (!r.ok) return;
            var d = await r.json();
            var saldo = '0';
            (d.balances || []).forEach(function (b) {
                if (b.asset_code === ASSET && b.asset_issuer === ISSUER) { saldo = b.balance; }
            });
            var el = document.getElementById('saldo');
            if (el) el.textContent = formatea(saldo);
            var act = document.getElementById('actualizado');
            if (act) act.textContent = 'Actualizado ' + new Date().toLocaleTimeString('es-ES');
        } catch (e) {}
    }
    refrescar();
    setInterval(refrescar, 15000);
</script>
</body>
</html>
