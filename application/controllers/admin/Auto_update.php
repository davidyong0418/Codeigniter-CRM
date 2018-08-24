<?php
defined('BASEPATH') OR exit('No direct script access allowed');

@ini_set('memory_limit', '128M');
@ini_set('max_execution_time', 240);

class Auto_update extends Admin_Controller
{
    private $tmp_update_dir;
    private $tmp_dir;

    function __construct()
    {
        parent::__construct();
        $this->load->model('admin_model');
    }

    public function validate()
    {
        $purchase_key = $this->input->post('purchase_key', false);
        $buyer = $this->input->post('buyer', false);
        $result = $this->remote_get_contents(UPDATE_URL . '/verify.php?verify_code=' . $purchase_key);
        $data = json_decode($result, true);
        if (empty($buyer)) {
            $buyer = config_item('purchase_buyer'); // check if they've bought this item id.
        }
        $purchases = $data['verify-purchase'];

        if (isset($purchases['buyer'])) {
            // format single purchases same as multi purchases
            $purchases = array($purchases);
        }
        $purchase_details = array();
        if (!empty($purchases)) {
            foreach ($purchases as $purchase) {
                $purchase = (array)$purchase; // json issues
                if ($purchase['buyer'] == $buyer) {
                    // we have a winner!
                    $purchaseed = true;
                }
            }
        } else {
            echo 'invalid purchase code Please enter the valid Purchase code';
            die();
        }

        if (!empty($purchaseed)) {
            true;
        } else {
            echo json_encode(array("status" => 'error', 'message' => 'Sorry you did not purchase the item.please enter accurate buyer name'));
            die();
        }

    }

    public function index()
    {
        $latest_version = $this->input->post('latest_version');
        $url = UPDATE_URL . "/" . $latest_version . ".zip";

        $tmp_dir = @ini_get('upload_tmp_dir');
        if (!$tmp_dir) {
            $tmp_dir = @sys_get_temp_dir();
            if (!$tmp_dir) {
                $tmp_dir = FCPATH . 'temp';
            }
        }

        $tmp_dir = rtrim($tmp_dir, '/') . '/';
        if (!is_writable($tmp_dir)) {
            header('HTTP/1.0 400');
            echo "Temporary directory not writable - <b>$tmp_dir</b><br />Please contact your hosting provider make this directory writable. The directory needs to be writable for the update files.";
            die;
        }

        $this->tmp_dir = $tmp_dir;
        $tmp_dir = $tmp_dir . 'v' . $latest_version . '/';
        $this->tmp_update_dir = $tmp_dir;

        if (!is_dir($tmp_dir)) {
            mkdir($tmp_dir);
            fopen($tmp_dir . 'index.html', 'w');
        }

        $zipFile = $tmp_dir . $latest_version . '.zip'; // Local Zip File Path
        $zipResource = fopen($zipFile, "w+");

        // Get The Zip File From Server
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_FILE, $zipResource);
        $success = curl_exec($ch);

        if (!$success) {
            $this->clean_tmp_files();
            header('HTTP/1.0 400 Bad error');
            echo curl_error($ch);
            die;
        }
        curl_close($ch);
        $zip = new ZipArchive;
        if ($zip->open($zipFile) === true) {
            if (!$zip->extractTo('./')) {
                header('HTTP/1.0 400 Bad error');
                echo 'Failed to extract downloaded zip file';
            }
            $zip->close();
        } else {
            header('HTTP/1.0 400 Bad error');
            echo 'Failed to open downloaded zip file';
        }
        $this->clean_tmp_files();
    }

    public
    function database()
    {
        $db_update = $this->admin_model->upgrade_database_silent();

        if ($db_update['success'] == false) {
            header('HTTP/1.0 400 Bad error');
            echo json_encode(array(
                $db_update['message']
            ));
            die;
        }
        echo '<div class="bold">
            <h4 class="bold">Hi! Thanks for updating Ultimate Project Manager CRM PRO - You are using version ' . config_item('version') . '></h4>
            <p>
                This window will reload automatically in 10 seconds and will try to clear your browser cache, however its recommended to clear your browser cache manually.
            </p>
        </div>';
        set_message('success', lang('using_latest_version'));
    }

    private
    function clean_tmp_files()
    {
        if (is_dir($this->tmp_update_dir)) {
            if (@!delete_dir($this->tmp_update_dir)) {
                @rename($this->tmp_update_dir, $this->tmp_dir . 'delete_this_' . uniqid());
            }
        }
    }

    public
    static function remote_get_contents($url)
    {
        if (function_exists('curl_get_contents') AND function_exists('curl_init')) {
            return self::curl_get_contents($url);
        } else {
            return file_get_contents($url);
        }
    }

    public
    static function curl_get_contents($url)
    {
        $ch = curl_init();
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => true
        );
        if (!ini_get('safe_mode') && !ini_get('open_basedir')) {
            $options[CURLOPT_FOLLOWLOCATION] = true;
        }
        curl_setopt_array($ch, $options);

        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
}
