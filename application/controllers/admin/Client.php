<?php

/**
 * Description of client
 *
 * @author NaYeM
 */
class Client extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('client_model');
        $this->load->model('invoice_model');
        $this->load->model('estimates_model');
        $this->load->model('contact_model');
    }
    public function manage_client($id = NULL)
    {
        
        if (!empty($id)) {
            if (is_numeric($id)) {
                $data['active'] = 2;
                // get all Client info by client id
                $this->client_model->_table_name = "tbl_contact"; //table name
                $this->client_model->_order_by = "id";
                $data['client_info'] = $this->client_model->get_by(array('id' => $id, 'contact_type'=>3), TRUE);
            } else {
                $data['active'] = 1;
                // $data['active'] = 2;
            }
        } else {
            $data['active'] = 1;
            // $data['active'] = 2;
        }
        $data['title'] = lang('manage_client'); //Page title
        $data['page'] = lang('contact');
        // get all country
        $this->client_model->_table_name = "tbl_countries"; //table name
        $this->client_model->_order_by = "id";
        $data['countries'] = $this->client_model->get();
        // get all currencies
        $this->client_model->_table_name = 'tbl_currencies';
        $this->client_model->_order_by = 'name';
        $data['currencies'] = $this->client_model->get();
        // get all language
        $data['languages'] = $this->db->where('active', 1)->order_by('name', 'ASC')->get('tbl_languages')->result();
        $data['whitebrand'] = $this->db->get('tbl_whitebrand')->result();
        $data['all_staffes'] = $this->db->where('role_id',3)->get('tbl_users')->result();
        $data['prices'] = $this->db->get('tbl_price')->result();
        $data['industries'] = $this->db->get('tbl_industry')->result();
        $id = $this->uri->segment(5);
        if (!empty($id)) {
            $search_by = $this->uri->segment(4);
            if ($search_by == 'group') {
                $where = array('customer_group_id' => $id);
                $data['all_client_info'] = $this->db->where($where)->get('tbl_contact')->result();
            }
        } else {
            $where = array('contact_type'=>3);
            $data['all_client_info'] = $this->db->where($where)->get('tbl_contact')->result();
        }
        $data['subview'] = $this->load->view('admin/client/manage_client', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }
    public function change_client_status($id = null)
    {
        $data['active'] = $this->input->post('active', true);
        $this->client_model->_table_name = 'tbl_client';
        $this->client_model->_primary_key = "client_id";
        $this->client_model->save($data, $id);
        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'client',
            'module_field_id' => $id,
            'activity' => 'activity_change_status',
            'icon' => 'fa-ticket',
            'value1' => $data['active'],
        );
        // Update into tbl_project
        $this->client_model->_table_name = "tbl_activities"; //table name
        $this->client_model->_primary_key = "activities_id";
        $this->client_model->save($activities);
        $type = "success";
        $message = lang('update_client_status');
        echo json_encode(array("status" => $type, "message" => $message));
        exit();
    }
    public function searchByGroup($id = NULL)
    {

        $data['active'] = 1;

        $data['title'] = lang('manage_client'); //Page title
        $data['page'] = lang('client');

        // get all country
        $this->client_model->_table_name = "tbl_countries"; //table name
        $this->client_model->_order_by = "id";
        $data['countries'] = $this->client_model->get();

        // get all currencies
        $this->client_model->_table_name = 'tbl_currencies';
        $this->client_model->_order_by = 'name';
        $data['currencies'] = $this->client_model->get();
        // get all language
        $data['languages'] = $this->db->where('active', 1)->order_by('name', 'ASC')->get('tbl_languages')->result();

        $data['all_client_info'] = $this->db->where('customer_group_id', $id)->get('tbl_client')->result();

        $data['subview'] = $this->load->view('admin/client/manage_client', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function import()
    {
        $data['title'] = lang('import') . ' ' . lang('client');
        // get all country
        $this->client_model->_table_name = "tbl_countries"; //table name
        $this->client_model->_order_by = "id";
        $data['countries'] = $this->client_model->get();

        // get all currencies
        $this->client_model->_table_name = 'tbl_currencies';
        $this->client_model->_order_by = 'name';
        $data['currencies'] = $this->client_model->get();
        // get all language
        $data['languages'] = $this->db->where('active', 1)->order_by('name', 'ASC')->get('tbl_languages')->result();

        $data['subview'] = $this->load->view('admin/client/import_client', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function save_imported()
    {
        //load the excel library
        $this->load->library('excel');
        ob_start();
        $file = $_FILES["upload_file"]["tmp_name"];
        if (!empty($file)) {
            $valid = false;
            $types = array('Excel2007', 'Excel5');
            foreach ($types as $type) {
                $reader = PHPExcel_IOFactory::createReader($type);
                if ($reader->canRead($file)) {
                    $valid = true;
                }
            }
            if (!empty($valid)) {
                try {
                    $objPHPExcel = PHPExcel_IOFactory::load($file);
                } catch (Exception $e) {
                    die("Error loading file :" . $e->getMessage());
                }
                //All data from excel
                $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

                for ($x = 2; $x <= count($sheetData); $x++) {
                    // **********************
                    // Save Into leads table
                    // **********************
                    $data = $this->client_model->array_from_post(array('customer_group_id', 'vat', 'language', 'currency', 'country'));

                    $data['name'] = trim($sheetData[$x]["A"]);
                    $data['email'] = trim($sheetData[$x]["B"]);
                    $data['short_note'] = trim($sheetData[$x]["C"]);
                    $data['phone'] = trim($sheetData[$x]["D"]);
                    $data['mobile'] = trim($sheetData[$x]["E"]);
                    $data['fax'] = trim($sheetData[$x]["F"]);
                    $data['city'] = trim($sheetData[$x]["G"]);
                    $data['zipcode'] = trim($sheetData[$x]["H"]);
                    $data['address'] = trim($sheetData[$x]["I"]);
                    $data['skype_id'] = trim($sheetData[$x]["J"]);
                    $data['twitter'] = trim($sheetData[$x]["K"]);
                    $data['facebook'] = trim($sheetData[$x]["L"]);
                    $data['linkedin'] = trim($sheetData[$x]["M"]);
                    $data['hosting_company'] = trim($sheetData[$x]["N"]);
                    $data['hostname'] = trim($sheetData[$x]["O"]);
                    $data['username'] = trim($sheetData[$x]["P"]);
                    $data['password'] = trim($sheetData[$x]["Q"]);
                    $data['port'] = trim($sheetData[$x]["R"]);

                    $this->client_model->_table_name = 'tbl_client';
                    $this->client_model->_primary_key = "client_id";
                    $id = $this->client_model->save($data);

                    $action = ('activity_update_company');
                    $activities = array(
                        'user' => $this->session->userdata('user_id'),
                        'module' => 'client',
                        'module_field_id' => $id,
                        'activity' => $action,
                        'icon' => 'fa-user',
                        'value1' => $data['name']
                    );
                    $this->client_model->_table_name = 'tbl_activities';
                    $this->client_model->_primary_key = "activities_id";
                    $this->client_model->save($activities);
                }
            } else {
                $type = 'error';
                $message = "Sorry your uploaded file type not allowed ! please upload XLS/CSV File ";
            }
        } else {
            $type = 'error';
            $message = "You did not Select File! please upload XLS/CSV File ";
        }
        set_message($type, $message);
        redirect('admin/client/manage_client');

    }


    public function save_client($id = NULL)
    {

        $data = $this->contact_model->array_from_post(array('contact_type','first_name','last_name','nick_name','email','phone','industries','latitude','longitude','interest_level','white_brand','sales_rep','referral_note','contact_date','web','social_media','networking','cold_call','sales_rep_check','request_proposal','homeaddress','birthday','spouse','anniversary','likes_interest','dislikes','biography','client_code','company_color','seo_keyword','account_number','monthly_budget','schedule_monday','schedule_tuesday','schedule_wednesday','schedule_thursday','schedule_friday','schedule_saturday','schedule_sunday','target_areas','call_tracking_phone','nagative_keyword','adwords_notes','articles_per_month','content_notes','strategy','access_name','access_login_url'));
        // $data = $this->client_model->array_from_post(array('contact', 'email', 'short_note', 'website', 'phone', 'mobile', 'fax', 'address', 'city', 'zipcode', 'currency'
            // 'skype_id', 'linkedin', 'facebook', 'twitter', 'language', 'country', 'vat', 'hosting_company', 'hostname', 'port', 'username', 'latitude', 'longitude', 'customer_group_id'));
        $password = $this->input->post('password', true);
        if (!empty($password)) {
            // $data['password'] = encrypt($password);
            $data['password'] = $password;
        }

        if (!empty($_FILES['company_logo']['name'])) {
            $val = $this->client_model->uploadImage('company_logo');
            $val == TRUE || redirect('admin/client/manage_client');
            $data['company_logo'] = $val['path'];
        }
        $action = '';
        if(!empty($id)){
            $action = 'update';
        }
        $this->contact_model->_table_name = 'tbl_contact';
        $this->contact_model->_primary_key = "id";
        $return_id = $this->contact_model->save($data, $id);

        $personal_social_data = '';
        $personal_social_data = $this->contact_model->array_from_post(array('personal_facebook','personal_instagram','personal_linkedin','personal_pinterest','personal_youtube','personal_other'));
        
        $personal_social_data['contact_id'] = $return_id;
        $this->contact_model->_table_name = 'tbl_personal_social';
        $this->contact_model->_primary_key = 'contact_id';
        $this->contact_model->save($personal_social_data, $id);


        $website_url = $this->input->post('website_url');
        $website_url_label = $this->input->post('website_url_label');

        if(!empty($website_url)){
            $this->contact_model->insert_website_url($return_id,$website_url,$website_url_label,$action);
        }



        $addition_phone = $this->input->post('additional_phone');
        $addition_phone_label = $this->input->post('additional_phone_label');
        $addition_email = $this->input->post('additional_email');
        $addition_email_label = $this->input->post('additional_email_label');

        if(!empty($addition_email)){
            $this->contact_model->insert_additional_email($return_id,$addition_email,$addition_email_label,$action);
        }
        if(!empty($addition_phone)){
            $this->contact_model->insert_additional_phone($return_id,$addition_phone, $addition_phone_label,$action);
        }

// sales
        if(!empty($data['web']))
        {
            $web_data = $this->contact_model->array_from_post(array('w_wbsite','w_google_organic','w_bing_organic','w_google_ads','w_bing_ads','w_other'));
            $web_data['contact_id'] = $return_id;
            $this->contact_model->_table_name = 'tbl_contact_web';
            $this->contact_model->_primary_key = "contact_id";
            $this->contact_model->save($web_data, $id);
        }
       
        if(!empty($data['social_media']))
        {
            $social_data = $this->contact_model->array_from_post(array('s_facebook_or','s_facebook_ad','social_instagram_or','social_instagram_ad','social_pinterest_or','social_pinterest_ad','social_youtube_or','social_youtube_ad','social_linkedin_or','social_linkedin_ad'));
            $social_data['contact_id'] = $return_id;
            $this->contact_model->_table_name = 'tbl_contact_social_media';
            $this->contact_model->_primary_key = "contact_id";
            $this->contact_model->save($social_data, $id);
        }
        if(!empty($data['networking']))
        {
            $networking_data = $this->contact_model->array_from_post(array('n_event_group','n_referred_by'));
            $networking_data['contact_id'] = $return_id;
            $this->contact_model->_table_name = 'tbl_contact_networking';
            $this->contact_model->_primary_key = "contact_id";
            $this->contact_model->save($networking_data, $id);
        }
        if(!empty($data['cold_call']))
        {
            $cold_call_data = $this->contact_model->array_from_post(array('c_call_type','c_referred_by'));
            $cold_call_data['contact_id'] = $return_id;
            $this->contact_model->_table_name = 'tbl_contact_cold_call';
            $this->contact_model->_primary_key = "contact_id";
            $this->contact_model->save($cold_call_data, $id);
        }
        if(!empty($data['request_proposal']))
        {
            $request_proposal_data = $this->contact_model->array_from_post(array('branding','website_analysis','website_proposal','seo','sea','smm','sma','content_marketing','marketing_analysis','recommendations','why_us_page','price_category','due_nlt'));
            $request_proposal_data['contact_id'] = $return_id;
            $this->contact_model->_table_name = 'tbl_contact_request_proposal';
            $this->contact_model->_primary_key = "contact_id";
            $this->contact_model->save($request_proposal_data, $id);
        }
        if(!empty($data['sales_rep']))
        {
            $sales_rep_data = $this->contact_model->array_from_post(array('sales_rep_name'));
            $sales_rep_data['contact_id'] = $return_id;
            $this->contact_model->_table_name = 'tbl_contact_sales_rep';
            $this->contact_model->_primary_key = "contact_id";
            $this->contact_model->save($sales_rep_data, $id);
        }
        //    relationship
       
        

        $child = $this->input->post('family_child');
        $child_label = $this->input->post('family_child_label');
        $pet = $this->input->post('family_pet');
        $pet_label = $this->input->post('family_pet_label');

        if(!empty($child)){
            $this->contact_model->insert_family_child($return_id,$child,$child_label,$action);
        }
        if(!empty($pet)){
            $this->contact_model->insert_family_pet($return_id,$pet,$pet_label,$action);
        }


        $relationship_name = $this->input->post('relationship_name');
        $relationship_profile_link = $this->input->post('relationship_profile_link');
        $relationship_notes = $this->input->post('relationship_notes');
        if(!empty($relationship_name))
        {
            $this->contact_model->insert_relationships_develop($return_id,$relationship_name,$relationship_profile_link,$relationship_notes,$action);
        }
        // Access info
        $access_username = $this->input->post('access_username');
        $access_email_address = $this->input->post('access_email_address');
        $access_password = $this->input->post('access_password');
        $access_view = $this->input->post('access_view');

        if(!empty($access_username))
        {
            $this->contact_model->insert_access_info($return_id,$access_username,$access_email_address,$access_password,$access_view,$action);
        }
        if (!empty($id)) {
            $id = $id;
            $action = ('activity_added_new_company');
        } else {
            $id = $return_id;
            $action = ('activity_update_company');
        }
        save_custom_field(12, $id);

        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'client',
            'module_field_id' => $id,
            'activity' => $action,
            'icon' => 'fa-user',
            'value1' => $data['name']
        );
        $this->contact_model->_table_name = 'tbl_activities';
        $this->contact_model->_primary_key = "activities_id";
        $this->contact_model->save($activities);
        // messages for user
        $type = "success";
        $message = lang('client_updated');
        set_message($type, $message);
        $save_and_create_contact = $this->input->post('save_and_create_contact', true);
        if (!empty($save_and_create_contact)) {
            redirect('admin/client/client_details/' . $id . '/add_contacts');
        } else {
            redirect('admin/client/manage_client');
        }


    }
    public function contact_details($id = null)
    {
        if (!empty($id)) {
            if (is_numeric($id)) {
                $data['active'] = 2;
                // get all Client info by client id
                $this->client_model->_table_name = "tbl_contact"; //table name
                $this->client_model->_order_by = "id";
                $data['client_info'] = $this->client_model->get_by(array('id' => $id), TRUE);
            } else {
                // $data['active'] = 1;
                $data['active'] = 2;
            }
        } else {
            // $data['active'] = 1;
            $data['active'] = 2;
        }
        $data['title'] = lang('manage_client'); //Page title
        $data['page'] = lang('contact');
        // get all country
        $this->client_model->_table_name = "tbl_countries"; //table name
        $this->client_model->_order_by = "id";
        $data['countries'] = $this->client_model->get();
        // get all currencies
        $this->client_model->_table_name = 'tbl_currencies';
        $this->client_model->_order_by = 'name';
        $data['currencies'] = $this->client_model->get();
        // get all language

        $data['languages'] = $this->db->where('active', 1)->order_by('name', 'ASC')->get('tbl_languages')->result();
        $data['whitebrand'] = $this->db->get('tbl_whitebrand')->result();
        $data['all_staffes'] = $this->db->where('role_id',3)->get('tbl_users')->result();
        $data['prices'] = $this->db->get('tbl_price')->result();
        $data['industries'] = $this->db->get('tbl_industry')->result();

        $id = $this->uri->segment(5);
        if (!empty($id)) {
            $search_by = $this->uri->segment(4);
            if ($search_by == 'group') {
                $where = array('customer_group_id' => $id);
                $data['all_client_info'] = $this->db->where($where)->get('tbl_contact')->result();
            }
        } else {
            $data['all_client_info'] = $this->db->get('tbl_contact')->result();
        }
        $data['subview'] = $this->load->view('admin/client/client_details', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }
    public function see_password($type = null)
    {
        $data['title'] = lang('see_password');
        if (!empty($type) && !is_numeric($type)) {
            $ex = explode('_', $type);
            if ($ex[0] == 'c') {
                $data['password'] = get_row('tbl_client', array('client_id' => $ex[1]), 'password');
            } elseif ($ex[0] == 'smtp') {
                $data['password'] = config_item('smtp_pass');
            } elseif ($ex[0] == 'emin') {
                $data['password'] = config_item('config_password');
            }
        } else {
            $data['password'] = null;
        }
        $data['subview'] = $this->load->view('admin/settings/see_password', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function customer_group()
    {
        $data['title'] = lang('customer_group');
        $data['subview'] = $this->load->view('admin/client/customer_group', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function update_customer_group($id = null)
    {
        $this->client_model->_table_name = 'tbl_customer_group';
        $this->client_model->_primary_key = 'customer_group_id';

        $cate_data['customer_group'] = $this->input->post('customer_group', TRUE);
        $cate_data['description'] = $this->input->post('description', TRUE);
        $cate_data['type'] = 'client';

        // update root category
        $where = array('type' => 'client', 'customer_group' => $cate_data['customer_group']);
        // duplicate value check in DB
        if (!empty($id)) { // if id exist in db update data
            $customer_group_id = array('customer_group_id !=' => $id);
        } else { // if id is not exist then set id as null
            $customer_group_id = null;
        }
        // check whether this input data already exist or not
        $check_category = $this->client_model->check_update('tbl_customer_group', $where, $customer_group_id);
        if (!empty($check_category)) { // if input data already exist show error alert
            // massage for user
            $type = 'error';
            $msg = "<strong style='color:#000'>" . $cate_data['customer_group'] . '</strong>  ' . lang('already_exist');
        } else { // save and update query
            $id = $this->client_model->save($cate_data, $id);

            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'settings',
                'module_field_id' => $id,
                'activity' => ('customer_group_added'),
                'value1' => $cate_data['customer_group']
            );
            $this->client_model->_table_name = 'tbl_activities';
            $this->client_model->_primary_key = 'activities_id';
            $this->client_model->save($activity);

            // messages for user
            $type = "success";
            $msg = lang('customer_group_added');
        }
        if (!empty($id)) {
            $result = array(
                'id' => $id,
                'group' => $cate_data['customer_group'],
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
    }


    public function client_details($id, $action = null)
    {
        if ($action == 'add_contacts') {
            // get all language
            $data['languages'] = $this->db->where('active', 1)->order_by('name', 'ASC')->get('tbl_languages')->result();
            // get all location
            $this->client_model->_table_name = 'tbl_locales';
            $this->client_model->_order_by = 'name';
            $data['locales'] = $this->client_model->get();
            $data['company'] = $id;
            $user_id = $this->uri->segment(6);
            if (!empty($user_id)) {
                // get all user_info by user id
                $data['account_details'] = $this->client_model->check_by(array('user_id' => $user_id), 'tbl_account_details');

                $data['user_info'] = $this->client_model->check_by(array('user_id' => $user_id), 'tbl_users');
            }

        }
        $data['title'] = "View Client Details"; //Page title
        // get all client details
        $this->client_model->_table_name = "tbl_client"; //table name
        $this->client_model->_order_by = "client_id";
        $data['client_details'] = $this->client_model->get_by(array('client_id' => $id), TRUE);

        // get all invoice by client id
        $this->client_model->_table_name = "tbl_invoices"; //table name
        $this->client_model->_order_by = "client_id";
        $data['client_invoices'] = $this->client_model->get_by(array('client_id' => $id), FALSE);

        // get all estimates by client id
        $this->client_model->_table_name = "tbl_estimates"; //table name
        $this->client_model->_order_by = "client_id";
        $data['client_estimates'] = $this->client_model->get_by(array('client_id' => $id), FALSE);

        // get client contatc by client id
        $data['client_contacts'] = $this->client_model->get_client_contacts($id);

        $data['subview'] = $this->load->view('admin/client/client_details', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function elfinder_init($client_id)
    {
        $this->load->helper('path');
        $_allowed_files = explode('|', config_item('allowed_files'));
        $config_allowed_files = array();
        if (is_array($_allowed_files)) {
            foreach ($_allowed_files as $v_extension) {
                array_push($config_allowed_files, '.' . $v_extension);
            }
        }
        $allowed_files = array();
        if (is_array($config_allowed_files)) {
            foreach ($config_allowed_files as $extension) {
                $_mime = get_mime_by_extension($extension);
                if ($_mime == 'application/x-zip') {
                    array_push($allowed_files, 'application/zip');
                }
                if ($extension == '.exe') {
                    array_push($allowed_files, 'application/x-executable');
                    array_push($allowed_files, 'application/x-msdownload');
                    array_push($allowed_files, 'application/x-ms-dos-executable');
                }
                array_push($allowed_files, $_mime);
            }
        }
        $client_info = $this->db->where('client_id', $client_id)->get('tbl_client')->row();
        $c_slug = slug_it($client_info->name);
        $path = set_realpath('filemanager/' . $c_slug);
        $root_options = array(
            'driver' => 'LocalFileSystem',
//            'path' => $path,
//            'URL' => site_url('-') . '/' . $c_slug . '/',
            'uploadMaxSize' => config_item('max_file_size') . 'M',
            'accessControl' => 'access',
            'uploadAllow' => $allowed_files,
            'uploadOrder' => array(
                'allow',
                'deny'
            ),
            'attributes' => array(
                array(
                    'pattern' => '/.tmb/',
                    'hidden' => true
                ),
                array(
                    'pattern' => '/.quarantine/',
                    'hidden' => true
                )
            )
        );
        $client_contacts = $this->client_model->get_client_contacts($client_id);
        if (!empty($client_contacts)) {
            foreach ($client_contacts as $contact) {
                $c_slug = slug_it($client_info->name);
                $path = set_realpath('filemanager/' . $c_slug);
                if (!is_dir($path)) {
                    mkdir($path);
                }
                $c_path = set_realpath('filemanager/' . $c_slug . '/' . $contact->media_path_slug);
                if (empty($contact->media_path_slug)) {
                    $this->db->where('user_id', $contact->user_id);
                    $slug = slug_it($contact->username);
                    $this->db->update('tbl_users', array(
                        'media_path_slug' => $slug
                    ));
                    $contact->media_path_slug = $slug;
                    $c_path = set_realpath('filemanager/' . $c_slug . '/' . $contact->media_path_slug);
                }
                if (!is_dir($c_path)) {
                    mkdir($c_path);
                }
                if (!file_exists($c_path . '/index.html')) {
                    fopen($c_path . '/index.html', 'w');
                }
                array_push($root_options['attributes'], array(
                    'pattern' => '/.(' . $contact->media_path_slug . '+)/', // Prevent deleting/renaming folder
                    'read' => true,
                    'write' => true,
                ));
                $root_options['path'] = $path;
                $root_options['URL'] = site_url('filemanager/' . $contact->media_path_slug) . '/';

                $opts = array(
                    'roots' => array(
                        $root_options
                    )
                );

                $this->load->library('elfinder_lib', $opts);
            }
        }
    }

    public function save_contact($id = NULL)
    {
        $data = $this->client_model->array_from_post(array('fullname', 'company', 'phone', 'mobile', 'skype', 'language', 'locale', 'direction'));
        if (!empty($id)) {
            $u_data['email'] = $this->input->post('email', TRUE);
            $u_data['last_ip'] = $this->input->ip_address();
            $this->client_model->_table_name = 'tbl_users';
            $this->client_model->_primary_key = 'user_id';
            $user_id = $this->client_model->save($u_data, $id);
            $data['user_id'] = $user_id;
            $acount_info = $this->client_model->check_by(array('user_id' => $id), 'tbl_account_details');

            $this->client_model->_table_name = 'tbl_account_details';
            $this->client_model->_primary_key = 'account_details_id';
            $return_id = $this->client_model->save($data, $acount_info->account_details_id);

            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'client',
                'module_field_id' => $id,
                'activity' => ('activity_update_contact'),
                'icon' => 'fa-user',
                'value1' => $data['fullname']
            );
            $this->client_model->_table_name = 'tbl_activities';
            $this->client_model->_primary_key = "activities_id";
            $this->client_model->save($activities);
        } else {
            $user_data = $this->client_model->array_from_post(array('email', 'username', 'password'));
            $u_data['last_ip'] = $this->input->ip_address();
            $check_email = $this->client_model->check_by(array('email' => $user_data['email']), 'tbl_users');
            $check_username = $this->client_model->check_by(array('username' => $user_data['username']), 'tbl_users');

            if ($user_data['password'] == $this->input->post('confirm_password', TRUE)) {
                $u_data['password'] = $this->hash($user_data['password']);

                if (!empty($check_username)) {
                    $message['error'][] = lang('this_username_already_exist');
                } else {
                    $u_data['username'] = $user_data['username'];
                }
                if (!empty($check_email)) {
                    $message['error'][] = lang('this_email_already_exist');
                } else {
                    $u_data['email'] = $user_data['email'];
                }
            } else {
                $message['error'][] = lang('password_does_not_macth');
            }

            if (!empty($u_data['password']) && !empty($u_data['username']) && !empty($u_data['email'])) {
                $u_data['role_id'] = $this->input->post('role_id', true);
                $u_data['activated'] = '1';

                $this->client_model->_table_name = 'tbl_users';
                $this->client_model->_primary_key = 'user_id';
                $user_id = $this->client_model->save($u_data, $id);

                $data['user_id'] = $user_id;

                $this->client_model->_table_name = 'tbl_account_details';
                $this->client_model->_primary_key = 'account_details_id';
                $return_id = $this->client_model->save($data, $id);
                // check primary contact
                $primary_contact = $this->client_model->check_by(array('client_id' => $data['company']), 'tbl_client');

                if ($primary_contact->primary_contact == 0) {
                    $c_data['primary_contact'] = $return_id;
                    $this->client_model->_table_name = 'tbl_client';
                    $this->client_model->_primary_key = 'client_id';
                    $this->client_model->save($c_data, $data['company']);
                }
                if ($this->input->post('send_email_password') == 'on') {
                    $this->send_confirmation_email($u_data, $user_data['password']); //send thank you email
                }
//                $send_email_password = $this->input->post('send_email_password', true);
//                if (!empty($send_email_password)) {
//
//                    $email_template = $this->client_model->check_by(array('email_group' => 'registration'), 'tbl_email_templates');
//                    $SITE_URL = str_replace("{SITE_URL}", base_url(), $email_template->template_body);
//                    $username = str_replace("{USERNAME}", $u_data['username'], $SITE_URL);
//                    $user_email = str_replace("{EMAIL}", $u_data['email'], $username);
//
//                    $user_password = str_replace("{PASSWORD}", $user_data['password'], $user_email);
//                    $message = str_replace("{SITE_NAME}", config_item('company_name'), $user_password);
//
//                    $params['recipient'] = $u_data['email'];
//                    $params['subject'] = '[ ' . config_item('company_name') . ' ]' . ' ' . $email_template->subject;
//                    $params['message'] = $message;
//                    $params['resourceed_file'] = '';
//
//                    $this->client_model->send_email($params);
//                }
                $activities = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'client',
                    'module_field_id' => $id,
                    'activity' => ('activity_added_new_contact'),
                    'icon' => 'fa-user',
                    'value1' => $data['fullname']
                );
                $this->client_model->_table_name = 'tbl_activities';
                $this->client_model->_primary_key = "activities_id";
                $this->client_model->save($activities);
            }
        }
        if (!empty($user_id)) {
            $this->client_model->_table_name = 'tbl_client_role'; //table name
            $this->client_model->delete_multiple(array('user_id' => $user_id));

            $all_client_menu = $this->db->get('tbl_client_menu')->result();

            foreach ($all_client_menu as $v_client_menu) {
                $client_role_data['menu_id'] = $this->input->post($v_client_menu->label, true);
                if (!empty($client_role_data['menu_id'])) {
                    $client_role_data['user_id'] = $user_id;
                    $this->client_model->_table_name = 'tbl_client_role';
                    $this->client_model->_primary_key = 'client_role_id';
                    $this->client_model->save($client_role_data);
                }
            }
        }
        // messages for user
        $message['success'] = lang('contact_information_successfully_update');
        if (!empty($message['error'])) {
            $this->session->set_userdata($message);
        } else {
            set_message('success', lang('contact_information_successfully_update'));
        }
        if (!empty($data['company'])) {
            redirect('admin/client/client_details/' . $data['company']);
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }

    }

    function send_confirmation_email($u_data, $password)
    {
        $email_template = $this->client_model->check_by(array('email_group' => 'registration'), 'tbl_email_templates');
        $SITE_URL = str_replace("{SITE_URL}", base_url(), $email_template->template_body);
        $username = str_replace("{USERNAME}", $u_data['username'], $SITE_URL);
        $user_email = str_replace("{EMAIL}", $u_data['email'], $username);

        $user_password = str_replace("{PASSWORD}", $password, $user_email);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $user_password);

        $params['recipient'] = $u_data['email'];
        $params['subject'] = '[ ' . config_item('company_name') . ' ]' . ' ' . $email_template->subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';

        $this->client_model->send_email($params);
    }

    public function make_primary($user_id, $client_id)
    {
        $user_info = $this->client_model->check_by(array('user_id' => $user_id), 'tbl_account_details');

        $this->db->set('primary_contact', $user_id);
        $this->db->where('client_id', $client_id)->update('tbl_client');
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'client',
            'module_field_id' => $client_id,
            'activity' => ('activity_primary_contact'),
            'icon' => 'fa-user',
            'value1' => $user_info->fullname
        );
        $this->client_model->_table_name = 'tbl_activities';
        $this->client_model->_primary_key = "activities_id";
        $this->client_model->save($activities);

        // messages for user
        $type = "success";
        $message = lang('primary_contact_set');
        set_message($type, $message);
        redirect('admin/client/client_details/' . $client_id);
    }

    public function delete_contacts($client_id, $id)
    {
        $sbtn = $this->input->post('submit', true);
        if (!empty($sbtn)) {
            // delete into user table by user id
            $this->client_model->_table_name = 'tbl_client';
            $this->client_model->_order_by = 'primary_contact';
            $primary_contact = $this->client_model->get_by(array('primary_contact' => $id), TRUE);
            if (!empty($primary_contact)) {
                // delete into user table by user id
                $this->client_model->_table_name = 'tbl_account_details';
                $this->client_model->_order_by = 'company';
                $client_info = $this->client_model->get_by(array('company' => $client_id), FALSE);
                $result = count($client_info);
                if ($result != '1') {
                    $data['primary_contact'] = $client_info[1]->account_details_id;
                } else {
                    $data['primary_contact'] = 0;
                }
                $this->client_model->_table_name = 'tbl_client';
                $this->client_model->_primary_key = 'primary_contact';
                $this->client_model->save($data, $client_id);
            }
            $user_info = $this->client_model->check_by(array('user_id' => $id), 'tbl_account_details');
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'client',
                'module_field_id' => $id,
                'activity' => ('activity_deleted_contact'),
                'icon' => 'fa-user',
                'value1' => $user_info->fullname
            );
            $this->client_model->_table_name = 'tbl_account_details';
            $this->client_model->delete_multiple(array('user_id' => $id));

            $cwhere = array('user_id' => $id);
            $this->client_model->_table_name = 'tbl_private_chat';
            $this->client_model->delete_multiple($cwhere);

            $this->client_model->_table_name = 'tbl_private_chat_users';
            $this->client_model->delete_multiple($cwhere);

            $this->client_model->_table_name = 'tbl_private_chat_messages';
            $this->client_model->delete_multiple($cwhere);

            $this->client_model->_table_name = 'tbl_activities';
            $this->client_model->delete_multiple(array('user' => $id));

            $this->client_model->_table_name = 'tbl_payments';
            $this->client_model->delete_multiple(array('paid_by' => $id));

            // delete all tbl_quotations by id
            $this->client_model->_table_name = 'tbl_quotations';
            $this->client_model->_order_by = 'user_id';
            $quotations_info = $this->client_model->get_by(array('user_id' => $id), FALSE);

            if (!empty($quotations_info)) {
                foreach ($quotations_info as $v_quotations) {
                    $this->client_model->_table_name = 'tbl_quotation_details';
                    $this->client_model->delete_multiple(array('quotations_id' => $v_quotations->quotations_id));
                }
            }
            $this->client_model->_table_name = 'tbl_quotations';
            $this->client_model->delete_multiple(array('user_id' => $id));

            $this->client_model->_table_name = 'tbl_quotationforms';
            $this->client_model->delete_multiple(array('quotations_created_by_id' => $id));
            $this->client_model->_table_name = 'tbl_users';
            $this->client_model->delete_multiple(array('user_id' => $id));

            $this->client_model->_table_name = 'tbl_user_role';
            $this->client_model->delete_multiple(array('designations_id' => $id));

            $this->client_model->_table_name = 'tbl_inbox';
            $this->client_model->delete_multiple(array('user_id' => $id));

            $this->client_model->_table_name = 'tbl_sent';
            $this->client_model->delete_multiple(array('user_id' => $id));

            $this->client_model->_table_name = 'tbl_draft';
            $this->client_model->delete_multiple(array('user_id' => $id));

            $this->client_model->_table_name = 'tbl_tickets';
            $this->client_model->delete_multiple(array('reporter' => $id));

            $this->client_model->_table_name = 'tbl_tickets_replies';
            $this->client_model->delete_multiple(array('replierid' => $id));

            // messages for user
            $type = "success";
            $message = lang('delete_contact');
            set_message($type, $message);
            redirect('admin/client/client_details/' . $client_id);
        } else {
            $data['title'] = "Delete Client Contact"; //Page title
            $data['user_info'] = $this->db->where('user_id', $id)->get('tbl_account_details')->row();
            $data['client_id'] = $client_id;
            $data['subview'] = $this->load->view('admin/user/delete_user', $data, TRUE);
            $this->load->view('admin/_layout_main', $data); //page load
        }
    }

    public
    function delete_client($client_id, $yes = null)
    {
        $sbtn = $this->input->post('submit', true);


   
        if (!empty($sbtn) && !empty($yes)) {
            
            $this->client_model->_table_name = 'tbl_contact';
            $this->client_model->delete_multiple(array('id' => $client_id));

            $this->client_model->_table_name = 'tbl_personal_social';
            $this->client_model->delete_multiple(array('contact_id' => $client_id));

            $this->client_model->_table_name = 'tbl_contact_web';
            $this->client_model->delete_multiple(array('contact_id' => $client_id));

            $this->client_model->_table_name = 'tbl_contact_social_media';
            $this->client_model->delete_multiple(array('contact_id' => $client_id));

            $this->client_model->_table_name = 'tbl_contact_networking';
            $this->client_model->delete_multiple(array('contact_id' => $client_id));

            $this->client_model->_table_name = 'tbl_contact_cold_call';
            $this->client_model->delete_multiple(array('contact_id' => $client_id));

            $this->client_model->_table_name = 'tbl_contact_request_proposal';
            $this->client_model->delete_multiple(array('contact_id' => $client_id));

            $this->client_model->_table_name = 'tbl_contact_sales_rep';
            $this->client_model->delete_multiple(array('contact_id' => $client_id));

            $this->client_model->_table_name = 'tbl_additional_email';
            $this->client_model->delete_multiple(array('user_id' => $client_id,'user_type' => 'contact' ));

            $this->client_model->_table_name = 'tbl_additional_phone';
            $this->client_model->delete_multiple(array('user_id' => $client_id,'user_type' => 'contact'));

            $this->client_model->_table_name = 'tbl_family_child';
            $this->client_model->delete_multiple(array('contact_id' => $client_id));

            $this->client_model->_table_name = 'tbl_family_pet';
            $this->client_model->delete_multiple(array('contact_id' => $client_id));

            $this->client_model->_table_name = 'tbl_relationship_develop';
            $this->client_model->delete_multiple(array('contact_id' => $client_id));

            $this->client_model->_table_name = 'tbl_access_info';
            $this->client_model->delete_multiple(array('contact_id' => $client_id));

            $this->client_model->_table_name = 'tbl_website_url';
            $this->client_model->delete_multiple(array('contact_id' => $client_id));

            // deletre into tbl_account details by user id

            // messages for user
            $type = "success";
            $message = lang('delete_client');
            set_message($type, $message);
            redirect('admin/client/manage_client');
        } else {
            $data['title'] = "Delete Contact "; //Page title
            $data['client_info'] = $this->db->where('id', $client_id)->get('tbl_contact')->row();
            $data['subview'] = $this->load->view('admin/client/delete_client', $data, TRUE);
            $this->load->view('admin/_layout_main', $data); //page load
        }
    }

    function hash($string)
    {
        return hash('sha512', $string . config_item('encryption_key'));
    }

    public function new_notes($id = NULL)
    {
        $data['title'] = lang('give_award');
        $notes = $this->input->post('notes', true);
        $n_data['user_id'] = $this->input->post('client_id', true);
        if (!empty($notes)) {
            $n_data['notes'] = $notes;
            $n_data['is_client'] = 'Yes';
            $n_data['added_by'] = $this->session->userdata('user_id');
            // deletre into tbl_account details by user id
            $this->client_model->_table_name = 'tbl_notes';
            $this->client_model->_primary_key = 'notes_id';
            $this->client_model->save($n_data, $id);
        }
        redirect('admin/client/client_details/' . $n_data['user_id'] . '/notes');
    }

    public function delete_notes($id, $client_id)
    {
        $notes_info = $this->db->where('notes_id', $id)->get('tbl_notes')->row();
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'client',
            'module_field_id' => $this->session->userdata('user_id'),
            'activity' => ('activity_deleted_notes'),
            'icon' => 'fa-user',
            'value1' => $notes_info->notes
        );
        $this->client_model->_table_name = 'tbl_activities';
        $this->client_model->_primary_key = "activities_id";
        $this->client_model->save($activities);

        $this->client_model->_table_name = 'tbl_notes';
        $this->client_model->_primary_key = 'notes_id';
        $this->client_model->delete($id);
        redirect('admin/client/client_details/' . $client_id . '/notes');
    }

}
