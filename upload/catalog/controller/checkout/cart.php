<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class ControllerCheckoutCart extends Controller {

    private $error = array();

    public function index() {
        $this->load->language('checkout/cart');

        if ($this->request->server['REQUEST_METHOD'] == 'GET' && isset($this->request->get['product_id'])) {

            if (isset($this->request->get['option'])) {
                $option = $this->request->get['option'];
            } else {
                $option = array();
            }

            if (isset($this->request->get['quantity'])) {
                $quantity = $this->request->get['quantity'];
            } else {
                $quantity = 1;
            }

            unset($this->session->data['shipping_methods']);
            unset($this->session->data['shipping_method']);
            unset($this->session->data['payment_methods']);
            unset($this->session->data['payment_method']);

            $this->cart->add($this->request->get['product_id'], $quantity, $option);

            $this->redirect(HTTPS_SERVER . 'index.php?route=checkout/cart');
        } elseif ($this->request->server['REQUEST_METHOD'] == 'POST') {


            if (isset($this->request->post['quantity'])) {
                if (!is_array($this->request->post['quantity'])) {
                    if (isset($this->request->post['option'])) {
                        $option = $this->request->post['option'];
                    } else {
                        $option = array();
                    }

                    $this->cart->add($this->request->post['product_id'], $this->request->post['quantity'], $option);
                } else {
                    foreach ($this->request->post['quantity'] as $key => $value) {
                        $this->cart->update($key, $value);
                    }
                }

                unset($this->session->data['shipping_methods']);
                unset($this->session->data['shipping_method']);
                unset($this->session->data['payment_methods']);
                unset($this->session->data['payment_method']);
            }

            if (isset($this->request->post['remove'])) {
                foreach (array_keys($this->request->post['remove']) as $key) {
                    $this->cart->remove($key);
                }
            }

            if (isset($this->request->post['redirect'])) {
                $this->session->data['redirect'] = $this->request->post['redirect'];
            }

            if (isset($this->request->post['quantity']) || isset($this->request->post['remove'])) {
                unset($this->session->data['shipping_methods']);
                unset($this->session->data['shipping_method']);
                unset($this->session->data['payment_methods']);
                unset($this->session->data['payment_method']);

                $this->redirect(HTTPS_SERVER . 'index.php?route=checkout/cart');
            }
        }
        $this->document->title = $this->language->get('heading_title');
        $this->document->breadcrumbs = array();
        $this->document->breadcrumbs[] = array(
            'href' => HTTP_SERVER . 'index.php?route=common/home',
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        );
        $this->document->breadcrumbs[] = array(
            'href' => HTTP_SERVER . 'index.php?route=checkout/cart',
            'text' => $this->language->get('text_basket'),
            'separator' => $this->language->get('text_separator')
        );
        if ($this->cart->hasProducts() || !empty($this->session->data['vouchers'])) {
            $this->data['heading_title'] = $this->language->get('heading_title');
            $this->data['text_select'] = $this->language->get('text_select');
            $this->data['text_sub_total'] = $this->language->get('text_sub_total');
            $this->data['text_discount'] = $this->language->get('text_discount');
            $this->data['text_weight'] = $this->language->get('text_weight');
            $this->data['column_remove'] = $this->language->get('column_remove');
            $this->data['column_image'] = $this->language->get('column_image');
            $this->data['column_name'] = $this->language->get('column_name');
            $this->data['column_model'] = $this->language->get('column_model');
            $this->data['column_quantity'] = $this->language->get('column_quantity');
            $this->data['column_price'] = $this->language->get('column_price');
            $this->data['column_total'] = $this->language->get('column_total');
            $this->data['button_update'] = $this->language->get('button_update');
            $this->data['button_shopping'] = $this->language->get('button_shopping');
            $this->data['button_checkout'] = $this->language->get('button_checkout');
            if (isset($this->error['warning'])) {
                $this->data['error_warning'] = $this->error['warning'];
            } elseif (!$this->cart->hasStock() && (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'))) {
                $this->data['error_warning'] = $this->language->get('error_stock');
            } else {
                $this->data['error_warning'] = '';
            }

            $this->data['action'] = HTTPS_SERVER . 'index.php?route=checkout/cart';
            $this->load->model('tool/seo_url');
            $this->load->model('tool/image');

            $this->data['products'] = array();
            foreach ($this->cart->getProducts() as $result) {
                $option_data = array();
                foreach ($result['option'] as $option) {
                    $option_data[] = array(
                        'name' => $option['name'],
                        'value' => $option['value']
                    );
                }

                if ($result['image']) {
                    $image = $result['image'];
                } else {
                    $image = 'no_image.jpg';
                }

                $this->data['products'][] = array(
                    'product_id' => $result['product_id'],
                    'key' => $result['key'],
                    'name' => $result['name'],
                    'model' => $result['model'],
                    'thumb' => $this->model_tool_image->resize($image, $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height')),
                    'option' => $option_data,
                    'quantity' => $result['quantity'],
                    'stock' => $result['stock'],
                    'price' => $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax'))),
                    'total' => $this->currency->format($this->tax->calculate($result['total'], $result['tax_class_id'], $this->config->get('config_tax'))),
                    'href' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/product&product_id=' . $result['product_id'])
                );
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

            if (!$this->config->get('config_customer_price')) {
                $this->data['display_price'] = TRUE;
            } elseif ($this->customer->isLogged()) {
                $this->data['display_price'] = TRUE;
            } else {
                $this->data['display_price'] = FALSE;
            }

            if ($this->config->get('config_cart_weight')) {
                $this->data['weight'] = $this->weight->format($this->cart->getWeight(), $this->config->get('config_weight_class'));
            } else {
                $this->data['weight'] = FALSE;
            }

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

            $this->load->model('setting/extension');

            $sort_order = array();

            $results = $this->model_setting_extension->getExtensions('total');

            $results[] = array(
                'extension_id' => 9999,
                'type' => 'total',
                'key' => 'voucher'
            );

            $sortOrderArray = [];
            foreach ($results as $key => $value) {

                if ($value['key'] == 'voucher') {
                    // Make position total before
                    if (isset($sortOrderArray['total']) && $sortOrderArray['total'] > 1) {
                        $sort_order[$key] = $sortOrderArray['total'] - 1;
                    } else {
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

            $this->data['totals'] = $total_data;
            if (isset($this->session->data['redirect'])) {
                $this->data['continue'] = $this->session->data['redirect'];
                unset($this->session->data['redirect']);
            } else {
                $this->data['continue'] = HTTP_SERVER . 'index.php?route=common/home';
            }

            $this->data['checkout'] = HTTPS_SERVER . 'index.php?route=checkout/shipping';

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/cart.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/checkout/cart.tpl';
            } else {
                $this->template = 'default/template/checkout/cart.tpl';
            }

            $this->children = array(
                'common/column_right',
                'common/column_left',
                'common/footer',
                'common/header'
            );

            $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
        } else {
            $this->data['heading_title'] = $this->language->get('heading_title');
            $this->data['text_error'] = $this->language->get('text_error');
            $this->data['button_continue'] = $this->language->get('button_continue');
            $this->data['continue'] = HTTP_SERVER . 'index.php?route=common/home';
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/error/not_found.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/error/not_found.tpl';
            } else {
                $this->template = 'default/template/error/not_found.tpl';
            }
            $this->children = array(
                'common/column_right',
                'common/column_left',
                'common/footer',
                'common/header'
            );
            $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
        }
    }

}
