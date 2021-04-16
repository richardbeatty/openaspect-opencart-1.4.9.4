<?php

class ModelTotalSubTotal extends Model {

    public function getTotal(&$total_data, &$total, &$taxes) {
        if ($this->config->get('sub_total_status')) {
            $this->load->language('total/sub_total');

            $sub_total = $this->cart->getSubTotal();

            if (!empty($this->session->data['vouchers'])) {
                foreach ($this->session->data['vouchers'] as $voucher) {
                    $sub_total += $voucher['amount'];
                }
            }

            $total_data[] = array(
                'title' => $this->language->get('text_sub_total'),
                'text' => $this->currency->format($sub_total),
                'value' => $sub_total,
                'sort_order' => $this->config->get('sub_total_sort_order')
            );

            $total += $sub_total;
        }
    }

}

?>