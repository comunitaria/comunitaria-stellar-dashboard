<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Agrega la columna `keystore` (monedero cifrado del lado cliente) a
 * beneficiarios y comercios, para poder recuperar la wallet en cualquier
 * dispositivo a partir del usuario+contraseña sin regenerarla.
 *
 * El contenido es un blob JSON opaco (PBKDF2 + AES-GCM) cifrado en el cliente.
 */
class AddKeystoreToUsuarios extends Migration
{
    private array $tablas = ['beneficiarios', 'comercios'];

    public function up()
    {
        $campo = [
            'keystore' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ];
        foreach ($this->tablas as $tabla) {
            if (! $this->db->fieldExists('keystore', $tabla)) {
                $this->forge->addColumn($tabla, $campo);
            }
        }
    }

    public function down()
    {
        foreach ($this->tablas as $tabla) {
            if ($this->db->fieldExists('keystore', $tabla)) {
                $this->forge->dropColumn($tabla, 'keystore');
            }
        }
    }
}
