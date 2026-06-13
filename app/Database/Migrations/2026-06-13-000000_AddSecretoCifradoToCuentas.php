<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Modelo custodial: guarda el secreto Stellar de cada cuenta cifrado en reposo
 * (AES-256-GCM con la master key del sistema). El blob JSON {v,iv,ct,tag} va en
 * una sola columna. Ver ARQUITECTURA-CUSTODIA.md.
 */
class AddSecretoCifradoToCuentas extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('secretoCifrado', 'cuentas')) {
            $this->forge->addColumn('cuentas', [
                'secretoCifrado' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('secretoCifrado', 'cuentas')) {
            $this->forge->dropColumn('cuentas', 'secretoCifrado');
        }
    }
}
