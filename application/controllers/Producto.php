<?php
defined('BASEPATH') OR exit('No direct script access allowed'); 
require APPPATH . 'libraries/REST_Controller.php';

class Producto extends REST_Controller {
 
	public function __construct()
    {
        parent::__construct();
        $this->load->model('Producto_model', 'producto');
    }
	public function method_get()
    {
        $id = $this->input->get('id');
        $res = [];
        if(!empty($id)) { 
            $data = $this->producto->get_by_id($id);  
            $res['message'] = 'success get data by id'; 
        }else { 
            $data = $this->producto->get_all(); 
            $res['message'] = 'success get all data';  
        }
        if($data) {
            $res['error'] = false;
            $res['data'] = $data->result();    
            
        }else {
            $res['error'] = true;
            $res['message'] = 'failed get data';
        }
        $this->response($res, 200);        
    }

    public function method_post()
    {  
        $json = json_decode($this->security->xss_clean($this->input->raw_input_stream)); 
        $nombre = $json->nombre;
        $descripcion = $json->descripcion;
        $precio = $json->precio;

        $data = array( 
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'precio' => $precio 
        );

        $insert = $this->producto->save($data);

        if($insert) {
            $res['error'] = false;
            $res['message'] = 'insert success';
            $res['data'] = $data;
        } else {
            $res['error'] = true;
            $res['message'] = 'insert failed';
            $res['data'] = null;
        }

        $this->response($res, 200);        
    }

    public function method_put()
    {
        $id = $this->input->get('id');

        $json = json_decode($this->security->xss_clean($this->input->raw_input_stream)); 
        $nombre = $json->nombre;
        $descripcion = $json->descripcion;
        $precio = $json->precio;

        $data = array( 
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'precio' => $precio 
        );

        if(!empty($id)) {
           $update = $this->producto->edit($id, $data);

           if($update) {
                $res['error'] = false;
                $res['message'] = 'update success';
                $res['data'] = $data;
           } else {
                $res['error'] = true;
                $res['message'] = 'update failed';
                $res['data'] = null;
           }
        } else {
            $res['error'] = true;
            $res['message'] = 'update failed';
            $res['data'] = null;
        }

        $this->response($res, 200);        
    }

    public function method_delete()
    {
        $id = $this->input->get('id');

        if(!empty($id)) {

            $delete = $this->producto->delete($id);

            if($delete) {
                $res['error'] = false;
                $res['message'] = 'delete success';
                
           } else {
                $res['error'] = true;
                $res['message'] = 'delete failed';
                
           }
        } else {
            $res['error'] = true;
            $res['message'] = 'delete failed';
        }

        $this->response($res, 200);        
    }
}
