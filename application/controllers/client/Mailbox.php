<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of mailbox
 *
 * @author NaYeM
 */
class Mailbox extends Client_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('mailbox_model');
        $this->load->helper('ckeditor');
        $this->load->helper('text');
        $this->data['ckeditor'] = array(
            'id' => 'ck_editor',
            'path' => 'asset/js/ckeditor',
            'config' => array(
                'toolbar' => "Full",
                'width' => "99.8%",
                'height' => "350px"
            )
        );
    }

    public function index($action = NULL, $id = NULL, $status = NULL) {
        $data['page'] = lang('mailbox');
        $data['breadcrumbs'] = lang('mailbox');
        $data['title'] = lang('mailbox');
        $user_id = $this->session->userdata('user_id');
        $email = $this->session->userdata('email');
        $this->mailbox_model->_table_name = 'tbl_inbox';
        $this->mailbox_model->_order_by = 'inbox_id';
        $data['read_mail'] = $this->mailbox_model->get_by(array('inbox_id' => $id), true);
        $data['unread_mail'] = count($this->mailbox_model->get_inbox_message($email, TRUE));
        if ($action == 'sent') {
            $data['menu_active'] = 'sent';
            $data['view'] = 'sent';
            $data['get_sent_message'] = $this->mailbox_model->get_sent_message($user_id);
        } elseif ($action == 'read_send_mail') {
            $data['menu_active'] = 'sent';
            $data['view'] = 'read_mail';
            $data['read_mail'] = $this->mailbox_model->check_by(array('sent_id' => $id), 'tbl_sent');
        } elseif ($action == 'draft') {
            $data['menu_active'] = 'draft';
            $data['view'] = 'draft';
            $data['draft_message'] = $this->mailbox_model->get_draft_message($user_id);
        } elseif ($action == 'read_draft_mail') {
            $data['menu_active'] = 'draft';
            $data['view'] = 'read_mail';
            $data['read_mail'] = $this->mailbox_model->check_by(array('draft_id' => $id), 'tbl_draft');
        } elseif ($action == 'favourites') {
            $data['menu_active'] = 'favourites';
            $data['view'] = 'favourites';
            $data['favourites_mail'] = $this->mailbox_model->get_by(array('user_id' => $user_id, 'deleted' => 'no', 'favourites' => '1'), FALSE);
        } elseif ($action == 'trash') {
            $data['menu_active'] = 'trash';
            $data['view'] = 'trash';
            if ($id == 'sent') {
                $data['trash_view'] = 'sent';
                $data['get_sent_message'] = $this->mailbox_model->get_sent_message($user_id, TRUE);
            } elseif ($id == 'draft') {
                $data['trash_view'] = 'draft';
                $data['draft_message'] = $this->mailbox_model->get_draft_message($user_id, TRUE);
            } else {
                $data['trash_view'] = 'inbox';
                $data['get_inbox_message'] = $this->mailbox_model->get_inbox_message($email, '', TRUE);
            }
        } elseif ($action == 'read_inbox_mail') {
            $data['menu_active'] = 'inbox';
            $data['view'] = 'read_mail';
            $data['reply'] = 1;
            $this->mailbox_model->_primary_key = 'inbox_id';
            $updata['view_status'] = '1';
            $this->mailbox_model->save($updata, $id);
        } elseif ($action == 'added_favourites') {
            $favdata['favourites'] = $status;
            $this->mailbox_model->_primary_key = 'inbox_id';
            $this->mailbox_model->save($favdata, $id);
            redirect('client/mailbox/index/inbox');
        } elseif ($action == 'compose') {
            $data['view'] = 'compose_mail';
            $data['menu_active'] = 'inbox';
            $profile = profile();
            if ($profile->role_id == 2) {
                $where = array('role_id !=' => '2', 'activated' => '1');
            } else {
                $where = array('activated' => '1');
            }
            $data['get_user_info'] = get_result('tbl_users', $where);

            if (!empty($status)) {
                $data['inbox_info'] = $this->mailbox_model->check_by(array('inbox_id' => $id), 'tbl_inbox');
            } elseif (!empty($id)) {
                $this->mailbox_model->_table_name = 'tbl_draft';
                $this->mailbox_model->_order_by = 'draft_id';
                $data['get_draft_info'] = $this->mailbox_model->get_by(array('draft_id' => $id), TRUE);
            }

            $data['editor'] = $this->data;
        } else {
            $data['menu_active'] = 'inbox';
            $data['view'] = 'inbox';
            $data['get_inbox_message'] = $this->mailbox_model->get_inbox_message($email);
        }
        $data['subview'] = $this->load->view('client/mailbox/mailbox', $data, TRUE);
        $this->load->view('client/_layout_main', $data);
    }

    public function delete_inbox_mail($id) {
        $value = array('deleted' => 'Yes');
        $this->mailbox_model->set_action(array('inbox_id' => $id), $value, 'tbl_inbox');
        $type = "success";
        $message = lang('delete_msg');
        set_message($type, $message);
        redirect('client/mailbox/index/inbox');
    }

    public function delete_mail($action, $from_trash = NULL, $v_id = NULL) {

        // get sellected id into inbox email page
        $selected_id = $this->input->post('selected_id', TRUE);
        if (!empty($selected_id)) { // check selected message is empty or not
            foreach ($selected_id as $v_id) {
                if (!empty($from_trash)) {
                    if ($action == 'inbox') {
                        $this->mailbox_model->_table_name = 'tbl_inbox';
                        $this->mailbox_model->delete_multiple(array('inbox_id' => $v_id));
                    } elseif ($action == 'sent') {
                        $this->mailbox_model->_table_name = 'tbl_sent';
                        $this->mailbox_model->delete_multiple(array('sent_id' => $v_id));
                    } else {

                        $this->mailbox_model->_table_name = 'tbl_draft';
                        $this->mailbox_model->delete_multiple(array('draft_id' => $v_id));
                    }
                } else {
                    $value = array('deleted' => 'Yes');
                    if ($action == 'inbox') {
                        $this->mailbox_model->set_action(array('inbox_id' => $v_id), $value, 'tbl_inbox');
                    } elseif ($action == 'sent') {
                        $this->mailbox_model->set_action(array('sent_id' => $v_id), $value, 'tbl_sent');
                    } else {
                        $this->mailbox_model->set_action(array('draft_id' => $v_id), $value, 'tbl_draft');
                    }
                }
            }
            $type = "success";
            $message = lang('delete_msg');
        } elseif (!empty($v_id)) {
            if ($action == 'inbox') {
                $this->mailbox_model->_table_name = 'tbl_inbox';
                $this->mailbox_model->delete_multiple(array('inbox_id' => $v_id));
            } elseif ($action == 'sent') {
                $this->mailbox_model->_table_name = 'tbl_sent';
                $this->mailbox_model->delete_multiple(array('sent_id' => $v_id));
            } else {

                $this->mailbox_model->_table_name = 'tbl_draft';
                $this->mailbox_model->delete_multiple(array('draft_id' => $v_id));
            }
            if ($action == 'inbox') {
                redirect('client/mailbox/index/trash/inbox');
            } elseif ($action == 'sent') {
                redirect('client/mailbox/index/trash/sent');
            } else {
                redirect('client/mailbox/index/trash/draft');
            }
            $type = "success";
            $message = lang('delete_msg');
        } else {
            $type = "error";
            $message = lang('select_message');
        }
        set_message($type, $message);
        if ($action == 'inbox') {
            redirect('client/mailbox/index/inbox');
        } elseif ($action == 'sent') {
            redirect('client/mailbox/index/sent');
        } else {
            redirect('client/mailbox/index/draft');
        }
    }

    public function send_mail() {

        $discard = $this->input->post('discard', TRUE);

        if (!empty($discard)) {
            redirect('client/mailbox/index/inbox');
        }
        $all_email = $this->input->post('to', TRUE);

        // get all email address
        foreach ($all_email as $v_email) {
            $data = $this->mailbox_model->array_from_post(array('subject', 'message_body'));
            if (!empty($_FILES['attach_file']['name'])) {
                $old_path = $this->input->post('attach_file_path');
                if ($old_path) {
                    unlink($old_path);
                }
                $val = $this->mailbox_model->uploadAllType('attach_file');
                $val == TRUE || redirect('client/mailbox/compose');
                // save into send table
                $data['attach_filename'] = $val['fileName'];
                $data['attach_file'] = $val['path'];
                $data['attach_file_path'] = $val['fullPath'];
                // save into inbox table
                $idata['attach_filename'] = $val['fileName'];
                $idata['attach_file'] = $val['path'];
                $idata['attach_file_path'] = $val['fullPath'];
            } else {
                $data['attach_filename'] = NULL;
                $data['attach_file'] = NULL;
                $data['attach_file_path'] = NULL;
                // save into inbox table
                $idata['attach_filename'] = NULL;
                $idata['attach_file'] = NULL;
                $idata['attach_file_path'] = NULL;
            }
            $data['to'] = $v_email;
            /*
              * Email Configuaration
              */
            $user_id = $this->session->userdata('user_id');
            $profile_info = $this->mailbox_model->check_by(array('user_id' => $user_id), 'tbl_account_details');
            $user_info = $this->mailbox_model->check_by(array('user_id' => $user_id), 'tbl_users');
            $mailbox = array('email' => $user_info->email, 'name' => $profile_info->fullname);

            // get company name
            $name = $profile_info->fullname;
            $info = $data['subject'];
            // set from email
            $from = array($name, $info);
            // set sender email
            $to = $v_email;
            //set subject
            $subject = $data['subject'];
            $data['user_id'] = $user_id;
            $data['message_time'] = date('Y-m-d H:i:s');
            $draf = $this->input->post('draf', TRUE);
            if (!empty($draf)) {
                $data['to'] = serialize($all_email);
                // save into send
                $this->mailbox_model->_table_name = 'tbl_draft';
                $this->mailbox_model->_primary_key = 'draft_id';
                $this->mailbox_model->save($data);
                redirect('client/mailbox/index/inbox');
            } else {
                // save into send
                $this->mailbox_model->_table_name = 'tbl_sent';
                $this->mailbox_model->_primary_key = 'sent_id';
                $send_id = $this->mailbox_model->save($data);
                // get mail info by send id to send
                $this->mailbox_model->_order_by = 'sent_id';
                $data['read_mail'] = $this->mailbox_model->get_by(array('sent_id' => $send_id), true);
                // set view page
                $message = $this->load->view('client/mailbox/read_mail', $data, TRUE);

                $params['subject'] = $subject;
                $params['message'] = $message;
                $params['resourceed_file'] = '';
                $params['recipient'] = $data['to'];
                $send_email = $this->mailbox_model->send_email($params, $mailbox);

                // save into inbox table procees
                $idata['to'] = $data['to'];
                $idata['from'] = $user_info->email;
                $idata['user_id'] = $user_id;
                $idata['subject'] = $data['subject'];
                $idata['message_body'] = $data['message_body'];
                $idata['message_time'] = date('Y-m-d H:i:s');
                // save into inbox
                $this->mailbox_model->_table_name = 'tbl_inbox';
                $this->mailbox_model->_primary_key = 'inbox_id';
                $this->mailbox_model->save($idata);
            }
            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'mailbox',
                'module_field_id' => $user_id,
                'activity' => lang('activity_msg_sent'),
                'icon' => 'fa-circle-o',
                'value1' => $v_email
            );
            $this->mailbox_model->_table_name = 'tbl_activities';
            $this->mailbox_model->_primary_key = 'activities_id';
            $this->mailbox_model->save($activity);
        }
        if ($send_email) {
            $type = "success";
            $message = lang('msg_sent');
            set_message($type, $message);
            redirect('client/mailbox/index/sent');
        } else {
            show_error($this->email->print_debugger());
        }
    }

    public function restore($action, $id) {
        $value = array('deleted' => 'No');
        if ($action == 'inbox') {
            $this->mailbox_model->set_action(array('inbox_id' => $id), $value, 'tbl_inbox');
        } elseif ($action == 'sent') {
            $this->mailbox_model->set_action(array('sent_id' => $id), $value, 'tbl_sent');
        } else {
            $this->mailbox_model->set_action(array('draft_id' => $id), $value, 'tbl_draft');
        }
        if ($action == 'inbox') {
            redirect('client/mailbox/index/inbox');
        } elseif ($action == 'sent') {
            redirect('client/mailbox/index/sent');
        } else {
            redirect('client/mailbox/index/draft');
        }
    }

}
