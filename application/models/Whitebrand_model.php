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
class Whitebrand_Model extends MY_Model
{
    // tbl_whitelabelkeyword
    // tbl_whitebrand
    public $_table_name = 'tbl_whitebrand';
    public $_order_by;
    public $_primary_key = "id";
    function __construct(){
        $this->load->dbforge();
    }
    public function get_industries(){
        $result = $this->db->get("tbl_whitebrand");
        return $result->result_array();
    }
    public function delete_industry($column){
        $this->db->where('id', $column);
        $this->db->delete('tbl_whitebrand');
        $query = "ALTER TABLE `tbl_whitelabelkeyword` DROP column `".$column."`";
        $result = $this->db->query($query);
        return $result;
    }
    public function get_cm_data($id=null){
        if($id != 'new')
        {
            $part_query = ",a.".$id;
        }
        else{
            $part_query='';
        }
        $query_header = "select a.id, a.keyword ".$part_query;
        $query_footer = " from `tbl_whitelabelkeyword` as a";
        $query = $query_header.$query_footer;
        $result = $this->db->query($query);
        return $result->result_array();
    }
    public function get_breadcrumb($id)
    {
        if($id == 'new')
        {
            return 'New';
        }
        else 
        {
            $data = array(
                'id' => $id
            );
            $this->db->where($data);
            $this->db->select('white_brand');
            $query = $this->db->get('tbl_whitebrand');
            $result = $query->result_array();
            return $result[0]['white_brand'];
        }
    }
    public function update_cm_data($id,$cm_request)
    {
        $new_industry_id = $id;
        foreach ($cm_request as $key => $item)
        {
            $query ="UPDATE `tbl_whitelabelkeyword` AS a SET a.`".$new_industry_id."`='".$item."' WHERE a.id=".$key;
            $this->db->query($query);
        }

        // industry roi
        return 'success';
    }
    public function add_new_industry_cm($new_industry, $cm_request)
    {
        $insert = array(
            'white_brand' => $new_industry
        );
        $this->db->insert('tbl_whitebrand', $insert);
        $new_industry_id = $this->db->insert_id();

        // industry cm
        $query = "ALTER TABLE `tbl_whitelabelkeyword` ADD `" 
        .$new_industry_id. "` VARCHAR(100)";
        $result = $this->db->query($query);

        foreach ($cm_request as $key => $item)
        {
            $query ="UPDATE `tbl_whitelabelkeyword` AS a SET a.`".$new_industry_id."`='".$item."' WHERE a.id=".$key;
            $this->db->query($query);
        }
        // industry roi
        return $new_industry_id;
       
    }
    public function add_new_cmkeyword($new_keyword, $new_keyword_value,$field)
    {
        foreach($new_keyword as $key => $new_item){
                $data = array(
                    "`".$field."`" => $new_keyword_value[$key],
                    'keyword' => $new_item,
                );
            $this->db->insert("tbl_whitelabelkeyword",$data);
        }
    }
    public function check_industry($industry)
    {
        $query = $this->db->get_where('tbl_whitebrand', array('white_brand' => $industry));
        if(empty($query->result_array()))
        {
            return 'no industry';
        }
        else{
            return 'duplicate';
        }
    }
}
