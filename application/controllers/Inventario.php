<?php
defined('BASEPATH') OR exit('No direct script access allowed'); 
require APPPATH . 'libraries/REST_Controller.php';

class Inventario extends REST_Controller {
 
	public function __construct()
    {
        parent::__construct();
        $this->load->model('Inventario_model', 'inventario');
        $this->load->model('Producto_model', 'producto');
    }
	public function method_get()
    {
        $id = $this->input->get('id');
        $res = []; 
        if(!empty($id)) { 

            $data = $this->inventario->get_by_id($id); 
            $res['message'] = 'success get all data';  
            if($data) {
                $res['error'] = false;
                $res['data'] = $data->result();    
                
            }else {
                $res['error'] = true;
                $res['message'] = 'failed get data';
            } 
        }else{

            $data = $this->inventario->get_all(); 
            $res['message'] = 'success get all data';  
            if($data) {
                $res['error'] = false;
                $res['data'] = $data->result();    
                
            }else {
                $res['error'] = true;
                $res['message'] = 'failed get data';
            } 
        }
        $this->response($res, 200);        
    }

    public function method_post()
    {  
        $json = json_decode($this->security->xss_clean($this->input->raw_input_stream)); 
        if(!isset($json->id_producto) || !isset($json->cantidad) || !isset($json->monto_unitario)){
            $res['error'] = true;
            $res['message'] = 'parameter error';
            $res['data'] = null;
            $this->response($res, 404);   
            return;
        }
        $id_producto = $json->id_producto;
        $cantidad = $json->cantidad;
        $descripcion = $json->descripcion;
        $monto_unitario = $json->monto_unitario;


        $producto = $this->producto->get_by_id($id_producto)->result(); 
        $prodInv = $this->inventario->get_by_id($id_producto)->result(); 
        $res["data"] = [];
        if( count($producto)>0   ){
            $id_inventario =0;
            if(count($prodInv)>0){
                $invActual = $prodInv[0];
                $id_inventario =$invActual->id_inventario;
                $nuevo_costo_total = intval($cantidad) * floatval($monto_unitario);
                $costo_total = $invActual->costo_total + $nuevo_costo_total;
                $costo_promedio = $costo_total/(intval($cantidad)+$invActual->cantidad_en_existencia);
                $cantidad_en_existencia = intval($cantidad) + intval($invActual->cantidad_en_existencia);
            }else{ 
                $costo_total = $monto_unitario + intval($cantidad);
                $costo_promedio = $monto_unitario; 
                $cantidad_en_existencia = intval($cantidad) ;
                $nuevo_costo_total = intval($cantidad) * floatval($monto_unitario);
            }
            $data = array( 
                'id_producto' => $id_producto,
                'cantidad_en_existencia' => $cantidad_en_existencia,
                'costo_promedio' => $costo_promedio ,
                'costo_total' => $costo_total ,
            );  
            $insert = $this->inventario->procesardata($data,$id_inventario); 
            if($insert){

                $data = array( 
                    'id_producto' => $id_producto,
                    'tipo_movimiento' => 1,
                    'cantidad' => $cantidad ,
                    'monto_unitario' => $monto_unitario ,
                    'monto_total' => $nuevo_costo_total , 
                    'descripcion' => $descripcion ,
                );  
                $insert = $this->inventario->guardar_movimiento($data );
            }

            if($insert) {
                $res['error'] = false;
                $res['message'] = 'insert success';
                $res['data'] = $data;
            } else {
                $res['error'] = true;
                $res['message'] = 'insert failed';
                $res['data'] = $data;
            }
            $this->response($res, 200);        
        }else{
            $res['error'] = true;
            $res['message'] = 'Product not found';
            $res['data'] = null;
            $this->response($res, 404);        
        }
    }

    public function method_put()
    {


        $json = json_decode($this->security->xss_clean($this->input->raw_input_stream)); 
        if(!isset($json->id_producto) || !isset($json->cantidad)){
            $res['error'] = true;
            $res['message'] = 'parameter error';
            $res['data'] = null;
            $this->response($res, 404);   
            return;
        }

        $id_producto = $json->id_producto;
        $cantidad = $json->cantidad;
        $descripcion = $json->descripcion; 
        
        $producto = $this->producto->get_by_id($id_producto)->result(); 
        $prodInv = $this->inventario->get_by_id($id_producto)->result(); 

        if( count($producto)>0  && count($prodInv)>0   ){ 
            $invActual = $prodInv[0];
            $nueva_existencia =$invActual->cantidad_en_existencia-intval($cantidad);
            $costo_total = floatval($invActual->costo_promedio) * $nueva_existencia ;
            $data = array(  
                'cantidad_en_existencia' =>$nueva_existencia ,
                'costo_promedio' => $invActual->costo_promedio  ,
                'costo_total' => $costo_total ,
            );  
            $id_inventario =$invActual->id_inventario;
            $insert = $this->inventario->procesardata($data,$id_inventario); 

            if($insert){ 
                $data = array( 
                    'id_producto' => $id_producto,
                    'tipo_movimiento' => 2,
                    'cantidad' => $cantidad ,
                    'monto_unitario' => $invActual->costo_promedio ,
                    'monto_total' => $cantidad * $invActual->costo_promedio, 
                    'descripcion' => $descripcion ,
                );  
                $insert = $this->inventario->guardar_movimiento($data );
            }
            if($insert) {
                $res['error'] = false;
                $res['message'] = 'data updated';
                $res['data'] = $data;
            } else {
                $res['error'] = true;
                $res['message'] = 'failed update failed';
                $res['data'] = $data;
            }
            $this->response($res, 200);    
        }else{
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

            $delete = $this->inventario->delete($id);

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
