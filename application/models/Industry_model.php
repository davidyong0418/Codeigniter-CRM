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
            $this->db->select('industry');
            $query = $this->db->get('tbl_industry');
            $result = $query->result_array();
            return $result[0]['industry'];
        }
    }

    public function get_industries(){
        $result = $this->db->get("tbl_industry");
        return $result->result_array();
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
        $query_footer = " from ".$this->_table_name.' as a';
        $query = $query_header.$query_footer;
        $result = $this->db->query($query);
        return $result->result_array();
    }
    public function get_roi_data($id = null){
        if($id != 'new')
        {
            $part_query = ", a.".$id;
        }
        else{
            $part_query='';
        }
        $query_header = "select a.id, a.keyword ".$part_query;
        $query_footer = " from `tbl_industryroi` as a";
        $query = $query_header.$query_footer;
        $result = $this->db->query($query);
        return $result->result_array();
    }
    public function get_seo_data($id = null){
        if ($id != 'new')
        {
            $part_query = ", a.".$id."_keyword, a.".$id."_value, a.".$id."_competition";
        }
        else{
            $part_query = '';
        }
        $query_header = "select a.id, a.keyword ".$part_query;
        $query_footer = " from `tbl_industryseo` as a";
        $query = $query_header.$query_footer;
        $result = $this->db->query($query);
        return $result->result_array();
    }

    public function update_cm_data($id,$cm_request,$roi_request,$seo_keyword,$seo_value,$seo_competition)
    {
        $new_industry_id = $id;
        foreach ($cm_request as $key => $item)
        {
            $query ="UPDATE `tbl_industrycm` AS a SET a.`".$new_industry_id."`='".$item."' WHERE a.id=".$key;
            $this->db->query($query);
        }

        // industry roi
       
        foreach ($roi_request as $key => $item)
        {
            $query ="UPDATE `tbl_industryroi` AS a SET a.`".$new_industry_id."`='".$item."' WHERE a.id=".$key;
            $this->db->query($query);
        }


        // industry seo
       
        foreach ($seo_keyword as $key => $keyword)
        {
                $this->db->set($new_industry_id.'_keyword', $keyword);
                $this->db->set($new_industry_id.'_value', $seo_value[$key]);
                $this->db->set($new_industry_id.'_keyword', $seo_competition[$key]);
                $this->db->where('id', $key);
                $this->db->update("tbl_industryseo");
        }
        return 'success';
    }




    
    public function add_new_industry_cm($new_industry, $cm_request, $roi_request, $seo_keyword, $seo_value, $seo_competition)
    {
        $insert = array(
            'industry' => $new_industry
        );
        $this->db->insert('tbl_industry', $insert);
        $new_industry_id = $this->db->insert_id();

        // industry cm
        $query = "ALTER TABLE `tbl_industrycm` ADD `" 
        .$new_industry_id. "` VARCHAR(100)";
        $result = $this->db->query($query);


        foreach ($cm_request as $key => $item)
        {
            $query ="UPDATE `tbl_industrycm` AS a SET a.`".$new_industry_id."`='".$item."' WHERE a.id=".$key;
            $this->db->query($query);
        }

        // industry roi
        $query = "ALTER TABLE `tbl_industryroi` ADD `" 
        .$new_industry_id. "` VARCHAR(100)";
        $result = $this->db->query($query);


        foreach ($roi_request as $key => $item)
        {
            $query ="UPDATE `tbl_industryroi` AS a SET a.`".$new_industry_id."`='".$item."' WHERE a.id=".$key;
            $this->db->query($query);
        }


        // industry seo
        $query = "ALTER TABLE `tbl_industryseo`  ADD " 
            .$new_industry_id.'_keyword'. " VARCHAR(100)";
        $result = $this->db->query($query);

        $query = "ALTER TABLE `tbl_industryseo` ADD " 
            .$new_industry_id.'_value'. " VARCHAR(100)";
        $result = $this->db->query($query);

        $query = "ALTER TABLE `tbl_industryseo`  ADD " 
            .$new_industry_id.'_competition'. " VARCHAR(100)";
        $result = $this->db->query($query);
        foreach ($seo_keyword as $key => $keyword)
        {
                $this->db->set($new_industry_id.'_keyword', $keyword);
                $this->db->set($new_industry_id.'_value', $seo_value[$key]);
                $this->db->set($new_industry_id.'_competition', $seo_competition[$key]);
                $this->db->where('id', $key);
                $this->db->update("tbl_industryseo");
        }
        return $new_industry_id;
       
    }
    public function delete_industry($column){
        $this->db->where('id', $column);
        $this->db->delete('tbl_industry');
        $query = "ALTER TABLE `tbl_industrycm` DROP column `".$column."`";
        $result = $this->db->query($query);
        $query = "ALTER TABLE `tbl_industryroi` DROP column `".$column."`";
        $result = $this->db->query($query);
        $query = "ALTER TABLE `tbl_industryseo` DROP column `".$column."_keyword`";
        $result = $this->db->query($query);
        $query = "ALTER TABLE `tbl_industryseo` DROP column `".$column."_value`";
        $result = $this->db->query($query);
        $query = "ALTER TABLE `tbl_industryseo` DROP column `".$column."_competition`";
        $result = $this->db->query($query);


        return $result;
    }

    public function add_new_cmkeyword($new_keyword, $new_keyword_value,$field)
    {
        foreach($new_keyword as $key => $new_item){
                $data = array(
                    "`".$field."`" => $new_keyword_value[$key],
                    'keyword' => $new_item,
                );
            $this->db->insert("tbl_industrycm",$data);
        }
    }

    public function add_new_seokeyword($new_keywords,$new_keyword_items,$new_values,$new_competitions,$field)
    {
        foreach($new_keywords as $key => $new_item){
                $data = array(
                    'keyword' => $new_item,
                    $field.'_keyword' => $new_keyword_items[$key],
                    $field.'_value' => $new_values[$key],
                    $field.'_competition' => $new_competitions[$key]
                );
            $this->db->insert("tbl_industryseo",$data);
        }
    }

    public function check_industry($industry)
    {
        $query = $this->db->get_where('tbl_industry', array('industry' => $industry));
        if(empty($query->result_array()))
        {
            return 'no industry';
        }
        else{
            return 'duplicate';
        }
    }


    
}
