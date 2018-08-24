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
class Contact_Model extends MY_Model
{

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    function get_primary_contatc($user, $field)
    {

        $this->db->where('user_id', $user);
        $this->db->select($field);
        $query = $this->db->get('tbl_account_details');

        if ($query->num_rows() > 0) {
            $row = $query->row();

            return $row->$field;
        }
    }

    public function client_paid($client_id)
    {
        $query = $this->db->where('paid_by', $client_id)->select_sum('amount')->get('tbl_payments')->row();
        return $query->amount;
    }

    public function get_client_contacts($client_id)
    {
        $this->db->select('tbl_account_details.*', FALSE);
        $this->db->select('tbl_users.*', FALSE);
        $this->db->from('tbl_account_details');
        $this->db->join('tbl_users', 'tbl_users.user_id = tbl_account_details.user_id', 'left');
        $this->db->where('tbl_account_details.company', $client_id);
        $query_result = $this->db->get();

        $result = $query_result->result();
        return $result;
    }
    public function insert_additional_email($id, $addition_email, $addition_email_label,$action){
      
        if($action =='update')
        {
            $this->db->where('user_id', $id);
            $this->db->where('user_type', 'contact');
            $this->db->delete('tbl_additional_email');
        }
       
            foreach ($addition_email as $key =>$item)
            {
                $data=array(
                    'user_id' => $id,
                    'type' => $addition_email_label[$key],
                    'additional_email' => $item,
                    'user_type' => 'contact'
                );
                $this->db->insert('tbl_additional_email',$data);
            }

    }
    public function insert_additional_phone($id, $additional_phone, $additional_phone_label,$action)
    {
        if($action == 'update')
        {
            $this->db->where('user_id', $id);
            $this->db->where('user_type', 'contact');
            $this->db->delete('tbl_additional_phone');
        }
        foreach ($additional_phone as $key=>$item)
            {
               
                    $data =array(
                        'user_id' => $id,
                        'type' => $additional_phone_label[$key],
                        'additional_phone' => $item,
                        'user_type' => 'contact'
                    );
                    $this->db->insert('tbl_additional_phone',$data);
            }
    }
    public function save($data, $id = NULL)
    {
        // Set timestamps
        if ($this->_timestamps == TRUE) {
            $now = date('Y-m-d H:i:s');
            $id || $data['created'] = $now;
            $data['modified'] = $now;
        }
        // Insert
        if ($id === NULL) {
            // !isset($data[$this->_primary_key]) || $data[$this->_primary_key] = NULL;
            $this->db->set($data);
            $this->db->insert($this->_table_name);
            $id = $this->db->insert_id();
        } // Update
        else {
            $filter = $this->_primary_filter;
            $id = $filter($id);
            $this->db->set($data);
            $this->db->where($this->_primary_key, $id);
            $this->db->update($this->_table_name);
        }
        return $id;
    }
    public function insert_family_child($id, $child, $child_label,$action){
      
        if($action =='update')
        {
            $this->db->where('contact_id', $id);
            $this->db->delete('tbl_family_child');
        }
            foreach ($child as $key =>$item)
            {
               
                $data=array(
                    'contact_id' => $id,
                    'child' => $item,
                    'type' => $child_label[$key]
                );
                $this->db->insert('tbl_family_child',$data);
            }

    }
    public function insert_family_pet($id, $pet, $pet_label,$action){
        if($action =='update')
        {
            $this->db->where('contact_id', $id);
            $this->db->delete('tbl_family_pet');
        }
       
            foreach ($pet as $key =>$item)
            {
                $data=array(
                    'contact_id' => $id,
                    'pet' => $item,
                    'type' => $pet_label[$key]
                );
                $this->db->insert('tbl_family_pet',$data);

            }
    }
    public function insert_relationships_develop($id, $relationship_name, $relationship_profile_link,$relationship_notes,$action){
        if($action =='update')
        {
            $this->db->where('contact_id', $id);
            $this->db->delete('tbl_relationship_develop');
        }
        foreach ($relationship_name as $key =>$item)
        {
            $data=array(
                'contact_id' => $id,
                'name' => $item,
                'profile_link' => $relationship_profile_link[$key],
                'notes' => $relationship_notes[$key]
            );
            $this->db->insert('tbl_relationship_develop',$data);
        }
    }


    public function insert_access_info($id,$access_username,$access_email_address,$access_password,$access_view,$action){
        if($action =='update')
        {
            $this->db->where('contact_id', $id);
            $this->db->delete('tbl_access_info');
        }
        foreach ($access_username as $key =>$item)
        {
            $data=array(
                'contact_id' => $id,
                'access_username' => $item,
                'access_email_address' => $access_email_address[$key],
                'access_password' => $access_password[$key],
                'access_view' => $access_view[$key]
            );
            $this->db->insert('tbl_access_info',$data);
        }
    }
    public function insert_website_url($id,$website_url,$website_url_label,$action){
        if($action =='update')
        {
            $this->db->where('contact_id', $id);
            $this->db->delete('tbl_website_url');
        }
        foreach ($website_url as $key =>$item)
        {
            $data=array(
                'contact_id' => $id,
                'website_url' => $item,
                'label' => $website_url_label[$key],
            );
            $this->db->insert('tbl_website_url',$data);
        }
    }

}
