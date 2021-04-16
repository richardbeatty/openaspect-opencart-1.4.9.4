<?php

class ControllerModuleCart extends Controller {

    protected function index() {
        $this->language->load('module/cart');

        $this->load->model('tool/seo_url');

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_subtotal'] = $this->language->get('text_subtotal');
        $this->data['text_empty'] = $this->language->get('text_empty');
        $this->data['text_remove'] = $this->language->get('text_remove');
        $this->data['text_confirm'] = $this->language->get('text_confirm');
        $this->data['text_view'] = $this->language->get('text_view');
        $this->data['text_checkout'] = $this->language->get('text_checkout');

        $this->data['view'] = HTTP_SERVER . 'index.php?route=checkout/cart';
        $this->data['checkout'] = HTTPS_SERVER . 'index.php?route=checkout/shipping';

        $this->data['products'] = array();

        foreach ($this->cart->getProducts() as $result) {
            $option_data = array();

            foreach ($result['option'] as $option) {
                $option_data[] = array(
                    'name' => $option['name'],
                    'value' => $option['value']
                );
            }

            $this->data['products'][] = array(
                'key' => $result['key'],
                'name' => $result['name'],
                'option' => $option_data,
                'quantity' => $result['quantity'],
                'stock' => $result['stock'],
                'price' => $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax'))),
                'href' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/product&product_id=' . $result['product_id']),
            );
        }

        if (!$this->config->get('config_customer_price')) {
            $this->data['display_price'] = TRUE;
        } elseif ($this->customer->isLogged()) {
            $this->data['display_price'] = TRUE;
        } else {
            $this->data['display_price'] = FALSE;
        }

        // Gift Voucher
        $this->data['vouchers'] = array();

        if (!empty($this->session->data['vouchers'])) {
            foreach ($this->session->data['vouchers'] as $key => $voucher) {
                $this->data['vouchers'][] = array(
                    'key' => $key,
                    'description' => $voucher['description'],
                    'amount' => $this->currency->format($voucher['amount'], $this->session->data['currency']),
                    'remove' => '#', //$this->url->link('checkout/cart', 'remove=' . $key)
                    'quantity' => 1
                );
            }
        }

        // $total_data = array();
        // $total = 0;
        // $taxes = $this->cart->getTaxes();

        $total_data = array();

        $totals = array();
        $taxes = $this->cart->getTaxes();
        $total = 0;

        // Because __call can not keep var references so we put them into an array. 			
        $total_data = array(
            'totals' => &$totals,
            'taxes' => &$taxes,
            'total' => &$total
        );

        // $this->load->model('checkout/extension');
        $this->load->model('setting/extension');

        $sort_order = array();

        $results = $this->model_checkout_extension->getExtensions('total');
        $results[] = array(
            'extension_id' => 9999,
            'type' => 'total',
            'key' => 'voucher'
        );
        
        $sortOrderArray = [];
        foreach ($results as $key => $value) {
            
            if ($value['key'] == 'voucher') {
                // Make position total before
                if(isset($sortOrderArray['total']) && $sortOrderArray['total'] > 1){    
                    $sort_order[$key] = $sortOrderArray['total'] - 1;
                }else{
                    $sort_order[$key] = 999;
                }
                
            } else {
                $sort_order[$key] = $this->config->get($value['key'] . '_sort_order');
                $sortOrderArray[$value['key']] = $this->config->get($value['key'] . '_sort_order');
            }
        }
        
        array_multisort($sort_order, SORT_ASC, $results);
        
        foreach ($results as $result) {
            
            $this->load->model('total/' . $result['key']);

            $this->{'model_total_' . $result['key']}->getTotal($total_data, $total, $taxes);
        }
        

        $sort_order = array();

        foreach ($total_data as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $total_data);
        
        $this->data['totals'] = $total_data;

        $this->data['ajax'] = $this->config->get('cart_ajax');

        $this->id = 'cart';

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/cart.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/module/cart.tpl';
        } else {
            $this->template = 'default/template/module/cart.tpl';
        }

        $this->render();
    }

    public function callback() {
        $this->language->load('module/cart');

        $this->load->model('tool/seo_url');

        unset($this->session->data['shipping_methods']);
        unset($this->session->data['shipping_method']);
        unset($this->session->data['payment_methods']);
        unset($this->session->data['payment_method']);

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {

            if (isset($this->request->post['remove'])) {
                $result = explode('_', $this->request->post['remove']);
                $this->cart->remove(trim($result[1]));
            } else {
                if (isset($this->request->post['option'])) {
                    $option = $this->request->post['option'];
                } else {
                    $option = array();
                }

                $this->cart->add($this->request->post['product_id'], $this->request->post['quantity'], $option);
            }
        }

        $output = '<table cellpadding="2" cellspacing="0" style="width: 100%;">';

        if ($this->cart->getProducts()) {

            foreach ($this->cart->getProducts() as $product) {
                $output .= '<tr>';
                $output .= '<td width="1" valign="top" align="left"><span class="cart_remove" id="remove_ ' . $product['key'] . '" />&nbsp;</span></td><td width="1" valign="top" align="right">' . $product['quantity'] . '&nbsp;x&nbsp;</td>';
                $output .= '<td align="left" valign="top"><a href="' . $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/product&product_id=' . $product['product_id']) . '">' . $product['name'] . '</a>';
                $output .= '<div>';

                foreach ($product['option'] as $option) {
                    $output .= ' - <small style="color: #999;">' . $option['name'] . ' ' . $option['value'] . '</small><br />';
                }

                $output .= '</div></td>';
                $output .= '</tr>';
            }

            $output .= '</table>';
            $output .= '<br />';

            $total = 0;
            $taxes = $this->cart->getTaxes();

            $this->load->model('checkout/extension');

            $sort_order = array();

            $view = HTTP_SERVER . 'index.php?route=checkout/cart';
            $checkout = HTTPS_SERVER . 'index.php?route=checkout/shipping';

            $results = $this->model_checkout_extension->getExtensions('total');

            foreach ($results as $key => $value) {
                $sort_order[$key] = $this->config->get($value['key'] . '_sort_order');
            }

            array_multisort($sort_order, SORT_ASC, $results);

            foreach ($results as $result) {
                $this->load->model('total/' . $result['key']);

                $this->{'model_total_' . $result['key']}->getTotal($total_data, $total, $taxes);
            }

            $sort_order = array();

            foreach ($total_data as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $total_data);

            $output .= '<table cellpadding="0" cellspacing="0" align="right" style="display:inline-block;">';
            foreach ($total_data as $total) {
                $output .= '<tr>';
                $output .= '<td align="right"><span class="cart_module_total"><b>' . $total['title'] . '</b></span></td>';
                $output .= '<td align="right"><span class="cart_module_total">' . $total['text'] . '</span></td>';
                $output .= '</tr>';
            }
            $output .= '</table>';
            $output .= '<div style="padding-top:5px;text-align:center;clear:both;"><a href="' . $view . '">' . $this->language->get('text_view') . '</a> | <a href="' . $checkout . '">' . $this->language->get('text_checkout') . '</a></div>';
        } else {
            $output .= '<div style="text-align: center;">' . $this->language->get('text_empty') . '</div>';
        }

        $this->response->setOutput($output, $this->config->get('config_compression'));
    }

}

?>