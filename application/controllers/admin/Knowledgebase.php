<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Knowledgebase extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('kb_model');

    }

    public function index()
    {
        $data['title'] = lang('knowledgebase');
        $data['all_kb_category'] = get_result('tbl_kb_category', array('type' => 'kb', 'status' => 1));
        $data['subview'] = $this->load->view('admin/kb/kb_list', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function categories()
    {
        $data['title'] = lang('categories');
        $data['all_kb_category_info'] = get_result('tbl_kb_category', array('type' => 'kb'));
        $data['subview'] = $this->load->view('admin/kb/categories', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function new_categories($id = null)
    {
        $data['title'] = lang('new') . ' ' . lang('new_categories');
        if (!empty($id) && is_numeric($id)) {
            $edited = can_action('142', 'edited');
            if (!empty($edited)) {
                $data['category_info'] = $this->kb_model->check_by(array('kb_category_id' => $id), 'tbl_kb_category');
            }
        } elseif (!empty($id) && !is_numeric($id)) {
            $data['inline'] = $id;
        }
        $data['subview'] = $this->load->view('admin/kb/new_categories', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data); //page load
    }

    public function saved_categories($id = null)
    {

        $created = can_action('142', 'created');
        $edited = can_action('142', 'edited');
        if (!empty($created) && !empty($edited)) {
            $this->kb_model->_table_name = 'tbl_kb_category';
            $this->kb_model->_primary_key = 'kb_category_id';
            $data = $this->kb_model->array_from_post(array('category', 'description', 'type', 'sort', 'status'));

            if (empty($data['status'])) {
                $data['status'] = 1;
            }
            // update root category
            $where = array('category' => $data['category']);
            // duplicate value check in DB
            if (!empty($id) && is_numeric($id)) { // if id exist in db update data
                $kb_category_id = array('kb_category_id !=' => $id);
            } else { // if id is not exist then set id as null
                $kb_category_id = null;
            }
            // check whether this input data already exist or not
            $check_category = $this->kb_model->check_update('tbl_kb_category', $where, $kb_category_id);
            if (!empty($check_category)) { // if input data already exist show error alert
                // massage for user
                $type = 'error';
                $msg = "<strong style='color:#000' >" . $data['category'] . "</strong>" . lang('already_exist');
            } else { // save and update query

                if (!empty($id) && !is_numeric($id)) { // if id exist in db update data
                    $return_id = $this->kb_model->save($data);
                } else {
                    $return_id = $this->kb_model->save($data, $id);
                }
                if (!empty($id) && is_numeric($id)) {
                    $return_id = $id;
                    $action = 'activity_update_kb_category';
                    $msg = lang('update_kb_category');
                } else {
                    $return_id = $return_id;
                    $action = 'activity_save_kb_category';
                    $msg = lang('save_kb_category');
                }

                $activity = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'knowledgebase',
                    'module_field_id' => $return_id,
                    'activity' => $action,
                    'icon' => 'fa-circle-o',
                    'link' => '#',
                    'value1' => $data['category']
                );
                $this->kb_model->_table_name = 'tbl_activities';
                $this->kb_model->_primary_key = 'activities_id';
                $this->kb_model->save($activity);
                // messages for user
                $type = "success";
            }
            $message = $msg;
            set_message($type, $message);
        }

        if (!empty($id) && !is_numeric($id)) {
            if (!empty($return_id)) {
                $result = array(
                    'id' => $return_id,
                    'category' => $data['category'],
                    'status' => $type,
                    'message' => $msg,
                );
            } else {
                $result = array(
                    'status' => $type,
                    'message' => $msg,
                );
            }
            echo json_encode($result);
            exit();
        } else {
            redirect('admin/knowledgebase/categories');
        }
    }

    public function change_status($flag, $id)
    {
        $edited = can_action('142', 'edited');
        if (!empty($edited)) {
            // if flag == 1 it is active user else deactive user
            if ($flag == 1) {
                $msg = lang('active');
            } else {
                $msg = lang('inactive');
            }
            $where = array('kb_category_id' => $id);
            $action = array('status' => $flag);
            $this->kb_model->set_action($where, $action, 'tbl_kb_category');

            $type = "success";
            $message = lang('categories') . ' ' . $msg . " Successfully!";
        } else {
            $type = 'error';
            $message = lang('there_in_no_value');
        }
        echo json_encode(array("status" => $type, "message" => $message));
        exit();
    }

    public function delete_categories($id)
    {
        $deleted = can_action('142', 'deleted');
        $check_category_info = $this->kb_model->check_by(array('kb_category_id' => $id), 'tbl_knowledgebase');
        if (empty($check_category_info)) {
            $type = "error";
            $message = lang('already_exist');
        } else {
            if (!empty($deleted)) {
                $type = "success";
                $message = lang('delete_kb_category');
                $action = 'activity_delete_kb_category';
                $category_info = $this->kb_model->check_by(array('kb_category_id' => $id), 'tbl_kb_category');
                $activity = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'knowledgebase',
                    'module_field_id' => $id,
                    'activity' => $action,
                    'icon' => 'fa-circle-o',
                    'value1' => $category_info->category
                );
                $this->kb_model->_table_name = 'tbl_activities';
                $this->kb_model->_primary_key = 'activities_id';
                $this->kb_model->save($activity);

                $this->kb_model->_table_name = 'tbl_kb_category';
                $this->kb_model->_primary_key = 'kb_category_id';
                $this->kb_model->delete($id);
            }
        }

        if (!empty($type)) {
            set_message($type, $message);
        }
        redirect('admin/knowledgebase/categories');
    }

    public function articles()
    {
        $data['title'] = lang('articles');
        $data['all_kb_info'] = $this->kb_model->get_kb_info();
        $data['subview'] = $this->load->view('admin/kb/articles', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function new_articles($id = null)
    {
        $data['title'] = lang('new') . ' ' . lang('articles');
        if (!empty($id)) {
            $edited = can_action('143', 'edited');
            if (!empty($edited)) {
                $data['articles_info'] = $this->kb_model->check_by(array('kb_id' => $id), 'tbl_knowledgebase');
            }
        }
        $data['dropzone'] = true;
        $data['subview'] = $this->load->view('admin/kb/new_articles', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function saved_articles($id = null)
    {

        $created = can_action('143', 'created');
        $edited = can_action('143', 'edited');
        if (!empty($created) && !empty($edited)) {
            $this->kb_model->_table_name = 'tbl_knowledgebase';
            $this->kb_model->_primary_key = 'kb_id';
            $data = $this->kb_model->array_from_post(array('title', 'slug', 'kb_category_id', 'description', 'for_all', 'status'));
            if (empty($data['status'])) {
                $data['status'] = 1;
            }
            if (empty($data['for_all'])) {
                $data['for_all'] = 'No';
            }

            $data['slug'] = slug_it($data['slug']);

            // update root category
            $where = array('slug' => $data['slug']);
            // duplicate value check in DB
            if (!empty($id)) { // if id exist in db update data
                $kb_id = array('kb_id !=' => $id);
            } else { // if id is not exist then set id as null
                $kb_id = null;
            }
            // check whether this input data already exist or not
            $check_kb = $this->kb_model->check_update('tbl_knowledgebase', $where, $kb_id);
            if (!empty($check_kb)) { // if input data already exist show error alert
                // massage for user
                $type = 'error';
                $msg = "<strong style='color:#000'>" . $data['slug'] . '</strong>  ' . lang('already_exist');
            } else { // save and update query

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

                        array_push($upload_file, $old);
                    }
                }
                if (!empty($upload_file)) {
                    $data['attachments'] = json_encode($upload_file);
                } else {
                    $data['attachments'] = '';
                }
                if (empty($id)) {
                    $data['created_by'] = $this->session->userdata('user_id');
                }

                $return_id = $this->kb_model->save($data, $id);
                if (!empty($id)) {
                    $id = $id;
                    $action = 'activity_update_kb';
                    $msg = lang('update_kb');
                } else {
                    $id = $return_id;
                    $action = 'activity_save_kb';
                    $msg = lang('save_kb');
                }

                $activity = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'knowledgebase',
                    'module_field_id' => $id,
                    'activity' => $action,
                    'icon' => 'fa-circle-o',
                    'link' => 'admin/knowledgebase/articles_details/' . $id,
                    'value1' => $data['title']
                );
                $this->kb_model->_table_name = 'tbl_activities';
                $this->kb_model->_primary_key = 'activities_id';
                $this->kb_model->save($activity);
                // messages for user
                $type = "success";
            }
            $message = $msg;
            set_message($type, $message);
        }

        redirect('admin/knowledgebase/new_articles/' . $id);
    }

    public function delete_articles($id, $row = null)
    {
        $deleted = can_action('143', 'deleted');

        $type = "success";
        $message = lang('delete_kb');
        if (!empty($deleted)) {
            $action = 'activity_delete_kb';
            $kb_info = $this->kb_model->check_by(array('kb_id' => $id), 'tbl_knowledgebase');
            if (!empty($kb_info)) {
                $activity = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'knowledgebase',
                    'module_field_id' => $id,
                    'activity' => $action,
                    'icon' => 'fa-circle-o',
                    'value1' => $kb_info->title
                );
                $this->kb_model->_table_name = 'tbl_activities';
                $this->kb_model->_primary_key = 'activities_id';
                $this->kb_model->save($activity);

                $this->kb_model->_table_name = 'tbl_knowledgebase';
                $this->kb_model->_primary_key = 'kb_id';
                $this->kb_model->delete($id);
                echo json_encode(array("status" => $type, 'message' => $message));
                exit();
            } else {
                echo json_encode(array("status" => 'error', 'message' => lang('there_in_no_value')));
                exit();
            }
        } else {
            redirect('admin/knowledgebase/categories');
        }
        if (!empty($row)) {
            set_message($type, $message);
            redirect('admin/knowledgebase/categories');
        }
    }


    public function change_kb_status($flag, $id)
    {
        $edited = can_action('143', 'edited');
        if (!empty($edited)) {
            // if flag == 1 it is active user else deactive user
            if ($flag == 1) {
                $msg = lang('active');
            } else {
                $msg = lang('inactive');
            }
            $where = array('kb_id' => $id);
            $action = array('status' => $flag);
            $this->kb_model->set_action($where, $action, 'tbl_knowledgebase');

            $type = "success";
            $message = lang('knowledgebase') . ' ' . $msg . " Successfully!";
        } else {
            $type = 'error';
            $message = lang('there_in_no_value');
        }
        echo json_encode(array("status" => $type, "message" => $message));
        exit();
    }

    public function getSlug()
    {
        $title = $this->input->get('title', TRUE);
        echo slug_it($title);
        exit();
    }

    public function details($type, $id)
    {
        $data['title'] = lang('articles') . ' ' . lang('details');
        $data['all_kb_category'] = get_result('tbl_kb_category', array('type' => 'kb', 'status' => 1));
        if ($type == 'articles') {
            $this->kb_model->increase_total_view($id);
            $data['articles_info'] = $this->kb_model->get_kb_info('articles', $id);
        } else {
            $data['articles_by_category'] = $this->kb_model->get_kb_info('category', $id);
        }
        $data['subview'] = $this->load->view('admin/kb/articles_details', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    function get_article_suggestion()
    {
        $search = $this->input->post("search", true);
        if ($search) {
            $result = $this->kb_model->get_suggestions($search);
            echo json_encode($result);
            exit();
        }
    }

    public function download($id, $fileName)
    {
        if (!empty($fileName)) {
            $this->load->helper('download');
            if ($id) {
                $down_data = file_get_contents('uploads/' . $fileName); // Read the file's contents
                force_download($fileName, $down_data);
            } else {
                $type = "error";
                $message = 'Operation Fieled !';
                set_message($type, $message);
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }
}
