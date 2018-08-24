<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of client_model
 *
 * @author NaYeM
 */
class Industry_Model extends MY_Model
{
    public $_table_name;
    public $_order_by;
    public $_primary_key = "id";
    function __construct(){
        $this->load->dbforge();
    }
    public function get_comments()
    {
        $query = "SHOW FULL COLUMNS FROM `".$this->_table_name."`";
        $result = $this->db->query($query);
        return $result->result_array();
    }   
    public function get_tiers()
    {
        $this->db->select('id, industry');
        $query = $this->db->get($this->_table_name);
        return $query->result_array();
    }
    public function add_new_industry($comment,$data,$field)
    {
        
        $query = "ALTER TABLE ".$this->_table_name."  ADD " 
            .$field. " VARCHAR(100) 
            COMMENT '".$comment."'";
        $result = $this->db->query($query);
       foreach ($data as $key => $price)
       {
            $this->db->set($field, $price);
            $this->db->where('id', $key);
            $this->db->update($this->_table_name);
       }
    }
    public function add_new_keyword($new_keyword, $new_keyword_field)
    {
        $this->db->insert($this->_table_name, $new_keyword_field);
    }
    public function save_edit_industry($id,$data)
    {
        $query_data = array();
        foreach($data as $key=> $param){
            if ($key!='id')
            {
                $query_data[$key] = $param;
                $this->db->set($key, $param);
            }
        }
        $this->db->where('id', $id);
        $this->db->update($this->_table_name);
    }
    public function count()
    {
        $query = $this->db->get($this->_table_name);
        return $query->result_array();
    }


}
