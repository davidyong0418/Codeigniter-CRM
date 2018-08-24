<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Postmaster extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('tickets_model');
        $this->load->helper('string');
    }

    function index()
    {
        if (config_item('for_tickets') == "on" || config_item('for_leads') == "on") {
            $input_data['last_postmaster_run'] = time();
            foreach ($input_data as $key => $value) {
                $data = array('value' => $value);
                $this->db->where('config_key', $key)->update('tbl_config', $data);
                $exists = $this->db->where('config_key', $key)->get('tbl_config');
                if ($exists->num_rows() == 0) {
                    $this->db->insert('tbl_config', array("config_key" => $key, "value" => $value));
                }
            }

            // this shows basic IMAP, no TLS required
            $config['login'] = config_item('config_username');
            $config['pass'] = config_item('config_password');
            $config['host'] = config_item('config_host');
            $config['mailbox'] = config_item('config_mailbox');
            $config['encryption'] = config_item('encryption');

            $this->load->library('peeker', $config);
            //attachment folder
            $bool = $this->peeker->set_attachment_dir('uploads/');

            // get notify user
            $notified_user = json_decode(config_item('notified_user'));
            $action = array('edit', 'delete', 'view');
            if (!empty($notified_user)) {
                foreach ($notified_user as $v_user) {
                    $permission[$v_user] = $action;
                }
            }


            // search filter for tickets
            if (config_item('for_tickets') == "on") {

                $ticket_keyword = config_item('tickets_keyword');
                $search_type = config_item('imap_search_for_tickets');
                $search = $search_type . '"' . $ticket_keyword . '"';
                $this->peeker->set_search($search);

                if ($this->peeker->search_and_count_messages() != "0") {

                    log_message('error', 'Postmaster fetched ' . $this->peeker->search_and_count_messages() . ' new email tickets.');

                    $id_array = $this->peeker->get_ids_from_search();

                    //walk trough emails
                    foreach ($id_array as $email_id) {

                        $email = $this->peeker->get_message($email_id);


                        $email->rewrite_html_transform_img_tags('uploads/');

                        $emailbody = nl2br($email->get_plain());

                        if ($emailbody == "") {
                            echo "";
                            $emailbody = $email->get_HTML();
                        }

                        iconv(mb_detect_encoding($emailbody, mb_detect_order(), true), "UTF-8", $emailbody);

                        $emailaddr = $email->get_from_array();

                        $emailaddr = $emailaddr[0]->mailbox . '@' . $emailaddr[0]->host;

                        $reporter = $this->db->where('email', $emailaddr)->get('tbl_users')->row();
                        if (!empty($reporter)) {
                            $user_id = $reporter->user_id;
                        } else {
                            $user_id = $this->session->userdata('user_id');
                        }

                        //Ticket Data
                        $ticket_data = array(
                            'ticket_code' => strtoupper(random_string('alnum', 7)),
                            'subject' => $email->get_subject(),
                            'body' => $emailbody,
                            'status' => config_item('default_status'),
                            'reporter' => $user_id,
                            'priority' => config_item('default_priority'),
                            'permission' => json_encode($permission),

                        );

                        //Attachments
                        $parts = $email->get_parts_array();

                        if ($email->has_attachment()) {
                            foreach ($parts as $part) {

                                $size = $part->get_bytes();
                                $attchmnt['fileName'] = $part->get_filename();
                                $attchmnt['path'] = '/uploads/' . $part->get_filename();;
                                $attchmnt['fullPath'] = getcwd() . '/uploads/' . $part->get_filename();
                                $attchmnt['size'] = $size * 1024;
                                $result[] = $attchmnt;
                            }
                            $email->save_all_attachments('uploads/');

                            $ticket_data['upload_file'] = json_encode($result);

                        }
                        $this->tickets_model->_table_name = 'tbl_tickets';
                        $this->tickets_model->_primary_key = 'tickets_id';
                        $this->tickets_model->save($ticket_data);

                        // send email to reporter
                        $this->send_tickets_info_by_email($ticket_data);
                        // send email to client
                        $this->send_tickets_info_by_email($ticket_data, true);

                        log_message('error', 'New ticket created #' . $ticket_data['ticket_code']);

                        if (config_item('delete_mail_after_import') == "on") {
                            $email->set_delete();
                            $email->expunge();
                            $this->peeker->delete_and_expunge($email_id);
                        }
                    }
                }

                $this->peeker->close();
            }


            // search filter for leads
            if (config_item('for_leads') == "on") {

                $ticket_keyword = config_item('leads_keyword');
                $search_type = config_item('imap_search_for_leads');
                $search = $search_type . '"' . $ticket_keyword . '"';
                $this->peeker->set_search($search);

                if ($this->peeker->search_and_count_messages() != "0") {

                    log_message('error', 'Postmaster fetched ' . $this->peeker->search_and_count_messages() . ' new email tickets.');

                    $id_array = $this->peeker->get_ids_from_search();

                    //walk trough emails
                    foreach ($id_array as $email_id) {
                        $ticket = false;
                        $email = $this->peeker->get_message($email_id);

                        $email->rewrite_html_transform_img_tags('uploads/');

                        $emailbody = nl2br($email->get_plain());

                        if ($emailbody == "") {
                            echo "";
                            $emailbody = $email->get_HTML();
                        }

                        iconv(mb_detect_encoding($emailbody, mb_detect_order(), true), "UTF-8", $emailbody);

                        $emailaddr = $email->get_from_array();

                        $emailaddr = $emailaddr[0]->mailbox . '@' . $emailaddr[0]->host;

                        $reporter = $this->db->where('email', $emailaddr)->get('tbl_users')->row();
                        if (!empty($reporter)) {
                            $profile_info = $this->db->where('user_id', $reporter->user_id)->get('tbl_account_details')->row();
                        }
                        if (!empty($profile_info)) {
                            $client_id = $profile_info->company;
                        } else {
                            $client_id = '-';
                        }

                        //Ticket Data
                        $leads_data = array(
                            'client_id' => $client_id,
                            'lead_name' => $email->get_subject(),
                            'lead_status_id' => config_item('default_lead_status'),
                            'lead_source_id' => config_item('default_leads_source'),
                            'contact_name' => $email->get_to(),
                            'email' => $emailaddr,
                            'notes' => $emailbody,
                            'permission' => config_item('default_lead_permission'),

                        );

                        $this->tickets_model->_table_name = 'tbl_leads';
                        $this->tickets_model->_primary_key = 'leads_id';
                        $this->tickets_model->save($leads_data);

                        log_message('error', 'New Leads created #' . $leads_data['lead_name']);

                        if (config_item('delete_mail_after_import') == "on") {
                            $email->set_delete();
                            $email->expunge();
                            $this->peeker->delete_and_expunge($email_id);
                        }
                    }
                }

                $this->peeker->close();
            }

        }
        die();
    }

    function send_tickets_info_by_email($postdata, $client = NULL)
    {

        if (!empty($postdata['reporter'])) {
            $postdata['reporter'] = $postdata['reporter'];
        } else {
            $postdata['reporter'] = $this->session->userdata('user_id');
        }

        $user_login_info = $this->tickets_model->check_by(array('user_id' => $postdata['reporter']), 'tbl_users');
        $ticket_info = $this->tickets_model->check_by(array('ticket_code' => $postdata['ticket_code']), 'tbl_tickets');

        if (!empty($client)) {
            $email_template = $this->tickets_model->check_by(array('email_group' => 'ticket_client_email'), 'tbl_email_templates');
            $message = $email_template->template_body;
            $subject = $email_template->subject;

            $client_email = str_replace("{CLIENT_EMAIL}", $user_login_info->email, $message);
            $ticket_code = str_replace("{TICKET_CODE}", $postdata['ticket_code'], $client_email);
            $TicketLink = str_replace("{TICKET_LINK}", base_url() . 'client/tickets/index/tickets_details/' . $ticket_info->tickets_id, $ticket_code);
            $message = str_replace("{SITE_NAME}", config_item('company_name'), $TicketLink);
            $data['message'] = $message;

            $message = $this->load->view('email_template', $data, TRUE);

            $subject = str_replace("[TICKET_CODE]", '[' . $postdata['ticket_code'] . ']', $subject);

            $params['recipient'] = $user_login_info->email;
            $params['subject'] = $subject;
            $params['message'] = $message;
            $params['resourceed_file'] = '';
            $this->tickets_model->send_email($params);
        } else {
            $email_template = $this->tickets_model->check_by(array('email_group' => 'ticket_staff_email'), 'tbl_email_templates');
            $department = config_item('default_department');
            if (!empty($department) && $department != 0) {
                $departments_id = $department;
            } else {
                $department_info = $this->db->get('tbl_departments')->row();
                if (!empty($department_info)) {
                    $departments_id = $department_info;
                }
            }
            if (!empty($departments_id)) {
                $designation_info = $this->db->where('departments_id', $departments_id)->get('tbl_designations')->result();
                if (!empty($designation_info)) {
                    foreach ($designation_info as $v_designation) {
                        $user_info[] = $this->db->where('designations_id', $v_designation->designations_id)->get('tbl_account_details')->row();
                    }
                }
            }
            $message = $email_template->template_body;
            $subject = $email_template->subject;

            $TicketCode = str_replace("{TICKET_CODE}", $postdata['ticket_code'], $message);
            $ReporterEmail = str_replace("{REPORTER_EMAIL}", $user_login_info->email, $TicketCode);
            $TicketLink = str_replace("{TICKET_LINK}", base_url() . 'admin/tickets/index/tickets_details/' . $ticket_info->tickets_id, $ReporterEmail);
            $message = str_replace("{SITE_NAME}", config_item('company_name'), $TicketLink);
            $data['message'] = $message;
            $message = $this->load->view('email_template', $data, TRUE);

            $subject = str_replace("[TICKET_CODE]", '[' . $postdata['ticket_code'] . ']', $subject);

            $params['subject'] = $subject;
            $params['message'] = $message;
            $params['resourceed_file'] = '';
            if (!empty($user_info)) {
                foreach ($user_info as $v_user) {
                    if (!empty($v_user)) {
                        $login_info = $this->tickets_model->check_by(array('user_id' => $v_user->user_id), 'tbl_users');
                        $params['recipient'] = $login_info->email;
                        $this->tickets_model->send_email($params);
                    }
                }
            }
        }
    }

}
