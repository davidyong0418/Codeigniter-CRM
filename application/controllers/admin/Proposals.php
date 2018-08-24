<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Proposals extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('proposal_model');
        $this->load->library('gst');
        $this->load->library('email');
        $this->load->helper('ckeditor');
        $this->data['ckeditor'] = array(
            'id' => 'ck_editor',
            'path' => 'asset/js/ckeditor',
            'config' => array(
                'toolbar' => "Full",
                'width' => "99.8%",
                'height' => "400px"
            )
        );
    }
    public function get_mail_receivers()
    {
        $users = $this->db->get('tbl_account_details')->result();
        foreach ($users as $key => $user)
        {
        }
        
    }
    public function send_email($sales_rep)
    {
        $where = 'b.`user_id`='.$sales_rep;
        $query = 'SELECT a.`email` FROM `tbl_users` AS a LEFT JOIN `tbl_staff_role` AS b ON b.`super_admin` = 1 and b.`sales_admin` = 1 and '.$where.' WHERE a.user_id = b.user_id';
        $emails = $this->db->query($query)->result_array();
        foreach ($eamils as $item)
        {

        }
        $this->db->where('user_id',$sales_rep);
        $this->db->or_where('super_admin', 1);
        $this->db->or_where('sales_admin', 1);
        $this->db->get('tbl_staff_role');
        $result = $this->db->result();
        foreach ($result as $item)
        {
            // $this->email->from('MERCHANTSIDE MARKETING GROUP')->to($item->email)->subject('test')->message('test')->send();
        }
    }
    public function manage_proposal($id = NULL)
    {
       
        $generate_link = $this->uri->segment(4);
        $data['page'] = lang('sales');
        $data['sub_active'] = lang('proposals');
        $data['active'] = 1;
        $data['action'] = '';
        if(!empty($generate_link)){
            if($generate_link == 'new')
            {
                $data['breadcrumb_f'] = 'New';
                $data['action']= 'new';
            }
            else{
                $data = $this->edit_contact_info($generate_link);
                $data['breadcrumb_f'] = 'Edit';
            }
            $data['active'] = 2;
        }
        // get all client
        $this->proposal_model->_table_name = 'tbl_client';
        $this->proposal_model->_order_by = 'client_id';
        $data['all_client'] = $this->proposal_model->get();
        // get permission user
        $data['permission_user'] = $this->proposal_model->all_permission_user('140');
        $type = $this->uri->segment(5);
        if (empty($type)) {
            $type = '_' . date('Y');
        }

        if (!empty($type) && !is_numeric($type)) {
            $filterBy = $type;
        } else {
            $filterBy = null;
        }
        $data['user_type'] = $this->session->userdata('user_type');
        if($data['user_type'] ==2 || $data['user_type'] == 4)
        {
            $client_id = $this->session->userdata('user_id');
            $data['all_proposals'] = $this->db->where('contact_id',$client_id)->get('tbl_proposal')->result();
        }
        else
        {
            $data['all_proposals'] = $this->db->get('tbl_proposal')->result();
        }
        $data['all_contacts']=$this->db->get('tbl_contact')->result();
        // get all proposals
        // $data['all_proposals_info'] = $this->proposal_model->get_proposals($filterBy);
        //  get all staffes
        $data['all_staffes'] = $this->db->where('role_id',3)->get('tbl_users')->result();
        $data['whitebrand'] = $this->db->get('tbl_whitebrand')->result();
        $data['companies'] = $this->db->get('tbl_company')->result();
        $data['prices'] = $this->db->get('tbl_price')->result();
        $data['industries_info'] = $this->db->get('tbl_industry')->result();
        $data['page'] = 'show proposals';
        $subview = 'proposals';
        // $data['subview'] = $this->load->view('admin/proposals/' . $subview, $data, TRUE);
        $data['subview'] = $this->load->view('admin/proposals/' . $subview, $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }
    public function edit_contact_info($generate_link=NULL)
    {
        // $value = $this->input->post('select_contact');
        $proposal_info = $this->db->select('contact_id')->where('link',$generate_link)->get('tbl_proposal')->result();
        $contact_id = $proposal_info[0]->contact_id;
        $contact_info = $this->db->where('id',$contact_id)->get('tbl_contact')->result();
        // $contact_id = $contact_info[0]->id;
        $company_id = $contact_info[0]->company_id;
        $resquest_proposal_info = $this->db->where('contact_id',$contact_id)->get('tbl_contact_request_proposal')->result();
        $website_info = $this->db->where('contact_id',$contact_id)->get('tbl_website_url')->result();
        $data['active'] = 2;
        $data['contact_info'] = (array)$contact_info[0];
        $data['resquest_proposal_info'] = (array)$resquest_proposal_info[0];
        $data['website_info'] = (array)$website_info;
        $data['contact_id'] = $contact_id;
        $data['generate_link'] = $generate_link;
        return $data;
    }
    public function get_contact_info($data = NULL)
    {
        $value = $this->input->post('select_contact');
        $contact_info = $this->db->where('id',$value)->get('tbl_contact')->result();
        $contact_id = $contact_info[0]->id;
        $company_id = $contact_info[0]->company_id;
        $resquest_proposal_info = $this->db->where('contact_id',$contact_id)->get('tbl_contact_request_proposal')->result();
        
        $website_info = $this->db->where('contact_id',$contact_id)->get('tbl_website_url')->result();
    
        $result = array();
        array_push($result,(array)$contact_info[0], (array)$website_info, (array)$resquest_proposal_info[0]);
        print_r(json_encode($result));
        exit;
    }
    public function pdf_proposals($id)
    {
        $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $id), 'tbl_proposals');
        $data['title'] = "proposals PDF"; //Page title
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('admin/proposals/proposals_pdf', $data, TRUE);
        pdf_create($viewfile, 'proposals  # ' . $data['proposals_info']->reference_no);
    }

    public function save_proposals($action = NULL)
    {
        $created = can_action('140', 'created');
        $edited = can_action('140', 'edited');
        if (!empty($created) || !empty($edited)) {
           // messages for user
            $contact_info = $this->proposal_model->array_from_post(array('sales_rep','white_brand','company_id','industries','g_areas_served'));
            $contact_id = $this->input->post('contact_id');

            $this->proposal_model->_table_name = 'tbl_contact';
            $this->proposal_model->_primary_key = 'id';
            $this->proposal_model->save($contact_info, $contact_id);
            
            $website_url = $this->input->post('website_url');
            $website_url_label = $this->input->post('website_url_label');

            if(!empty($website_url)){
                $this->proposal_model->insert_website_url($contact_id,$website_url,$website_url_label);
            }

            $request_proposal_data = $this->proposal_model->array_from_post(array('cover_page','branding','website_analysis','website_proposal','seo','sea','smm','sma','content_marketing','marketing_analysis','recommendations','why_us_page','price_category'));
            $request_proposal_data['contact_id'] = $contact_id;
            $this->proposal_model->_table_name = 'tbl_contact_request_proposal';
            $this->proposal_model->_primary_key = "contact_id";
            $this->proposal_model->save($request_proposal_data, $contact_id);
            // $action = $this->input->post('action');
            $generate_link = $this->input->post('generate_link');
            if(empty($generate_link))
            {
               $generate_link = $this->generateRandomString();
            }
            $this->proposal_model->_table_name = 'tbl_proposal';
            $this->proposal_model->_primary_key = 'id';
            $this->proposal_model->save_proposal($contact_id,$generate_link,$action);
            $this->send_email($this->input->post('contact_id'));

            // $type = "success";
            // $message = 'sadfsfd';
            // set_message($type, $message);
        }
        // $this->show_proposals($contact_id);
        // $data['request_proposal_info'] = $this->db->where('contact_id',$contact_id)->get('tbl_contact_request_proposal')->result();
        // $data['page'] = 'view proposals';
        // $subview = 'view_proposals';
        // $data['subview'] = $this->load->view('admin/proposals/' . $subview, $data, TRUE);
        // $this->load->view('admin/_layout_main', $data);
        redirect('admin/proposals/view_proposals/'.$generate_link);

    }
    public function view_proposals($link)
    {
        $link = $this->uri->segment(4);
        $proposal_info = $this->db->where('link',$link)->get('tbl_proposal')->result();
        $contact_id = $proposal_info[0]->contact_id;
        $data['request_proposal_info'] = $this->db->where('contact_id',$contact_id)->get('tbl_contact_request_proposal')->result();
        $data['contact'] = $this->db->where('id',$contact_id)->get('tbl_contact')->result();
        $data['contact_id'] = $contact_id;
        $data['page'] = 'view proposals';
        $data['user_type'] = $this->session->userdata('user_type');
        $subview = 'view_proposals';
        if($data['request_proposal_info'][0]->website_analysis == 'on')
        {
            $data['response'] = json_decode($this->analysis($contact_id));
        }

        $brand = $data['contact'][0]->white_brand;
        if(!empty($brand))
        {
            $query = 'select a.'.$brand.' FROM `tbl_whitelabelkeyword` AS a WHERE a.`keyword`="Logo on Light"';
            $logo = $this->db->query($query)->result_array();
            $data['white_brand_logo'] = $logo[0]['3'];
        }
        $sales_rep = $data['contact'][0]->sales_rep;
        if(!empty($sales_rep))
        {
            $query = 'select a.'.$brand.' FROM `tbl_whitelabelkeyword` AS a WHERE a.`keyword`="Logo on Light"';
            $logo = $this->db->query($query)->result_array();
            $data['white_brand_logo'] = $logo[0]['3'];
        }


        $data['subview'] = $this->load->view('admin/proposals/' . $subview, $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }
    public function add_comment(){
        $comment = $this->input->post('proposal_comment');
        $user_id = $this->session->userdata('user_id');
        $this->proposal_model->add_comment_proposal($user_id,$comment);
        $user_info = $this->db->where('user_id',$user_id)->get('tbl_account_details')->result();
        $result = array(
            'status' => 'success',
            'message' => 'Successfully added',
            'data' =>array(
                'user_id' => $user_id,
                'comment' => $comment,
                'username' =>$user_info[0]->fullname,
                'img_url' => $user_info[0]->avatar
            ),
            'created_at' => date('Y-m-d')
        );
        echo json_encode($result);
        exit;
    }
    public function discuss_proposal()
    {
        $data['title'] = 'Discuss';
        $data['user_id'] = $this->session->userdata('user_id');
        $data['comment_data'] = $this->proposal_model->get_comments();
        // print_r($data['comment_data']);
        // $data['comment_data'] = $this->db->get('tbl_proposal_comment')->result();
        $data['subview'] = $this->load->view('admin/proposals/discuss_modal', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);

    }

    public function show_proposal($link=NULL)
    {
        $result = $this->db->select('contact_id')->where('link',$link)->get('tbl_proposal')->result();
        $contact_id = $result[0]->contact_id;


    }
    function generateRandomString($length = 18) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }






















    public function analysis($contact_id)
    {
        $websites = $this->db->where('contact_id',$contact_id)->get('tbl_website_url')->result_array();
        $website = $websites[0]['website_url'];
        $server_url = 'https://www.googleapis.com/pagespeedonline/v4/runPagespeed?key=AIzaSyDxdC-iyoa71BTZjIc0xB-5LRn4SIX7Hm0&url='.'http://mms.merchantside.com';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $server_url,
            CURLOPT_USERAGENT => 'Codular Sample cURL Request'
        ));
        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp;

    }
    public function settings($template=NULL)
    {
        $data['page'] = 'settings';
        $subview = 'settings';
        if($template != NULL)
        {
            $data['template'] = $template;
        }
        else{
            $data['template'] = 'website_analysis';
            $template = 'website_analysis';
        }
        $result = $this->db->where('template', $template)->get('tbl_proposal_template')->result();
        $data['content'] = $result[0]->content;
        // $data['breadcrumb_f'] = 'Setting';
        $data['subview'] = $this->load->view('admin/proposals/settings', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function save_content()
    {
        $content = $this->input->post('content');
        $template = $this->input->post('template');
        $this->proposal_model->_table_name = 'tbl_proposal_template';
        $this->proposal_model->_primary_key = 'id';
        $this->proposal_model->proposal_content_save($content, $template);
        $data = array(
            'success'=>'success',
            'result' =>'ok'
        );
        // print_r(json_encode($data));
        return 'success';
        exit;
    }

    public function delete_proposal($proposal_id=NULL)
    {
        $this->db->delete('tbl_proposal',array('id'=>$proposal_id));
        redirect('admin/proposals/manage_proposal');
    }



























    public function insert_items($proposals_id)
    {
        $edited = can_action('140', 'edited');
        $can_edit = $this->proposal_model->can_action('tbl_proposals', 'edit', array('proposals_id' => $proposals_id));
        if (!empty($can_edit) && !empty($edited)) {
            $data['proposals_id'] = $proposals_id;
            $data['modal_subview'] = $this->load->view('admin/proposals/_modal_insert_items', $data, FALSE);
            $this->load->view('admin/_layout_modal', $data);
        } else {
            set_message('error', lang('there_in_no_value'));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function add_insert_items($proposals_id)
    {
        $can_edit = $this->proposal_model->can_action('tbl_proposals', 'edit', array('proposals_id' => $proposals_id));
        $edited = can_action('140', 'edited');
        if (!empty($can_edit) && !empty($edited)) {
            $saved_items_id = $this->input->post('saved_items_id', TRUE);
            if (!empty($saved_items_id)) {
                foreach ($saved_items_id as $v_items_id) {
                    $items_info = $this->proposal_model->check_by(array('saved_items_id' => $v_items_id), 'tbl_saved_items');
                    $tax_info = json_decode($items_info->tax_rates_id);
                    $tax_name = array();
                    if (!empty($tax_info)) {
                        foreach ($tax_info as $v_tax) {
                            $all_tax = $this->db->where('tax_rates_id', $v_tax)->get('tbl_tax_rates')->row();
                            $tax_name[] = $all_tax->tax_rate_name . '|' . $all_tax->tax_rate_percent;

                        }
                    }
                    if (!empty($tax_name)) {
                        $tax_name = $tax_name;
                    } else {
                        $tax_name = array();
                    }

                    $data['quantity'] = 1;
                    $data['proposals_id'] = $proposals_id;
                    $data['item_name'] = $items_info->item_name;
                    $data['item_desc'] = $items_info->item_desc;
                    $data['hsn_code'] = $items_info->hsn_code;
                    $data['unit_cost'] = $items_info->unit_cost;
                    $data['item_tax_rate'] = '0.00';
                    $data['item_tax_name'] = json_encode($tax_name);
                    $data['item_tax_total'] = $items_info->item_tax_total;
                    $data['total_cost'] = $items_info->unit_cost;

                    $this->proposal_model->_table_name = 'tbl_proposals_items';
                    $this->proposal_model->_primary_key = 'proposals_items_id';
                    $items_id = $this->proposal_model->save($data);
                    $action = 'activity_proposal_items_added';
                    $msg = lang('proposals_item_save');
                    $activity = array(
                        'user' => $this->session->userdata('user_id'),
                        'module' => 'proposals',
                        'module_field_id' => $items_id,
                        'activity' => $action,
                        'icon' => 'fa-shopping-cart',
                        'link' => 'admin/proposals/index/proposals_details/' . $proposals_id,
                        'value1' => $items_info->item_name
                    );
                    $this->proposal_model->_table_name = 'tbl_activities';
                    $this->proposal_model->_primary_key = 'activities_id';
                    $this->proposal_model->save($activity);
                }
                $type = "success";
                $this->update_invoice_tax($saved_items_id, $proposals_id);
            } else {
                $type = "error";
                $msg = 'Please Select a items';
            }
            $message = $msg;
            set_message($type, $message);
            redirect('admin/proposals/index/proposals_details/' . $proposals_id);
        } else {
            set_message('error', lang('there_in_no_value'));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    function update_invoice_tax($saved_items_id, $proposals_id)
    {

        $invoice_info = $this->proposal_model->check_by(array('proposals_id' => $proposals_id), 'tbl_proposals');
        $tax_info = json_decode($invoice_info->total_tax);

        $tax_name = $tax_info->tax_name;
        $total_tax = $tax_info->total_tax;
        $invoice_tax = array();
        if (!empty($tax_name)) {
            foreach ($tax_name as $t_key => $v_tax_info) {
                array_push($invoice_tax, array('tax_name' => $v_tax_info, 'total_tax' => $total_tax[$t_key]));
            }
        }
        $all_tax_info = array();
        if (!empty($saved_items_id)) {
            foreach ($saved_items_id as $v_items_id) {
                $items_info = $this->proposal_model->check_by(array('saved_items_id' => $v_items_id), 'tbl_saved_items');

                $tax_info = json_decode($items_info->tax_rates_id);
                if (!empty($tax_info)) {
                    foreach ($tax_info as $v_tax) {
                        $all_tax = $this->db->where('tax_rates_id', $v_tax)->get('tbl_tax_rates')->row();
                        array_push($all_tax_info, array('tax_name' => $all_tax->tax_rate_name . '|' . $all_tax->tax_rate_percent, 'total_tax' => $items_info->unit_cost / 100 * $all_tax->tax_rate_percent));
                    }
                }
            }
        }
        if (!empty($invoice_tax) && is_array($invoice_tax) && !empty($all_tax_info)) {
            $all_tax_info = array_merge($all_tax_info, $invoice_tax);
        }

        $results = array();
        foreach ($all_tax_info as $value) {
            if (!isset($results[$value['tax_name']])) {
                $results[$value['tax_name']] = 0;
            }
            $results[$value['tax_name']] += $value['total_tax'];

        }
        if (!empty($results)) {
            foreach ($results as $key => $value) {
                $structured_results['tax_name'][] = $key;
                $structured_results['total_tax'][] = $value;
            }
            $invoice_data['tax'] = array_sum($structured_results['total_tax']);
            $invoice_data['total_tax'] = json_encode($structured_results);

            $this->proposal_model->_table_name = 'tbl_proposals';
            $this->proposal_model->_primary_key = 'proposals_id';
            $this->proposal_model->save($invoice_data, $proposals_id);
        }
        return true;
    }

    public function add_item($id = NULL)
    {
        $data = $this->proposal_model->array_from_post(array('proposals_id', 'item_order'));
        $can_edit = $this->proposal_model->can_action('tbl_proposals', 'edit', array('proposals_id' => $data['proposals_id']));
        $edited = can_action('140', 'edited');
        if (!empty($can_edit) && !empty($edited)) {
            $quantity = $this->input->post('quantity', TRUE);
            $array_data = $this->proposal_model->array_from_post(array('item_name', 'item_desc', 'item_tax_rate', 'unit_cost'));
            if (!empty($quantity)) {
                foreach ($quantity as $key => $value) {
                    $data['quantity'] = $value;
                    $data['item_name'] = $array_data['item_name'][$key];
                    $data['item_desc'] = $array_data['item_desc'][$key];
                    $data['unit_cost'] = $array_data['unit_cost'][$key];
                    $data['item_tax_rate'] = $array_data['item_tax_rate'][$key];
                    $sub_total = $data['unit_cost'] * $data['quantity'];

                    $data['item_tax_total'] = ($data['item_tax_rate'] / 100) * $sub_total;
                    $data['total_cost'] = $sub_total + $data['item_tax_total'];

                    // get all client
                    $this->proposal_model->_table_name = 'tbl_proposals_items';
                    $this->proposal_model->_primary_key = 'proposals_items_id';
                    if (!empty($id)) {
                        $proposals_items_id = $id;
                        $this->proposal_model->save($data, $id);
                        $action = ('activity_proposals_items_updated');
                    } else {
                        $proposals_items_id = $this->proposal_model->save($data);
                        $action = 'activity_proposals_items_added';
                    }
                    $activity = array(
                        'user' => $this->session->userdata('user_id'),
                        'module' => 'proposals',
                        'module_field_id' => $proposals_items_id,
                        'activity' => $action,
                        'icon' => 'fa-shopping-cart',
                        'value1' => $data['item_name']
                    );
                    $this->proposal_model->_table_name = 'tbl_activities';
                    $this->proposal_model->_primary_key = 'activities_id';
                    $this->proposal_model->save($activity);
                }
            }
            // messages for user
            $type = "success";
            $message = lang('proposals_item_save');
            set_message($type, $message);
            redirect('admin/proposals/index/proposals_details/' . $data['proposals_id']);
        } else {
            set_message('error', lang('there_in_no_value'));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

   

    public function change_status($action, $id)
    {
        $can_edit = $this->proposal_model->can_action('tbl_proposals', 'edit', array('proposals_id' => $id));
        $edited = can_action('140', 'edited');
        if (!empty($can_edit) && !empty($edited)) {
            $where = array('proposals_id' => $id);
            if ($action == 'hide') {
                $data = array('show_client' => 'No');
            } elseif ($action == 'show') {
                $data = array('show_client' => 'Yes');
            } elseif ($action == 'sent') {
                $data = array('emailed' => 'Yes', 'date_sent' => date("Y-m-d H:i:s", time()), 'status' => 'sent');
            } elseif (!empty($action)) {
                $data = array('status' => $action);
            } else {
                $data = array('show_client' => 'Yes');
            }
            $this->proposal_model->set_action($where, $data, 'tbl_proposals');
            // messages for user
            $type = "success";
            $message = lang('proposals_status', $action);
            set_message($type, $message);
            redirect('admin/proposals/index/proposals_details/' . $id);
        } else {
            set_message('error', lang('there_in_no_value'));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public
    function delete($action, $proposals_id, $item_iditem_id = NULL)
    {
        $can_delete = $this->proposal_model->can_action('tbl_proposals', 'delete', array('proposals_id' => $proposals_id));
        $deleted = can_action('140', 'deleted');
        if (!empty($can_delete) && !empty($deleted)) {
            if ($action == 'delete_item') {
                $this->proposal_model->_table_name = 'tbl_proposals_items';
                $this->proposal_model->_primary_key = 'proposals_items_id';
                $this->proposal_model->delete($item_id);
            } elseif ($action == 'delete_proposals') {

                $this->proposal_model->_table_name = 'tbl_proposals_items';
                $this->proposal_model->delete_multiple(array('proposals_id' => $proposals_id));

                $this->proposal_model->_table_name = 'tbl_reminders';
                $this->proposal_model->delete_multiple(array('module' => 'proposal', 'module_id' => $proposals_id));

                $this->proposal_model->_table_name = 'tbl_proposals';
                $this->proposal_model->_primary_key = 'proposals_id';
                $this->proposal_model->delete($proposals_id);
            }
            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'proposals',
                'module_field_id' => $proposals_id,
                'activity' => ('activity_' . $action),
                'icon' => 'fa-shopping-cart',
                'link' => 'admin/proposals/index/proposals_details/' . $proposals_id,
                'value1' => $action
            );

            $this->proposal_model->_table_name = 'tbl_activities';
            $this->proposal_model->_primary_key = 'activities_id';
            $this->proposal_model->save($activity);
            $type = 'success';

            if ($action == 'delete_item') {
                $text = lang('proposals_item_deleted');
                echo json_encode(array("status" => $type, 'message' => $text));
                exit();
            } else {
                $text = lang('proposals_deleted');
                echo json_encode(array("status" => $type, 'message' => $text));
                exit();
            }
        } else {
            echo json_encode(array("status" => 'error', 'message' => lang('there_in_no_value')));
            exit();
        }
    }

    public function send_proposals_email($proposals_id, $row = null)
    {
        if (!empty($row)) {
            $proposals_info = $this->proposal_model->check_by(array('proposals_id' => $proposals_id), 'tbl_proposals');
            if ($proposals_info->module == 'client') {
                $client_info = $this->proposal_model->check_by(array('client_id' => $proposals_info->module_id), 'tbl_client');
                $client = $client_info->name;
                $currency = $this->proposal_model->client_currency_sambol($proposals_info->module_id);
            } else if ($proposals_info->module == 'leads') {
                $client_info = $this->proposal_model->check_by(array('leads_id' => $proposals_info->module_id), 'tbl_leads');
                $client = $client_info->lead_name;
                $currency = $this->proposal_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
            } else {
                $client = '-';
                $currency = $this->proposal_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
            }

            $amount = $this->proposal_model->proposal_calculation('total', $proposals_info->proposals_id);
            $currency = $currency->code;
            $email_template = $this->proposal_model->check_by(array('email_group' => 'proposal_email'), 'tbl_email_templates');
            $message = $email_template->template_body;
            $ref = $proposals_info->reference_no;
            $subject = $email_template->subject;
        } else {
            $message = $this->input->post('message', TRUE);
            $ref = $this->input->post('ref', TRUE);
            $subject = $this->input->post('subject', TRUE);
            $client = $this->input->post('client_name', TRUE);
            $amount = $this->input->post('amount', true);
            $currency = $this->input->post('currency', TRUE);
        }

        $client_name = str_replace("{CLIENT}", $client, $message);
        $Ref = str_replace("{PROPOSAL_REF}", $ref, $client_name);
        $Amount = str_replace("{AMOUNT}", $amount, $Ref);
        $Currency = str_replace("{CURRENCY}", $currency, $Amount);
        $link = str_replace("{PROPOSAL_LINK}", base_url() . 'client/proposals/index/proposals_details/' . $proposals_id, $Currency);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $link);

        $this->send_email_proposals($proposals_id, $message, $subject); // Email proposals

        $data = array('status' => 'sent', 'emailed' => 'Yes', 'date_sent' => date("Y-m-d H:i:s", time()));

        $this->proposal_model->_table_name = 'tbl_proposals';
        $this->proposal_model->_primary_key = 'proposals_id';
        $this->proposal_model->save($data, $proposals_id);

        // Log Activity
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'proposals',
            'module_field_id' => $proposals_id,
            'activity' => 'activity_proposals_sent',
            'icon' => 'fa-shopping-cart',
            'link' => 'admin/proposals/index/proposals_details/' . $proposals_id,
            'value1' => $ref
        );
        $this->proposal_model->_table_name = 'tbl_activities';
        $this->proposal_model->_primary_key = 'activities_id';
        $this->proposal_model->save($activity);

        $type = 'success';
        $text = lang('proposals_email_sent');
        set_message($type, $text);
        redirect('admin/proposals/index/proposals_details/' . $proposals_id);
    }

    function send_email_proposals($proposals_id, $message, $subject)
    {
        $proposals_info = $this->proposal_model->check_by(array('proposals_id' => $proposals_id), 'tbl_proposals');
        if ($proposals_info->module == 'client') {
            $client_info = $this->proposal_model->check_by(array('client_id' => $proposals_info->module_id), 'tbl_client');
            $email = $client_info->email;
        } else if ($proposals_info->module == 'leads') {
            $client_info = $this->proposal_model->check_by(array('leads_id' => $proposals_info->module_id), 'tbl_leads');
            $email = $client_info->email;
        } else {
            $email = '-';
        }
        $recipient = $email;

        $data['message'] = $message;

        $message = $this->load->view('email_template', $data, TRUE);
        $params = array(
            'recipient' => $recipient,
            'subject' => $subject,
            'message' => $message
        );
        $params['resourceed_file'] = 'uploads/' . lang('proposal') . '_' . $proposals_info->reference_no . '.pdf';
        $params['resourcement_url'] = base_url() . 'uploads/' . lang('proposal') . '_' . $proposals_info->reference_no . '.pdf';

        $this->attach_pdf($proposals_id);

        $this->proposal_model->send_email($params);
        //Delete estimate in tmp folder
        if (is_file('uploads/' . lang('proposal') . '_' . $proposals_info->reference_no . '.pdf')) {
            unlink('uploads/' . lang('proposal') . '_' . $proposals_info->reference_no . '.pdf');
        }
        // send notification to client
        if ($proposals_info->module == 'client') {
            if (!empty($client_info->primary_contact)) {
                $notifyUser = array($client_info->primary_contact);
            } else {
                $user_info = $this->proposal_model->check_by(array('company' => $proposals_info->module_id), 'tbl_account_details');
                if (!empty($user_info)) {
                    $notifyUser = array($user_info->user_id);
                }
            }
            if (!empty($notifyUser)) {
                foreach ($notifyUser as $v_user) {
                    if ($v_user != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $v_user,
                            'icon' => 'shopping-cart',
                            'description' => 'not_email_send_alert',
                            'link' => 'client/proposals/index/proposals_details/' . $proposals_id,
                            'value' => lang('estimate') . ' ' . $proposals_info->reference_no,
                        ));
                    }
                }
                show_notification($notifyUser);
            }
        }
    }

    public function attach_pdf($id)
    {
        $data['page'] = lang('proposals');
        $data['sortable'] = true;
        $data['typeahead'] = true;
        $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $id), 'tbl_proposals');
        $data['title'] = lang('proposals'); //Page title
        $this->load->helper('dompdf');
        $html = $this->load->view('admin/proposals/proposals_pdf', $data, TRUE);
        $result = pdf_create($html, lang('proposal') . '_' . $data['proposals_info']->reference_no, 1, null, true);
        return $result;
    }

    function proposals_email($proposals_id)
    {
        $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $proposals_id), 'tbl_proposals');
        $proposals_info = $data['proposals_info'];
        $client_info = $this->proposal_model->check_by(array('client_id' => $data['proposals_info']->client_id), 'tbl_client');

        $recipient = $client_info->email;

        $message = $this->load->view('admin/proposals/proposals_pdf', $data, TRUE);

        $data['message'] = $message;

        $message = $this->load->view('email_template', $data, TRUE);
        $params = array(
            'recipient' => $recipient,
            'subject' => '[ ' . config_item('company_name') . ' ]' . ' New proposals' . ' ' . $data['proposals_info']->reference_no,
            'message' => $message
        );
        $params['resourceed_file'] = '';

        $this->proposal_model->send_email($params);

        $data = array('status' => 'sent', 'emailed' => 'Yes', 'date_sent' => date("Y-m-d H:i:s", time()));

        $this->proposal_model->_table_name = 'tbl_proposals';
        $this->proposal_model->_primary_key = 'proposals_id';
        $this->proposal_model->save($data, $proposals_id);

        // Log Activity
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'proposals',
            'module_field_id' => $proposals_id,
            'activity' => 'activity_proposals_sent',
            'icon' => 'fa-shopping-cart',
            'link' => 'admin/proposals/index/proposals_details/' . $proposals_id,
            'value1' => $proposals_info->reference_no
        );
        $this->proposal_model->_table_name = 'tbl_activities';
        $this->proposal_model->_primary_key = 'activities_id';
        $this->proposal_model->save($activity);

        // send notification to client
        if (!empty($client_info->primary_contact)) {
            $notifyUser = array($client_info->primary_contact);
        } else {
            $user_info = $this->proposal_model->check_by(array('company' => $proposals_info->client_id), 'tbl_account_details');
            if (!empty($user_info)) {
                $notifyUser = array($user_info->user_id);
            }
        }
        if (!empty($notifyUser)) {
            foreach ($notifyUser as $v_user) {
                if ($v_user != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $v_user,
                        'icon' => 'shopping-cart',
                        'description' => 'not_email_send_alert',
                        'link' => 'client/proposals/index/proposals_details/' . $proposals_id,
                        'value' => lang('estimate') . ' ' . $proposals_info->reference_no,
                    ));
                }
            }
            show_notification($notifyUser);
        }

        $type = 'success';
        $text = lang('proposals_email_sent');
        set_message($type, $text);
        redirect('admin/proposals/index/proposals_details/' . $proposals_id);
    }

    public
    function convert_to($type, $id)
    {

        $data['title'] = lang('convert') . ' ' . lang($type);
        $edited = can_action('140', 'edited');
        $can_edit = $this->proposal_model->can_action('tbl_proposals', 'edit', array('proposals_id' => $id));
        if (!empty($can_edit) && !empty($edited)) {
            // get all client
            $this->proposal_model->_table_name = 'tbl_client';
            $this->proposal_model->_order_by = 'client_id';
            $data['all_client'] = $this->proposal_model->get();
            // get permission user
            $data['permission_user'] = $this->proposal_model->all_permission_user('140');

            $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $id), 'tbl_proposals');

            $data['modal_subview'] = $this->load->view('admin/proposals/convert_to_' . $type, $data, FALSE);
            $this->load->view('admin/_layout_modal_large', $data);
        } else {
            set_message('error', lang('there_in_no_value'));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function converted_to_invoice($proposal_id)
    {
        $data = $this->proposal_model->array_from_post(array('reference_no', 'client_id', 'project_id', 'discount_type', 'discount_percent', 'user_id', 'adjustment', 'discount_total', 'show_quantity_as'));

        $data['allow_paypal'] = ($this->input->post('allow_paypal') == 'Yes') ? 'Yes' : 'No';
        $data['allow_stripe'] = ($this->input->post('allow_stripe') == 'Yes') ? 'Yes' : 'No';
        $data['allow_2checkout'] = ($this->input->post('allow_2checkout') == 'Yes') ? 'Yes' : 'No';
        $data['allow_authorize'] = ($this->input->post('allow_authorize') == 'Yes') ? 'Yes' : 'No';
        $data['allow_ccavenue'] = ($this->input->post('allow_ccavenue') == 'Yes') ? 'Yes' : 'No';
        $data['allow_braintree'] = ($this->input->post('allow_braintree') == 'Yes') ? 'Yes' : 'No';
        $data['allow_mollie'] = ($this->input->post('allow_mollie') == 'Yes') ? 'Yes' : 'No';
        $data['allow_payumoney'] = ($this->input->post('allow_payumoney') == 'Yes') ? 'Yes' : 'No';
        $data['client_visible'] = ($this->input->post('client_visible') == 'Yes') ? 'Yes' : 'No';
        $data['invoice_date'] = date('Y-m-d', strtotime($this->input->post('invoice_date', TRUE)));
        if (empty($data['invoice_date'])) {
            $data['invoice_date'] = date('Y-m-d');
        }
        if (empty($data['discount_total'])) {
            $data['discount_total'] = 0;
        }
        $data['invoice_year'] = date('Y', strtotime($this->input->post('invoice_date', TRUE)));
        $data['invoice_month'] = date('Y-m', strtotime($this->input->post('invoice_date', TRUE)));
        $data['due_date'] = date('Y-m-d', strtotime($this->input->post('due_date', TRUE)));
        $data['notes'] = $this->input->post('notes', TRUE);
        $tax['tax_name'] = $this->input->post('total_tax_name', TRUE);
        $tax['total_tax'] = $this->input->post('total_tax', TRUE);
        $data['total_tax'] = json_encode($tax);
        $i_tax = 0;
        if (!empty($tax['total_tax'])) {
            foreach ($tax['total_tax'] as $v_tax) {
                $i_tax += $v_tax;
            }
        }
        $data['tax'] = $i_tax;
        $save_as_draft = $this->input->post('save_as_draft', TRUE);
        if (!empty($save_as_draft)) {
            $data['status'] = 'draft';
        }
        $currency = $this->proposal_model->client_currency_sambol($data['client_id']);
        if (!empty($currency->code)) {
            $curren = $currency->code;
        } else {
            $curren = config_item('default_currency');
        }
        $data['currency'] = $curren;

        $permission = $this->input->post('permission', true);
        if (!empty($permission)) {
            if ($permission == 'everyone') {
                $assigned = 'all';
            } else {
                $assigned_to = $this->proposal_model->array_from_post(array('assigned_to'));
                if (!empty($assigned_to['assigned_to'])) {
                    foreach ($assigned_to['assigned_to'] as $assign_user) {
                        $assigned[$assign_user] = $this->input->post('action_' . $assign_user, true);
                    }
                }
            }
            if (!empty($assigned)) {
                if ($assigned != 'all') {
                    $assigned = json_encode($assigned);
                }
            } else {
                $assigned = 'all';
            }
            $data['permission'] = $assigned;
        } else {
            set_message('error', lang('assigned_to') . ' Field is required');
            redirect($_SERVER['HTTP_REFERER']);
        }

        // get all client
        $this->proposal_model->_table_name = 'tbl_invoices';
        $this->proposal_model->_primary_key = 'invoices_id';

        $invoice_id = $this->proposal_model->save($data);
        $recuring_frequency = $this->input->post('recuring_frequency', TRUE);

        if (!empty($recuring_frequency) && $recuring_frequency != 'none') {
            $recur_data = $this->proposal_model->array_from_post(array('recur_start_date', 'recur_end_date'));
            $recur_data['recuring_frequency'] = $recuring_frequency;
            $this->get_recuring_frequency($invoice_id, $recur_data); // set recurring
        }
        // save items
        $qty_calculation = config_item('qty_calculation_from_items');
        // save items
        $invoices_to_merge = $this->input->post('invoices_to_merge', TRUE);
        $cancel_merged_invoices = $this->input->post('cancel_merged_invoices', TRUE);
        if (!empty($invoices_to_merge)) {
            foreach ($invoices_to_merge as $inv_id) {
                if (empty($cancel_merged_invoices)) {
                    if (!empty($qty_calculation) && $qty_calculation == 'Yes') {
                        $all_items_info = $this->db->where('invoices_id', $inv_id)->get('tbl_items')->result();
                        if (!empty($all_items_info)) {
                            foreach ($all_items_info as $v_items) {
                                $this->return_items($v_items->items_id);
                            }
                        }
                    }
                    $this->db->where('invoices_id', $inv_id);
                    $this->db->delete('tbl_invoices');

                    $this->db->where('invoices_id', $inv_id);
                    $this->db->delete('tbl_items');

                } else {
                    $mdata = array('status' => 'Cancelled');
                    $this->proposal_model->_table_name = 'tbl_invoices';
                    $this->proposal_model->_primary_key = 'invoices_id';
                    $this->proposal_model->save($mdata, $inv_id);
                }
            }
        }

        $removed_items = $this->input->post('removed_items', TRUE);
        if (!empty($removed_items)) {
            foreach ($removed_items as $r_id) {
                if ($r_id != 'undefined') {
                    if (!empty($qty_calculation) && $qty_calculation == 'Yes') {
                        $this->return_items($r_id);
                    }

                    $this->db->where('items_id', $r_id);
                    $this->db->delete('tbl_items');
                }
            }
        }

        $itemsid = $this->input->post('items_id', TRUE);
        $items_data = $this->input->post('items', true);

        if (!empty($items_data)) {
            $index = 0;
            foreach ($items_data as $items) {
                $items['invoices_id'] = $invoice_id;
                $tax = 0;
                if (!empty($items['taxname'])) {
                    foreach ($items['taxname'] as $tax_name) {
                        $tax_rate = explode("|", $tax_name);
                        $tax += $tax_rate[1];

                    }
                    $items['item_tax_name'] = $items['taxname'];
                    unset($items['taxname']);
                    $items['item_tax_name'] = json_encode($items['item_tax_name']);
                }
                if (empty($items['saved_items_id'])) {
                    $items['saved_items_id'] = 0;
                }
                if (!empty($qty_calculation) && $qty_calculation == 'Yes') {
                    if (!empty($items['saved_items_id']) && $items['saved_items_id'] != 'undefined') {
                        $this->proposal_model->reduce_items($items['saved_items_id'], $items['quantity']);
                    }
                }
                $price = $items['quantity'] * $items['unit_cost'];
                $items['item_tax_total'] = ($price / 100 * $tax);
                $items['total_cost'] = $price;
                // get all client
                $this->proposal_model->_table_name = 'tbl_items';
                $this->proposal_model->_primary_key = 'items_id';
                $this->proposal_model->save($items);
                if (!empty($items['items_id'])) {
                    $items_id = $items['items_id'];
                    if (!empty($qty_calculation) && $qty_calculation == 'Yes') {
                        $this->check_existing_qty($items_id, $items['quantity']);
                    }
                }
                $index++;
            }
        }

        $p_data = array('status' => 'accepted', 'convert' => 'Yes', 'convert_module' => 'invoice', 'convert_module_id' => $invoice_id);

        $this->proposal_model->_table_name = 'tbl_proposals';
        $this->proposal_model->_primary_key = 'proposals_id';
        $this->proposal_model->save($p_data, $proposal_id);

        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'proposals',
            'module_field_id' => $invoice_id,
            'activity' => 'convert_to_invoice_from_proposal',
            'icon' => 'fa-shopping-cart',
            'link' => 'admin/proposals/index/proposals_details/' . $proposal_id,
            'value1' => $data['reference_no']
        );
        $this->proposal_model->_table_name = 'tbl_activities';
        $this->proposal_model->_primary_key = 'activities_id';
        $this->proposal_model->save($activity);

        // send notification to client
        if (!empty($data['client_id'])) {
            $client_info = $this->proposal_model->check_by(array('client_id' => $data['client_id']), 'tbl_client');
            if (!empty($client_info->primary_contact)) {
                $notifyUser = array($client_info->primary_contact);
            } else {
                $user_info = $this->proposal_model->check_by(array('company' => $data['client_id']), 'tbl_account_details');
                if (!empty($user_info)) {
                    $notifyUser = array($user_info->user_id);
                }
            }
        }
        if (!empty($notifyUser)) {
            foreach ($notifyUser as $v_user) {
                if ($v_user != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $v_user,
                        'icon' => 'shopping-cart',
                        'description' => 'proposal_convert_to_invoice',
                        'link' => 'client/invoice/manage_invoice/invoice_details/' . $invoice_id,
                        'value' => $data['reference_no'],
                    ));
                }
            }
            show_notification($notifyUser);
        }
        // messages for user
        $type = "success";
        $message = lang('convert_to_invoice') . ' ' . lang('successfully');
        set_message($type, $message);
        redirect('admin/proposals/index/proposals_details/' . $proposal_id);
    }

    function return_items($items_id)
    {
        $items_info = $this->db->where('items_id', $items_id)->get('tbl_items')->row();
        if (!empty($items_info->saved_items_id)) {
            $this->proposal_model->return_items($items_info->saved_items_id, $items_info->quantity);
        }
        return true;

    }

    function check_existing_qty($items_id, $qty)
    {
        $items_info = $this->db->where('items_id', $items_id)->get('tbl_items')->row();
        if (!empty($items_info)) {
            if ($items_info->quantity != $qty) {
                if ($qty > $items_info->quantity) {
                    $reduce_qty = $qty - $items_info->quantity;
                    if (!empty($items_info->saved_items_id)) {
                        $this->proposal_model->reduce_items($items_info->saved_items_id, $reduce_qty);
                    }
                }
                if ($qty < $items_info->quantity) {
                    $return_qty = $items_info->quantity - $qty;
                    if (!empty($items_info->saved_items_id)) {
                        $this->proposal_model->return_items($items_info->saved_items_id, $return_qty);
                    }
                }
            }
        }
        return true;

    }

    function get_recuring_frequency($invoices_id, $recur_data)
    {
        $recur_days = $this->get_calculate_recurring_days($recur_data['recuring_frequency']);
        $due_date = $this->proposal_model->get_table_field('tbl_invoices', array('invoices_id' => $invoices_id), 'due_date');

        $next_date = date("Y-m-d", strtotime($due_date . "+ " . $recur_days . " days"));

        if ($recur_data['recur_end_date'] == '') {
            $recur_end_date = '0000-00-00';
        } else {
            $recur_end_date = date('Y-m-d', strtotime($recur_data['recur_end_date']));
        }
        $update_invoice = array(
            'recurring' => 'Yes',
            'recuring_frequency' => $recur_days,
            'recur_frequency' => $recur_data['recuring_frequency'],
            'recur_start_date' => date('Y-m-d', strtotime($recur_data['recur_start_date'])),
            'recur_end_date' => $recur_end_date,
            'recur_next_date' => $next_date
        );
        $this->proposal_model->_table_name = 'tbl_invoices';
        $this->proposal_model->_primary_key = 'invoices_id';
        $this->proposal_model->save($update_invoice, $invoices_id);
        return TRUE;
    }

    function get_calculate_recurring_days($recuring_frequency)
    {
        switch ($recuring_frequency) {
            case '7D':
                return 7;
                break;
            case '1M':
                return 31;
                break;
            case '3M':
                return 90;
                break;
            case '6M':
                return 182;
                break;
            case '1Y':
                return 365;
                break;
        }
    }

    public function converted_to_estimate($proposal_id)
    {
        $data = $this->proposal_model->array_from_post(array('reference_no', 'client_id', 'project_id', 'discount_type', 'discount_percent', 'user_id', 'adjustment', 'discount_total', 'show_quantity_as'));

        $data['client_visible'] = ($this->input->post('client_visible') == 'Yes') ? 'Yes' : 'No';
        $data['estimate_date'] = date('Y-m-d', strtotime($this->input->post('estimate_date', TRUE)));
        if (empty($data['estimate_date'])) {
            $data['estimate_date'] = date('Y-m-d');
        }
        if (empty($data['discount_total'])) {
            $data['discount_total'] = 0;
        }
        $data['estimate_year'] = date('Y', strtotime($this->input->post('estimate_date', TRUE)));
        $data['estimate_month'] = date('Y-m', strtotime($this->input->post('estimate_date', TRUE)));
        $data['due_date'] = date('Y-m-d', strtotime($this->input->post('due_date', TRUE)));
        $data['notes'] = $this->input->post('notes', TRUE);
        $tax['tax_name'] = $this->input->post('total_tax_name', TRUE);
        $tax['total_tax'] = $this->input->post('total_tax', TRUE);
        $data['total_tax'] = json_encode($tax);
        $i_tax = 0;
        if (!empty($tax['total_tax'])) {
            foreach ($tax['total_tax'] as $v_tax) {
                $i_tax += $v_tax;
            }
        }
        $data['tax'] = $i_tax;
        $save_as_draft = $this->input->post('status', TRUE);
        if (!empty($save_as_draft)) {
            $data['status'] = $save_as_draft;
        } else {
            $data['status'] = 'pending';
        }
        $currency = $this->proposal_model->client_currency_sambol($data['client_id']);
        if (!empty($currency->code)) {
            $curren = $currency->code;
        } else {
            $curren = config_item('default_currency');
        }
        $data['currency'] = $curren;

        $permission = $this->input->post('permission', true);
        if (!empty($permission)) {
            if ($permission == 'everyone') {
                $assigned = 'all';
            } else {
                $assigned_to = $this->proposal_model->array_from_post(array('assigned_to'));
                if (!empty($assigned_to['assigned_to'])) {
                    foreach ($assigned_to['assigned_to'] as $assign_user) {
                        $assigned[$assign_user] = $this->input->post('action_' . $assign_user, true);
                    }
                }
            }
            if (!empty($assigned)) {
                if ($assigned != 'all') {
                    $assigned = json_encode($assigned);
                }
            } else {
                $assigned = 'all';
            }
            $data['permission'] = $assigned;
        } else {
            set_message('error', lang('assigned_to') . ' Field is required');
            redirect($_SERVER['HTTP_REFERER']);
        }
        // get all client
        $this->proposal_model->_table_name = 'tbl_estimates';
        $this->proposal_model->_primary_key = 'estimates_id';
        if (!empty($id)) {
            $estimates_id = $id;
            $this->proposal_model->save($data, $id);
        } else {
            $estimates_id = $this->proposal_model->save($data);
        }
        // save items
        $invoices_to_merge = $this->input->post('invoices_to_merge', TRUE);
        $cancel_merged_invoices = $this->input->post('cancel_merged_estimate', TRUE);
        if (!empty($invoices_to_merge)) {
            foreach ($invoices_to_merge as $inv_id) {
                if (empty($cancel_merged_invoices)) {
                    $this->db->where('estimates_id', $inv_id);
                    $this->db->delete('tbl_estimates');

                    $this->db->where('estimate_items_id', $inv_id);
                    $this->db->delete('tbl_estimate_items');

                } else {
                    $mdata = array('status' => 'cancelled');
                    $this->proposal_model->_table_name = 'tbl_estimates';
                    $this->proposal_model->_primary_key = 'estimates_id';
                    $this->proposal_model->save($mdata, $inv_id);
                }
            }
        }

        $removed_items = $this->input->post('removed_items', TRUE);
        if (!empty($removed_items)) {
            foreach ($removed_items as $r_id) {
                if ($r_id != 'undefined') {
                    $this->db->where('estimate_items_id', $r_id);
                    $this->db->delete('tbl_estimate_items');
                }
            }
        }

        $itemsid = $this->input->post('estimate_items_id', TRUE);
        $items_data = $this->input->post('items', true);

        if (!empty($items_data)) {
            $index = 0;
            foreach ($items_data as $items) {
                $items['estimates_id'] = $estimates_id;
                if (!empty($items['taxname'])) {
                    $tax = 0;
                    foreach ($items['taxname'] as $tax_name) {
                        $tax_rate = explode("|", $tax_name);
                        $tax += $tax_rate[1];

                    }
                    $price = $items['quantity'] * $items['unit_cost'];
                    $items['item_tax_total'] = ($price / 100 * $tax);
                    $items['total_cost'] = $price;

                    $items['item_tax_name'] = $items['taxname'];
                    unset($items['taxname']);
                    $items['item_tax_name'] = json_encode($items['item_tax_name']);
                }
                // get all client
                $this->proposal_model->_table_name = 'tbl_estimate_items';
                $this->proposal_model->_primary_key = 'estimate_items_id';
                if (!empty($itemsid[$index])) {
                    $items_id = $itemsid[$index];
                    $this->proposal_model->save($items, $items_id);
                } else {
                    $items_id = $this->proposal_model->save($items);
                }
                $index++;
            }
        }
        $p_data = array('status' => 'accepted', 'convert' => 'Yes', 'convert_module' => 'estimate', 'convert_module_id' => $estimates_id);
        $this->proposal_model->_table_name = 'tbl_proposals';
        $this->proposal_model->_primary_key = 'proposals_id';
        $this->proposal_model->save($p_data, $proposal_id);

        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'proposals',
            'module_field_id' => $estimates_id,
            'activity' => 'convert_to_estimate_from_proposal',
            'icon' => 'fa-shopping-cart',
            'link' => 'admin/proposals/index/proposals_details/' . $proposal_id,
            'value1' => $data['reference_no']
        );
        $this->proposal_model->_table_name = 'tbl_activities';
        $this->proposal_model->_primary_key = 'activities_id';
        $this->proposal_model->save($activity);

        // send notification to client
        if (!empty($data['client_id'])) {
            $client_info = $this->proposal_model->check_by(array('client_id' => $data['client_id']), 'tbl_client');
            if (!empty($client_info->primary_contact)) {
                $notifyUser = array($client_info->primary_contact);
            } else {
                $user_info = $this->proposal_model->check_by(array('company' => $data['client_id']), 'tbl_account_details');
                if (!empty($user_info)) {
                    $notifyUser = array($user_info->user_id);
                }
            }
        }
        if (!empty($notifyUser)) {
            foreach ($notifyUser as $v_user) {
                if ($v_user != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $v_user,
                        'icon' => 'shopping-cart',
                        'description' => 'proposal_convert_to_estimate',
                        'link' => 'client/estimates/index/estimates_details/' . $estimates_id,
                        'value' => $data['reference_no'],
                    ));
                }
            }
            show_notification($notifyUser);
        }
        // messages for user
        $type = "success";
        $message = lang('convert_to_estimate') . ' ' . lang('successfully');
        set_message($type, $message);
        redirect('admin/proposals/index/proposals_details/' . $proposal_id);
    }

}
