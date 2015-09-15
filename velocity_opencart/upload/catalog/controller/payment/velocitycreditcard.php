<?php

/*
 * Description : from payment controller for velocity gateway
 * get data from language file and model and display on view
 * and listed in the front end payment option.
 */
class ControllerPaymentVelocityCreditCard extends Controller {
    
    public function index() {
        $this->load->language('payment/velocitycreditcard'); // load language file details
        
        $data['text_credit_card']     = $this->language->get('text_credit_card');
        $data['text_wait']            = $this->language->get('text_wait');
        $data['entry_cc_type']        = $this->language->get('entry_cc_type');
        $data['entry_cc_owner']       = $this->language->get('entry_cc_owner');
        $data['entry_cc_number']      = $this->language->get('entry_cc_number');
        $data['entry_cc_expire_date'] = $this->language->get('entry_cc_expire_date');
        $data['entry_cc_cvv2']        = $this->language->get('entry_cc_cvv2');
        $data['button_confirm']       = $this->language->get('button_confirm');

        $this->load->model('checkout/order'); // load checkout model
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $data['months'] = array();

        for ($i = 1; $i <= 12; $i++) {
                $data['months'][] = array(
                        'text'  => strftime('%B', mktime(0, 0, 0, $i, 1, 2000)),
                        'value' => sprintf('%02d', $i)
                );
        }

        $today = getdate();

        $data['year_expire'] = array();

        for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
                $data['year_expire'][] = array(
                        'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
                        'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i))
                );
        }
        
        $data['cc_type'] = array('Visa' => 'Visa', 'MasterCard' => 'MasterCard', 'Discover' => 'Discover', 'American Express' => 'AmericanExpress');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/velocitycreditcard.tpl')) {
                return $this->load->view($this->config->get('config_template') . '/template/payment/velocitycreditcard.tpl', $data);
        } else {
                return $this->load->view('default/template/payment/velocitycreditcard.tpl', $data);
        }
    }
        
    /*
     * Send is the method which is perform payment from fronend using authorizeandcapture method. 
     */
    public function send() {
 
        if ($this->request->post['cc_owner'] == '') {
            $json['error'] = 'Card Qwner name is required.';
        } else if (!preg_match('/^[a-zA-Z ]+$/', $this->request->post['cc_owner'])) {
            $json['error'] = 'Card Qwner name is like john dev.';
        } else if (!preg_match('/^[0-9]*$/', $this->request->post['cc_number'])) {
            $json['error'] = 'Credit card number is required digits only';
        } else if (!(strlen($this->request->post['cc_number']) >= 12 && strlen($this->request->post['cc_number']) <= 16)) {
            $json['error'] = 'Credit card number is must be 12 to 16 digit only';
        } else if (!preg_match('/^[0-9]*$/', $this->request->post['cc_cvv2'])) {
            $json['error'] = 'CVV number is required digits only';
        } else if (!(strlen($this->request->post['cc_cvv2']) >= 3 && strlen($this->request->post['cc_cvv2']) <= 4)) {
            $json['error'] = 'CVV number is must be 3 to 4 digit only';
        }
        
        if (!isset($json['error'])) {
            include_once 'sdk/Velocity.php';

            $identitytoken        = $this->config->get('velocitycreditcard_identitytoken');
            $workflowid           = $this->config->get('velocitycreditcard_workflowid');
            $applicationprofileid = $this->config->get('velocitycreditcard_applicationprofileid');
            $merchantprofileid    = $this->config->get('velocitycreditcard_merchantprofileid');
            
            if ($this->config->get('velocitycreditcard_test'))
                $isTestAccount    = TRUE;
            else
                $isTestAccount    = FALSE;

            try {            
                $velocityProcessor = new VelocityProcessor( $applicationprofileid, $merchantprofileid, $workflowid, $isTestAccount, $identitytoken );                
            } catch (Exception $e) {
                $json['error'] = $e->getMessage();
            }

            if (!isset($json['error'])) {
                $this->load->model('checkout/order');

                $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

                $avsData = array (
                        'Street'        => $order_info['payment_address_1'] . ' ' . $order_info['payment_address_2'],
                        'City'          => $order_info['payment_city'],
                        'StateProvince' => $order_info['payment_zone_code'],
                        'PostalCode'    => $order_info['payment_postcode'],
                        'Country'       => $order_info['payment_iso_code_3']
                );

                $cardData = array (
                        'cardtype'    => str_replace(' ', '', $this->request->post['cc_type']), 
                        'pan'         => $this->request->post['cc_number'], 
                        'expire'      => $this->request->post['cc_expire_date_month'].substr($this->request->post['cc_expire_date_year'], -2), 
                        'cvv'         => $this->request->post['cc_cvv2'],
                        'track1data'  => '', 
                        'track2data'  => ''
                );

                /* Request for the verify avsdata and card data*/
                try {          
                    $response = $velocityProcessor->verify(array(  
                            'amount'       => $order_info['total'],
                            'avsdata'      => $avsData, 
                            'carddata'     => $cardData,
                            'entry_mode'   => 'Keyed',
                            'IndustryType' => 'Ecommerce',
                            'Reference'    => 'xyz',
                            'EmployeeId'   => '11'
                    ));

                } catch (Exception $e) {
                    $json['error'] = $e->getMessage();    
                }

                if ( is_array($response) && isset($response['Status']) && $response['Status'] == 'Successful' ) {

                     /* Request for the authrizeandcapture transaction */
                    try {
                            $cap_response = $velocityProcessor->authorizeAndCapture( array(
                                    'amount'       => $order_info['total'], 
                                    'avsdata'      => $avsData,
                                    'token'        => $response['PaymentAccountDataToken'], 
                                    'order_id'     => $order_info['order_id'],
                                    'entry_mode'   => 'Keyed',
                                    'IndustryType' => 'Ecommerce',
                                    'Reference'    => 'xyz',
                                    'EmployeeId'   => '11'
                            ));


                            $xml = VelocityXmlCreator::authorizeandcaptureXML( array(
                                                                                'amount'       => $order_info['total'], 
                                                                                'avsdata'      => $avsData,
                                                                                'token'        => $response['PaymentAccountDataToken'], 
                                                                                'order_id'     => $order_info['order_id'],
                                                                                'entry_mode'   => 'Keyed',
                                                                                'IndustryType' => 'Ecommerce',
                                                                                'Reference'    => 'xyz',
                                                                                'EmployeeId'   => '11'
                                                                                )                                         
                                                                      );  // got authorizeandcapture xml object. 

                            $req = $xml->saveXML();
                            $obj_req = serialize($req);
                            
                            if ( is_array($cap_response) && !empty($cap_response) && isset($cap_response['Status']) && $cap_response['Status'] == 'Successful') {

                                $log = 'Payment has been successfully done Transaction Id is ' . $cap_response['TransactionId'];   

                                /* save the transaction detail with that order.*/ 
                                $this->db->query("INSERT INTO `" . DB_PREFIX . "velocity_transactions`
                                SET transaction_id = '" . $cap_response['TransactionId'] . "',
                                transaction_status = '" . $cap_response['CaptureState'] . "',
                                order_id = '" . $this->db->escape($order_info['order_id']) . "',
                                request_obj = '" . $obj_req . "',    
                                response_obj = '" . serialize($cap_response) . "'");

                                /* save the authandcap response into 'zen_velocity_transactions' custom table.*/ 
                                if ($this->session->data['payment_method']['code'] == 'velocitycreditcard') {
                                    $this->load->model('checkout/order');
                                    //order status pending code is 1
                                    $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], 1, "Velocity Txn id" . $cap_response['TransactionId'] . "<br>Txn status is " . $cap_response['CaptureState'] . " <br> Approval code is " . $cap_response['ApprovalCode']);
                                }
                                $this->log->write($log);
                                $json['redirect'] = $this->url->link('checkout/success', $cap_response['TransactionId'], 'SSL');

                            } else if ( is_array($cap_response) && !empty($cap_response) ) {
                                    $json['error'] = $cap_response['StatusMessage'];
                                    $json['redirect'] = $this->url->link('checkout/failure', $json['error'], 'SSL');
                            } else if (is_string($cap_response)) {
                                    $json['error'] = $cap_response;
                                    $json['redirect'] = $this->url->link('checkout/failure', $json['error'], 'SSL');
                            } else {
                                    $json['error'] = 'Unknown Error in authandcap process please contact the site admin';
                                    $json['redirect'] = $this->url->link('checkout/failure', $json['error'], 'SSL');
                            }
                    } catch(Exception $e) {
                            $json['redirect'] = $this->url->link('checkout/failure', $e->getMessage(), 'SSL');
                    }

                } else if ( is_array($response) &&(isset($response['Status']) && $response['Status'] != 'Successful' )) {
                    $json['error'] = $response['StatusMessage'];
                } else if ( is_string($response) ) {
                    $json['error'] = $response;
                } else {
                    $json['error'] = 'Unknown Error in verification process please contact the site admin';
                }
            } 
        }  
        
        if (isset($json['error'])) {
            
            $this->log->write($json['error']);
            if ($this->session->data['payment_method']['code'] == 'velocitycreditcard') {
                $this->load->model('checkout/order');
                //order status failed code is 10
                $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], 10);
            }
        }
            
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
}