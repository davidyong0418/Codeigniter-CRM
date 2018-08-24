<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Proposals extends Client_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('proposal_model');
        $this->load->library('gst');

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

    public function index($action = NULL, $id = NULL, $item_id = NULL)
    {

        $data['page'] = lang('sales');
        $data['breadcrumbs'] = lang('proposals');
        if (!empty($item_id)) {
            $can_edit = $this->proposal_model->can_action('tbl_proposals', 'edit', array('proposals_id' => $id));
            if (!empty($can_edit)) {
                $data['item_info'] = $this->proposal_model->check_by(array('proposals_items_id' => $item_id), 'tbl_proposals_items');
            }
        }
        if ($action == 'client' || $action == 'leads') {
            $data['module_id'] = $id;
            $data['module'] = $action;
            $data['active'] = 2;
        } else {
            $data['active'] = 1;
        }
        if (!empty($id)) {
            $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $id), 'tbl_proposals');
            if (empty($data['proposals_info'])) {
                redirect('client/proposals');
            }
            $client_id = client_id();
            if ($data['proposals_info']->module != 'client') {
                redirect('client/proposals');
            } else {
                if ($client_id != $data['proposals_info']->module_id) {
                    redirect('client/proposals');
                }
            }

        }


        // get all client
        $this->proposal_model->_table_name = 'tbl_client';
        $this->proposal_model->_order_by = 'client_id';
        $data['all_client'] = $this->proposal_model->get();
        // get permission user
        $data['permission_user'] = $this->proposal_model->all_permission_user('14');

        // get all proposals
        $data['all_proposals_info'] = $this->db->where(array('status !=' => 'draft', 'module' => 'client', 'module_id' => $this->session->userdata('client_id')))->get('tbl_proposals')->result();

        if ($action == 'proposals_details') {
            $data['title'] = lang('proposals') . ' ' . lang('details'); //Page title
            $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $id), 'tbl_proposals');
            if (empty($data['proposals_info'])) {
                set_message('error', 'No data Found');
                redirect('client/proposals');
            }
            $subview = 'proposals_details';
        } elseif ($action == 'proposals_history') {
            $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $id), 'tbl_proposals');
            if (empty($data['proposals_info'])) {
                redirect('client/proposals');
            }
            $data['title'] = "proposals History"; //Page title
            $subview = 'proposals_history';
        } elseif ($action == 'email_proposals') {
            $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $id), 'tbl_proposals');
            if (empty($data['proposals_info'])) {
                redirect('client/proposals');
            }
            $data['title'] = "Email proposals"; //Page title
            $subview = 'email_proposals';
            $data['editor'] = $this->data;
        } elseif ($action == 'pdf_proposals') {
            $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $id), 'tbl_proposals');
            if (empty($data['proposals_info'])) {
                redirect('client/proposals');
            }
            $data['title'] = "proposals PDF"; //Page title
            $this->load->helper('dompdf');
            $viewfile = $this->load->view('client/proposals/proposals_pdf', $data, TRUE);
            pdf_create($viewfile, 'proposals  # ' . $data['proposals_info']->reference_no);
        } else {
            $data['title'] = lang('proposals'); //Page title
            $subview = 'proposals';
        }
        $data['subview'] = $this->load->view('client/proposals/' . $subview, $data, TRUE);
        $this->load->view('client/_layout_main', $data); //page load
    }

    public function pdf_proposals($id)
    {
        $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $id), 'tbl_proposals');
        if (empty($data['proposals_info'])) {
            redirect('client/proposals');
        }
        $client_id = client_id();
        if ($data['proposals_info']->module != 'client') {
            redirect('client/proposals');
        } else {
            if ($client_id != $data['proposals_info']->module_id) {
                redirect('client/proposals');
            }
        }
        $data['title'] = "proposals PDF"; //Page title
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('client/proposals/proposals_pdf', $data, TRUE);
        pdf_create($viewfile, 'proposals  # ' . $data['proposals_info']->reference_no);
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
            redirect('client/proposals/index/proposals_details/' . $id);
        } else {
            set_message('error', lang('there_in_no_value'));
            redirect($_SERVER['HTTP_REFERER']);
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
            'icon' => 'fa-envelope',
            'value1' => $ref
        );
        $this->proposal_model->_table_name = 'tbl_activities';
        $this->proposal_model->_primary_key = 'activities_id';
        $this->proposal_model->save($activity);

        $type = 'success';
        $text = lang('proposals_email_sent');
        set_message($type, $text);
        redirect('client/proposals/index/proposals_details/' . $proposals_id);
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
    }

    public function attach_pdf($id)
    {
        $data['page'] = lang('proposals');
        $data['sortable'] = true;
        $data['typeahead'] = true;
        $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $id), 'tbl_proposals');
        if (empty($data['proposals_info'])) {
            redirect('client/proposals');
        }
        $data['title'] = lang('proposals'); //Page title
        $this->load->helper('dompdf');
        $html = $this->load->view('client/proposals/proposals_pdf', $data, TRUE);
        $result = pdf_create($html, lang('proposal') . '_' . $data['proposals_info']->reference_no, 1, null, true);
        return $result;
    }

    function proposals_email($proposals_id)
    {
        $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $proposals_id), 'tbl_proposals');
        if (empty($data['proposals_info'])) {
            redirect('client/proposals');
        }
        $proposals_info = $data['proposals_info'];
        $client_info = $this->proposal_model->check_by(array('client_id' => $data['proposals_info']->client_id), 'tbl_client');

        $recipient = $client_info->email;

        $message = $this->load->view('client/proposals/proposals_pdf', $data, TRUE);

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
            'icon' => 'fa-envelope',
            'value1' => $proposals_info->reference_no
        );
        $this->proposal_model->_table_name = 'tbl_activities';
        $this->proposal_model->_primary_key = 'activities_id';
        $this->proposal_model->save($activity);

        $type = 'success';
        $text = lang('proposals_email_sent');
        set_message($type, $text);
        redirect('client/proposals/index/proposals_details/' . $proposals_id);
    }

}
