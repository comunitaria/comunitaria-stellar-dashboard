#!/usr/bin/env bash
#
# acunar.sh — Emite (acuña) ILLA desde la cuenta EMISORA hacia la DISTRIBUIDORA.
#
# Emitir ILLA = un pago del Issuer (emisora) al Distributor (distribuidora) del
# propio asset: la emisora "crea" la moneda al enviarla (no necesita saldo
# previo). Es el paso 4 del README ("Emitir el activo"), pero automatizado:
# lee las claves del .env y firma con la emisora dentro del contenedor de la app.
#
# Uso (ejecutar en el SERVIDOR, donde corre docker):
#   ./scripts/acunar.sh <cantidad> [-y]
#
# Ejemplos:
#   ./scripts/acunar.sh 1000        # acuña 1000 ILLA a la distribuidora (pide confirmación)
#   ./scripts/acunar.sh 1000 -y     # sin confirmación interactiva
#
# Variables opcionales:
#   APP_CONTAINER   nombre del contenedor de la app (default: comunitaria-app)
#
# OJO: esto AUMENTA la oferta de ILLA en circulación. Es una decisión de
# gobernanza; usarlo cuando la distribuidora se quede sin ILLA para distribuir.

APP_CONTAINER="${APP_CONTAINER:-comunitaria-app}"
RAIZ="$(cd "$(dirname "$0")/.." && pwd)"
ENV="$RAIZ/.env"

CANT="${1:-}"
CONFIRMAR=1
[ "${2:-}" = "-y" ] && CONFIRMAR=0

# Validación de la cantidad
if [ -z "$CANT" ] || ! echo "$CANT" | grep -qE '^[0-9]+(\.[0-9]{1,7})?$'; then
  echo "Uso: $0 <cantidad> [-y]   (cantidad: número positivo, hasta 7 decimales)"
  exit 1
fi
if ! awk "BEGIN{exit !($CANT>0)}"; then
  echo "La cantidad debe ser mayor que 0."
  exit 1
fi

[ -f "$ENV" ] || { echo "No encuentro el .env en $ENV"; exit 1; }

# Lee un valor del .env (formato CI4: clave = 'valor' / clave=valor)
val(){ grep -E "^[[:space:]]*$1[[:space:]]*=" "$ENV" | head -1 | sed -E "s/^[^=]*=[[:space:]]*//; s/^['\"]//; s/['\"].*$//" | tr -d "[:space:]"; }

EPUB="$(val 'moneda.emisora.publica')"
EPRIV="$(val 'moneda.emisora.privada')"
DPUB="$(val 'moneda.distribuidora.publica')"
CRIPTO="$(val 'moneda.nombre')"
RED="$(val 'moneda.red')"
NODO="$(val "moneda.nodo.$RED")"
CANT7="$(printf '%.7f' "$CANT")"

if [ -z "$EPUB" ] || [ -z "$EPRIV" ] || [ -z "$DPUB" ] || [ -z "$NODO" ]; then
  echo "Faltan valores en el .env (moneda.emisora.* / moneda.distribuidora.publica / moneda.nodo.$RED)."
  exit 1
fi

echo "Red:           $RED ($NODO)"
echo "Emisora:       G$EPUB"
echo "Distribuidora: G$DPUB"
echo "A emitir:      $CANT7 $CRIPTO"
echo

if [ "$CONFIRMAR" = "1" ]; then
  printf "Esto EMITE %s %s (aumenta la oferta). Escribí 'si' para continuar: " "$CANT7" "$CRIPTO"
  read -r R
  [ "$R" = "si" ] || { echo "Cancelado."; exit 0; }
fi

sudo docker exec \
  -e EPUB="$EPUB" -e EPRIV="$EPRIV" -e DPUB="$DPUB" \
  -e CRIPTO="$CRIPTO" -e CANT="$CANT7" -e NODO="$NODO" -e RED="$RED" \
  "$APP_CONTAINER" php -r '
require "/var/www/html/vendor/autoload.php";
use Soneso\StellarSDK\StellarSDK;
use Soneso\StellarSDK\Network;
use Soneso\StellarSDK\AssetTypeCreditAlphanum4;
use Soneso\StellarSDK\PaymentOperationBuilder;
use Soneso\StellarSDK\TransactionBuilder;
use Soneso\StellarSDK\Crypto\KeyPair;
use Soneso\StellarSDK\Crypto\StrKey;
$sdk = new StellarSDK(getenv("NODO"));
$par = KeyPair::fromPrivateKey(StrKey::decodeSeed("S".getenv("EPRIV")));
if (substr($par->getAccountId(),1) !== getenv("EPUB")) {
    fwrite(STDERR, "ERROR: la clave privada del .env no corresponde a la emisora.\n"); exit(1);
}
$cuentaDe = $sdk->requestAccount("G".getenv("EPUB"));
$asset = new AssetTypeCreditAlphanum4(getenv("CRIPTO"), "G".getenv("EPUB"));
$op = (new PaymentOperationBuilder("G".getenv("DPUB"), $asset, getenv("CANT")))->build();
$tx = (new TransactionBuilder($cuentaDe))->addOperation($op)->build();
$tx->sign($par, getenv("RED")=="public" ? Network::public() : Network::testnet());
$r = $sdk->submitTransaction($tx);
if ($r->isSuccessful()) { echo "OK: emitidos ".getenv("CANT")." ".getenv("CRIPTO")." a la distribuidora\n"; }
else { fwrite(STDERR, "FALLO al emitir (transacción rechazada por la red)\n"); exit(1); }
'
RES=$?
echo
if [ "$RES" = "0" ]; then
  echo "Saldo actual de la distribuidora:"
  curl -s "$NODO/accounts/G$DPUB" 2>/dev/null | python3 -c "
import json,sys
try:
    d=json.load(sys.stdin)
    for b in d.get('balances',[]):
        if b.get('asset_code')=='$CRIPTO': print('  '+b['balance']+' $CRIPTO')
except Exception: pass
" 2>/dev/null || true
fi
exit $RES
