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
class Industryseo_Model extends MY_Model
{
    public $_table_name="tbl_industryseo_category";
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
    public function get_category(){
        $this->db->select('category');
        $result = $this->db->get('tbl_industryseo_category');
      
        return $result->result_array();
    }
    public function get_tiers()
    {
        $this->db->select('id, industry');
        $query = $this->db->get($this->_table_name);
        return $query->result_array();
    }
    public function add_new_industry($comment,$new_price,$keywords,$values,$competitions)
    {
        
        $query = "ALTER TABLE ".$this->_table_name."  ADD " 
            .$new_price.'_keyword'. " VARCHAR(100) 
            COMMENT '".$comment."'";
        $result = $this->db->query($query);

        $query = "ALTER TABLE ".$this->_table_name."  ADD " 
            .$new_price.'_value'. " INT
            COMMENT '".$comment."'";
        $result = $this->db->query($query);

        $query = "ALTER TABLE ".$this->_table_name."  ADD " 
            .$new_price.'_competition'. " VARCHAR(100) 
            COMMENT '".$comment."'";
        $result = $this->db->query($query);
        
       foreach ($keywords as $key => $keyword)
       {
            $this->db->set($new_price.'_keyword', $keyword);
            $this->db->set($new_price.'_value', $values[$key]);
            $this->db->set($new_price.'_keyword', $competitions[$key]);
            $this->db->where('id', $key);
            $this->db->update($this->_table_name);
       }
        $this->db->set('category', $new_price);
        $this->db->insert('tbl_industryseo_category');
       
    }
    // public function add_new_keyword($new_keyword, $new_keyword_field)
    // {
    //     $this->db->insert($this->_table_name, $new_keyword_field);
    // }
    public function add_new_keyword($new_keywords,$new_keyword_items,$new_values,$new_competitions,$field)
    {
        foreach($new_keywords as $key => $new_item){
            $data = array(
                'industry' => $new_item,
                $field.'_keyword' => $new_keyword_items[$key],
                $field.'_value' => $new_values[$key],
                $field.'_competition' => $new_competitions[$key]

            );
            $this->db->insert($this->_table_name,$data);
        }
    }
    public function save_edit_industry($column_suffix,$data)
    {
        $query_data = array();
        foreach($data['keyword'] as $key=> $param){
                $this->db->set($column_suffix.'_keyword', $param);
                $this->db->set($column_suffix.'_value', $data['value'][$key]);
                $this->db->set($column_suffix.'_competition', $data['competition'][$key]);
                $this->db->where('id', $key);
                $this->db->update($this->_table_name);
        }
       
    }
    public function count()
    {
        $query = $this->db->get($this->_table_name);
        return $query->result_array();
    }
    public function existed_field($new_field)
    { 
        $query = $this->db->get('tbl_industryseo_category');
        $result = $query->result_array();
        $flag = '';
        foreach ($result as $field)
        {
            if ($field['category'] == $new_field){
                $flag = 'existed';
            }
            
        }
        return $flag;
    
    }
    public function get_industry_values($field_suffix)
    {
        $select = $field_suffix.'_keyword,'.$field_suffix.'_value,'.$field_suffix.'_competition';
        $this->db->select($select);
        $query = $this->db->get($this->_table_name);
        return $query->result_array();
    }
    public function delete_industry($column){
        $query = "ALTER TABLE `".$this->_table_name."` DROP `".$column."_keyword`";
        $result = $this->db->query($query);
        $query = "ALTER TABLE `".$this->_table_name."` DROP `".$column."_value`";
        $result = $this->db->query($query);
        $query = "ALTER TABLE `".$this->_table_name."` DROP `".$column."_competition`";
        $result = $this->db->query($query);
        $query = "DELETE FROM `tbl_industryseo_category` WHERE category = '".$column."'";
        $result = $this->db->query($query);
        return $result;
    }


}
