<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of employee
 *
 * @author Ashraf
 */
class Award extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('award_model');
    }

    public function index($id = NULL)
    {
        $data['title'] = lang('employee_award');
        // retrive all data from department table
        $data['all_employee'] = $this->award_model->get_all_employee();
        /// edit and update get employee award info
        if (!empty($id)) {
            $data['active'] = 2;
            $data['award_info'] = $this->award_model->get_employee_award_by_id($id);
        } else {
            $data['active'] = 2;
        }
        // get all_employee_award_info
        $data['all_employee_award_info'] = $this->award_model->get_employee_award_by_id();

        $data['subview'] = $this->load->view('admin/award/award_list', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function give_award($id = NULL)
    {
        $data['title'] = lang('give_award');
        // retrive all data from department table
        $data['all_employee'] = $this->award_model->get_all_employee();
        /// edit and update get employee award info
        if (!empty($id)) {
            $data['award_info'] = $this->award_model->get_employee_award_by_id($id);
        }
        // get all_employee_award_info
        $data['all_employee_award_info'] = $this->award_model->get_employee_award_by_id();

        $data['subview'] = $this->load->view('admin/award/give_award', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function save_employee_award($id = NULL)
    {
        $created = can_action('99', 'created');
        $edited = can_action('99', 'edited');
        if (!empty($created) || !empty($edited)) {
            $data = $this->award_model->array_from_post(array('award_name', 'user_id', 'gift_item', 'award_amount', 'award_date', 'given_date'));

            $this->award_model->_table_name = "tbl_employee_award"; // table name
            $this->award_model->_primary_key = "employee_award_id"; // $id
            $this->award_model->save($data, $id);

            if (!empty($id)) {
                $activity = 'activity_update_a_award';
                $msg = lang('award_information_saved');
                $description = 'not_award_received';
            } else {
                $activity = 'activity_added_a_award';
                $msg = lang('award_information_update');
                $description = 'not_award_update';
            }
            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'award',
                'module_field_id' => $id,
                'activity' => $activity,
                'icon' => 'fa-trophy',
                'value1' => $data['award_name'],
                'value2' => date('Y M', strtotime($data['award_date'])),
            );

            // Update into tbl_project
            $this->award_model->_table_name = "tbl_activities"; //table name
            $this->award_model->_primary_key = "activities_id";
            $this->award_model->save($activities);
            $profile_info = $this->award_model->check_by(array('user_id' => $data['user_id']), 'tbl_account_details');
            $user_info = $this->award_model->check_by(array('user_id' => $data['user_id']), 'tbl_users');
            if (empty($id)) {
                $award_email = config_item('award_email');
                if (!empty($award_email) && $award_email == 1) {
                    $email_template = $this->award_model->check_by(array('email_group' => 'award_email'), 'tbl_email_templates');
                    $message = $email_template->template_body;
                    $subject = $email_template->subject;
                    $username = str_replace("{NAME}", $profile_info->fullname, $message);
                    $award_name = str_replace("{AWARD_NAME}", $data['award_name'], $username);
                    $award_date = str_replace("{MONTH}", date('M Y', strtotime($data['award_date'])), $award_name);
                    $message = str_replace("{SITE_NAME}", config_item('company_name'), $award_date);
                    $data['message'] = $message;
                    $message = $this->load->view('email_template', $data, TRUE);

                    $params['subject'] = $subject;
                    $params['message'] = $message;
                    $params['resourceed_file'] = '';
                    $params['recipient'] = $user_info->email;
                    $this->award_model->send_email($params);

                }
                $notifyUser = array($user_info->user_id);
                if (!empty($notifyUser)) {
                    foreach ($notifyUser as $v_user) {
                        add_notification(array(
                            'to_user_id' => $v_user,
                            'description' => $description,
                            'icon' => 'trophy',
                            'link' => 'admin/award',
                            'value' => date('M Y', strtotime($data['award_date'])),
                        ));
                    }
                }
                if (!empty($notifyUser)) {
                    show_notification($notifyUser);
                }

            }
            // messages for user
            $type = "success";
            $message = $msg;
            set_message($type, $message);
        }
        redirect('admin/award'); //redirect page
    }

    public function delete_employee_award($id = NULL)
    {
        $deleted = can_action('99', 'deleted');
        if (!empty($deleted)) {
            $award_info = $this->db->where('employee_award_id', $id)->get('tbl_employee_award')->row();
            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'award',
                'module_field_id' => $id,
                'activity' => 'activity_delete_award',
                'icon' => 'fa-trophy',
                'value1' => $award_info->award_name,
                'value2' => date('Y M', strtotime($award_info->award_date)),
            );

            // Update into tbl_project
            $this->award_model->_table_name = "tbl_activities"; //table name
            $this->award_model->_primary_key = "activities_id";
            $this->award_model->save($activities);


            $this->award_model->_table_name = "tbl_employee_award"; // table name
            $this->award_model->_primary_key = "employee_award_id"; // $id
            $this->award_model->delete($id); // delete

            // messages for user
            $type = "success";
            $message = lang('award_information_delete');
            set_message($type, $message);
        }
        redirect('admin/award'); //redirect page
    }

    public function employee_award_pdf()
    {
        $data['employee_award_info'] = $this->db->get('tbl_employee_award')->result();
        $this->load->helper('dompdf');
        $view_file = $this->load->view('admin/award/employee_award_pdf', $data, true);
        pdf_create($view_file, lang('employee_award'));
    }

}
