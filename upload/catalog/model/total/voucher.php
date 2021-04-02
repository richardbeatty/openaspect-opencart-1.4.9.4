<?php

class ModelTotalVoucher extends Model {

    public function addVoucher($order_id, $data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "voucher SET order_id = '" . (int) $order_id . "', code = '" . $this->db->escape($data['code']) . "', from_name = '" . $this->db->escape($data['from_name']) . "', from_email = '" . $this->db->escape($data['from_email']) . "', to_name = '" . $this->db->escape($data['to_name']) . "', to_email = '" . $this->db->escape($data['to_email']) . "', voucher_theme_id = '" . (int) $data['voucher_theme_id'] . "', message = '" . $this->db->escape($data['message']) . "', amount = '" . (float) $data['amount'] . "', status = '1', date_added = NOW()");

        return $this->db->getLastId();
    }

    public function disableVoucher($order_id) {
        $this->db->query("UPDATE " . DB_PREFIX . "voucher SET status = '0' WHERE order_id = '" . (int) $order_id . "'");
    }

    public function getVoucher($code) {
        $status = true;
        $voucher_query = $this->db->query("SELECT *, vtd.name AS theme FROM " . DB_PREFIX . "voucher v LEFT JOIN " . DB_PREFIX . "voucher_theme vt ON (v.voucher_theme_id = vt.voucher_theme_id) LEFT JOIN " . DB_PREFIX . "voucher_theme_description vtd ON (vt.voucher_theme_id = vtd.voucher_theme_id) WHERE v.code = '" . $this->db->escape($code) . "' AND vtd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND v.status = '1'");
        
        if ($voucher_query->num_rows) {
            if ($voucher_query->row['order_id']) {
                $implode = array();
                $orderid = (int) $voucher_query->row['order_id'];
                $order_query = $this->db->query("SELECT order_id FROM `" . DB_PREFIX . "order` WHERE order_id = '" . $orderid . "'");
               
                if (!$order_query->num_rows) {
                    $status = false;
                }
                $order_voucher_query = $this->db->query("SELECT order_voucher_id FROM `" . DB_PREFIX . "order_voucher` WHERE order_id = '" . (int) $voucher_query->row['order_id'] . "' AND voucher_id = '" . (int) $voucher_query->row['voucher_id'] . "'");
                if (!$order_voucher_query->num_rows) {
                    $status = false;
                }
                
            }

            $voucher_history_query = $this->db->query("SELECT SUM(amount) AS total FROM `" . DB_PREFIX . "voucher_history` vh WHERE vh.voucher_id = '" . (int) $voucher_query->row['voucher_id'] . "' GROUP BY vh.voucher_id");
            
            if ($voucher_history_query->num_rows) {
                $amount = $voucher_query->row['amount'] + $voucher_history_query->row['total'];
            } else {
                $amount = $voucher_query->row['amount'];
            }
            $status = true;
            
            if ($amount <= 0) {
                $status = false;
            }
        } else {

            $status = false;
        }

        if ($status) {

            return array(
                'voucher_id' => $voucher_query->row['voucher_id'],
                'code' => $voucher_query->row['code'],
                'from_name' => $voucher_query->row['from_name'],
                'from_email' => $voucher_query->row['from_email'],
                'to_name' => $voucher_query->row['to_name'],
                'to_email' => $voucher_query->row['to_email'],
                'voucher_theme_id' => $voucher_query->row['voucher_theme_id'],
                'theme' => $voucher_query->row['theme'],
                'message' => $voucher_query->row['message'],
                'image' => $voucher_query->row['image'],
                'amount' => $amount,
                'status' => $voucher_query->row['status'],
                'date_added' => $voucher_query->row['date_added']
            );
        }

        return $status;
    }

    public function getTotal(&$total_data, &$total, &$taxes) {

        if (isset($this->session->data['voucher'])) {
            $get_last_array = end($total_data);

            $voucher_info = $this->getVoucher($this->session->data['voucher']);
            
            if ($voucher_info) {
//                $amount = min($voucher_info['amount'], $total['total']);
                if ($voucher_info['amount'] > $total) {
                    $amount = $total;
                } else {
                    $amount = $voucher_info['amount'];
                }

                if ($amount > 0) {
                    $total_data[] = array(
                        'title' => sprintf('Gift Certificate (%s):', $this->session->data['voucher']),
                        'text' => $this->currency->getSymbolLeft($this->session->data['currency']).'-'. number_format($amount, 2),
                        'value' => -$amount,
                        'sort_order' => $get_last_array['sort_order'] + 1
                    );
                    $total -= $amount;
                } 
            }
            
        }
    }
 
    public function confirm($order_info, $order_total) {
        
        $code = '';
        $voucher_code = '';
        $ordertotalValue = '';
        if(isset($order_total->rows)){
            foreach($order_total->rows as $totals){
                $start = strpos($totals['title'], '(') + 1;
                $end = strrpos($totals['title'], ')');
                $code = substr($totals['title'], $start, $end - $start);

                if(trim($this->session->data['voucher']) == trim($code)){
                    $voucher_code = $code;
                    $ordertotalValue = $totals['value'];
                    continue;
                }
            }
        }

        if ($voucher_code && $ordertotalValue) {
            $voucher_info = $this->getVoucher($voucher_code);

            if ($voucher_info) {
                $this->db->query("INSERT INTO `" . DB_PREFIX . "voucher_history` SET voucher_id = '" . (int) $voucher_info['voucher_id'] . "', order_id = '" . (int) $order_info->row['order_id'] . "', amount = '" . (float) $ordertotalValue . "', date_added = NOW()");
            } else {
                return $this->config->get('config_fraud_status_id');
            }
        }
    }

    public function unconfirm($order_id) {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "voucher_history` WHERE order_id = '" . (int) $order_id . "'");
    }

}
