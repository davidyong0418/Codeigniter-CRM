<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class User extends Admin_Controller
{

    public function __construct()
    {

        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('payroll_model');
        $this->load->model('attendance_model');
        $this->load->model('utilities_model');
    }

    public function buildChild($parent, $menu)
    {
        if (isset($menu['parents'][$parent])) {
            foreach ($menu['parents'][$parent] as $ItemID) {
                if (!isset($menu['parents'][$ItemID->menu_id])) {
                    $result[$ItemID->label] = $ItemID->menu_id;
                }
                if (isset($menu['parents'][$ItemID->menu_id])) {
                    $result[$ItemID->label][$ItemID->menu_id] = self::buildChild($ItemID->menu_id, $menu);
                }
            }
        }
        return $result;
    }

    public function user_list($action = NULL, $id = NULL)
    {

        $user_id = $id;
        if ($action == 'edit_user') {
            $data['active'] = 2;
            $can_edit = $this->user_model->can_action('tbl_users', 'edit', array('user_id' => $id));
            $edited = can_action('24', 'edited');
            $data['additional_emails'] = $this->user_model->get_additional_emails($id);
            $data['additional_phones'] = $this->user_model->get_additional_phones($id);
            $data['subcontructor_marketplace'] = $this->user_model->get_subcontructor_marketplace($id);
            $data['subcontructor_pay'] = $this->user_model->get_subcontructor_pay($id);
            $data['breadcrumb_f'] = $this->user_model->get_breadcrumb($id);
            if (!empty($can_edit) || !empty($edited)) {
                $data['login_info'] = $this->db->where('user_id', $user_id)->get('tbl_users')->row();
            }
        } else {
            $data['active'] = 1;
        }

        $data['title'] = 'User List';

        $this->user_model->_table_name = 'tbl_client'; //table name
        $this->user_model->_order_by = 'client_id';
        $data['all_client_info'] = $this->user_model->get();
        $data['whitebrand'] = $this->db->get('tbl_whitebrand')->result();
        // get all language
        $data['languages'] = $this->db->where('active', 1)->order_by('name', 'ASC')->get('tbl_languages')->result();

        $data['permission_user'] = $this->user_model->all_permission_user('24');
        $data['all_user_info'] = $this->user_model->get_permission('tbl_users',null,'staff');
        $data['all_designation_info'] = $this->user_model->all_designation();
    
        $data['subview'] = $this->load->view('admin/user/user_list', $data, true);
        $this->load->view('admin/_layout_main', $data);
    }
    public function add_additional_label_modal(){
        $data['title'] = 'Add additional label';
        $data['subview'] = $this->load->view('admin/user/additional_modal', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }
    public function update_additional_label()
    {
        $new_label = $this->input->post('new_label', TRUE);
        $result = '';
        $data = array(
            'label' => $new_label,
        );

        $check_result = $this->db->where($data)->get('tbl_additional_label')->result();
        if(!empty($check_result)){
            $result = array(
                'status' => 'error',
                'message' => 'already existed'
            );
        }
        else{
            $this->db->insert('tbl_additional_label', $data);
            $additional_label = $this->db->get('tbl_additional_label')->result();
            $result = array(
                'status' => 'success',
                'message' => 'Successfully added',
                'data' => $additional_label
            );
        }
        echo json_encode($result);
        exit();
    }
    public function user_details($id, $active = null)
    {
        $data['title'] = lang('user_details');
        $data['id'] = $id;
        if (!empty($active)) {
            $data['active'] = $active;
        } else {
            $data['active'] = 1;
        }
        $date = $this->input->post('date', true);
        if (!empty($date)) {
            $data['date'] = $date;
        } else {
            $data['date'] = date('Y-m');
        }
        $data['attendace_info'] = $this->get_report($id, $data['date']);

        $data['my_leave_report'] = leave_report($id);

        //
        if ($this->input->post('year', TRUE)) { // if input year
            $data['year'] = $this->input->post('year', TRUE);
        } else { // else current year
            $data['year'] = date('Y'); // get current year
        }
        // get all expense list by year and month
        $data['provident_fund_info'] = $this->get_provident_fund_info($data['year'], $id);

        if ($this->input->post('overtime_year', TRUE)) { // if input year
            $data['overtime_year'] = $this->input->post('overtime_year', TRUE);
        } else { // else current year
            $data['overtime_year'] = date('Y'); // get current year
        }
        // get all expense list by year and month
        $data['all_overtime_info'] = $this->get_overtime_info($data['overtime_year'], $id);
        $data['profile_info'] = $this->db->where('user_id', $id)->get('tbl_account_details')->row();

        $data['total_attendance'] = count($this->total_attendace_in_month($id));

        $data['total_absent'] = count($this->total_attendace_in_month($id, 'absent'));

        $data['total_leave'] = count($this->total_attendace_in_month($id, 'leave'));
        //award received
        $data['total_award'] = count($this->db->where('user_id', $id)->get('tbl_employee_award')->result());

        // get working days holiday
        $holidays = $this->global_model->get_holidays(); //tbl working Days Holiday

        $num = cal_days_in_month(CAL_GREGORIAN, date('n'), date('Y'));
        $working_holiday = 0;
        for ($i = 1; $i <= $num; $i++) {
            $day_name = date('l', strtotime("+0 days", strtotime(date('Y') . '-' . date('n') . '-' . $i)));

            if (!empty($holidays)) {
                foreach ($holidays as $v_holiday) {
                    if ($v_holiday->day == $day_name) {
                        $working_holiday += count($day_name);
                    }
                }
            }
        }
        // get public holiday
        $public_holiday = count($this->total_attendace_in_month($id, TRUE));

        // get total days in a month
        $month = date('m');
        $year = date('Y');
        $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        // total attend days in a month without public holiday and working days
        $data['total_days'] = $days - $working_holiday - $public_holiday;

        $data['all_working_hour'] = $this->all_attendance_id_by_date($id, true);

        $data['this_month_working_hour'] = $this->all_attendance_id_by_date($id);

        $data['subview'] = $this->load->view('admin/user/user_details', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }
    //  I added 
    public function add_roles_staff($user_id, $roles,$action)
    {
        

    } 

    public function all_attendance_id_by_date($user_id, $flag = null)
    {
        if (!empty($flag)) {
            $get_total_attendance = $this->db->where(array('user_id' => $user_id, 'attendance_status' => '1'))->get('tbl_attendance')->result();
            if (!empty($get_total_attendance)) {
                foreach ($get_total_attendance as $v_attendance_id) {
                    $aresult[] = $this->global_model->get_total_working_hours($v_attendance_id->attendance_id);
                }
                return $aresult;
            }
        } else {

            $month = date('n');
            $year = date('Y');
            if ($month >= 1 && $month <= 9) {
                $yymm = $year . '-' . '0' . $month;
            } else {
                $yymm = $year . '-' . $month;
            }
            $num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            for ($i = 1; $i <= $num; $i++) {
                if ($i >= 1 && $i <= 9) {
                    $sdate = $yymm . '-' . '0' . $i;
                } else {
                    $sdate = $yymm . '-' . $i;
                }
                $get_total_attendance = $this->global_model->get_total_attendace_by_date($sdate, $sdate, $user_id); // get all attendace by start date and in date
                if (!empty($get_total_attendance)) {
                    foreach ($get_total_attendance as $v_attendance_id) {
                        $result[] = $this->global_model->get_total_working_hours($v_attendance_id->attendance_id);
                    }
                }
            }
            if (!empty($result)) {
                return $result;
            }
        }
    }

    public function total_attendace_in_month($user_id, $flag = NULL)
    {
        $month = date('m');
        $year = date('Y');

        if ($month >= 1 && $month <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
            $start_date = $year . "-" . '0' . $month . '-' . '01';
            $end_date = $year . "-" . '0' . $month . '-' . '31';
        } else {
            $start_date = $year . "-" . $month . '-' . '01';
            $end_date = $year . "-" . $month . '-' . '31';
        }
        if (!empty($flag) && $flag == 1) { // if flag is not empty that means get pulic holiday
            $get_public_holiday = $this->global_model->get_holiday_list_by_date($start_date, $end_date);

            if (!empty($get_public_holiday)) { // if not empty the public holiday
                foreach ($get_public_holiday as $v_holiday) {
                    if ($v_holiday->start_date == $v_holiday->end_date) { // if start date and end date is equal return one data
                        $total_holiday[] = $v_holiday->start_date;
                    } else { // if start date and end date not equan return all date
                        for ($j = $v_holiday->start_date; $j <= $v_holiday->end_date; $j++) {
                            $total_holiday[] = $j;
                        }
                    }
                }
                return $total_holiday;
            }
        } elseif (!empty($flag)) { // if flag is not empty that means get pulic holiday
            $get_total_absent = $this->global_model->get_total_attendace_by_date($start_date, $end_date, $user_id, $flag); // get all attendace by start date and in date
            return $get_total_absent;
        } else {
            $get_total_attendance = $this->global_model->get_total_attendace_by_date($start_date, $end_date, $user_id); // get all attendace by start date and in date
            return $get_total_attendance;
        }
    }

    public function get_overtime_info($year, $user_id)
    {// this function is to create get monthy recap report

        for ($i = 1; $i <= 12; $i++) { // query for months
            if ($i >= 1 && $i <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
                $start_date = $year . "-" . '0' . $i . '-' . '01';
                $end_date = $year . "-" . '0' . $i . '-' . '31';
            } else {
                $start_date = $year . "-" . $i . '-' . '01';
                $end_date = $year . "-" . $i . '-' . '31';
            }
            $get_expense_list[$i] = $this->utilities_model->get_overtime_info_by_date($start_date, $end_date, $user_id); // get all report by start date and in date
        }

        return $get_expense_list; // return the result
    }

    public function overtime_report_pdf($year, $user_id)
    {
        $data['all_overtime_info'] = $this->get_overtime_info($year, $user_id);

        $data['monthyaer'] = $year;
        $data['user_info'] = $this->db->where('user_id', $user_id)->get('tbl_account_details')->row();
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('admin/user/overtime_report_pdf', $data, TRUE);
        pdf_create($viewfile, 'Overtime Report  - ' . $data['monthyaer']);
    }

    public function get_provident_fund_info($year, $user_id)
    {// this function is to create get monthy recap report

        for ($i = 1; $i <= 12; $i++) { // query for months
            if ($i >= 1 && $i <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
                $start_date = $year . "-" . '0' . $i;
                $end_date = $year . "-" . '0' . $i;
            } else {
                $start_date = $year . "-" . $i;
                $end_date = $year . "-" . $i;
            }
            $provident_fund_info[$i] = $this->payroll_model->get_provident_fund_info_by_date($start_date, $end_date, $user_id); // get all report by start date and in date
        }

        return $provident_fund_info; // return the result
    }

    public function provident_fund_pdf($year, $user_id)
    {

        $data['provident_fund_info'] = $this->get_provident_fund_info($year, $user_id);
        $data['monthyaer'] = $year;

        $data['user_info'] = $this->db->where('user_id', $user_id)->get('tbl_account_details')->row();

        $this->load->helper('dompdf');
        $viewfile = $this->load->view('admin/user/provident_fund_pdf', $data, TRUE);
        pdf_create($viewfile, lang('provident_found_report') . ' - ' . $data['monthyaer']);
    }

    public function timecard_details_pdf($id, $date)
    {
        $data['profile_info'] = $this->db->where('user_id', $id)->get('tbl_account_details')->row();
        $data['date'] = $date;
        $data['attendace_info'] = $this->get_report($id, $date);

        $viewfile = $this->load->view('admin/user/timecard_details_pdf', $data, TRUE);

        $this->load->helper('dompdf');
        pdf_create($viewfile, lang('timecard_details') . '- ' . $data['profile_info']->fullname);
    }

    public function get_report($user_id, $date)
    {
        $month = date('n', strtotime($date));
        $year = date('Y', strtotime($date));
        $num = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $holidays = $this->global_model->get_holidays(); //tbl working Days Holiday

        if ($month >= 1 && $month <= 9) {
            $yymm = $year . '-' . '0' . $month;
        } else {
            $yymm = $year . '-' . $month;
        }

        $public_holiday = $this->global_model->get_public_holidays($yymm);

        //tbl a_calendar Days Holiday
        if (!empty($public_holiday)) {
            foreach ($public_holiday as $p_holiday) {
                $p_hday = $this->attendance_model->GetDays($p_holiday->start_date, $p_holiday->end_date);
            }
        }

        $key = 1;
        $x = 0;
        for ($i = 1; $i <= $num; $i++) {

            if ($i >= 1 && $i <= 9) {
                $sdate = $yymm . '-' . '0' . $i;
            } else {
                $sdate = $yymm . '-' . $i;
            }
            $day_name = date('l', strtotime("+$x days", strtotime($year . '-' . $month . '-' . $key)));

            $data['week_info'][date('W', strtotime($sdate))][$sdate] = $sdate;

            // get leave info
            if (!empty($holidays)) {
                foreach ($holidays as $v_holiday) {
                    if ($v_holiday->day == $day_name) {
                        $flag = 'H';
                    }
                }
            }
            if (!empty($p_hday)) {
                foreach ($p_hday as $v_hday) {
                    if ($v_hday == $sdate) {
                        $flag = 'H';
                    }
                }
            }
            if (!empty($flag)) {
                $attendace_info[date('W', strtotime($sdate))][$sdate] = $this->attendance_model->attendance_report_by_empid($user_id, $sdate, $flag);
            } else {
                $attendace_info[date('W', strtotime($sdate))][$sdate] = $this->attendance_model->attendance_report_by_empid($user_id, $sdate);
            }
            $key++;
            $flag = '';
        }
        return $attendace_info;

    }

    public function update_contact($update = null, $id = null)
    {
        $data['title'] = lang('update_contact');
        $data['update'] = $update;
        $data['profile_info'] = $this->db->where('account_details_id', $id)->get('tbl_account_details')->row();
        $data['modal_subview'] = $this->load->view('admin/user/update_contact', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function update_details($id)
    {
        $data = $this->user_model->array_from_post(array('joining_date', 'gender', 'date_of_birth', 'maratial_status', 'father_name', 'mother_name', 'phone', 'mobile', 'skype', 'present_address', 'passport'));

        $this->user_model->_table_name = 'tbl_account_details'; // table name
        $this->user_model->_primary_key = 'account_details_id'; // $id
        $this->user_model->save($data, $id);

        $profile_info = $this->db->where('account_details_id', $id)->get('tbl_account_details')->row();
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'user',
            'module_field_id' => $id,
            'activity' => 'activity_update_user',
            'icon' => 'fa-user',
            'value1' => $profile_info->fullname
        );
        $this->user_model->_table_name = 'tbl_activities';
        $this->user_model->_primary_key = "activities_id";
        $this->user_model->save($activities);

        $message = lang('update_user_info');
        $type = 'success';
        set_message($type, $message);
        redirect('admin/user/user_details/' . $profile_info->user_id); //redirect page
    }

    public function user_documents($id)
    {
        $data['title'] = lang('update_documents');
        $data['profile_info'] = $this->db->where('user_id', $id)->get('tbl_account_details')->row();
        $data['document_info'] = $this->db->where('user_id', $id)->get('tbl_employee_document')->row();
        $data['modal_subview'] = $this->load->view('admin/user/user_documents', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function update_documents($id)
    {
        $edited = can_action('24', 'edited');
        if (!empty($edited)) {
            $profile_info = $this->db->where('account_details_id', $id)->get('tbl_account_details')->row();
            // ** Employee Document Information Save and Update Start  **
            // Resume File upload

            if (!empty($_FILES['resume']['name'])) {
                $old_path = $this->input->post('resume_path');
                if ($old_path) {
                    unlink($old_path);
                }
                $val = $this->user_model->uploadAllType('resume');
                $val == TRUE || redirect('admin/user/user_details/' . $profile_info->user_id);
                $document_data['resume_filename'] = $val['fileName'];
                $document_data['resume'] = $val['path'];
                $document_data['resume_path'] = $val['fullPath'];
            }
            // offer_letter File upload
            if (!empty($_FILES['offer_letter']['name'])) {
                $old_path = $this->input->post('offer_letter_path');
                if ($old_path) {
                    unlink($old_path);
                }
                $val = $this->user_model->uploadAllType('offer_letter');
                $val == TRUE || redirect('admin/user/user_details/' . $profile_info->user_id);
                $document_data['offer_letter_filename'] = $val['fileName'];
                $document_data['offer_letter'] = $val['path'];
                $document_data['offer_letter_path'] = $val['fullPath'];
            }
            // joining_letter File upload
            if (!empty($_FILES['joining_letter']['name'])) {
                $old_path = $this->input->post('joining_letter_path');
                if ($old_path) {
                    unlink($old_path);
                }
                $val = $this->user_model->uploadAllType('joining_letter');
                $val == TRUE || redirect('admin/user/user_details/' . $profile_info->user_id);
                $document_data['joining_letter_filename'] = $val['fileName'];
                $document_data['joining_letter'] = $val['path'];
                $document_data['joining_letter_path'] = $val['fullPath'];
            }

            // contract_paper File upload
            if (!empty($_FILES['contract_paper']['name'])) {
                $old_path = $this->input->post('contract_paper_path');
                if ($old_path) {
                    unlink($old_path);
                }
                $val = $this->user_model->uploadAllType('contract_paper');
                $val == TRUE || redirect('admin/user/user_details/' . $profile_info->user_id);
                $document_data['contract_paper_filename'] = $val['fileName'];
                $document_data['contract_paper'] = $val['path'];
                $document_data['contract_paper_path'] = $val['fullPath'];
            }
            // id_proff File upload
            if (!empty($_FILES['id_proff']['name'])) {
                $old_path = $this->input->post('id_proff_path');
                if ($old_path) {
                    unlink($old_path);
                }
                $val = $this->user_model->uploadAllType('id_proff');
                $val == TRUE || redirect('admin/user/user_details/' . $profile_info->user_id);
                $document_data['id_proff_filename'] = $val['fileName'];
                $document_data['id_proff'] = $val['path'];
                $document_data['id_proff_path'] = $val['fullPath'];
            }

            $fileName = $this->input->post('fileName');
            $path = $this->input->post('path');
            $fullPath = $this->input->post('fullPath');
            $size = $this->input->post('size');
            $is_image = $this->input->post('is_image');

            if (!empty($fileName)) {
                foreach ($fileName as $key => $name) {
                    $old['fileName'] = $name;
                    $old['path'] = $path[$key];
                    $old['fullPath'] = $fullPath[$key];
                    $old['size'] = $size[$key];
                    $old['is_image'] = $is_image[$key];
                    $result[] = $old;
                }

                $document_data['other_document'] = json_encode($result);
            }

            if (!empty($_FILES['other_document']['name']['0'])) {
                $old_path_info = $this->input->post('upload_path');
                if (!empty($old_path_info)) {
                    foreach ($old_path_info as $old_path) {
                        unlink($old_path);
                    }
                }
                $mul_val = $this->user_model->multi_uploadAllType('other_document');
                $document_data['other_document'] = json_encode($mul_val);
            }

            if (!empty($result) && !empty($mul_val)) {
                $file = array_merge($result, $mul_val);
                $document_data['other_document'] = json_encode($file);
            }

            $document_data['user_id'] = $profile_info->user_id;

            $this->user_model->_table_name = "tbl_employee_document"; // table name
            $this->user_model->_primary_key = "document_id"; // $id
            $document_id = $this->input->post('document_id', TRUE);
            if (!empty($document_id)) {
                $this->user_model->save($document_data, $document_id);
            } else {
                $this->user_model->save($document_data);
            }

            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'user',
                'module_field_id' => $id,
                'activity' => 'activity_documents_update',
                'icon' => 'fa-user',
                'value1' => $profile_info->fullname
            );
            $this->user_model->_table_name = 'tbl_activities';
            $this->user_model->_primary_key = "activities_id";
            $this->user_model->save($activities);

            $message = lang('emplyee_documents_update');
            $type = 'success';
            set_message($type, $message);
            redirect('admin/user/user_details/' . $profile_info->user_id . '/' . '4'); //redirect page
        } else {
            redirect('admin/user/user_list');
        }
    }

    public function new_bank($user_id, $bank_id = null)
    {
        $data['title'] = lang('new_bank');

        $data['profile_info'] = $this->db->where('user_id', $user_id)->get('tbl_account_details')->row();
        if (!empty($bank_id)) {
            $data['bank_info'] = $this->db->where('employee_bank_id', $bank_id)->get('tbl_employee_bank')->row();
        }
        $data['modal_subview'] = $this->load->view('admin/user/new_bank', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function update_bank_info($user_id, $bank_id = null)
    {
        $edited = can_action('24', 'edited');
        if (!empty($edited)) {
            $bank_data = $this->user_model->array_from_post(array('bank_name', 'routing_number', 'account_name', 'account_number','type_of_account'));
            $bank_data['user_id'] = $user_id;
            $this->user_model->_table_name = "tbl_employee_bank"; // table name
            $this->user_model->_primary_key = "employee_bank_id"; // $id

            if (!empty($bank_id)) {
                $activity = 'activity_update_user_bank';
                $msg = lang('update_bank_info');
                $this->user_model->save($bank_data, $bank_id);
            } else {
                $activity = 'activity_new_user_bank';
                $msg = lang('save_bank_info');
                $bank_id = $this->user_model->save($bank_data);
            }
            $profile_info = $this->db->where('user_id', $user_id)->get('tbl_account_details')->row();
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'user',
                'module_field_id' => $bank_id,
                'activity' => $activity,
                'icon' => 'fa-user',
                'value1' => $profile_info->fullname
            );
            $this->user_model->_table_name = 'tbl_activities';
            $this->user_model->_primary_key = "activities_id";
            $this->user_model->save($activities);

            $type = 'success';
            set_message($type, $msg);
            redirect('admin/user/user_details/' . $profile_info->user_id . '/' . '3'); //redirect page
        } else {
            redirect('admin/user/user_list');
        }
    }

    public function delete_user_bank($user_id, $bank_id)
    {
        $this->user_model->_table_name = "tbl_employee_bank"; // table name
        $this->user_model->_primary_key = "employee_bank_id"; // $id
        $this->user_model->delete($bank_id);

        $profile_info = $this->db->where('user_id', $user_id)->get('tbl_account_details')->row();
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'user',
            'module_field_id' => $bank_id,
            'activity' => 'activity_delete_user_bank',
            'icon' => 'fa-user',
            'value1' => $profile_info->fullname
        );
        $this->user_model->_table_name = 'tbl_activities';
        $this->user_model->_primary_key = "activities_id";
        $this->user_model->save($activities);
        $type = 'success';
        $msg = lang('delete_user_bank');
        set_message($type, $msg);
        redirect('admin/user/user_details/' . $profile_info->user_id);
    }

    /*     * * Save New User ** */
    public function save_user()
    {
        $created = can_action('24', 'created');
        $edited = can_action('24', 'edited');
        if (!empty($created) || !empty($edited)) {
            $login_data = $this->user_model->array_from_post(array('username', 'email', 'role_id','activated'));
            $user_id = $this->input->post('user_id', true);
            // update root category
            $where = array('username' => $login_data['username']);
            $email = array('email' => $login_data['email']);
            // duplicate value check in DB
            $action = '';
            if (!empty($user_id)) { // if id exist in db update data
                $check_id = array('user_id !=' => $user_id);
                $action = 'update';
            } else { // if id is not exist then set id as null
                $check_id = null;
            }
            // check whether this input data already exist or not
            $check_user = $this->user_model->check_update('tbl_users', $where, $check_id);
            $check_email = $this->user_model->check_update('tbl_users', $email, $check_id);
            if (!empty($check_email)) { // if input data already exist show error alert
                if (!empty($check_user)) {
                    $error = $login_data['username'];
                } else {
                    $error = $login_data['email'];
                }

                // massage for user
                $type = 'error';
                $message = "<strong style='color:#000'>" . $error . '</strong>  ' . lang('already_exist');

                // $password = $this->input->post('password', TRUE);
                // $confirm_password = $this->input->post('confirm_password', TRUE);
                // if ($password != $confirm_password) {
                //     $type = 'error';
                //     $message = lang('password_does_not_match');
                // }
            } else {
                
                // save and update query
                $login_data['last_ip'] = $this->input->ip_address();

                // if (empty($user_id)) {
                //     $password = $this->input->post('password', TRUE);
                //     $login_data['password'] = $this->hash($password);
                // }
                /*
                $permission = $this->input->post('permission', true);
                if (!empty($permission)) {
                    if ($permission == 'everyone') {
                        $assigned = 'all';
                    } else {
                        $assigned_to = $this->user_model->array_from_post(array('assigned_to'));
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
                    $login_data['permission'] = $assigned;
                } else {
                    set_message('error', lang('assigned_to') . ' Field is required');
                    redirect($_SERVER['HTTP_REFERER']);
                }
                */
                $this->user_model->_table_name = 'tbl_users'; // table name
                $this->user_model->_primary_key = 'user_id'; // $id
                if (!empty($user_id)) {
                    $id = $this->user_model->save($login_data, $user_id);
                } else {
                    $id = $this->user_model->save($login_data);
                }
                save_custom_field(13, $id);
                // save into tbl_account details
                $addition_phone = $this->input->post('additional_phone');
                $addition_phone_label = $this->input->post('additional_phone_label');
                $addition_email = $this->input->post('additional_email');
                $addition_email_label = $this->input->post('additional_email_label');

                $this->user_model->add_role_staff($id,$this->input->post('role_ids'), $action);
                $profile_data = $this->user_model->array_from_post(array('first_name','last_name', 'phone', 'white_label_brand','staff', 'sign'));
                $profile_data['fullname'] = $profile_data['first_name'].' '.$profile_data['last_name'];
               
                if(!empty($addition_email)){
                    $this->user_model->insert_additional_email($id,$addition_email,$addition_email_label,$action);
                }
                if(!empty($addition_phone)){
                    $this->user_model->insert_additional_phone($id,$addition_phone, $addition_phone_label,$action);
                }
                


                $staff_pay = $this->input->post('employee-pay');
                $staff_repeat = $this->input->post('employee-repeat');
                $subconstructor_marketplace = $this->input->post('subconstructor-marketplace');
                $subconstructor_pay = $this->input->post('subconstructor-pay');
                $subconstructor_pay_description = $this->input->post('subconstructor-pay-description');
          
                $profile_data['staff_pay'] = $staff_pay;
                $profile_data['staff_repeat'] = $staff_repeat;
                // tbl_marketplace
                if(!empty($subconstructor_marketplace)){
                    $this->user_model->insert_subcontructor_marketplace($id, $subconstructor_marketplace,$action);
                }
                // tbl_employee_subcontractor
                if(!empty($subconstructor_pay))
                {
                    $this->user_model->insert_subcontructor_pay($id, $subconstructor_pay,$subconstructor_pay_description,$action);
                }

                $account_details_id = $this->input->post('account_details_id', TRUE);
                if (!empty($_FILES['avatar']['name'])) {
                    $val = $this->user_model->uploadImage('avatar');
                    $val == TRUE || redirect('admin/user/user_list');
                    $profile_data['avatar'] = $val['path'];
                }

                $profile_data['user_id'] = $id;
                $this->user_model->_table_name = 'tbl_account_details'; // table name
                $this->user_model->_primary_key = 'account_details_id'; // $id
                if (!empty($account_details_id)) {
                    $this->user_model->save($profile_data, $account_details_id);

                } else {
                    $this->user_model->save($profile_data);
                }
                if (!empty($profile_data['designations_id'])) {
                    $desig = $this->db->where('designations_id', $profile_data['designations_id'])->get('tbl_designations')->row();
                    $department_head_id = $this->input->post('department_head_id', true);
                    if (!empty($department_head_id)) {
                        $head['department_head_id'] = $id;
                    } else {
                        $dep_head = $this->user_model->check_by(array('departments_id' => $desig->departments_id), 'tbl_departments');

                        if (empty($dep_head->department_head_id)) {
                            $head['department_head_id'] = $id;
                        }
                    }
                    if (!empty($desig->departments_id) && !empty($head)) {
                        $this->user_model->_table_name = "tbl_departments"; //table name
                        $this->user_model->_primary_key = "departments_id";
                        $this->user_model->save($head, $desig->departments_id);
                    }
                }

                $activities = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'user',
                    'module_field_id' => $id,
                    'activity' => 'activity_added_new_user',
                    'icon' => 'fa-user',
                    'value1' => $login_data['username']
                );
                $this->user_model->_table_name = 'tbl_activities';
                $this->user_model->_primary_key = "activities_id";
                $this->user_model->save($activities);
                if (!empty($id)) {
                    $this->user_model->_table_name = 'tbl_client_role'; //table name
                    $this->user_model->delete_multiple(array('user_id' => $id));
                    $all_client_menu = $this->db->get('tbl_client_menu')->result();
                    foreach ($all_client_menu as $v_client_menu) {
                        $client_role_data['menu_id'] = $this->input->post($v_client_menu->label, true);
                        if (!empty($client_role_data['menu_id'])) {
                            $client_role_data['user_id'] = $id;
                            $this->user_model->_table_name = 'tbl_client_role';
                            $this->user_model->_primary_key = 'client_role_id';
                            $this->user_model->save($client_role_data);
                        }
                    }
                }
                if (!empty($user_id)) {
                    $message = lang('update_user_info');
                } else {
                    $message = lang('save_user_info');
                }
                $type = 'success';
            }
            set_message($type, $message);
        }
        redirect('admin/user/user_list'); //redirect page
    }

    public function send_welcome_email($id)
    {
        $user_info = $this->db->where('user_id', $id)->get('tbl_users')->row();
        $profile_info = $this->db->where('user_id', $id)->get('tbl_account_details')->row();
        $email_template = $this->user_model->check_by(array('email_group' => 'wellcome_email'), 'tbl_email_templates');

        $message = $email_template->template_body;
        $subject = $email_template->subject;
        $NAME = str_replace("{NAME}", $profile_info->fullname, $message);
        $URL = str_replace("{COMPANY_URL}", base_url(), $NAME);
        $message = str_replace("{COMPANY_NAME}", config_item('company_name'), $URL);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);

        $params['subject'] = $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';
        $params['recipient'] = $user_info->email;

        $this->user_model->send_email($params);

        $type = 'success';
        $message = lang('welcome_email_success');
        set_message($type, $message);
        redirect('admin/user/user_list'); //redirect page
    }

    /*     * * Delete User ** */

    public function delete_user($id = null)
    {
        $deleted = can_action('24', 'deleted');
        if (!empty($deleted)) {
            if (!empty($id)) {
                $id = $id;
                $user_id = $this->session->userdata('user_id');

                //checking login user trying delete his own account
                if ($id == $user_id) {
                    //same user can not delete his own account
                    // redirect with error msg
                    $type = 'error';
                    $message = 'Sorry You can not delete your own account!';
                    set_message($type, $message);
                    redirect('admin/user/user_list'); //redirect page
                } else {
                    $sbtn = $this->input->post('submit', true);

                    if (!empty($sbtn)) {
                        //delete procedure run
                        // Check user in db or not
                        $this->user_model->_table_name = 'tbl_users'; //table name
                        $this->user_model->_order_by = 'user_id';
                        $result = $this->user_model->get_by(array('user_id' => $id), true);

                        if (!empty($result)) {
                            //delete user roll id
                            $this->user_model->_table_name = 'tbl_account_details';
                            $this->user_model->delete_multiple(array('user_id' => $id));//delete user roll id

                            $cwhere = array('user_id' => $id);
                            $this->user_model->_table_name = 'tbl_private_chat';
                            $this->user_model->delete_multiple($cwhere);

                            $this->user_model->_table_name = 'tbl_private_chat_users';
                            $this->user_model->delete_multiple($cwhere);

                            $this->user_model->_table_name = 'tbl_private_chat_messages';
                            $this->user_model->delete_multiple($cwhere);

                            $this->user_model->_table_name = 'tbl_activities';
                            $this->user_model->delete_multiple(array('user' => $id));

                            $this->user_model->_table_name = 'tbl_payments';
                            $this->user_model->delete_multiple(array('paid_by' => $id));

                            // delete all tbl_quotations by id
                            $this->user_model->_table_name = 'tbl_quotations';
                            $this->user_model->_order_by = 'user_id';
                            $quotations_info = $this->user_model->get_by(array('user_id' => $id), FALSE);

                            if (!empty($quotations_info)) {
                                foreach ($quotations_info as $v_quotations) {
                                    $this->user_model->_table_name = 'tbl_quotation_details';
                                    $this->user_model->delete_multiple(array('quotations_id' => $v_quotations->quotations_id));
                                }
                            }

                            $this->user_model->_table_name = 'tbl_quotations';
                            $this->user_model->delete_multiple(array('user_id' => $id));

                            $this->user_model->_table_name = 'tbl_quotationforms';
                            $this->user_model->delete_multiple(array('quotations_created_by_id' => $id));

                            $this->user_model->_table_name = 'tbl_users';
                            $this->user_model->delete_multiple(array('user_id' => $id));

                            $this->user_model->_table_name = 'tbl_user_role';
                            $this->user_model->delete_multiple(array('designations_id' => $id));

                            $this->user_model->_table_name = 'tbl_inbox';
                            $this->user_model->delete_multiple(array('user_id' => $id));

                            $this->user_model->_table_name = 'tbl_sent';
                            $this->user_model->delete_multiple(array('user_id' => $id));

                            $this->user_model->_table_name = 'tbl_draft';
                            $this->user_model->delete_multiple(array('user_id' => $id));

                            $tickets_info = $this->db->get('tbl_tickets')->result();
                            if (!empty($tickets_info)) {
                                foreach ($tickets_info as $v_tickets) {
                                    if (!empty($v_tickets->permission) && $v_tickets->permission != 'all') {
                                        $allowad_user = json_decode($v_tickets->permission);
                                        if (!empty($allowad_user)) {
                                            foreach ($allowad_user as $op_user_id => $v_user) {
                                                if ($op_user_id == $id || $v_tickets->reporter == $id) {
                                                    $this->user_model->_table_name = 'tbl_tickets';
                                                    $this->user_model->delete_multiple(array('tickets_id' => $v_tickets->tickets_id));
                                                    $this->user_model->_table_name = 'tbl_tickets_replies';
                                                    $this->user_model->delete_multiple(array('tickets_id' => $v_tickets->tickets_id));
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            // xpm crm
                            // delete all leads by id
                            $leads_info = $this->db->get('tbl_leads')->result();
                            if (!empty($leads_info)) {
                                foreach ($leads_info as $v_leads) {
                                    if (!empty($v_leads->permission) && $v_leads->permission != 'all') {
                                        $allowad_user = json_decode($v_leads->permission);
                                        if (!empty($allowad_user)) {
                                            foreach ($allowad_user as $op_user_id => $v_user) {
                                                if ($op_user_id == $id) {
                                                    //delete data into table.
                                                    $this->user_model->_table_name = "tbl_calls"; // table name
                                                    $this->user_model->delete_multiple(array('leads_id' => $v_leads->leads_id));

                                                    //delete data into table.
                                                    $this->user_model->_table_name = "tbl_mettings"; // table name
                                                    $this->user_model->delete_multiple(array('leads_id' => $v_leads->leads_id));

                                                    //delete data into table.
                                                    $this->user_model->_table_name = "tbl_task_comment"; // table name
                                                    $this->user_model->delete_multiple(array('leads_id' => $v_leads->leads_id));

                                                    $this->user_model->_table_name = "tbl_task_attachment"; //table name
                                                    $this->user_model->_order_by = "leads_id";
                                                    $files_info = $this->user_model->get_by(array('leads_id' => $v_leads->leads_id), FALSE);

                                                    if (!empty($files_info)) {
                                                        foreach ($files_info as $v_files) {
                                                            //save data into table.
                                                            $this->user_model->_table_name = "tbl_task_uploaded_files"; // table name
                                                            $this->user_model->delete_multiple(array('task_attachment_id' => $v_files->task_attachment_id));
                                                        }
                                                    }
                                                    //save data into table.
                                                    $this->user_model->_table_name = "tbl_task_attachment"; // table name
                                                    $this->user_model->delete_multiple(array('leads_id' => $v_leads->leads_id));

                                                    $this->user_model->_table_name = "tbl_task"; // table name
                                                    $this->user_model->delete_multiple(array('leads_id' => $v_leads->leads_id));

                                                    $this->user_model->_table_name = 'tbl_leads';
                                                    $this->user_model->_primary_key = 'leads_id';
                                                    $this->user_model->delete($v_leads->leads_id);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            //save data into table.
                            $this->user_model->_table_name = "tbl_milestones"; // table name
                            $this->user_model->delete_multiple(array('user_id' => $id));
                            // todo
                            $this->user_model->_table_name = "tbl_todo"; // table name
                            $this->user_model->delete_multiple(array('user_id' => $id));

                            // opportunity
                            $oppurtunity_info = $this->db->get('tbl_opportunities')->result();
                            if (!empty($oppurtunity_info)) {
                                foreach ($oppurtunity_info as $v_oppurtunity) {
                                    if (!empty($v_oppurtunity->permission) && $v_oppurtunity->permission != 'all') {
                                        $allowad_user = json_decode($v_oppurtunity->permission);
                                        if (!empty($allowad_user)) {
                                            foreach ($allowad_user as $op_user_id => $v_user) {
                                                if ($op_user_id == $id)
                                                    //delete data into table.
                                                    $this->user_model->_table_name = "tbl_calls"; // table name
                                                $this->user_model->delete_multiple(array('opportunities_id' => $v_oppurtunity->opportunities_id));

                                                //delete data into table.
                                                $this->user_model->_table_name = "tbl_mettings"; // table name
                                                $this->user_model->delete_multiple(array('opportunities_id' => $v_oppurtunity->opportunities_id));

                                                //delete data into table.
                                                $this->user_model->_table_name = "tbl_task_comment"; // table name
                                                $this->user_model->delete_multiple(array('opportunities_id' => $v_oppurtunity->opportunities_id));

                                                $this->user_model->_table_name = "tbl_task_attachment"; //table name
                                                $this->user_model->_order_by = "task_id";
                                                $files_info = $this->user_model->get_by(array('opportunities_id' => $v_oppurtunity->opportunities_id), FALSE);
                                                if (!empty($files_info)) {
                                                    foreach ($files_info as $v_files) {
                                                        //save data into table.
                                                        $this->user_model->_table_name = "tbl_task_uploaded_files"; // table name
                                                        $this->user_model->delete_multiple(array('task_attachment_id' => $v_files->task_attachment_id));
                                                    }
                                                }
                                                //save data into table.
                                                $this->user_model->_table_name = "tbl_task_attachment"; // table name
                                                $this->user_model->delete_multiple(array('opportunities_id' => $v_oppurtunity->opportunities_id));

                                                //save data into table.
                                                $this->user_model->_table_name = "tbl_task"; // table name
                                                $this->user_model->delete_multiple(array('opportunities_id' => $v_oppurtunity->opportunities_id));

                                                //save data into table.
                                                $this->user_model->_table_name = "tbl_bug"; // table name
                                                $this->user_model->delete_multiple(array('opportunities_id' => $v_oppurtunity->opportunities_id));

                                                $this->user_model->_table_name = 'tbl_opportunities';
                                                $this->user_model->_primary_key = 'opportunities_id';
                                                $this->user_model->delete($v_oppurtunity->opportunities_id);
                                            }
                                        }
                                    }
                                }
                            }
                            // project
                            $project_info = $this->db->get('tbl_project')->result();
                            if (!empty($project_info)) {
                                foreach ($project_info as $v_project) {
                                    if (!empty($v_project->permission) && $v_project->permission != 'all') {
                                        $allowad_user = json_decode($v_project->permission);
                                        if (!empty($allowad_user)) {
                                            foreach ($allowad_user as $user_id => $v_user) {
                                                if ($user_id == $id) {
                                                    //delete data into table.
                                                    $this->user_model->_table_name = "tbl_task_comment"; // table name
                                                    $this->user_model->delete_multiple(array('project_id' => $v_project->project_id));

                                                    $this->user_model->_table_name = "tbl_task_attachment"; //table name
                                                    $this->user_model->_order_by = "task_id";
                                                    $files_info = $this->user_model->get_by(array('project_id' => $v_project->project_id), FALSE);
                                                    if (!empty($files_info)) {
                                                        foreach ($files_info as $v_files) {
                                                            //save data into table.
                                                            $this->user_model->_table_name = "tbl_task_uploaded_files"; // table name
                                                            $this->user_model->delete_multiple(array('task_attachment_id' => $v_files->task_attachment_id));
                                                        }
                                                    }
                                                    //save data into table.
                                                    $this->user_model->_table_name = "tbl_task_attachment"; // table name
                                                    $this->user_model->delete_multiple(array('project_id' => $v_project->project_id));

                                                    //save data into table.
                                                    $this->user_model->_table_name = "tbl_milestones"; // table name
                                                    $this->user_model->delete_multiple(array('project_id' => $v_project->project_id));

                                                    // tasks
                                                    $taskss_info = $this->db->where('project_id', $v_project->project_id)->get('tbl_task')->result();
                                                    if (!empty($taskss_info)) {
                                                        foreach ($taskss_info as $v_taskss) {
                                                            if (!empty($v_taskss->permission) && $v_taskss->permission != 'all') {
                                                                $allowad_user = json_decode($v_taskss->permission);
                                                                if (!empty($allowad_user)) {
                                                                    foreach ($allowad_user as $task_user_id => $v_user) {
                                                                        if ($task_user_id == $id) {

                                                                            $this->user_model->_table_name = "tbl_task_attachment"; //table name
                                                                            $this->user_model->_order_by = "task_id";
                                                                            $files_info = $this->user_model->get_by(array('task_id' => $v_taskss->task_id), FALSE);
                                                                            foreach ($files_info as $v_files) {
                                                                                $this->user_model->_table_name = "tbl_task_uploaded_files"; //table name
                                                                                $this->user_model->delete_multiple(array('task_attachment_id' => $v_files->task_attachment_id));
                                                                            }
                                                                            //delete into table.
                                                                            $this->user_model->_table_name = "tbl_task_attachment"; // table name
                                                                            $this->user_model->delete_multiple(array('task_id' => $v_taskss->task_id));

                                                                            //delete data into table.
                                                                            $this->user_model->_table_name = "tbl_task_comment"; // table name
                                                                            $this->user_model->delete_multiple(array('task_id' => $v_taskss->task_id));

                                                                            $this->user_model->_table_name = "tbl_task"; // table name
                                                                            $this->user_model->_primary_key = "task_id"; // $id
                                                                            $this->user_model->delete($v_taskss->task_id);
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                    // Bugs
                                                    $bugs_info = $this->db->where('project_id', $v_project->project_id)->get('tbl_bug')->result();
                                                    if (!empty($bugs_info)) {
                                                        foreach ($bugs_info as $v_bugs) {
                                                            if (!empty($v_bugs->permission) && $v_bugs->permission != 'all') {
                                                                $allowad_user = json_decode($v_bugs->permission);
                                                                if (!empty($allowad_user)) {
                                                                    foreach ($allowad_user as $bugs_user_id => $v_user) {
                                                                        if ($bugs_user_id == $id) {

                                                                            $this->user_model->_table_name = "tbl_task_attachment"; //table name
                                                                            $this->user_model->_order_by = "bug_id";
                                                                            $files_info = $this->user_model->get_by(array('bug_id' => $v_bugs->bug_id), FALSE);
                                                                            foreach ($files_info as $v_files) {
                                                                                $this->user_model->_table_name = "tbl_task_uploaded_files"; //table name
                                                                                $this->user_model->delete_multiple(array('task_attachment_id' => $v_files->task_attachment_id));
                                                                            }
                                                                            //delete into table.
                                                                            $this->user_model->_table_name = "tbl_task_attachment"; // table name
                                                                            $this->user_model->delete_multiple(array('bug_id' => $v_bugs->bug_id));

                                                                            //delete data into table.
                                                                            $this->user_model->_table_name = "tbl_task_comment"; // table name
                                                                            $this->user_model->delete_multiple(array('bug_id' => $v_bugs->bug_id));

                                                                            //delete data into table.
                                                                            $this->user_model->_table_name = "tbl_task"; // table name
                                                                            $this->user_model->delete_multiple(array('bug_id' => $v_bugs->bug_id));

                                                                            $this->user_model->_table_name = "tbl_bug"; // table name
                                                                            $this->user_model->_primary_key = "bug_id"; // $id
                                                                            $this->user_model->delete($v_bugs->bug_id);
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }

                                                    $this->user_model->_table_name = 'tbl_project';
                                                    $this->user_model->_primary_key = 'project_id';
                                                    $this->user_model->delete($v_project->project_id);
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            // tasks
                            $taskss_info = $this->db->get('tbl_task')->result();
                            if (!empty($taskss_info)) {
                                foreach ($taskss_info as $v_taskss) {
                                    if (!empty($v_taskss->permission) && $v_taskss->permission != 'all') {
                                        $allowad_user = json_decode($v_taskss->permission);
                                        if (!empty($allowad_user)) {
                                            foreach ($allowad_user as $task_user_id => $v_user) {
                                                if ($task_user_id == $id) {

                                                    $this->user_model->_table_name = "tbl_task_attachment"; //table name
                                                    $this->user_model->_order_by = "task_id";
                                                    $files_info = $this->user_model->get_by(array('task_id' => $v_taskss->task_id), FALSE);
                                                    foreach ($files_info as $v_files) {
                                                        $this->user_model->_table_name = "tbl_task_uploaded_files"; //table name
                                                        $this->user_model->delete_multiple(array('task_attachment_id' => $v_files->task_attachment_id));
                                                    }
                                                    //delete into table.
                                                    $this->user_model->_table_name = "tbl_task_attachment"; // table name
                                                    $this->user_model->delete_multiple(array('task_id' => $v_taskss->task_id));

                                                    //delete data into table.
                                                    $this->user_model->_table_name = "tbl_task_comment"; // table name
                                                    $this->user_model->delete_multiple(array('task_id' => $v_taskss->task_id));

                                                    $this->user_model->_table_name = "tbl_task"; // table name
                                                    $this->user_model->_primary_key = "task_id"; // $id
                                                    $this->user_model->delete($v_taskss->task_id);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            // Bugs
                            $bugs_info = $this->db->get('tbl_bug')->result();
                            if (!empty($bugs_info)) {
                                foreach ($bugs_info as $v_bugs) {
                                    if (!empty($v_bugs->permission) && $v_bugs->permission != 'all') {
                                        $allowad_user = json_decode($v_bugs->permission);
                                        if (!empty($allowad_user)) {
                                            foreach ($allowad_user as $bugs_user_id => $v_user) {
                                                if ($bugs_user_id == $id) {

                                                    $this->user_model->_table_name = "tbl_task_attachment"; //table name
                                                    $this->user_model->_order_by = "bug_id";
                                                    $files_info = $this->user_model->get_by(array('bug_id' => $v_bugs->bug_id), FALSE);
                                                    foreach ($files_info as $v_files) {
                                                        $this->user_model->_table_name = "tbl_task_uploaded_files"; //table name
                                                        $this->user_model->delete_multiple(array('task_attachment_id' => $v_files->task_attachment_id));
                                                    }
                                                    //delete into table.
                                                    $this->user_model->_table_name = "tbl_task_attachment"; // table name
                                                    $this->user_model->delete_multiple(array('bug_id' => $v_bugs->bug_id));

                                                    //delete data into table.
                                                    $this->user_model->_table_name = "tbl_task_comment"; // table name
                                                    $this->user_model->delete_multiple(array('bug_id' => $v_bugs->bug_id));

                                                    //delete data into table.
                                                    $this->user_model->_table_name = "tbl_task"; // table name
                                                    $this->user_model->delete_multiple(array('bug_id' => $v_bugs->bug_id));

                                                    $this->user_model->_table_name = "tbl_bug"; // table name
                                                    $this->user_model->_primary_key = "bug_id"; // $id
                                                    $this->user_model->delete($v_bugs->bug_id);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            // tbl_invoices
                            $invoices_info = $this->db->get('tbl_invoices')->result();
                            if (!empty($invoices_info)) {
                                foreach ($invoices_info as $v_invoices) {
                                    if (!empty($v_invoices->permission) && $v_invoices->permission != 'all') {
                                        $allowad_user = json_decode($v_invoices->permission);
                                        if (!empty($allowad_user)) {
                                            foreach ($allowad_user as $invoice_user_id => $v_user) {
                                                if ($invoice_user_id == $id) {
                                                    $this->user_model->_table_name = "tbl_invoices"; // table name
                                                    $this->user_model->_primary_key = "invoices_id"; // $id
                                                    $this->user_model->delete($v_invoices->invoices_id);

                                                    $this->user_model->_table_name = "tbl_items"; // table name
                                                    $this->user_model->delete_multiple(array('invoices_id' => $v_invoices->invoices_id));
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            // tbl_estimates
                            $estimate_info = $this->db->get('tbl_estimates')->result();
                            if (!empty($estimate_info)) {
                                foreach ($estimate_info as $v_estimate) {
                                    if (!empty($v_estimate->permission) && $v_estimate->permission != 'all') {
                                        $allowad_user = json_decode($v_estimate->permission);
                                        if (!empty($allowad_user)) {
                                            foreach ($allowad_user as $estimate_user_id => $v_user) {
                                                if ($estimate_user_id == $id) {
                                                    $this->user_model->_table_name = "tbl_estimates"; // table name
                                                    $this->user_model->_primary_key = "estimates_id"; // $id
                                                    $this->user_model->delete($v_estimate->estimates_id);

                                                    $this->user_model->_table_name = "tbl_estimate_items"; // table name
                                                    $this->user_model->delete_multiple(array('estimates_id' => $v_estimate->estimates_id));
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            // 	tbl_tax_rates
                            $tax_rate_info = $this->db->get('tbl_tax_rates')->result();
                            if (!empty($tax_rate_info)) {
                                foreach ($tax_rate_info as $v_tax_rat) {
                                    if (!empty($v_tax_rat->permission) && $v_tax_rat->permission != 'all') {
                                        $allowad_user = json_decode($v_tax_rat->permission);
                                        if (!empty($allowad_user)) {
                                            foreach ($allowad_user as $tax_rate_user_id => $v_user) {
                                                if ($tax_rate_user_id == $id) {
                                                    $this->user_model->_table_name = "tbl_tax_rates"; // table name
                                                    $this->user_model->_primary_key = "tax_rates_id"; // $id
                                                    $this->user_model->delete($v_tax_rat->tax_rates_id);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            $transactions_info = $this->db->get('tbl_transactions')->result();
                            if (!empty($transactions_info)) {
                                foreach ($transactions_info as $v_transactions) {
                                    if (!empty($v_transactions->permission) && $v_transactions->permission != 'all') {
                                        $allowad_user = json_decode($v_transactions->permission);
                                        if (!empty($allowad_user)) {
                                            foreach ($allowad_user as $trsn_user_id => $v_user) {
                                                if ($trsn_user_id == $id) {
                                                    $this->user_model->_table_name = 'tbl_transactions';
                                                    $this->user_model->delete_multiple(array('transactions_id' => $v_transactions->transactions_id));
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            $transfer_info = $this->db->get('tbl_transfer')->result();
                            if (!empty($transfer_info)) {
                                foreach ($transfer_info as $v_transfer) {
                                    if (!empty($v_transfer->permission) && $v_transfer->permission != 'all') {
                                        $allowad_user = json_decode($v_transfer->permission);
                                        if (!empty($allowad_user)) {
                                            foreach ($allowad_user as $trfr_user_id => $v_user) {
                                                if ($trfr_user_id == $id) {
                                                    $this->user_model->_table_name = 'tbl_transfer';
                                                    $this->user_model->delete_multiple(array('transfer_id' => $v_transfer->transfer_id));
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            //redirect successful msg
                            $type = 'success';
                            $message = 'User Delete Successfully!';
                        } else {
                            //redirect error msg
                            $type = 'error';
                            $message = 'Sorry this user not find in database!';

                        }
                        set_message($type, $message);
                        redirect('admin/user/user_list'); //redirect page
                    } else {
                        $data['title'] = "Delete Users"; //Page title
                        $data['user_info'] = $this->db->where('user_id', $id)->get('tbl_account_details')->row();
                        $data['subview'] = $this->load->view('admin/user/delete_user', $data, TRUE);
                        $this->load->view('admin/_layout_main', $data); //page load
                    }
                }
            } else {
                redirect('admin/user/user_list'); //redirect page
            }
        }
    }

    public function change_status($flag, $id)
    {
        $can_edit = $this->user_model->can_action('tbl_users', 'edit', array('user_id' => $id));
        $edited = can_action('24', 'edited');
        if (!empty($can_edit) && !empty($edited)) {
            $user_info = $this->db->where('user_id', $id)->get('tbl_users')->row();
            // if flag == 1 it is active user else deactive user
            if ($flag == 1) {
                $msg = 'Active';
            } else {
                $msg = 'Deactive';
            }
            $where = array('user_id' => $id);
            $action = array('activated' => $flag);
            $this->user_model->set_action($where, $action, 'tbl_users');

            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'user',
                'module_field_id' => $id,
                'activity' => 'activity_change_status',
                'icon' => 'fa-user',
                'value1' => $user_info->username . ' ' . $msg,
            );
            $this->user_model->_table_name = 'tbl_activities';
            $this->user_model->_primary_key = "activities_id";
            $this->user_model->save($activities);

            $type = "success";
            $message = "User " . $msg . " Successfully!";
        } else {
            $type = 'error';
            $message = lang('there_in_no_value');
        }
        echo json_encode(array("status" => $type, "message" => $message));
        exit();
    }

    public function set_banned($flag, $id)
    {
        $can_edit = $this->user_model->can_action('tbl_users', 'edit', array('user_id' => $id));
        $edited = can_action('24', 'edited');
        if (!empty($can_edit) && !empty($edited)) {
            if ($flag == 1) {
                $msg = lang('banned');
                $action = array('activated' => 0, 'banned' => $flag, 'ban_reason' => $this->input->post('ban_reason', TRUE));
            } else {
                $msg = lang('unbanned');
                $action = array('activated' => 1, 'banned' => $flag);
            }
            $where = array('user_id' => $id);

            $this->user_model->set_action($where, $action, 'tbl_users');

            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'user',
                'module_field_id' => $id,
                'activity' => 'activity_change_status',
                'icon' => 'fa-user',
                'value1' => $flag,
            );
            $this->user_model->_table_name = 'tbl_activities';
            $this->user_model->_primary_key = "activities_id";
            $this->user_model->save($activities);

            $type = "success";
            $message = "User " . $msg . " Successfully!";
        } else {
            $type = 'error';
            $message = lang('there_in_no_value');
        }
        set_message($type, $message);
        redirect('admin/user/user_list'); //redirect page
    }

    public function change_banned($id)
    {

        $data['user_id'] = $id;
        $data['modal_subview'] = $this->load->view('admin/user/_modal_banned_reson', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);

    }

    public function hash($string)
    {
        return hash('sha512', $string . config_item('encryption_key'));
    }

// crud for sidebar todo list
    function todo($task = '', $todo_id = '', $swap_with = '')
    {
        if ($task == 'add') {
            $this->add_todo();
        }
        if ($task == 'reload_incomplete_todo') {
            $this->get_incomplete_todo();
        }
        if ($task == 'mark_as_done') {
            $this->mark_todo_as_done($todo_id);
        }
        if ($task == 'mark_as_undone') {
            $this->mark_todo_as_undone($todo_id);
        }
        if ($task == 'swap') {

            $this->swap_todo($todo_id, $swap_with);
        }
        if ($task == 'delete') {
            $this->delete_todo($todo_id);
        }
        $todo['opened'] = 1;
        $this->session->set_userdata($todo);
        redirect('admin/dashboard/');
    }

    function add_todo()
    {
        $data['title'] = $this->input->post('title');
        $data['user_id'] = $this->session->userdata('user_id');

        $this->db->insert('tbl_todo', $data);
        $todo_id = $this->db->insert_id();

        $data['order'] = $todo_id;
        $this->db->where('todo_id', $todo_id);
        $this->db->update('tbl_todo', $data);
    }

    function mark_todo_as_done($todo_id = '')
    {
        $data['status'] = 1;
        $this->db->where('todo_id', $todo_id);
        $this->db->update('tbl_todo', $data);
    }

    function mark_todo_as_undone($todo_id = '')
    {
        $data['status'] = 0;
        $this->db->where('todo_id', $todo_id);
        $this->db->update('tbl_todo', $data);
    }

    function swap_todo($todo_id = '', $swap_with = '')
    {
        $counter = 0;
        $temp_order = $this->db->get_where('tbl_todo', array('todo_id' => $todo_id))->row()->order;
        $user = $this->session->userdata('user_id');

        // Move current todo up.
        if ($swap_with == 'up') {
            // Fetch all todo lists of current user in ascending order.
            $this->db->order_by('order', 'ASC');
            $todo_lists = $this->db->get_where('tbl_todo', array('user_id' => $user))->result_array();
            $array_length = count($todo_lists);

            // Create separate array for orders and todo_id's from above array.
            foreach ($todo_lists as $todo_list) {
                $id_list[] = $todo_list['todo_id'];
                $order_list[] = $todo_list['order'];
            }
        }

        // Move current todo down.
        if ($swap_with == 'down') {

            // Fetch all todo lists of current user in descending order.
            $this->db->order_by('order', 'DESC');
            $todo_lists = $this->db->get_where('tbl_todo', array('user_id' => $user))->result_array();
            $array_length = count($todo_lists);

            // Create separate array for orders and todo_id's from above array.
            foreach ($todo_lists as $todo_list) {
                $id_list[] = $todo_list['todo_id'];
                $order_list[] = $todo_list['order'];
            }
        }

        // Swap orders between current and next/previous todo.
        for ($i = 0; $i < $array_length; $i++) {
            if ($temp_order == $order_list[$i]) {
                if ($counter > 0) {
                    $swap_order = $order_list[$i - 1];
                    $swap_id = $id_list[$i - 1];

                    // Update order of current todo.
                    $data['order'] = $swap_order;
                    $this->db->where('todo_id', $todo_id);
                    $this->db->update('tbl_todo', $data);

                    // Update order of next/previous todo.
                    $data['order'] = $temp_order;
                    $this->db->where('todo_id', $swap_id);
                    $this->db->update('tbl_todo', $data);
                }
            } else
                $counter++;
        }
    }

    function delete_todo($todo_id = '')
    {
        $this->db->where('todo_id', $todo_id);
        $this->db->delete('tbl_todo');
    }

    function get_incomplete_todo()
    {
        $user = $this->session->userdata('user_id');
        $this->db->where('user_id', $user);
        $this->db->where('status', 0);
        $query = $this->db->get('tbl_todo');

        $incomplete_todo_number = $query->num_rows();
        if ($incomplete_todo_number > 0) {
            echo '<span class="badge badge-secondary">';
            echo $incomplete_todo_number;
            echo '</span>';
        }
    }

    public function reset_password($id)
    {
        if ($this->session->userdata('user_type') == 1) {
            $new_password = $this->input->post('password', true);
            $old_password = $this->input->post('my_password', true);
            if (!empty($new_password)) {
                $email = $this->session->userdata('email');
                $user_info = $this->db->where('user_id', $id)->get('tbl_users')->row();
                $old_password = $this->user_model->hash($old_password);
                if ($user_info->password == $old_password) {
                    $where = array('user_id' => $id);
                    $action = array('password' => $this->user_model->hash($new_password));
                    $this->user_model->set_action($where, $action, 'tbl_users');
                    $login_details = $this->db->where('user_id', $id)->get('tbl_users')->row();
                    $activities = array(
                        'user' => $this->session->userdata('user_id'),
                        'module' => 'user',
                        'module_field_id' => $id,
                        'activity' => 'activity_reset_password',
                        'icon' => 'fa-user',
                        'value1' => $login_details->username,
                    );

                    $this->user_model->_table_name = 'tbl_activities';
                    $this->user_model->_primary_key = "activities_id";
                    $this->user_model->save($activities);

                    $this->send_email_reset_password($email, $user_info, $new_password);

                    $type = "success";
                    $message = lang('message_new_password_sent');
                } else {
                    $type = "error";
                    $message = lang('password_does_not_match');
                }
                set_message($type, $message);
                redirect('admin/user/user_details/' . $id); //redirect page

            } else {
                $data['title'] = lang('see_password');
                $data['user_info'] = $this->db->where('user_id', $id)->get('tbl_users')->row();
                $data['subview'] = $this->load->view('admin/settings/reset_password', $data, FALSE);
                $this->load->view('admin/_layout_modal', $data);
            }

        } else {
            $type = 'error';
            $message = lang('there_in_no_value');
            set_message($type, $message);
            redirect('admin/user/user_list'); //redirect page
        }


    }

    function send_email_reset_password($email, $user_info, $password)
    {
        $email_template = $this->user_model->check_by(array('email_group' => 'reset_password'), 'tbl_email_templates');
        $message = $email_template->template_body;
        $subject = $email_template->subject;

        $username = str_replace("{USERNAME}", $user_info->username, $message);
        $user_email = str_replace("{EMAIL}", $user_info->email, $username);
        $user_password = str_replace("{NEW_PASSWORD}", $password, $user_email);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $user_password);
        $params['recipient'] = $email;
        $params['subject'] = '[ ' . config_item('company_name') . ' ]' . $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';
        $this->user_model->send_email($params);
    }

}
