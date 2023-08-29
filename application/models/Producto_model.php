<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Producto_model extends CI_Model {
    
    public function __construct(){
		parent::__construct();
		$this->load->database();
	}

    /** Implement function get_all for get all rows  our productos table */
    public function get_all()
    {
        //$this->db->limit($start, $page_size);
        $q = $this->db->get('productos');
        // var_dump($q);
        return $q;        
    }

    /** Implement function get_by_id for get row with id our productos table */
    public function get_by_id($id)
    {        
        $q = $this->db->get_where("productos", ['id_producto' => $id]);
        return $q;
    }

    public function save($data)
    {     
        return $this->db->insert('productos', $data);        
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