<?php

class Pay extends MY_Controller
{
    protected $amount;
    protected $description;
    protected $mollie_api_key;
    protected $invoices_id;

    function __construct()
    {
        parent::__construct();
        $this->load->model('invoice_model');
    }

    public function index($invoices_id)
    {
        $invoice_info = $this->db->where('invoices_id', $invoices_id)->get('tbl_invoices')->row();
        if (!empty($allow_customer_edit_amount) && $allow_customer_edit_amount == 'No') {
            $amount = $this->invoice_model->calculate_to('invoice_due', $invoice_info->invoices_id);
        } else {
            $amount = $this->input->post('amount', true);
        }
        if (!empty($this->input->post('amount', true))) {
            $this->amount = $amount;
            $this->description = $invoice_info->reference_no;
            $this->invoices_id = $invoice_info->invoices_id;
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
                 * Generate a unique order id for this example. It is important to include this unique attribute
                 * in the redirectUrl (below) so a proper return page can be shown to the customer.
                 */
                $order_id = time();
                /*
                 * Payment parameters:
                 *   amount        Amount in EUROs. This example creates a € 10,- payment.
                 *   description   Description of the payment.
                 *   redirectUrl   Redirect location. The customer will be redirected there after the payment.
                 *   webhookUrl    Webhook location, used to report when the payment changes state.
                 *   metadata      Custom metadata that is stored with the payment.
                 */

                $payment = $mollie->payments->create(array(
                    "amount" => $this->amount,
                    "description" => $this->description,
                    "redirectUrl" => base_url() . 'frontend/view_invoice/' . url_encode($this->invoices_id),
                    "webhookUrl" => base_url() . 'payment/mollie/webhook_verification/check_payment',
                    "metadata" => array(
                        "order_id" => $order_id,
                        "invoices_id" => $this->invoices_id
                    ),
                ));

                $get_payment = $mollie->payments->get($payment->id);
                if ($get_payment->isPaid()) {
                    $this->invoice_payment($this->invoices_id, $this->amount);
                }

                header("Location: " . $payment->getPaymentUrl());
            } catch (Mollie_API_Exception $e) {
                echo "API call failed: " . htmlspecialchars($e->getMessage());
            }
        } else {
            $invoice_info = $this->invoice_model->check_by(array('invoices_id' => $invoices_id), 'tbl_invoices');
            $invoice_due = $this->invoice_model->calculate_to('invoice_due', $invoices_id);
            if ($invoice_due <= 0) {
                $invoice_due = 0.00;
            }
            $data['invoices_info'] = $invoice_info;

            $data['invoice_info'] = array(
                'item_name' => $invoice_info->reference_no,
                'item_number' => $invoices_id,
                'currency' => $invoice_info->currency,
                'allow_stripe' => $invoice_info->allow_stripe,
                'amount' => $invoice_due);
            $data['stripe'] = TRUE;
            $data['subview'] = $this->load->view('payment/mollie', $data, FALSE);
            $this->load->view('client/_layout_modal', $data);
        }
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
