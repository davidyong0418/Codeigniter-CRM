<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Braintree extends MY_Controller
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
            'token' => $this->_gen_token(),
            'amount' => $invoice_due);

        $data['subview'] = $this->load->view('payment/braintree', $data, FALSE);
        $this->load->view('client/_layout_modal', $data);
    }

    function process()
    {
        if ($this->input->post()) {
            $invoice_id = $this->input->post('invoice_id');
            $invoice_info = $this->invoice_model->check_by(array('invoices_id' => $invoice_id), 'tbl_invoices');
            $client_info = $this->invoice_model->check_by(array('client_id' => $invoice_info->client_id), 'tbl_client');

            // If no errors, process the order:
            require_once(APPPATH . 'libraries/braintree/lib/Braintree.php');

            $braintree_env = (config_item('braintee_live') == 'TRUE') ? 'production' : 'sandbox';
            Braintree_Configuration::environment($braintree_env);
            Braintree_Configuration::merchantId(config_item('braintree_merchant_id'));
            Braintree_Configuration::publicKey(config_item('braintree_public_key'));
            Braintree_Configuration::privateKey(config_item('braintree_private_key'));

            $nonce = $this->input->post('payment_method_nonce');

            $result = Braintree_Transaction::sale([
                'amount' => $this->input->post('amount'),
                'orderId' => $invoice_info->reference_no,
                'paymentMethodNonce' => $nonce,
                'merchantAccountId' => config_item('braintree_default_account'),
                'customer' => [
                    'firstName' => '',
                    'lastName' => '',
                    'company' => $client_info->name,
                    'phone' => $client_info->phone,
                    'fax' => $client_info->fax,
                    'website' => $client_info->website,
                    'email' => $client_info->email
                ],
                'options' => [
                    'submitForSettlement' => True
                ]
            ]);

            if ($result->success) {
                $transaction = array(
                    'invoices_id' => $invoice_id,
                    'paid_by' => $client_info->client_id,
                    'payer_email' => $client_info->email,
                    'payment_method' => '1',
                    'notes' => 'Paid by ' . $this->session->userdata('name'),
                    'amount' => $_POST['amount'],
                    'trans_id' => $invoice_info->reference_no,
                    'month_paid' => date('m'),
                    'year_paid' => date('Y'),
                    'payment_date' => date('d-m-Y H:i:s')
                );

                $this->invoice_model->_table_name = 'tbl_payments';
                $this->invoice_model->_primary_key = 'payments_id';
                $this->invoice_model->save($transaction);
                $payments_id = $this->invoice_model->save($transaction);

                // Store the order in the database.
                if ($payments_id != 0) {

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
                        'value1' => $currency->symbol . ' ' . $_POST['amount'],
                        'value2' => $invoice_info->reference_no,
                    );
                    $this->invoice_model->_table_name = 'tbl_activities';
                    $this->invoice_model->_primary_key = 'activities_id';
                    $this->invoice_model->save($activity);

                    $this->send_payment_email($invoice_id, $_POST['amount']); // Send email to client

                    $this->notify_to_client($invoice_id, $invoice_info->reference_no); // Send email to client
                    // messages for user
                    $type = "success";
                    $message = 'Paid Succeesfully';
                } else {
                    $type = 'error';
                    $message = 'Payment not recorded in the database. Please contact the system Admin.';
                }
            } else if ($result->transaction) {
                $type = "error";
                $message = 'Error processing transaction:' . "\n  code: " . $result->transaction->processorResponseCode . "\n  text: " . $result->transaction->processorResponseText;
            } else {
                $type = "success";
                $message = 'Validation errors: \n' . $result->errors->deepAll();
            }
            set_message($type, $message);
            redirect('client/invoice/manage_invoice/invoice_details/' . $invoice_id);
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

    function _gen_token()
    {
        $braintree_env = (config_item('braintree_live_or_sandbox') == 'TRUE') ? 'production' : 'sandbox';

        require_once(APPPATH . 'libraries/braintree/lib/Braintree.php');
        Braintree_Configuration::environment($braintree_env);
        Braintree_Configuration::merchantId(config_item('braintree_merchant_id'));
        Braintree_Configuration::publicKey(config_item('braintree_public_key'));
        Braintree_Configuration::privateKey(config_item('braintree_private_key'));
        return Braintree_ClientToken::generate();
    }

}

////end 