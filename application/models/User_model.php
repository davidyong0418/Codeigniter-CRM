<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * 	@author : themetic.net
 * 	date	: 21 April, 2015
 * 	Inventory & Invoice Management System
 * 	http://themetic.net
 *  version: 1.0
 */

class User_Model extends MY_Model {

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    public function select_user_roll_by_id($user_id) {
        $this->db->select('tbl_user_role.*', false);
        $this->db->select('tbl_menu.*', false);
        $this->db->from('tbl_user_role');
        $this->db->join('tbl_menu', 'tbl_user_role.menu_id = tbl_menu.menu_id', 'left');
        $this->db->where('tbl_user_role.user_id', $user_id);
        $query_result = $this->db->get();
        $result = $query_result->result();

        return $result;
    }

    public function get_new_user() {
        $post = new stdClass();
        $post->user_name = '';
        $post->name = '';
        $post->email = '';
        $post->flag = 3;
        $post->employee_login_id = '';

        return $post;
    }
    public function insert_additional_email($id, $addition_email, $addition_email_label,$action){
      
        if($action =='update')
        {
            $where = array(
                'user_id' => $id,
                'user_type' => 'staff'
            );
            $this->db->where($where);
            $this->db->delete('tbl_additional_email');
        }
        
            foreach ($addition_email as $key =>$item)
            {
                $data=array(
                    'user_id' => $id,
                    'type' => $addition_email_label[$key],
                    'additional_email' => $item,
                    'user_type' => 'staff'
                );
                $this->db->insert('tbl_additional_email',$data);
            }
           
        

    }
    public function insert_additional_phone($id, $additional_phone, $additional_phone_label,$action)
    {
        if($action == 'update')
        {
            $where = array(
                'user_id' => $id,
                'user_type' => 'staff'
            );
            $this->db->where($where);
            $this->db->delete('tbl_additional_phone');
        }
        foreach ($additional_phone as $key=>$item)
            {
                $data =array(
                    'user_id' => $id,
                    'type' => $additional_phone_label[$key],
                    'additional_phone' => $item,
                    'user_type' => 'staff'
                );
                $this->db->insert('tbl_additional_phone',$data);
            }
    }
    public function get_additional_emails($id)
    {
        $where = array(
            'user_id' => $id,
            'user_type' => 'staff'
        );
        $this->db->where($where);
        $result = $this->db->get('tbl_additional_email');
        return $result->result_array();
    }
    public function get_additional_phones($id)
    {
        $where = array(
            'user_id' => $id,
            'user_type' => 'staff'
        );
        $this->db->where($where);
        $result = $this->db->get('tbl_additional_phone');
        return $result->result_array();
    }



    public function get_subcontructor_marketplace($id)
    {
        $this->db->where('user_id',$id);
        $result = $this->db->get('tbl_marketplace');
        return $result->result_array();
    }




    public function get_subcontructor_pay($id)
    {
        $this->db->where('user_id',$id);
        $result = $this->db->get('tbl_employee_subcontractor');
        return $result->result_array();
    }


    public function insert_subcontructor_marketplace($id, $items,$action)
    { 
        if($action == 'update')
        {
            $this->db->where('user_id',$id);
            $this->db->delete('tbl_marketplace');
        }
        foreach ($items as $item)
        {
            $data = array(
                'user_id' => $id,
                'marketplace_url' => $item
            );
            $this->db->insert('tbl_marketplace', $data);
        }
       
    }
    public function insert_subcontructor_pay($id, $items_description,$items, $action)
    {
        if($action == 'update')
        {
            $this->db->where('user_id',$id);
            $this->db->delete('tbl_employee_subcontractor');
        }
        foreach ($items as $key => $item)
        {
         
            $data = array(
                'user_id' => $id,
                'description' => $items_description[$key],
                'amount' => $item
            );
            $this->db->insert('tbl_employee_subcontractor', $data);
        }
    }
    public function get_breadcrumb($id)
    {
        $where = array(
            'user_id' => $id,
        );
        $result = $this->db->where($where)->get('tbl_users');
        return $result->result_array()[0]['username'];
    }
    public function add_role_staff($user_id, $roles, $action)
    {
        $push_role = array();
        foreach ($roles as $role)
        {
            switch ($role)
            {
                case 1:
                $push_role['super_admin'] = 1;
                break;
                case 2:
                $push_role['sales_rep'] = 1;
                break;
                case 3:
                $push_role['graphic_designer'] = 1;
                break;
                case 4:
                $push_role['web_developer'] = 1;
                break;
                case 5:
                $push_role['content_writer'] = 1;
                break;
                case 6:
                $push_role['content_admin'] = 1;
                break;
                case 7:
                $push_role['seo'] = 1;
                break;
                case 8:
                $push_role['seo_admin'] = 1;
                break;
                case 9:
                $push_role['sem'] =1;
                break;
                case 10:
                $push_role['sem_admin'] = 1;
                break;
                case 11:
                $push_role['social_media_manager'] = 1;
                break;
                case 12:
                $push_role['social_media_admin'] = 1;
                break;
                case 13:
                $push_role['accounting'] = 1;
                break;
                case 14:
                $push_role['proposal_writer'] = 1;
                break;
                case 15:
                $push_role['proposal_admin'] = 1;
                break;
                case 16:
                $push_role['human_resources'] = 1;
                break;
                case 17:
                $push_role['sales_admin'] = 1;
                break;
            }

        }
        $push_role['user_id'] = $user_id;
        if($action == 'update')
        {
            $this->db->where('user_id',$user_id);
            $this->db->update('tbl_staff_role', $push_role);
        }
        else{
            $this->db->insert('tbl_staff_role', $push_role);
        }
    }
}
