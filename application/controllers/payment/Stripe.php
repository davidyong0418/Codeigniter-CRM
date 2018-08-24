<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *
 *
 * @package
 */
class Stripe extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('invoice_model');
    }

    function pay($invoices_id = NULL)
    {
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

        $data['subview'] = $this->load->view('payment/stripe', $data, FALSE);
        $this->load->view('client/_layout_modal', $data);
    }

    function authenticate()
    {
        // Check for a form submission:
        if ($_POST) {
            // Stores errors:
            $errors = array();
            // Need a payment token:
            if (isset($_POST['stripeToken'])) {
                $token = $_POST['stripeToken'];
                // Check for a duplicate submission, just in case:
                // Uses sessions, you could use a cookie instead.
                if (isset($_SESSION['token']) && ($_SESSION['token'] == $token)) {
                    $errors['token'] = 'You have apparently resubmitted the form. Please do not do that.';
                } else { // New submission.
                    $_SESSION['token'] = $token;
                }
            } else {
                $errors['token'] = 'The order cannot be processed. Please make sure you have JavaScript enabled and try again.';
            }
            // If no errors, process the order:
            if (empty($errors)) {
                // create the charge on Stripe's servers - this will charge the user's card
                try {
                    // Include the Stripe library:
                    require_once APPPATH . '/libraries/stripe/init.php';
                    // set your secret key: remember to change this to your live secret key in production
                    // see your keys here https://manage.stripe.com/account
                    \Stripe\Stripe::setApiKey(config_item('stripe_private_key'));
                    $invoice_info = $this->invoice_model->check_by(array('invoices_id' => $_POST['invoices_id']), 'tbl_invoices');
                    $client_info = $this->invoice_model->check_by(array('client_id' => $invoice_info->client_id), 'tbl_client');
                    $user_info = $this->invoice_model->check_by(array('user_id' => $this->session->userdata('user_id')), 'tbl_users');
                    $invoices_id = $invoice_info->invoices_id;
                    $invoice_ref = $invoice_info->reference_no;
                    $currency = $invoice_info->currency;
                    $paid_by = $invoice_info->client_id;
                    $amount = $_POST['amount'] * 100;
                    $metadata = array(
                        'invoices_id' => $invoices_id,
                        'payer' => $user_info->username,
                        'payer_email' => $client_info->email,
                        'invoice_ref' => $invoice_ref
                    );
                    // Charge the order:
                    $charge = \Stripe\Charge::create(array(
                            "amount" => $amount, // amount in cents
                            "currency" => $currency,
                            "card" => $token,
                            "metadata" => $metadata,
                            "description" => "Payment for Invoice " . $invoice_ref
                        )
                    );

                    // Check that it was paid:
                    if ($charge->paid == true) {

                        $currency = $this->invoice_model->client_currency_sambol($invoice_info->client_id);
                        if (empty($currency)) {
                            $currency = $this->invoice_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                        }

                        $metadata = $charge->metadata;
                        $transaction = array(
                            'invoices_id' => $metadata->invoices_id,
                            'paid_by' => $paid_by,
                            'payer_email' => $metadata->payer_email,
                            'payment_method' => '1',
                            'notes' => $charge->description,
                            'amount' => round($charge->amount / 100, 2),
                            'currency' => $currency->symbol,
                            'trans_id' => $charge->balance_transaction,
                            'month_paid' => date('m'),
                            'year_paid' => date('Y'),
                            'payment_date' => date('d-m-Y')
                        );
                        $this->invoice_model->_table_name = 'tbl_payments';
                        $this->invoice_model->_primary_key = 'payments_id';
                        $payments_id = $this->invoice_model->save($transaction);

                        // Store the order in the database.
                        if ($payments_id != 0) {

                            $due = round($this->invoice_model->calculate_to('invoice_due', $metadata->invoices_id), 2);
                            if (round($charge->amount / 100, 2) < $due) {
                                $status = 'partially_paid';
                            }
                            if (round($charge->amount / 100, 2) == $due) {
                                $status = 'Paid';
                            }
                            $invoice_data['status'] = $status;
                            update('tbl_invoices', array('invoices_id' => $metadata->invoices_id), $invoice_data);

                            $currency = $this->invoice_model->client_currency_sambol($client_info->client_id);
                            $activity = array(
                                'user' => $this->session->userdata('user_id'),
                                'module' => 'invoice',
                                'module_field_id' => $invoice_info->invoices_id,
                                'activity' => 'activity_new_payment',
                                'icon' => 'fa-usd',
                                'value1' => $currency->symbol . ' ' . round($amount / 100, 2),
                                'value2' => $invoice_info->reference_no,
                            );
                            $this->invoice_model->_table_name = 'tbl_activities';
                            $this->invoice_model->_primary_key = 'activities_id';
                            $this->invoice_model->save($activity);

                            $this->send_payment_email($invoices_id, round($charge->amount / 100, 2)); // Send email to client

                            $this->notify_to_client($invoices_id, $invoice_info->reference_no); // Send email to client

                            $type = 'success';
                            $message = 'Payment received and applied to Invoice ' . $invoice_ref;
                        } else {
                            $type = 'error';
                            $message = 'Payment not recorded in the database. Please contact the system Admin.';
                        }
                    } else { // Charge was not paid!	
                        $type = 'error';
                        $message = 'Your payment could NOT be processed (i.e., you have not been charged) because the payment system rejected the transaction. You can try again or use another card.';
                    }
                    set_message($type, $message);
                    redirect($_SERVER['HTTP_REFERER']);
                } catch (Stripe_CardError $e) {
                    // Card was declined.
                    $e_json = $e->getJsonBody();
                    $err = $e_json['error'];
                    $errors['stripe'] = $err['message'];
                } catch (Stripe_ApiConnectionError $e) {
                    // Network problem, perhaps try again.
                } catch (Stripe_InvalidRequestError $e) {
                    // You screwed up in your programming. Shouldn't happen!
                } catch (Stripe_ApiError $e) {
                    // Stripe's servers are down!
                } catch (Stripe_CardError $e) {
                    // Something else that's not the customer's fault.
                }
            } // A user form submission error occurred, handled below.
            redirect($_SERVER['HTTP_REFERER']);
        } // Form submission.
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