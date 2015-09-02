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

            if ($this->config->get('velocitycreditcard_test')) {
                $identitytoken        = "PHNhbWw6QXNzZXJ0aW9uIE1ham9yVmVyc2lvbj0iMSIgTWlub3JWZXJzaW9uPSIxIiBBc3NlcnRpb25JRD0iXzdlMDhiNzdjLTUzZWEtNDEwZC1hNmJiLTAyYjJmMTAzMzEwYyIgSXNzdWVyPSJJcGNBdXRoZW50aWNhdGlvbiIgSXNzdWVJbnN0YW50PSIyMDE0LTEwLTEwVDIwOjM2OjE4LjM3OVoiIHhtbG5zOnNhbWw9InVybjpvYXNpczpuYW1lczp0YzpTQU1MOjEuMDphc3NlcnRpb24iPjxzYW1sOkNvbmRpdGlvbnMgTm90QmVmb3JlPSIyMDE0LTEwLTEwVDIwOjM2OjE4LjM3OVoiIE5vdE9uT3JBZnRlcj0iMjA0NC0xMC0xMFQyMDozNjoxOC4zNzlaIj48L3NhbWw6Q29uZGl0aW9ucz48c2FtbDpBZHZpY2U+PC9zYW1sOkFkdmljZT48c2FtbDpBdHRyaWJ1dGVTdGF0ZW1lbnQ+PHNhbWw6U3ViamVjdD48c2FtbDpOYW1lSWRlbnRpZmllcj5GRjNCQjZEQzU4MzAwMDAxPC9zYW1sOk5hbWVJZGVudGlmaWVyPjwvc2FtbDpTdWJqZWN0PjxzYW1sOkF0dHJpYnV0ZSBBdHRyaWJ1dGVOYW1lPSJTQUsiIEF0dHJpYnV0ZU5hbWVzcGFjZT0iaHR0cDovL3NjaGVtYXMuaXBjb21tZXJjZS5jb20vSWRlbnRpdHkiPjxzYW1sOkF0dHJpYnV0ZVZhbHVlPkZGM0JCNkRDNTgzMDAwMDE8L3NhbWw6QXR0cmlidXRlVmFsdWU+PC9zYW1sOkF0dHJpYnV0ZT48c2FtbDpBdHRyaWJ1dGUgQXR0cmlidXRlTmFtZT0iU2VyaWFsIiBBdHRyaWJ1dGVOYW1lc3BhY2U9Imh0dHA6Ly9zY2hlbWFzLmlwY29tbWVyY2UuY29tL0lkZW50aXR5Ij48c2FtbDpBdHRyaWJ1dGVWYWx1ZT5iMTVlMTA4MS00ZGY2LTQwMTYtODM3Mi02NzhkYzdmZDQzNTc8L3NhbWw6QXR0cmlidXRlVmFsdWU+PC9zYW1sOkF0dHJpYnV0ZT48c2FtbDpBdHRyaWJ1dGUgQXR0cmlidXRlTmFtZT0ibmFtZSIgQXR0cmlidXRlTmFtZXNwYWNlPSJodHRwOi8vc2NoZW1hcy54bWxzb2FwLm9yZy93cy8yMDA1LzA1L2lkZW50aXR5L2NsYWltcyI+PHNhbWw6QXR0cmlidXRlVmFsdWU+RkYzQkI2REM1ODMwMDAwMTwvc2FtbDpBdHRyaWJ1dGVWYWx1ZT48L3NhbWw6QXR0cmlidXRlPjwvc2FtbDpBdHRyaWJ1dGVTdGF0ZW1lbnQ+PFNpZ25hdHVyZSB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC8wOS94bWxkc2lnIyI+PFNpZ25lZEluZm8+PENhbm9uaWNhbGl6YXRpb25NZXRob2QgQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy8yMDAxLzEwL3htbC1leGMtYzE0biMiPjwvQ2Fub25pY2FsaXphdGlvbk1ldGhvZD48U2lnbmF0dXJlTWV0aG9kIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvMjAwMC8wOS94bWxkc2lnI3JzYS1zaGExIj48L1NpZ25hdHVyZU1ldGhvZD48UmVmZXJlbmNlIFVSST0iI183ZTA4Yjc3Yy01M2VhLTQxMGQtYTZiYi0wMmIyZjEwMzMxMGMiPjxUcmFuc2Zvcm1zPjxUcmFuc2Zvcm0gQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwLzA5L3htbGRzaWcjZW52ZWxvcGVkLXNpZ25hdHVyZSI+PC9UcmFuc2Zvcm0+PFRyYW5zZm9ybSBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnLzIwMDEvMTAveG1sLWV4Yy1jMTRuIyI+PC9UcmFuc2Zvcm0+PC9UcmFuc2Zvcm1zPjxEaWdlc3RNZXRob2QgQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwLzA5L3htbGRzaWcjc2hhMSI+PC9EaWdlc3RNZXRob2Q+PERpZ2VzdFZhbHVlPnl3NVZxWHlUTUh5NUNjdmRXN01TV2RhMDZMTT08L0RpZ2VzdFZhbHVlPjwvUmVmZXJlbmNlPjwvU2lnbmVkSW5mbz48U2lnbmF0dXJlVmFsdWU+WG9ZcURQaUorYy9IMlRFRjNQMWpQdVBUZ0VDVHp1cFVlRXpESERwMlE2ZW92T2lhN0pkVjI1bzZjTk1vczBTTzRISStSUGRUR3hJUW9xa0paeEtoTzZHcWZ2WHFDa2NNb2JCemxYbW83NUFSWU5jMHdlZ1hiQUVVQVFCcVNmeGwxc3huSlc1ZHZjclpuUytkSThoc2lZZW4vT0VTOUdtZUpsZVd1WUR4U0xmQjZJZnd6dk5LQ0xlS0FXenBkTk9NYmpQTjJyNUJWQUhQZEJ6WmtiSGZwdUlablp1Q2l5OENvaEo1bHU3WGZDbXpHdW96VDVqVE0wU3F6bHlzeUpWWVNSbVFUQW5WMVVGMGovbEx6SU14MVJmdWltWHNXaVk4c2RvQ2IrZXpBcVJnbk5EVSs3NlVYOEZFSEN3Q2c5a0tLSzQwMXdYNXpLd2FPRGJJUFpEYitBPT08L1NpZ25hdHVyZVZhbHVlPjxLZXlJbmZvPjxvOlNlY3VyaXR5VG9rZW5SZWZlcmVuY2UgeG1sbnM6bz0iaHR0cDovL2RvY3Mub2FzaXMtb3Blbi5vcmcvd3NzLzIwMDQvMDEvb2FzaXMtMjAwNDAxLXdzcy13c3NlY3VyaXR5LXNlY2V4dC0xLjAueHNkIj48bzpLZXlJZGVudGlmaWVyIFZhbHVlVHlwZT0iaHR0cDovL2RvY3Mub2FzaXMtb3Blbi5vcmcvd3NzL29hc2lzLXdzcy1zb2FwLW1lc3NhZ2Utc2VjdXJpdHktMS4xI1RodW1icHJpbnRTSEExIj5ZREJlRFNGM0Z4R2dmd3pSLzBwck11OTZoQ2M9PC9vOktleUlkZW50aWZpZXI+PC9vOlNlY3VyaXR5VG9rZW5SZWZlcmVuY2U+PC9LZXlJbmZvPjwvU2lnbmF0dXJlPjwvc2FtbDpBc3NlcnRpb24+";
                $workflowid           = '2317000001';
                $applicationprofileid = 14644;  
                $merchantprofileid    = 'PrestaShop Global HC';
                $isTestAccount        = TRUE;
            } else {
                $identitytoken        = $this->config->get('velocitycreditcard_identitytoken');
                $workflowid           = $this->config->get('velocitycreditcard_workflowid');
                $applicationprofileid = $this->config->get('velocitycreditcard_applicationprofileid');
                $merchantprofileid    = $this->config->get('velocitycreditcard_merchantprofileid');
                $isTestAccount        = FALSE;
            }

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