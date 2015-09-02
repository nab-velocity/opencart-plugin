<?php

class ModelPaymentVelocityCreditCard extends Model {
    
    public function getMethod($address, $total) {

        $this->load->language('payment/velocitycreditcard');
        
        $method_data = array(
                'code'       => 'velocitycreditcard',
                'title'      => $this->language->get('text_title'),
                'terms'      => '',
                'sort_order' => ''
        );
        
        return $method_data;
    }
    
}