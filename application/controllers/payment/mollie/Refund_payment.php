<?php

/*
 * Example 7 - How to refund a payment programmatically
 */

class Refund_payment extends Basic_Controller
{
    public function index()
    {
        try {
            /*
             * Initialize the Mollie API library with your API key.
             *
             * See: https://www.mollie.com/beheer/account/profielen/
             */
            require_once APPPATH . "libraries/Mollie/API/Autoloader.php";
            $mollie = new Mollie_API_Client;
            $mollie->setApiKey(config_item('mollie_api_key'));

            /*
             * Retrieve the payment you want to refund from the API.
             */
            $payment_id = $_REQUEST['transaction_id'];
            $amount = $_REQUEST['amount'];
            $payment = $mollie->payments->get($payment_id);
            $invoices_id = $payment->metadata->invoices_id;

            // Check if this payment can be refunded
            // You can also check if the payment can be partially refunded
            // by using $payment->canBePartiallyRefunded() and $payment->getAmountRemaining()
            if ($payment->canBeRefunded()) {
                $refund = $mollie->payments->refund($payment, $amount);
                $this->load->library('m_manager');
                $this->m_manager->mark_refunded($invoices_id, $amount);
                echo json_encode(array('success' => 1));
                exit();
            } else {
                echo json_encode(array('fail' => 1));
                exit();
            }
        } catch (Mollie_API_Exception $e) {
            echo json_encode(array('fail' => 1));
            exit();
        }
    }
}

