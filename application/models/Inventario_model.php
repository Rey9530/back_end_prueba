<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventario_model extends CI_Model {
    
    public function __construct(){
		parent::__construct();
		$this->load->database();
	}
 
    /** Implement function get_by_id for get row with id our productos table */
    public function get_by_id($id)
    {        
        $this->db->select('*');    
        $this->db->from('productos');
        $this->db->join('movimiento', 'movimiento.id_producto = productos.id_producto'); 
        $this->db->where('productos.id_producto', $id);
        $query = $this->db->get();
        return $query; 
    }

    /**  */
    public function get_all()
    {
        $this->db->select('*');    
        $this->db->from('productos');
        $this->db->join('inventario', 'inventario.id_producto = productos.id_producto'); 
        $query = $this->db->get();
        return $query;
    }

    public function procesardata($data,$id_inventario)
    {     
        if($id_inventario>0){ 
            $this->db->where('id_inventario', $id_inventario);
            return $this->db->update('inventario', $data);       
        }else{
            return $this->db->insert('inventario', $data);  
        }       
    }

    public function guardar_movimiento($data )
    {
        return $this->db->insert('movimiento', $data);       
    }

    public function edit($id, $data)
    {        
        $this->db->where('id_producto', $id);
        return $this->db->update('productos', $data);        
    }

    public function delete($id)
    {        
        $this->db->where('id_producto', $id);        
       return $this->db->delete('productos');        
    }
}