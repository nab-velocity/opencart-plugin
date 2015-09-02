<?php
/*
 * Model class use to provide data to controller
 * 
 */
class ModelPaymentVelocityCreditCard extends Model {
    
    public function getShipping($order_id) {
        try {
            $query = $this->db->query("SELECT SUM(value) as shipping from " . DB_PREFIX . "order_total where order_id =" . $order_id . " AND code = 'shipping'");
            if (!isset($query->row['shipping'])) {
                throw new Exception('No Shipping', '500');
            }
            return $query->row['shipping'];
        } catch (Exception $e) {
            return 0;
        }
    }
    
    public function addOrderHistory($order_id, $order_status_id, $comment, $notify = false) {
        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$order_status_id . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
    }
    
}