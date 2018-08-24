<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Checkout extends MY_Controller
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
            'amount' => $invoice_due);

        $data['subview'] = $this->load->view('payment/checkout', $data, FALSE);
        $this->load->view('client/_layout_modal', $data);
    }

    function process()
    {

        if ($this->input->post()) {
            $errors = array();
            $invoice_id = $this->input->post('invoice_id');
            if (!isset($_POST['token'])) {
                $errors['token'] = 'The order cannot be processed. Please make sure you have JavaScript enabled and try again.';
            }
            // If no errors, process the order:
            if (empty($errors)) {

                require_once(APPPATH . 'libraries/2checkout/Twocheckout.php');

                Twocheckout::privateKey(config_item('2checkout_private_key'));
                Twocheckout::sellerId(config_item('2checkout_seller_id'));
                Twocheckout::sandbox((config_item('two_checkout_live') == 'TRUE') ? false : true);
                $user_info = $this->invoice_model->check_by(array('user_id' => $this->session->userdata('user_id')), 'tbl_users');
                $invoice_info = $this->invoice_model->check_by(array('invoices_id' => $invoice_id), 'tbl_invoices');
                $client_info = $this->invoice_model->check_by(array('client_id' => $invoice_info->client_id), 'tbl_client');

                try {
                    $charge = Twocheckout_Charge::auth(array(
                        "merchantOrderId" => $invoice_info->invoices_id,
                        "token" => $this->input->post('token'),
                        "currency" => $invoice_info->currency,
                        "total" => $this->input->post('amount'),
                        "billingAddr" => array(
                            "name" => $client_info->name,
                            "addrLine1" => $client_info->address,
                            "city" => $client_info->city,
                            "country" => $client_info->country,
                            "email" => $client_info->email,
                            "phoneNumber" => $client_info->phone
                        )
                    ));


                    if ($charge['response']['responseCode'] == 'APPROVED') {
                        $transaction = array(
                            'invoices_id' => $charge['response']['merchantOrderId'],
                            'paid_by' => $client_info->client_id,
                            'payer_email' => $charge['response']['billingAddr']['email'],
                            'payment_method' => '1',
                            'currency' => $charge['response']['currencyCode'],
                            'notes' => 'Paid by ' . $user_info->username,
                            'amount' => $charge['response']['total'],
                            'trans_id' => $charge['response']['transactionId'],
                            'month_paid' => date('m'),
                            'year_paid' => date('Y'),
                            'payment_date' => date('d-m-Y H:i:s')
                        );

                        $this->invoice_model->_table_name = 'tbl_payments';
                        $this->invoice_model->_primary_key = 'payments_id';
                        $this->invoice_model->save($transaction);

                        $due = round($this->invoice_model->calculate_to('invoice_due', $invoice_id), 2);
                        if ($_POST['amount'] < $due) {
                            $status = 'partially_paid';
                        }
                        if ($_POST['amount'] == $due) {
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
                            'value1' => $currency->symbol . ' ' . $charge['response']['total'],
                            'value2' => $invoice_info->reference_no,
                        );
                        $this->invoice_model->_table_name = 'tbl_activities';
                        $this->invoice_model->_primary_key = 'activities_id';
                        $this->invoice_model->save($activity);
                    }
                    $this->send_payment_email($invoice_id, $charge['response']['total']); // Send email to client

                    $this->notify_to_client($invoice_id, $invoice_info->reference_no); // Send email to client
                } catch (Twocheckout_Error $e) {
                    $type = 'error';
                    $message = 'Payment declined with error: ' . $e->getMessage();
                    set_message($type, $message);
                    redirect('client/invoice/manage_invoice/invoice_details/' . $invoice_info->invoices_id);
                }
            }
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
    }

}

////end 