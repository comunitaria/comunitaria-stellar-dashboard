<?php 

namespace App\Models;

use CodeIgniter\Model;

class Cls_usuarios extends Model
{
    protected $table      = 'usuarios';
    protected $primaryKey = 'id_usr';
    protected $useAutoIncrement = true;

    protected $returnType     = \App\Entities\Usuario::class; 
    protected $useSoftDeletes = false;

    protected $allowedFields = ['nombre_usr', 'login_usr', 'pwd_usr', 'token', 'caracteristicas','hora_token', 'por_defecto', 'email_usr'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    
    protected function initialize()
    {
      }    
    

    function CambiarClave($usuario, $clave) {
        $CI = &get_instance();
        $valorRetorno = 0; // Si devuelve 0 todo ha ido bien, <> 0, error.
        $cadenaSQL = "UPDATE usuarios SET pwd_usr = '" . $clave . "'";
        $cadenaSQL .= ' WHERE id_usr = ' . $usuario;

        $query = $CI->db->query($cadenaSQL);

        if (!$query) $valorRetorno = 1;

        return $valorRetorno;
        
        
    }    


    function Buscar($email='') {
        // Si le pasa el email por par�metro buscar por �l, si no por el id del usuario (propiedad)
        $CI = &get_instance();
        $cadenaSQL = "SELECT * FROM usuarios WHERE ";
        if ($email != '') {
            $cadenaSQL .= "email_usr = '" . $email . "'" ;
            
        } else {
            $cadenaSQL .= "id_usr = " . $this->id_usr . " " ;
            
        }
        $rstResult = $CI->db->query($cadenaSQL);
        $row = $rstResult->row();
        $this->valorret = -1;        
        if (isset($row)) {
            $this->nombre_usr = $row->nombre_usr ;
            $this->login_usr = $row->login_usr ;
            $this->email_usr = $row->email_usr ;
            $this->id_usr = $row->id_usr ;
            $this->pwd_usr = $row->pwd_usr ;
            $this->unidad = $row->unidad ;
            $this->valorret = 0;        
        }
        $rstResult->free_result();   

    }


     function Validar() {
        $CI = &get_instance();
        $cadenaSQL = "SELECT * FROM usuarios WHERE ";
        $cadenaSQL .= "login_usr = '" . $this->login_usr . "' " ;
        $cadenaSQL .= " AND pwd_usr = '" . md5($this->pwd_usr) . "' " ;
//echo $cadenaSQL;
        $rstResult = $CI->db->query($cadenaSQL);
        $row = $rstResult->row();
        $this->id_usr = -1; //Esto lo usaremos para ver si un usuario existe o no
        $this->valorret = -1;
        if (isset($row)) {
            
            $this->id_usr = $row->id_usr;
            $this->valorret = 0;
        }
        
        $rstResult->free_result();    

    }


    function Listar() {
        $CI = &get_instance();
        
        
        $cadenaSQL = "SELECT usuarios.*, IFNULL(unidades.nombre, '') AS nombre_unidad FROM usuarios
                      LEFT JOIN unidades on unidades.ID = usuarios.unidad ";        
        
        
        $query = $CI->db->query($cadenaSQL);
        $this->num_rows = $query->num_rows();
        
        
        return $query->result_array();       
        $query->free_result() ;
    }
    
}
?>