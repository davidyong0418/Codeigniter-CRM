<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Leave_Management extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('application_model');

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

    public function index($action = null, $id = NULL)
    {
        $data['title'] = lang('leave_management');
        $data['active'] = 1;
        $data['leave_active'] = 1;
        if ($action == 'view_details') {
            $subview = 'leave_details';
            $data['active'] = 4;
        } else {
            $subview = 'leave_management';
        }
        if ($action == 'edit') {
            $data['leave_active'] = 2;
        }
        if ($id) {
            $data['application_info'] = $this->application_model->check_by(array('leave_application_id' => $id), 'tbl_leave_application');
        } else {
            $data['active'] = 1;
            $data['leave_active'] = 1;
        }

        $data['leave_report'] = leave_report();
        $data['my_leave_report'] = leave_report($this->session->userdata('user_id'));

        $data['subview'] = $this->load->view('admin/leave_management/' . $subview, $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function apply_leave()
    {
        $data['title'] = lang('apply') . ' ' . lang('leave');
        $data['modal_subview'] = $this->load->view('admin/leave_management/apply_leave', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function view_details($id)
    {
        $data['title'] = lang('leave') . ' ' . lang('details');
        $data['application_info'] = $this->application_model->check_by(array('leave_application_id' => $id), 'tbl_leave_application');
        $data['modal_subview'] = $this->load->view('admin/leave_management/leave_details', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);

    }

    public function save_leave_application()
    {

        $this->application_model->_table_name = "tbl_leave_application"; // table name
        $this->application_model->_primary_key = "leave_application_id"; // $id
        //receive form input by post
        $data['user_id'] = $this->input->post('user_id', true);

        if (empty($data['user_id'])) {
            $data['user_id'] = $this->session->userdata('user_id');
        }

        $data['leave_category_id'] = $this->input->post('leave_category_id', true);
        $data['leave_type'] = $this->input->post('leave_type', true);
        if (!empty($data['leave_type'])) {
            if ($data['leave_type'] == 'single_day') {
                $start_date = $this->input->post('single_day_start_date', true);
                $end_date = null;
                $hours = null;
            }
            if ($data['leave_type'] == 'multiple_days') {
                $start_date = $this->input->post('multiple_days_start_date', true);
                $end_date = $this->input->post('multiple_days_end_date', true);;
                $hours = null;
            }
            if ($data['leave_type'] == 'hours') {
                $start_date = $this->input->post('hours_start_date', true);
                $end_date = null;
                $hours = $this->input->post('hours', true);
            }
        }

        $data['leave_start_date'] = $start_date;
        $data['leave_end_date'] = $end_date;
        $data['hours'] = $hours;

        if (!empty($data['leave_end_date']) && strtotime($data['leave_start_date']) > strtotime($data['leave_end_date'])) {
            $type = "error";
            $message = lang('end_date_less_than_error');
        } else {
            $check_validation = $this->check_available_leave($data['user_id'], $data['leave_start_date'], $data['leave_end_date'], $data['leave_category_id']);

            if (!empty($check_validation)) {
                $type = "error";
                $message = $check_validation;
            } else {
                $data['reason'] = $this->input->post('reason');

                //  File upload
                $upload_file = array();
                $files = $this->input->post("files");
                $target_path = getcwd() . "/uploads/";
                //process the fiiles which has been uploaded by dropzone
                if (!empty($files) && is_array($files)) {
                    foreach ($files as $key => $file) {
                        if (!empty($file)) {
                            $file_name = $this->input->post('file_name_' . $file);
                            $new_file_name = move_temp_file($file_name, $target_path);
                            $file_ext = explode(".", $new_file_name);
                            $is_image = check_image_extension($new_file_name);
                            $size = $this->input->post('file_size_' . $file) / 1000;
                            if ($new_file_name) {
                                $up_data = array(
                                    "fileName" => $new_file_name,
                                    "path" => "uploads/" . $new_file_name,
                                    "fullPath" => getcwd() . "/uploads/" . $new_file_name,
                                    "ext" => '.' . end($file_ext),
                                    "size" => round($size, 2),
                                    "is_image" => $is_image,
                                );
                                array_push($upload_file, $up_data);
                            }
                        }
                    }
                }
                if (!empty($upload_file)) {
                    $data['attachment'] = json_encode($upload_file);
                } else {
                    $data['attachment'] = null;
                }
                //save data in database
                $id = $this->application_model->save($data);

                save_custom_field(17, $id);

                $appl_info = $this->application_model->check_by(array('leave_application_id' => $id), 'tbl_leave_application');
                $profile_info = $this->application_model->check_by(array('user_id' => $appl_info->user_id), 'tbl_account_details');
                $leave_category = $this->application_model->check_by(array('leave_category_id' => $appl_info->leave_category_id), '	tbl_leave_category');

                // save into activities
                if ($appl_info->leave_type == 'multiple_days') {
                    $value_2 = strftime(config_item('date_format'), strtotime($appl_info->leave_start_date)) . ' TO ' . strftime(config_item('date_format'), strtotime($appl_info->leave_end_date));
                } else {
                    $value_2 = strftime(config_item('date_format'), strtotime($appl_info->leave_start_date));
                }
                $activities = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'leave_management',
                    'module_field_id' => $id,
                    'activity' => 'activity_leave_save',
                    'icon' => 'fa-ticket',
                    'value1' => $profile_info->fullname . ' -> ' . $leave_category->leave_category,
                    'value2' => $value_2,
                );
                // Update into tbl_project
                $this->application_model->_table_name = "tbl_activities"; //table name
                $this->application_model->_primary_key = "activities_id";
                $this->application_model->save($activities);

                // send email to departments head
                if ($appl_info->application_status == 1) {
                    // get departments head user id
                    if (!empty($profile_info->designations_id)) {
                        $designation_info = $this->application_model->check_by(array('designations_id' => $profile_info->designations_id), 'tbl_designations');
                        if (!empty($designation_info)) {
                            // get departments head by departments id
                            $dept_head = $this->application_model->check_by(array('departments_id' => $designation_info->departments_id), 'tbl_departments');
                            if (!empty($dept_head->department_head_id)) {
                                $leave_email = config_item('leave_email');
                                if (!empty($leave_email) && $leave_email == 1) {
                                    $email_template = $this->application_model->check_by(array('email_group' => 'leave_request_email'), 'tbl_email_templates');
                                    $user_info = $this->application_model->check_by(array('user_id' => $dept_head->department_head_id), 'tbl_users');
                                    if (!empty($user_info)) {
                                        $message = $email_template->template_body;
                                        $subject = $email_template->subject;
                                        $username = str_replace("{NAME}", $profile_info->fullname, $message);
                                        $Link = str_replace("{APPLICATION_LINK}", base_url() . 'admin/leave_management/index/view_details/' . $id, $username);
                                        $message = str_replace("{SITE_NAME}", config_item('company_name'), $Link);
                                        $data['message'] = $message;
                                        $message = $this->load->view('email_template', $data, TRUE);

                                        $params['subject'] = $subject;
                                        $params['message'] = $message;
                                        $params['resourceed_file'] = '';
                                        $params['recipient'] = $user_info->email;
                                        $this->application_model->send_email($params);
                                    }
                                }
                                $notifyUser = array($dept_head->department_head_id);
                                if (!empty($notifyUser)) {
                                    foreach ($notifyUser as $v_user) {
                                        if (!empty($v_user)) {
                                            if ($v_user != $this->session->userdata('user_id')) {
                                                add_notification(array(
                                                    'to_user_id' => $v_user,
                                                    'description' => 'not_leave_request',
                                                    'icon' => 'clock-o',
                                                    'link' => 'admin/leave_management/index/view_details/' . $id,
                                                    'value' => lang('by') . ' ' . $profile_info->fullname,
                                                ));
                                            }
                                        }
                                    }
                                }
                                if (!empty($notifyUser)) {
                                    show_notification($notifyUser);
                                }
                            }
                        }
                    }

                }


                // messages for user
                $type = "success";
                $message = lang('leave_successfully_save');
            }
        }
        set_message($type, $message);
        redirect('admin/leave_management');
    }

    function check_available_leave($user_id, $start_date = NULL, $end_date = NULL, $leave_category_id = NULL)
    {

        if (!empty($leave_category_id) && !empty($start_date)) {
            $total_leave = $this->application_model->check_by(array('leave_category_id' => $leave_category_id), 'tbl_leave_category');
            $leave_total = $total_leave->leave_quota;

            $all_leave = $this->db->where(array('user_id' => $user_id))->get('tbl_leave_application')->result();

            if (!empty($all_leave)) {
                foreach ($all_leave as $v_all_leave) {
                    $get_dates = $this->application_model->GetDays($v_all_leave->leave_start_date, $v_all_leave->leave_end_date);
                    $result_start = in_array($start_date, $get_dates);
                    $result_end = in_array($end_date, $get_dates);
                    if (!empty($result_start) || !empty($result_end)) {
                        return lang('leave_date_conflict');
                    }
                }


            }

            $token_leave = $this->db->where(array('user_id' => $user_id, 'leave_category_id' => $leave_category_id, 'application_status' => '2'))->get('tbl_leave_application')->result();

            $total_token = 0;
            if (!empty($token_leave)) {
                $ge_days = 0;
                $m_days = 0;
                foreach ($token_leave as $v_leave) {
                    $month = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($v_leave->leave_start_date)), date('Y', strtotime($v_leave->leave_start_date)));

                    $datetime1 = new DateTime($v_leave->leave_start_date);
                    if (empty($v_leave->leave_end_date)) {
                        $v_leave->leave_end_date = $v_leave->leave_start_date;
                    }
                    $datetime2 = new DateTime($v_leave->leave_end_date);
                    $difference = $datetime1->diff($datetime2);
                    if ($difference->m != 0) {
                        $m_days += $month;
                    } else {
                        $m_days = 0;
                    }
                    $ge_days += $difference->d + 1;
                    $total_token = $m_days + $ge_days;
                }
            }
            if (empty($total_token)) {
                $total_token = 0;
            }
            $input_ge_days = 0;
            $input_m_days = 0;
            if (!empty($end_date) && $end_date != 'null') {
                $input_month = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($start_date)), date('Y', strtotime($end_date)));

                $input_datetime1 = new DateTime($start_date);
                $input_datetime2 = new DateTime($end_date);
                $input_difference = $input_datetime1->diff($input_datetime2);
                if ($input_difference->m != 0) {
                    $input_m_days += $input_month;
                } else {
                    $input_m_days = 0;
                }
                $input_ge_days += $input_difference->d + 1;
                $input_total_token = $input_m_days + $input_ge_days;
            } else {
                $input_total_token = 1;
            }
            $taken_with_input = $total_token + $input_total_token;
            $left_leave = $leave_total - $total_token;
            if ($leave_total < $taken_with_input) {
                if ($user_id == $this->session->userdata('user_id')) {
                    $t = 'You ';
                } else {
                    $profile = $this->db->where('user_id', $user_id)->get('tbl_account_details')->row();
                    $t = $profile->fullname;
                }
                echo "$t already took  $total_token $total_leave->leave_category You can apply maximum for $left_leave more";
                exit();
            }
        } else {
            return lang('all_required_fill');
        }
    }

    public function change_status($status, $id)
    {
        $data['status'] = $status;
        $data['application_info'] = $this->application_model->check_by(array('leave_application_id' => $id), 'tbl_leave_application');

        $data['modal_subview'] = $this->load->view('admin/leave_management/_change_status', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);

    }

    public function set_action($id)
    {
        $data['application_status'] = $this->input->post('application_status', TRUE);
        if (!empty($data['application_status'])) {
            $cdata['application_status'] = $data['application_status'];
        }
        $cdata['comments'] = $this->input->post('comment', TRUE);
        $cdata['approve_by'] = $this->input->post('approve_by', TRUE);
        if ($data['application_status'] == 2) {
            $atdnc_data = $this->application_model->array_from_post(array('user_id'));
            $leave_start_date = $this->input->post('leave_start_date', TRUE);
            $leave_end_date = $this->input->post('leave_end_date', TRUE);
            if (empty($leave_end_date)) {
                $leave_end_date = $leave_start_date;
            }

            $get_dates = $this->application_model->GetDays($leave_start_date, $leave_end_date);

            $already_leave = array();
            foreach ($get_dates as $v_dates) {
                $this->application_model->_table_name = 'tbl_attendance';
                $this->application_model->_order_by = 'attendance_id';
                $check_leave_date = $this->application_model->check_by(array('user_id' => $atdnc_data['user_id'], 'date_in' => $v_dates), 'tbl_attendance');

                if (!empty($check_leave_date) && !empty($check_leave_date->leave_application_id)) {
                    array_push($already_leave, $v_dates);
                }

                $atdnc_data['date_in'] = $v_dates;
                $atdnc_data['date_out'] = $v_dates;
                $atdnc_data['attendance_status'] = '3';
                $atdnc_data['leave_application_id'] = $id;

                if (!empty($check_leave_date) && empty($check_leave_date->leave_application_id) && $check_leave_date->attendance_status == '0') {
                    $this->application_model->_table_name = 'tbl_attendance';
                    $this->application_model->_primary_key = "attendance_id";
                    $this->application_model->save($atdnc_data, $check_leave_date->attendance_id);
                } elseif (empty($check_leave_date)) {
                    $this->application_model->_table_name = 'tbl_attendance';
                    $this->application_model->_primary_key = "attendance_id";
                    $this->application_model->save($atdnc_data);
                }
            }
        }

        $where = array('leave_application_id' => $id);
        $this->application_model->set_action($where, $cdata, 'tbl_leave_application');

        $appl_info = $this->application_model->check_by(array('leave_application_id' => $id), 'tbl_leave_application');
        if ($appl_info->application_status == '1') {
            $status = lang('pending');
        } elseif ($appl_info->application_status == '2') {
            $status = lang('accepted');
            $this->send_application_status_by_email($appl_info, true);
        } else {
            $status = lang('rejected');
            $this->send_application_status_by_email($appl_info);
        }
        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'leave_management',
            'module_field_id' => $id,
            'activity' => 'activity_leave_change',
            'icon' => 'fa-ticket',
            'value1' => $status,
            'value2' => strftime(config_item('date_format'), strtotime($appl_info->leave_start_date)) . ' TO ' . strftime(config_item('date_format'), strtotime($appl_info->leave_end_date)),
        );
        // Update into tbl_project
        $this->application_model->_table_name = "tbl_activities"; //table name
        $this->application_model->_primary_key = "activities_id";
        $this->application_model->save($activities);

        //message for user

        if (!empty($already_leave)) {
            foreach ($already_leave as $al_leave) {
                $etype = "error";
                $msg = lang('leave_date_conflict') . ' ' . $al_leave;;
                set_message($etype, $msg);
            }
        } else {
            $msg = null;
        }
        $type = "success";
        $message = lang('application_status_changed');
        set_message($type, $message);
        redirect('admin/leave_management'); //redirect page
    }

    function send_application_status_by_email($appl_info, $approve = null)
    {
        $leave_email = config_item('leave_email');
        $user_info = $this->application_model->check_by(array('user_id' => $appl_info->user_id), 'tbl_users');
        if (!empty($leave_email) && $leave_email == 1) {
            if (!empty($approve)) {
                $email_template = $this->application_model->check_by(array('email_group' => 'leave_approve_email'), 'tbl_email_templates');
                $description = 'not_leave_request_approve';
            } else {
                $email_template = $this->application_model->check_by(array('email_group' => 'leave_reject_email'), 'tbl_email_templates');
                $description = 'not_leave_request_reject';
            }
            $message = $email_template->template_body;
            $subject = $email_template->subject;
            $startDate = str_replace("{START_DATE}", $appl_info->leave_start_date, $message);
            $endDate = str_replace("{END_DATE}", $appl_info->leave_end_date, $startDate);
            $message = str_replace("{SITE_NAME}", config_item('company_name'), $endDate);
            $data['message'] = $message;
            $message = $this->load->view('email_template', $data, TRUE);

            $params['subject'] = $subject;
            $params['message'] = $message;
            $params['resourceed_file'] = '';
            $params['recipient'] = $user_info->email;

            $this->application_model->send_email($params);
        } else {
            return true;
        }
        $notifyUser = array($appl_info->user_id);
        if (!empty($notifyUser)) {
            foreach ($notifyUser as $v_user) {
                if (!empty($v_user)) {
                    if ($v_user != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $v_user,
                            'description' => $description,
                            'icon' => 'clock-o',
                            'link' => 'admin/leave_management/index/view_details/' . $appl_info->leave_application_id,
                            'value' => lang('by') . ' ' . $this->session->userdata('name'),
                        ));
                    }
                }
            }
        }
        if (!empty($notifyUser)) {
            show_notification($notifyUser);
        }
    }

    public function download_files($id, $fileName)
    {
        $appl_info = $this->application_model->check_by(array('leave_application_id' => $id), 'tbl_leave_application');

        $this->load->helper('download');
        if ($appl_info->attachment) {
            $down_data = file_get_contents('uploads/' . $fileName); // Read the file's contents
            force_download($fileName, $down_data);
        } else {
            $type = "error";
            $message = lang('operation_failed');
            set_message($type, $message);
            redirect('admin/leave_management/index/view_details/' . $id);
        }
    }

    public function delete_application($id)
    {
        $appl_info = $this->application_model->check_by(array('leave_application_id' => $id), 'tbl_leave_application');
        $profile_info = $this->application_model->check_by(array('user_id' => $appl_info->user_id), 'tbl_account_details');
        $leave_category = $this->application_model->check_by(array('leave_category_id' => $appl_info->leave_category_id), '	tbl_leave_category');
        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'leave_management',
            'module_field_id' => $id,
            'activity' => 'activity_leave_deleted',
            'icon' => 'fa-ticket',
            'value1' => $profile_info->fullname . ' -> ' . $leave_category->leave_category,
            'value2' => strftime(config_item('date_format'), strtotime($appl_info->leave_start_date)) . ' TO ' . strftime(config_item('date_format'), strtotime($appl_info->leave_end_date)),
        );
        // Update into tbl_project
        $this->application_model->_table_name = "tbl_activities"; //table name
        $this->application_model->_primary_key = "activities_id";
        $this->application_model->save($activities);

        $this->application_model->_table_name = "tbl_leave_application"; // table name
        $this->application_model->_primary_key = "leave_application_id"; // $id
        $this->application_model->delete($id);

        //message for user
        echo json_encode(array("status" => 'success', 'message' => lang('leave_application_delete')));
        exit();
    }

}
