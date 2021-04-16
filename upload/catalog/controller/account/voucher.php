<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class ControllerAccountVoucher extends Controller {

    private $error = array();

    public function index() {
        $this->load->language('account/voucher');

        $this->load->model('setting/setting');
        $option_settings = $this->model_setting_setting->getSetting();

        $this->document->setTitle($this->language->get('heading_title'));

        if (!isset($this->session->data['vouchers'])) {
            $this->session->data['vouchers'] = array();
        }
        $this->load->model('account/voucher_theme');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            $this->session->data['vouchers'][mt_rand()] = array(
                'description' => sprintf($this->language->get('text_for'), $this->currency->format($this->request->post['amount'], $this->session->data['currency'], 1.0), $this->request->post['to_name']),
                'to_name' => $this->request->post['to_name'],
                'to_email' => $this->request->post['to_email'],
                'from_name' => $this->request->post['from_name'],
                'from_email' => $this->request->post['from_email'],
                'voucher_theme_id' => $this->request->post['voucher_theme_id'],
                'message' => $this->request->post['message'],
                'amount' => $this->currency->convert(number_format($this->request->post['amount'], 2), $this->session->data['currency'], $this->config->get('config_currency'), true, true)
            );

            //$this->response->redirect($this->url->link('account/voucher/success'));

            $this->redirect(HTTPS_SERVER . 'index.php?route=account/voucher/success');
        }
        
        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => HTTPS_SERVER . 'index.php?route=common/home'
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_account'),
            'href' => HTTPS_SERVER . 'index.php?route=account/account'
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_voucher'),
            'href' => HTTPS_SERVER . 'index.php?route=account/voucher'
        );

        $config_voucher_min = $option_settings['config_voucher_min'];
        $config_voucher_max = $option_settings['config_voucher_max'];

        $this->data['config_voucher_min'] = trim($config_voucher_min);
        $this->data['config_voucher_max'] = trim($config_voucher_max);

        $this->data['help_amount'] = sprintf($this->language->get('help_amount'), $this->currency->format($config_voucher_min, $this->session->data['currency'], true, true), $this->currency->format($config_voucher_max, $this->session->data['currency'], true, true));

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->error['to_name'])) {
            $this->data['error_to_name'] = $this->error['to_name'];
        } else {
            $this->data['error_to_name'] = '';
        }

        if (isset($this->error['to_email'])) {
            $this->data['error_to_email'] = $this->error['to_email'];
        } else {
            $this->data['error_to_email'] = '';
        }

        if (isset($this->error['from_name'])) {
            $this->data['error_from_name'] = $this->error['from_name'];
        } else {
            $this->data['error_from_name'] = '';
        }

        if (isset($this->error['from_email'])) {
            $this->data['error_from_email'] = $this->error['from_email'];
        } else {
            $this->data['error_from_email'] = '';
        }

        if (isset($this->error['theme'])) {
            $this->data['error_theme'] = $this->error['theme'];
        } else {
            $this->data['error_theme'] = '';
        }

        if (isset($this->error['amount'])) {
            $this->data['error_amount'] = $this->error['amount'];
        } else {
            $this->data['error_amount'] = '';
        }

        //$this->load->model('account/voucher_theme');

        $this->data['voucher_themes'] = $this->model_account_voucher_theme->getVoucherThemes();

        $this->data['action'] = HTTPS_SERVER . 'index.php?route=account/voucher/';
        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['text_your_details'] = $this->language->get('text_description');
        $this->data['entry_from_name'] = $this->language->get('entry_from_name');
        $this->data['entry_from_email'] = $this->language->get('entry_from_email');
        $this->data['entry_to_name'] = $this->language->get('entry_to_name');
        $this->data['entry_to_email'] = $this->language->get('entry_to_email');
        $this->data['entry_theme'] = $this->language->get('entry_theme');
        $this->data['entry_message'] = $this->language->get('entry_message');
        $this->data['entry_amount'] = $this->language->get('entry_amount');
        $this->data['entry_status'] = $this->language->get('entry_status');
        $this->data['text_agree'] = $this->language->get('text_agree');
        $this->data['button_back'] = "Go Back";
        $this->data['button_continue'] = "Save";

        if (isset($this->request->post['to_name'])) {

            $this->data['to_name'] = $this->request->post['to_name'];
        } else {
            $this->data['to_name'] = '';
        }

        if (isset($this->request->post['to_email'])) {
            $this->data['to_email'] = $this->request->post['to_email'];
        } else {
            $this->data['to_email'] = '';
        }

        if (isset($this->request->post['from_name'])) {
            $this->data['from_name'] = $this->request->post['from_name'];
        } elseif ($this->customer->isLogged()) {
            $this->data['from_name'] = $this->customer->getFirstName() . ' ' . $this->customer->getLastName();
        } else {
            $this->data['from_name'] = '';
        }

        if (isset($this->request->post['from_email'])) {
            $this->data['from_email'] = $this->request->post['from_email'];
        } elseif ($this->customer->isLogged()) {
            $this->data['from_email'] = $this->customer->getEmail();
        } else {
            $this->data['from_email'] = '';
        }

        $vtheme = array();

        if (isset($this->request->post['voucher_theme_id'])) {
            $this->data['voucher_theme_id'] = $this->request->post['voucher_theme_id'];
        } else {
            $this->data['voucher_theme_id'] = '';
        }

        if (isset($this->request->post['message'])) {
            $this->data['message'] = $this->request->post['message'];
        } else {
            $this->data['message'] = '';
        }

        if (isset($this->request->post['amount'])) {
            $this->data['amount'] = $this->request->post['amount'];
        } else {
            $this->data['amount'] = $this->currency->format($this->config->get('config_voucher_min'), $this->config->get('config_currency'), false, false);
        }


        if (isset($this->request->post['agree'])) {

            $this->data['agree'] = $this->request->post['agree'];
        } else {
            $this->data['agree'] = false;
        }
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/voucher.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/account/voucher.tpl';
        } else {
            $this->template = 'default/template/account/voucher.tpl';
        }
        $this->children = array(
            'common/column_right',
            'common/footer',
            'common/column_left',
            'common/header'
        );

        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
        //	$this->response->setOutput($this->load->view('account/voucher', $data));
    }

    public function success() {

        $this->load->language('account/voucher');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['breadcrumbs'] = array();
        $this->data['text_message'] = $this->language->get('text_message');
        ;
        $this->data['button_continue'] = "Continue";
        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => HTTPS_SERVER . 'index.php?route=common/home'
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => HTTPS_SERVER . 'index.php?route=account/voucher'
        );

        $this->data['continue'] = HTTPS_SERVER . 'index.php?route=checkout/cart';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/success.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/account/success.tpl';
        } else {
            $this->template = 'default/template/account/success.tpl';
        }
        $this->children = array(
            'common/column_right',
            'common/footer',
            'common/column_left',
            'common/header'
        );
        $this->template = 'default/template/common/success.tpl';
        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
        //$this->response->setOutput($this->load->view('', $data));
        //	$this->redirect(HTTPS_SERVER . 'index.php?route=account/voucher');
    }

    protected function validate() {
        
        if ((strlen($this->request->post['to_name']) < 1) || (strlen($this->request->post['to_name']) > 64)) {
            $this->error['to_name'] = $this->language->get('error_to_name');
        }

        if ((strlen($this->request->post['to_email']) > 96) || !filter_var($this->request->post['to_email'], FILTER_VALIDATE_EMAIL)) {
            $this->error['to_email'] = $this->language->get('error_email');
        }
        
        if ((strlen($this->request->post['from_name']) < 1) || (strlen($this->request->post['from_name']) > 64)) {
            $this->error['from_name'] = $this->language->get('error_from_name');
        }
        
        if ((strlen($this->request->post['from_email']) > 96) || !filter_var($this->request->post['from_email'], FILTER_VALIDATE_EMAIL)) {
            $this->error['from_email'] = $this->language->get('error_email');
        }

        if (!isset($this->request->post['voucher_theme_id'])) {
            $this->error['theme'] = $this->language->get('error_theme');
        }

        if (isset($this->request->post['amount']) && $this->request->post['amount'] < trim($this->request->post['config_voucher_min'])) {
            $this->error['amount'] = sprintf($this->language->get('error_amount'), $this->currency->format($this->config->get('config_voucher_min'), $this->session->data['currency'], true, true), $this->currency->format($this->config->get('config_voucher_max'), $this->session->data['currency'], true, true));
        }

        if (isset($this->request->post['amount']) && $this->request->post['amount'] > trim($this->request->post['config_voucher_max'])) {
            $this->error['amount'] = sprintf($this->language->get('error_amount'), $this->currency->format($this->config->get('config_voucher_min'), $this->session->data['currency'], true, true), $this->currency->format($this->config->get('config_voucher_max'), $this->session->data['currency'], true, true));
        }

        if ((!isset($this->request->post['amount'])) || ($this->request->post['amount'] == 0 )) {
            $this->error['amount'] = sprintf($this->language->get('error_amount'), $this->currency->format($this->config->get('config_voucher_min'), $this->session->data['currency'], true, true), $this->currency->format($this->config->get('config_voucher_max'), $this->session->data['currency'], true, true));
        }

        if (!isset($this->request->post['agree'])) {
            $this->error['warning'] = $this->language->get('error_agree');
        }
        return !$this->error;
    }


}
