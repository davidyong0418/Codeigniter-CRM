<?php

/*
 * Example 2 - How to verify Mollie API Payments in a webhook.
 */

class Webhook_verification extends MY_Controller
{
    protected $mollie_api_key;

    function __construct()
    {
        parent::__construct();
        $this->load->model('invoice_model');
    }

    public function check_payment()
    {
        $this->mollie_api_key = config_item('mollie_api_key');
        try {
            /*
             * Initialize the Mollie API library with your API key.
             *
             * See: https://www.mollie.com/beheer/account/profielen/
             */
            require_once APPPATH . "libraries/Mollie/API/Autoloader.php";

            /*
             * Initialize the Mollie API library with your API key.
             *
             * See: https://www.mollie.com/beheer/account/profielen/
             */
            $mollie = new Mollie_API_Client;
            $mollie->setApiKey($this->mollie_api_key);

            /*
             * Check if this is a test request by Mollie
             */
            if (!empty($_GET['testByMollie'])) {
                die('OK');
            }
            /*
             * Retrieve the payment's current state.
             */
            $payment = $mollie->payments->get($_REQUEST["id"]);
            $order_id = $payment->metadata->order_id;
            $invoice_id = $payment->metadata->invoices_id;
            $status = $payment->status;

            if ($status === 'cancelled') {
                $status = 'open';
            } elseif ($status === 'paid') {
                $status = 'paid';
                $this->invoice_payment($invoice_id);
            } elseif ($status === 'refunded') {
                $status = 'refunded';
                $this->load->library('m_manager');
                $this->invoice_refunded($invoice_id, $payment->getAmountRefunded());
                $this->output->set_header('HTTP/1.1 200 OK');
            }
            $this->update_status($status, $invoice_id);


        } catch (Mollie_API_Exception $e) {
            echo "API call failed: " . htmlspecialchars($e->getMessage());
        }
    }

    public function update_status($status, $id)
    {
        $where = array('invoices_id' => $id);
        $data = array('status' => $status);
        $this->invoice_model->set_action($where, $data, 'tbl_invoices');
    }

    public function invoice_payment($invoice_id)
    {
        $invoice_info = $this->db->where('invoices_id', $invoice_id)->get('tbl_invoices')->row();
        $client_info = $this->db->where('client_id', $invoice_info->client_id)->get('tbl_client')->row();
        $user_info = $this->db->where('user_id', $this->session->userdata('user_id'))->get('tbl_users')->row();
        $amount = $this->invoice_model->calculate_to('invoice_due', $invoice_info->invoices_id);

        $transaction = array(
            'invoices_id' => $invoice_id,
            'paid_by' => $invoice_info->client_id,
            'payer_email' => $client_info->email,
            'payment_method' => '1',
            'notes' => "Payment for Invoice " . $invoice_info->reference_no,
            'amount' => $this->invoice_model->calculate_to('invoice_due', $invoice_info->invoices_id),
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
