<?php

class Proposal_Model extends MY_Model
{

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    function proposal_content_save($content,$template)
    {
        $this->db->set('content',$content);
        $this->db->where('template', $template);
        $this->db->update($this->_table_name);
        return 'save';
    }
    public function insert_website_url($id,$website_url,$website_url_label,$action = 'update'){
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
    public function save_proposal($contact_id, $generate_link, $action=NULL)
    {
        // $check = $this->db->where('contact_id',$contact_id)->get('tbl_proposal')->result_array();
        if(empty($action))
        {
            $this->db->set('updated_at',date('Y-m-d'));
            $this->db->where('contact_id',$contact_id);
            $this->db->update('tbl_proposal');
        }
        else{
            $data = array(
                'created_at' => date('Y-m-d'),
                'contact_id' => $contact_id,
                'updated_at' => date('Y-m-d'),
                'link' => $generate_link
            );
            $this->db->insert('tbl_proposal',$data);
        }
    }
    public function add_comment_proposal($user_id,$comment)
    {
        $data = array(
            'created_at' => date('Y-m-d'),
            'user_id' => $user_id,
            'comment' => $comment
        );
        $this->db->insert('tbl_proposal_comment', $data);
    }
    public function get_comments()
    {
        $this->db->select('tbl_proposal_comment.*');
        $this->db->order_by('created_at', 'desc');
        $this->db->limit(8);
        $result = $this->db->get('tbl_proposal_comment')->result();
        return $result;
    }

}
