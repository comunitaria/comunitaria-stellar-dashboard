<?php
namespace Modulos\Pagina\Libraries;

use Soneso\StellarSDK\Crypto\KeyPair;
use Soneso\StellarSDK\Crypto\StrKey;

/**
 * Custodia de claves Stellar de los usuarios.
 *
 * Genera keypairs y cifra/descifra el secreto en reposo con AES-256-GCM usando
 * la master key del sistema. El secreto NUNCA se guarda en claro; solo se
 * descifra en memoria el instante de firmar. Ver ARQUITECTURA-CUSTODIA.md.
 *
 * Convención del proyecto: las claves públicas se guardan sin la 'G' inicial y
 * los secretos sin la 'S' inicial (55 chars).
 */
class Custodia
{
    const VERSION = 1;

    /**
     * Genera un par nuevo.
     * @return array ['publica' => 55 chars sin G, 'privada' => 55 chars sin S]
     */
    public function generarPar(): array
    {
        $par = KeyPair::random();
        return [
            'publica' => substr($par->getAccountId(), 1),
            'privada' => substr($par->getSecretSeed(), 1),
        ];
    }

    /** Normaliza un secreto: quita la 'S' inicial si viene con ella (56 → 55). */
    public function normalizaSecreto(string $secreto): string
    {
        $secreto = trim($secreto);
        if (strlen($secreto) === 56 && ($secreto[0] === 'S')) {
            return substr($secreto, 1);
        }
        return $secreto;
    }

    /** Devuelve la pública (55 chars sin G) que corresponde a un secreto (sin S). */
    public function publicaDeSecreto(string $secretoSinS): string
    {
        $kp = KeyPair::fromPrivateKey(StrKey::decodeSeed('S' . $secretoSinS));
        return substr($kp->getAccountId(), 1);
    }

    /**
     * Master key (32 bytes). Hoy se lee de env `custodia.masterKey` (64 hex).
     * SEAM: para mover a Google Secret Manager, cambiar solo este método.
     */
    private function masterKey(): string
    {
        $hex = getenv('custodia.masterKey');
        if ($hex === false || $hex === '') {
            throw new \RuntimeException('custodia.masterKey no está configurada');
        }
        $clave = @hex2bin($hex);
        if ($clave === false || strlen($clave) !== 32) {
            throw new \RuntimeException('custodia.masterKey debe ser 64 caracteres hex (32 bytes)');
        }
        return $clave;
    }

    /** Cifra un secreto (55 chars sin S). Devuelve un blob JSON. */
    public function cifra(string $secreto): string
    {
        $iv = random_bytes(12);
        $tag = '';
        $ct = openssl_encrypt($secreto, 'aes-256-gcm', $this->masterKey(), OPENSSL_RAW_DATA, $iv, $tag);
        if ($ct === false) {
            throw new \RuntimeException('No se pudo cifrar el secreto');
        }
        return json_encode([
            'v'   => self::VERSION,
            'iv'  => base64_encode($iv),
            'ct'  => base64_encode($ct),
            'tag' => base64_encode($tag),
        ]);
    }

    /** Descifra un blob JSON y devuelve el secreto (55 chars sin S). */
    public function descifra(string $blob): string
    {
        $o = json_decode($blob, true);
        if (!is_array($o) || !isset($o['iv'], $o['ct'], $o['tag'])) {
            throw new \RuntimeException('Blob de secreto inválido');
        }
        $secreto = openssl_decrypt(
            base64_decode($o['ct']),
            'aes-256-gcm',
            $this->masterKey(),
            OPENSSL_RAW_DATA,
            base64_decode($o['iv']),
            base64_decode($o['tag'])
        );
        if ($secreto === false) {
            throw new \RuntimeException('No se pudo descifrar el secreto custodiado');
        }
        return $secreto;
    }
}
