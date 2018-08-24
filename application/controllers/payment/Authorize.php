<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 *
 * @package	Freelancer Office
 */
class Authorize extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('invoice_model');
    }

    function pay($invoice_id = NULL) {
        $invoice_info = $this->invoice_model->check_by(array('invoices_id' => $invoice_id), 'tbl_invoices');

        $invoice_due = $this->invoice_model->calculate_to('invoice_due', $invoice_id);
        if ($invoice_due <= 0) {
            $invoice_due = 0.00;
        }
        $data['invoice_info'] = array(
            'item_name' => $invoice_info->reference_no,
            'item_number' => $invoice_id,
            'currency' => $invoice_info->currency,
            'amount' => $invoice_due);

        $data['subview'] = $this->load->view('payment/authorize', $data, FALSE);
        $this->load->view('client/_layout_modal', $data);
    }

    public function process() {
        $card_expiration = $_POST['crMonth'] . $_POST['crYear'];

        $x_login = $this->config->item('authorize');
        $x_tran_key = $this->config->item('authorize_transaction_key');
        $card_number = $_POST['ccn'];

        $invoice_num = '';

        $x_card_code = $_POST['CSV'];

        $post_values['x_invoice_num'] = $invoice_num;
        $post_values['x_login'] = $x_login;
        $post_values['x_tran_key'] = $x_tran_key;

        $post_values['x_card_code'] = $x_card_code;

        $post_values['x_version'] = "3.1";
        $post_values['x_delim_data'] = "TRUE";
        $post_values['x_delim_char'] = "|";
        $post_values['x_relay_response'] = "FALSE";

        $post_values['x_type'] = "AUTH_CAPTURE"; //Optional
        $post_values['x_method'] = "CC";
        $post_values['x_card_num'] = $card_number;
        $post_values['x_exp_date'] = $card_expiration;
        $post_values['x_amount'] = $_POST['amount'];
        $post_values['x_description'] = 'Invoice Payment' . $_POST['ref'];
        $post_values['x_invoice_num'] = $_POST['invoice_id'];
        //Calling Payment function

        $paymentResponse = $this->do_payment($post_values);

        if ($paymentResponse[0] == 1 && $paymentResponse[1] == 1 && $paymentResponse[2] == 1) {
            $invoice_info = $this->invoice_model->check_by(array('invoices_id' => $_POST['invoice_id']), 'tbl_invoices');
            $client_info = $this->invoice_model->check_by(array('invoices_id' => $invoice_info->client_id), 'tbl_client');
            $user_info = $this->invoice_model->check_by(array('invoices_id' => $this->session->userdata('user_id')), 'tbl_users');
            // payment is successful. Do your action here
            $transaction = array(
                'invoices_id' => $_POST['invoice_id'],
                'paid_by' => $invoice_info->client_id,
                'payer_email' => $client_info->email,
                'payment_method' => '1',
                'notes' => 'Paid by ' . $user_info->username,
                'amount' => $_POST['amount'],
                'trans_id' => $_POST['ref'],
                'month_paid' => date('m'),
                'year_paid' => date('Y'),
                'payment_date' => date('d-m-Y H:i:s')
            );

            $this->invoice_model->_table_name = 'tbl_payments';
            $this->invoice_model->_primary_key = 'payments_id';
            $this->invoice_model->save($transaction);

            $due = round($this->invoice_model->calculate_to('invoice_due', $_POST['invoice_id']), 2);
            if ($_POST['amount'] < $due) {
                $status = 'partially_paid';
            }
            if ($_POST['amount'] == $due) {
                $status = 'Paid';
            }

            $invoice_data['status'] = $status;
            update('tbl_invoices', array('invoices_id' => $_POST['invoice_id']), $invoice_data);


            $currency = $this->invoice_model->client_currency_sambol($client_info->client_id);
            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'invoice',
                'module_field_id' => $invoice_info->invoices_id,
                'activity' => 'activity_new_payment',
                'icon' => 'fa-usd',
                'value1' => $invoice_info->reference_no,
            );
            $this->invoice_model->_table_name = 'tbl_activities';
            $this->invoice_model->_primary_key = 'activities_id';
            $this->invoice_model->save($activity);
            $type = "success";
            $message = lang('generate_payment');
        } else {

            $type = "error";
            $message = $paymentResponse[3];
            // payment failed.            
        }
        set_message($type, $message);
        redirect('client/invoice/manage_invoice/invoice_details/' . $_POST['invoice_id']);
    }

    function do_payment($post_values) {

        $post_url = "https://test.authorize.net/gateway/transact.dll";
        // This section takes the input fields and converts them to the proper format
        $post_string = "";
        foreach ($post_values as $key => $value) {
            $post_string .= "$key=" . urlencode($value) . "&";
        }
        $post_string = rtrim($post_string, "& ");

        // This sample code uses the CURL library for php to establish a connection,
        // submit the post, and record the response.
        // If you receive an error, you may want to ensure that you have the curl
        // library enabled in your php configuration

        $request = curl_init($post_url); // initiate curl object

        curl_setopt($request, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response

        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
        curl_setopt($request, CURLOPT_POSTFIELDS, $post_string); // use HTTP POST to send form data
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment this line if you get no gateway response.
        $post_response = curl_exec($request); // execute curl post and store results in $post_response
        // additional options may be required depending upon your server configuration
        // you can find documentation on curl options at http://www.php.net/curl_setopt
        curl_close($request); // close curl object
        // This line takes the response and breaks it into an array using the specified delimiting character
        $response_array = explode($post_values["x_delim_char"], $post_response);
        // The results are output to the screen in the form of an html numbered list.
        if ($response_array) {
            return $response_array;
        } else {
            return '';
        }
    }
    

}

////end 
