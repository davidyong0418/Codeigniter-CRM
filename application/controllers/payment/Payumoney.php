<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *
 *
 * @package
 */
class Payumoney extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('invoice_model');
    }

    public function pay($invoice_id)
    {

        $data['title'] = lang('payumoney');
        $posted = array();
        $success = base_url() . 'payment/payumoney/check_status/' . $invoice_id;
        $fail = base_url() . 'payment/payumoney/check_status/' . $invoice_id;
        $cancel = base_url() . 'payment/payumoney/check_status/' . $invoice_id;

        if ($this->input->post()) {
            // all values are required
            $amount = $this->input->post('amount');
            $product_info = $this->input->post('productinfo');
            $customer_name = $this->input->post('firstname');
            $customer_emial = $this->input->post('email');
            $customer_mobile = $this->input->post('phone');
            $customer_address = $this->input->post('address');

            //payumoney details
            $MERCHANT_KEY = config_item('payumoney_key'); //change  merchant with yours
            $SALT = config_item('payumoney_salt');  //change salt with yours
            $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
            //optional udf values
            $udf1 = '';
            $udf2 = '';
            $udf3 = '';
            $udf4 = '';
            $udf5 = '';

            $hashstring = $MERCHANT_KEY . '|' . $txnid . '|' . $amount . '|' . $product_info . '|' . $customer_name . '|' . $customer_emial . '|' . $udf1 . '|' . $udf2 . '|' . $udf3 . '|' . $udf4 . '|' . $udf5 . '||||||' . $SALT;
            $hash = strtolower(hash('sha512', $hashstring));

            $data = array(
                'mkey' => $MERCHANT_KEY,
                'tid' => $txnid,
                'hash' => $hash,
                'amount' => $amount,
                'firstname' => $customer_name,
                'productinfo' => $product_info,
                'mailid' => $customer_emial,
                'phoneno' => $customer_mobile,
                'address' => $customer_address,
                'action' => (config_item('payumoney_enable_test_mode') == 'TRUE') ? 'https://test.payu.in/_payment' : 'https://secure.payu.in/_payment', //for live change action  https://secure.payu.in
                'sucess' => $success,
                'failure' => $fail,
                'cancel' => $cancel
            );
            $invoice_info = $this->invoice_model->check_by(array('invoices_id' => $invoice_id), 'tbl_invoices');
            $client_info = $this->invoice_model->check_by(array('client_id' => $invoice_info->client_id), 'tbl_client');
            if (!empty($client_info)) {
                $data['cur'] = $this->invoice_model->client_currency_sambol($invoice_info->client_id);
            } else {
                $data['cur'] = $this->invoice_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
            }
            $data['reference_no'] = $invoice_info->reference_no;
            $data['attributes'] = array('class' => 'bs-example form-horizontal');
        } else {
            $data = array(
                'mkey' => '',
                'tid' => '',
                'hash' => '',
                'sucess' => $success,
                'failure' => $fail,
                'cancel' => $cancel
            );
            $data['action'] = base_url() . $this->uri->uri_string();
            $hash = '';
            $invoice_info = $this->invoice_model->check_by(array('invoices_id' => $invoice_id), 'tbl_invoices');
            $invoice_due = $this->invoice_model->calculate_to('invoice_due', $invoice_id);
            if ($invoice_due <= 0) {
                $invoice_due = 0.00;
            }
            $data['amount'] = $invoice_due;
            $client_info = $this->invoice_model->check_by(array('client_id' => $invoice_info->client_id), 'tbl_client');
            if (!empty($client_info)) {
                $name = $client_info->name;
                $data['cur'] = $this->invoice_model->client_currency_sambol($invoice_info->client_id);
            } else {
                $name = '-';
                $data['cur'] = $this->invoice_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
            }
            $data['firstname'] = $name;
            $data['mailid'] = $client_info->email;
            $data['phoneno'] = $client_info->phone;
            $data['address'] = $client_info->short_note;
            $data['reference_no'] = $invoice_info->reference_no;
            $data['attributes'] = array('id' => 'my_id', 'data-parsley-validate' => "", 'novalidate' => "", 'class' => 'bs-example form-horizontal');
        }
        $data['hash'] = $hash;


        $data['payumoney_url'] = base_url() . 'payment/payumoney/check_payment';
        $data['subview'] = $this->load->view('payment/payumoney/payumoney', $data, FALSE);
        $this->load->view('client/_layout_modal', $data);
    }

    public function check_payment()
    {

        // all values are required
        $amount = $this->input->post('amount');
        $product_info = $this->input->post('productinfo');
        $customer_name = $this->input->post('firstname');
        $customer_emial = $this->input->post('email');
        $customer_mobile = $this->input->post('mobile');
        $customer_address = $this->input->post('address');

        //payumoney details
        $MERCHANT_KEY = config_item('payumoney_key'); //change  merchant with yours
        $SALT = config_item('payumoney_salt');  //change salt with yours
        $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
        //optional udf values
        $udf1 = '';
        $udf2 = '';
        $udf3 = '';
        $udf4 = '';
        $udf5 = '';

        $hashstring = $MERCHANT_KEY . '|' . $txnid . '|' . $amount . '|' . $product_info . '|' . $customer_name . '|' . $customer_emial . '|' . $udf1 . '|' . $udf2 . '|' . $udf3 . '|' . $udf4 . '|' . $udf5 . '||||||' . $SALT;
        $hash = strtolower(hash('sha512', $hashstring));

        $success = base_url() . 'payment/payumoney/check_status';
        $fail = base_url() . 'payment/payumoney/check_status';
        $cancel = base_url() . 'payment/payumoney/check_status';

        $data = array(
            'mkey' => $MERCHANT_KEY,
            'tid' => $txnid,
            'hash' => $hash,
            'amount' => $amount,
            'name' => $customer_name,
            'productinfo' => $product_info,
            'mailid' => $customer_emial,
            'phoneno' => $customer_mobile,
            'address' => $customer_address,
            'action' => "https://test.payu.in", //for live change action  https://secure.payu.in
            'sucess' => $success,
            'failure' => $fail,
            'cancel' => $cancel
        );
        $data['title'] = lang('PayUmoney') . ' ' . lang('confirmation');

        $data['subview'] = $this->load->view('payment/payumoney/confirmation', $data, true);
        $this->load->view('frontend/_layout_main', $data);
    }

    public function check_status($invoice_id)
    {
        $status = $this->input->post('status');
        if (empty($status)) {
            redirect('payment/payumoney/pay/' . url_encode($invoice_id));
        }
        $firstname = $this->input->post('firstname');
        $amount = $this->input->post('amount');
        $txnid = $this->input->post('txnid');
        $posted_hash = $this->input->post('hash');
        $key = $this->input->post('key');
        $productinfo = $this->input->post('productinfo');
        $email = $this->input->post('email');
        $salt = config_item('payumoney_salt'); //  Your salt
        $add = $this->input->post('additionalCharges');
        If (isset($add)) {
            $additionalCharges = $this->input->post('additionalCharges');
            $retHashSeq = $additionalCharges . '|' . $salt . '|' . $status . '|||||||||||' . $email . '|' . $firstname . '|' . $productinfo . '|' . $amount . '|' . $txnid . '|' . $key;
        } else {
            $retHashSeq = $salt . '|' . $status . '|||||||||||' . $email . '|' . $firstname . '|' . $productinfo . '|' . $amount . '|' . $txnid . '|' . $key;
        }
        $data['hash'] = hash("sha512", $retHashSeq);
        $data['amount'] = $amount;
        $data['txnid'] = $txnid;
        $data['posted_hash'] = $posted_hash;
        $data['status'] = $status;
        if ($status == 'success') {
            $this->invoice_payment($invoice_id, $amount);
            $data['subview'] = $this->load->view('payment/payumoney/success', $data, true);
        } else {
            $data['subview'] = $this->load->view('payment/payumoney/fail', $data, true);
        }
        $this->load->view('frontend/_layout_main', $data);

    }

    public function invoice_payment($invoice_id, $amount)
    {
        $invoice_info = $this->db->where('invoices_id', $invoice_id)->get('tbl_invoices')->row();
        $client_info = $this->db->where('client_id', $invoice_info->client_id)->get('tbl_client')->row();
        $currency = $this->invoice_model->client_currency_sambol($invoice_info->client_id);
        if (empty($currency)) {
            $currency = $this->invoice_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
        }

        $transaction = array(
            'invoices_id' => $invoice_id,
            'paid_by' => $invoice_info->client_id,
            'payer_email' => $client_info->email,
            'payment_method' => '1',
            'notes' => "Payment for Invoice " . $invoice_info->reference_no,
            'amount' => $amount,
            'currency' => $currency->symbol,
            'trans_id' => $invoice_info->reference_no,
            'month_paid' => date('m'),
            'year_paid' => date('Y'),
            'payment_date' => date('d-m-Y')
        );
        $this->invoice_model->_table_name = 'tbl_payments';
        $this->invoice_model->_primary_key = 'payments_id';
        $payments_id = $this->invoice_model->save($transaction);

        $due = round($this->invoice_model->calculate_to('invoice_due', $invoice_id), 2);
        if ($amount < $due) {
            $status = 'partially_paid';
        }
        if ($amount == $due) {
            $status = 'Paid';
        }
        $invoice_data['status'] = $status;
        update('tbl_invoices', array('invoices_id' => $invoice_id), $invoice_data);

        // Store the order in the database.
        if ($payments_id != 0) {
            $currency = $this->invoice_model->client_currency_sambol($client_info->client_id);
            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'invoice',
                'module_field_id' => $invoice_info->invoices_id,
                'activity' => 'activity_new_payment',
                'icon' => 'fa-usd',
                'value1' => display_money($amount, $currency->symbol),
                'value2' => $invoice_info->reference_no,
            );
            $this->invoice_model->_table_name = 'tbl_activities';
            $this->invoice_model->_primary_key = 'activities_id';
            $this->invoice_model->save($activity);

            $this->send_payment_email($invoice_id, $amount); // Send email to client
            $this->notify_to_client($invoice_id, $invoice_info->reference_no); // Send email to client
            $type = 'success';
            $message = 'Payment received and applied to Invoice ' . $invoice_info->reference_no;
            set_message($type, $message);
        } else {
            $type = 'error';
            $message = 'Payment not recorded in the database. Please contact the system Admin.';
            set_message($type, $message);
        }

    }

    function send_payment_email($invoices_id, $paid_amount)
    {
        $email_template = $this->invoice_model->check_by(array('email_group' => 'payment_email'), 'tbl_email_templates');
        $message = $email_template->template_body;
        $subject = $email_template->subject;

        $inv_info = $this->invoice_model->check_by(array('invoices_id' => $invoices_id), 'tbl_invoices');
        $currency = $inv_info->currency;
        $reference = $inv_info->reference_no;

        $invoice_currency = str_replace("{INVOICE_CURRENCY}", $currency, $message);
        $reference = str_replace("{INVOICE_REF}", $reference, $invoice_currency);
        $amount = str_replace("{PAID_AMOUNT}", $paid_amount, $reference);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $amount);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);
        $client_info = $this->invoice_model->check_by(array('client_id' => $inv_info->client_id), 'tbl_client');

        $address = $client_info->email;

        $params['recipient'] = $address;

        $params['subject'] = '[ ' . config_item('company_name') . ' ]' . ' ' . $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';

        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'invoice',
            'module_field_id' => $invoices_id,
            'activity' => lang('activity_send_payment'),
            'icon' => 'fa-usd',
            'value1' => $reference,
            'value2' => $currency . ' ' . $amount,
        );
        $this->invoice_model->_table_name = 'tbl_activities';
        $this->invoice_model->_primary_key = 'activities_id';
        $this->invoice_model->save($activity);

        $this->invoice_model->send_email($params);
    }

    function notify_to_client($client_id, $invoice_ref)
    {
        $this->load->library('email');
        $client_info = $this->invoice_model->check_by(array('client_id' => $client_id), 'tbl_client');
        if (!empty($client_info->email)) {
            $data['invoice_ref'] = $invoice_ref;

            $email_msg = $this->load->view('payment/stripe_InvoicePaid', $data, TRUE);
            $email_subject = '[' . $this->config->item('company_name') . ' ] Purchase Confirmation';
            $this->email->from($this->config->item('company_email'), $this->config->item('company_name') . ' Payments');
            $this->email->to($client_info->email);
            $this->email->reply_to($this->config->item('company_email'), $this->config->item('company_name'));
            $this->email->subject($email_subject);

            $this->email->message($email_msg);

            $send = $this->email->send();
            if ($send) {
                return $send;
            } else {
                $error = show_error($this->email->print_debugger());
                return $error;
            }
        } else {
            return true;
        }
    }

}

////end