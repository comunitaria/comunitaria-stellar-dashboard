<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Agrega `tokenConsulta` a comercios: token aleatorio que da acceso a una
 * página pública de SOLO LECTURA del saldo (/saldo/<token>), para que los
 * dependientes/empleados consulten el saldo sin tener la clave privada.
 * El dueño conserva la app con acceso completo; el token solo permite leer.
 */
class AddTokenConsultaToComercios extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('tokenConsulta', 'comercios')) {
            $this->forge->addColumn('comercios', [
                'tokenConsulta' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 32,
                    'null'       => true,
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('tokenConsulta', 'comercios')) {
            $this->forge->dropColumn('comercios', 'tokenConsulta');
        }
    }
}
