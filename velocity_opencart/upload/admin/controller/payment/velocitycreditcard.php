<?php
/*
 * Description : admin payment controller for velocity gateway
 * get data from language file and model and display on view
 * configuartion and listed with other payment method at admin 
 * payments options.
 */
class ControllerPaymentVelocityCreditCard extends Controller {

    private $error = array();
    /*
     * index method is default method called as constructor to collect 
     * all detail from language file and get form data and set in view of 
     * admin configuration form which is save the velocity credentials and update.
     */
    public function index() {
        $this->load->language('payment/velocitycreditcard'); // load language file data

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting'); // load setting model
        
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
                $this->model_setting_setting->editSetting('velocitycreditcard', $this->request->post);
                $this->session->data['success'] = $this->language->get('text_success');
                $this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $data['heading_title']              = $this->language->get('heading_title');

        $data['text_edit']                  = $this->language->get('text_edit');
        $data['text_enabled']               = $this->language->get('text_enabled');
        $data['text_disabled']              = $this->language->get('text_disabled');
        $data['text_yes']                   = $this->language->get('text_yes');
        $data['text_no']                    = $this->language->get('text_no');

        $data['entry_identitytoken']        = $this->language->get('entry_identitytoken');
        $data['entry_workflowid']           = $this->language->get('entry_workflowid');
        $data['entry_applicationprofileid'] = $this->language->get('entry_applicationprofileid');
        $data['entry_merchantprofileid']    = $this->language->get('entry_merchantprofileid');
        $data['entry_test']                 = $this->language->get('entry_test');
        $data['entry_status']               = $this->language->get('entry_status');

        $data['button_save']                = $this->language->get('button_save');
        $data['button_cancel']              = $this->language->get('button_cancel');

        $data['tab_general']                = $this->language->get('tab_general');

        if (isset($this->error['warning'])) {
                $data['error_warning'] = $this->error['warning'];
        } else {
                $data['error_warning'] = '';
        }

        if (isset($this->error['identitytoken'])) {
                $data['error_identitytoken'] = $this->error['identitytoken'];
        } else {
                $data['error_identitytoken'] = '';
        }
        if (isset($this->error['workflowid'])) {
                $data['error_workflowid'] = $this->error['workflowid'];
        } else {
                $data['error_workflowid'] = '';
        }

        if (isset($this->error['applicationprofileid'])) {
                $data['error_applicationprofileid'] = $this->error['applicationprofileid'];
        } else {
                $data['error_applicationprofileid'] = '';
        }
        if (isset($this->error['merchantprofileid'])) {
                $data['error_merchantprofileid'] = $this->error['merchantprofileid'];
        } else {
                $data['error_merchantprofileid'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_payment'),
                'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('payment/velocitycreditcard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['action'] = $this->url->link('payment/velocitycreditcard', 'token=' . $this->session->data['token'], 'SSL');

        $data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

        if (isset($this->request->post['velocitycreditcard_identitytoken'])) {
                $data['velocitycreditcard_identitytoken'] = $this->request->post['velocitycreditcard_identitytoken'];
        } else {
                $data['velocitycreditcard_identitytoken'] = $this->config->get('velocitycreditcard_identitytoken');
        }
        
        if (isset($this->request->post['velocitycreditcard_workflowid'])) {
                $data['velocitycreditcard_workflowid'] = $this->request->post['velocitycreditcard_workflowid'];
        } else {
                $data['velocitycreditcard_workflowid'] = $this->config->get('velocitycreditcard_workflowid');
        }

        if (isset($this->request->post['velocitycreditcard_applicationprofileid'])) {
                $data['velocitycreditcard_applicationprofileid'] = $this->request->post['velocitycreditcard_applicationprofileid'];
        } else {
                $data['velocitycreditcard_applicationprofileid'] = $this->config->get('velocitycreditcard_applicationprofileid');
        }
        
        if (isset($this->request->post['velocitycreditcard_merchantprofileid'])) {
                $data['velocitycreditcard_merchantprofileid'] = $this->request->post['velocitycreditcard_merchantprofileid'];
        } else {
                $data['velocitycreditcard_merchantprofileid'] = $this->config->get('velocitycreditcard_merchantprofileid');
        }

        if (isset($this->request->post['velocitycreditcard_test'])) {
                $data['velocitycreditcard_test'] = $this->request->post['velocitycreditcard_test'];
        } else {
                $data['velocitycreditcard_test'] = $this->config->get('velocitycreditcard_test');
        }

        if (isset($this->request->post['velocitycreditcard_status'])) {
                $data['velocitycreditcard_status'] = $this->request->post['velocitycreditcard_status'];
        } else {
                $data['velocitycreditcard_status'] = $this->config->get('velocitycreditcard_status');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('payment/velocitycreditcard.tpl', $data)); // set response to view file.
    }

    /*
     * validate the data from get to form.
     */
    protected function validate() {
        if (!$this->user->hasPermission('modify', 'payment/velocitycreditcard')) {
                $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['velocitycreditcard_identitytoken']) {
                $this->error['identitytoken'] = $this->language->get('error_identitytoken');
        }
        if (!$this->request->post['velocitycreditcard_workflowid']) {
                $this->error['workflowid'] = $this->language->get('error_workflowid');
        }
        if (!$this->request->post['velocitycreditcard_applicationprofileid']) {
                $this->error['applicationprofileid'] = $this->language->get('error_applicationprofileid');
        }
        if (!$this->request->post['velocitycreditcard_merchantprofileid']) {
                $this->error['merchantprofileid'] = $this->language->get('error_merchantprofileid');
        }

        return !$this->error;
    }
    
    /*
     * install method call at the time of install the module and create custom table.
     */
    public function install() {
        $this->db->query("
                        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "velocity_transactions` (
                          `id`                 int(11)      NOT NULL,
                          `transaction_id`     varchar(255) NOT NULL,
                          `transaction_status` varchar(100) NOT NULL,
                          `order_id`           varchar(10)  NOT NULL,
                          `request_obj`        text         NOT NULL,
                          `response_obj`       text         NOT NULL,
                          PRIMARY KEY (id)
                        ) DEFAULT COLLATE=utf8_general_ci;");
        
    } 
    
    /*
     * orderAction is the method call default for order
     * and get detail for custom action and we are perform
     * refund form this method.
     */
    public function orderAction() {

        $this->load->language('payment/velocitycreditcard');

        $data['text_Refund_heading']  = $this->language->get('text_Refund_heading');
        $data['text_refund_amount']   = $this->language->get('text_refund_amount');
        $data['text_refund_shipping'] = $this->language->get('text_refund_shipping');
        $data['text_confirm_refund']  = $this->language->get('text_confirm_refund');
        $data['order_id']             = $this->request->get['order_id'];
        $data['token']                = $this->request->get['token'];

        return $this->load->view('payment/velocityordercreditcard.tpl', $data);
    }
    
    /*
     * refund method call by ajax form view of the orderAction.
     */
    public function refund() {
        
        $this->load->model('payment/velocitycreditcard');
        require_once '././../sdk/Velocity.php';
        
        $json['error'] = '';
        $json['success'] = '';
        
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
        } catch (Exception $e) { echo $e->getMessage();
            $json['error'] .= $e->getMessage();
        }
        
        $this->load->model('sale/order');
        $order_info = $this->model_sale_order->getOrder($this->request->post['order_id']);
        $order_shipping = $this->model_payment_velocitycreditcard->getShipping($this->request->post['order_id']);
        $sub_total = (float)$order_info['total'] - (float)$order_shipping;
        if((float)$sub_total >= (float)$this->request->post['amount']){
            
            try {
                $query = $this->db->query("select transaction_id from " . DB_PREFIX . "velocity_transactions where order_id =" . $this->request->post['order_id'] );
                if (!isset($query->row['transaction_id'])) {
                    throw new Exception('Transaction id not found for the Order', '500');
                }
                $transaction_id = $query->row['transaction_id'];

                $refund_amount = $this->request->post['shipping'] === 'true' ? $this->request->post['amount'] + $order_shipping : $this->request->post['amount'];

                // request for refund
                $response = $velocityProcessor->returnById(array(  
                    'amount'        => $refund_amount,
                    'TransactionId' => $transaction_id
                ));

                if ( is_array($response) && !empty($response) && isset($response['Status']) && $response['Status'] == 'Successful') {
                    $xml = VelocityXmlCreator::returnByIdXML(number_format($refund_amount, 2, '.', ''), $transaction_id);  // got ReturnById xml object.  

                    $req = $xml->saveXML();
                    /* save the returnbyid response into 'zen_velocity_transactions' custom table.*/ 
                    $this->db->query("insert into " . DB_PREFIX . "velocity_transactions (transaction_id, transaction_status, order_id, request_obj, response_obj) values('".$response['TransactionId']."', '".$response['TransactionState']."', '".$this->request->post['order_id']."', '".serialize($req)."', '".serialize($response)."')");
                    $json['success'] .= 'Refund has been done successfully, txnid : ' . $response['TransactionId'];

                    //order status pending code is 1
                    $this->model_payment_velocitycreditcard->addOrderHistory($this->request->post['order_id'], 11, "Velocity Txn id" . $response['TransactionId'] . "<br>Txn status is " . $response['TransactionState'] . " <br> Amount is " . $refund_amount);
                } else if (is_array($response) && !empty($response)) {
                    $json['error'] .= $response['StatusMessage'];
                } else if (is_string($response)) {
                    $json['error'] .= $response;
                } else {
                    $json['error'] .= 'Unknown Error please contact the site admin';
                }

            } catch(Exception $e) {
                $json['error'] .= $e->getMessage();
            }
                    
        } else {
            $json['error'] .= 'Refund amount can not be greater than ' . $sub_total . '  + shipping.';
        }

        $this->response->setOutput(json_encode($json));
        
    }
    
}