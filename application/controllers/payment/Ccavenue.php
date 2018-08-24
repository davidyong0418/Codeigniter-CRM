<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class CCAvenue extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('invoice_model');
    }

    function pay($invoice_id = NULL)
    {
        $invoice_info = $this->invoice_model->check_by(array('invoices_id' => $invoice_id), 'tbl_invoices');

        $invoice_due = $this->invoice_model->calculate_to('invoice_due', $invoice_id);
        if ($invoice_due <= 0) {
            $invoice_due = 0.00;
        }
        $data['invoice_info'] = array(
            'item_name' => $invoice_info->reference_no,
            'item_number' => $invoice_id,
            'currency' => $invoice_info->currency,
            'invoices_id' => $invoice_id,
            'client_id' => $invoice_info->client_id,
            'amount' => $invoice_due);

        $working_key = $this->config->item('ccavenue_key');
        $Merchant_Id = $this->config->item('ccavenue_merchant_id');//This id(also User Id) available at "Generate Working Key" of "Settings & Options"
        $Amount = $invoice_due;//your script should substitute the amount in the quotes provided here
        $Order_Id = $invoice_info->reference_no;//your script should substitute the order description in the quotes provided here
        $Redirect_Url = base_url() . 'payment/ccavenue/check_status/' . $invoice_id;//your redirect URL where your customer will be redirected after authorisation from CCAvenue
        $WorkingKey = $working_key;//put in the 32 bit alphanumeric key in the quotes provided here.Please note that get this key ,login to your CCAvenue merchant account and visit the "Generate Working Key" section at the "Settings & Options" page.
        $data['Checksum'] = $this->getCheckSum($Merchant_Id, $Amount, $Order_Id, $Redirect_Url, $WorkingKey);

        $data['subview'] = $this->load->view('payment/ccavenue', $data, FALSE);
        $this->load->view('client/_layout_modal', $data);
    }

    public function check_status($invoice_id)
    {
        $status = $this->input->post('status');
        if (empty($status)) {
            redirect('payment/ccavenue/pay/' . url_encode($invoice_id));
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

        $transaction = array(
            'invoices_id' => $invoice_id,
            'paid_by' => $invoice_info->client_id,
            'payer_email' => $client_info->email,
            'payment_method' => '1',
            'notes' => "Payment for Invoice " . $invoice_info->reference_no,
            'amount' => $amount,
            'trans_id' => $invoice_info->reference_no,
            'month_paid' => date('m'),
            'year_paid' => date('Y'),
            'payment_date' => date('d-m-Y')
        );
        $this->invoice_model->_table_name = 'tbl_payments';
        $this->invoice_model->_primary_key = 'payments_id';
        $payments_id = $this->invoice_model->save($transaction);

        // Store the order in the database.
        if ($payments_id != 0) {

            $due = round($this->invoice_model->calculate_to('invoice_due', $invoice_id), 2);
            if ($amount < $due) {
                $status = 'partially_paid';
            }
            if ($amount == $due) {
                $status = 'Paid';
            }
            $invoice_data['status'] = $status;
            update('tbl_invoices', array('invoices_id' => $invoice_id), $invoice_data);

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

    function getchecksum($MerchantId, $Amount, $OrderId, $URL, $WorkingKey)
    {
        $str = "$MerchantId|$OrderId|$Amount|$URL|$WorkingKey";
        $adler = 1;
        $adler = $this->adler32($adler, $str);
        return $adler;
    }

    function verifychecksum($MerchantId, $OrderId, $Amount, $AuthDesc, $CheckSum, $WorkingKey)
    {
        $str = "$MerchantId|$OrderId|$Amount|$AuthDesc|$WorkingKey";
        $adler = 1;
        $adler = $this->adler32($adler, $str);
        if ($adler == $CheckSum)
            return "true";
        else
            return "false";
    }


    function adler32($adler, $str)
    {
        $BASE = 65521;
        $s1 = $adler & 0xffff;
        $s2 = ($adler >> 16) & 0xffff;
        for ($i = 0; $i < strlen($str); $i++) {
            $s1 = ($s1 + Ord($str[$i])) % $BASE;
            $s2 = ($s2 + $s1) % $BASE;
        }
        return $this->leftshift($s2, 16) + $s1;
    }


    function leftshift($str, $num)
    {
        $str = DecBin($str);
        for ($i = 0; $i < (64 - strlen($str)); $i++)
            $str = "0" . $str;
        for ($i = 0; $i < $num; $i++)
            $str = $str . "0";
        $str = substr($str, 1);
        return $this->cdec($str);
    }


    function cdec($num)
    {
        $dec = '';
        for ($n = 0; $n < strlen($num); $n++) {
            $temp = $num[$n];
            $dec = $dec + $temp * pow(2, strlen($num) - $n - 1);
        }
        return $dec;
    }


}

////end 