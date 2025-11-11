<?php
// CLI script to set up ILLA asset: ensure XLM, change trust on distributor, and issue initial supply.
// Usage: php scripts/setup_illa.php <amount>

use CodeIgniter\Config\DotEnv;
use Soneso\StellarSDK\StellarSDK;
use Soneso\StellarSDK\AssetTypeCreditAlphanum4;
use Soneso\StellarSDK\TransactionBuilder;
use Soneso\StellarSDK\ChangeTrustOperationBuilder;
use Soneso\StellarSDK\PaymentOperationBuilder;
use Soneso\StellarSDK\Crypto\KeyPair;
use Soneso\StellarSDK\Crypto\StrKey;
use Soneso\StellarSDK\Network;

// Bootstrap minimal: Composer autoload + load .env (no CI front controller)
require __DIR__ . '/../vendor/autoload.php';
$root = realpath(__DIR__ . '/..');
if (file_exists($root.'/.env')) {
    (new DotEnv($root))->load();
}

if (php_sapi_name() !== 'cli') {
    fwrite(STDERR, "Run from CLI\n");
    exit(1);
}

$amount = $argv[1] ?? null;
if ($amount === null) {
    fwrite(STDERR, "Usage: php scripts/setup_illa.php <amount_to_issue>\n");
    exit(1);
}

$network = getenv('moneda.red') === 'public' ? Network::public() : Network::testnet();
$sdk = new StellarSDK(getenv('moneda.nodo.' . getenv('moneda.red')));

$issuerPub = 'G' . getenv('moneda.emisora.publica');
$issuerSec = 'S' . getenv('moneda.emisora.privada');
$distPub = 'G' . getenv('moneda.distribuidora.publica');
$distSec = 'S' . getenv('moneda.distribuidora.privada');
$assetCode = getenv('moneda.nombre');

$issuerKP = KeyPair::fromPrivateKey(StrKey::decodeSeed($issuerSec));
$distKP = KeyPair::fromPrivateKey(StrKey::decodeSeed($distSec));
$asset = new AssetTypeCreditAlphanum4($assetCode, $issuerPub);

function info($m){ fwrite(STDOUT, $m."\n"); }
function fail($m){ fwrite(STDERR, $m."\n"); exit(1);} 

// 1) Ensure distributor trustline exists
try {
    $distAcc = $sdk->requestAccount($distPub);
} catch (\Throwable $e) { fail('Distributor account not funded yet: '.$e->getMessage()); }

$hasTrust = false;
foreach ($distAcc->getBalances() as $bal) {
    if ($bal->getAssetType() !== 'native' && $bal->getAssetCode() === $assetCode && $bal->getAssetIssuer() === $issuerPub) {
        $hasTrust = true;
        break;
    }
}

if (!$hasTrust) {
    info('Creating trustline from distributor to '.$assetCode.' issued by '.$issuerPub.' ...');
    $ct = (new ChangeTrustOperationBuilder($asset, null))->build(); // null = max limit
    $tx = (new TransactionBuilder($distAcc))->addOperation($ct)->build();
    $tx->sign($distKP, $network);
    $res = $sdk->submitTransaction($tx);
    if (!$res->isSuccessful()) {
        fail('ChangeTrust failed: '.print_r($res, true));
    }
    info('Trustline created.');
    $distAcc = $sdk->requestAccount($distPub); // refresh
}

// 2) If issuer had AUTH_REQUIRED, we would authorize here (skipped as your issuer has auth_required=false)

// 3) Issue initial supply from issuer to distributor
info('Issuing '.$amount.' '.$assetCode.' from issuer to distributor ...');
$issuerAcc = $sdk->requestAccount($issuerPub);
$payment = (new PaymentOperationBuilder($distPub, $asset, number_format((float)$amount, 7)))->build();
$tx = (new TransactionBuilder($issuerAcc))->addOperation($payment)->build();
$tx->sign($issuerKP, $network);
$res = $sdk->submitTransaction($tx);
if (!$res->isSuccessful()) fail('Payment failed: '.print_r($res, true));
info('Done.');
