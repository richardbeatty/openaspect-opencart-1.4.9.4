<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
class ControllerCheckoutSuccess extends Controller { 
	public function index() { 
		if (isset($this->session->data['order_id'])) {
			$this->session->data['order_id'];
			$this->load->model('account/voucher');
			$vouchers = $this->session->data['vouchers']; 
			foreach($vouchers as $voucher)
			{
			$voucher_new_id = $this->model_account_voucher->addVoucher($this->session->data['order_id'], $this->session->data['vouchers']);
			$this->model_account_voucher->send_voucher_email($voucher_new_id);
			}
			$this->cart->clear();
			
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['guest']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);	
			unset($this->session->data['coupon']);
			unset($this->session->data['vouchers']);
			unset($this->session->data['voucher']);
		}	
		//add voucher here 							   
		$this->language->load('checkout/success');
		$this->document->title = $this->language->get('heading_title');
		$this->document->breadcrumbs = array(); 
      	$this->document->breadcrumbs[] = array(
        	'href'      => HTTP_SERVER . 'index.php?route=common/home',
        	'text'      => $this->language->get('text_home'),
        	'separator' => FALSE
      	); 
      	$this->document->breadcrumbs[] = array(
        	'href'      => HTTP_SERVER . 'index.php?route=checkout/cart',
        	'text'      => $this->language->get('text_basket'),
        	'separator' => $this->language->get('text_separator')
      	);
		if ($this->customer->isLogged()) {
			$this->document->breadcrumbs[] = array(
				'href'      => HTTP_SERVER . 'index.php?route=checkout/shipping',
				'text'      => $this->language->get('text_shipping'),
				'separator' => $this->language->get('text_separator')
			);
	
			$this->document->breadcrumbs[] = array(
				'href'      => HTTP_SERVER . 'index.php?route=checkout/payment',
				'text'      => $this->language->get('text_payment'),
				'separator' => $this->language->get('text_separator')
			);
	
			$this->document->breadcrumbs[] = array(
				'href'      => HTTP_SERVER . 'index.php?route=checkout/confirm',
				'text'      => $this->language->get('text_confirm'),
				'separator' => $this->language->get('text_separator')
			);
		} else {
			$this->document->breadcrumbs[] = array(
				'href'      => HTTP_SERVER . 'index.php?route=checkout/guest',
				'text'      => $this->language->get('text_guest'),
				'separator' => $this->language->get('text_separator')
			);
	
			$this->document->breadcrumbs[] = array(
				'href'      => HTTP_SERVER . 'index.php?route=checkout/guest/confirm',
				'text'      => $this->language->get('text_confirm'),
				'separator' => $this->language->get('text_separator')
			);			
		}
		
      	$this->document->breadcrumbs[] = array(
        	'href'      => HTTP_SERVER . 'index.php?route=checkout/success',
        	'text'      => $this->language->get('text_success'),
        	'separator' => $this->language->get('text_separator')
      	);
		
    	$this->data['heading_title'] = $this->language->get('heading_title');

    	$this->data['text_message'] = sprintf($this->language->get('text_message'), HTTPS_SERVER . 'index.php?route=account/account', HTTPS_SERVER . 'index.php?route=account/history', HTTP_SERVER . 'index.php?route=information/contact');

    	$this->data['button_continue'] = $this->language->get('button_continue');

    	$this->data['continue'] = HTTP_SERVER . 'index.php?route=common/home';
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/success.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/common/success.tpl';
		} else {
			$this->template = 'default/template/common/success.tpl';
		}
		
		$this->children = array(
			'common/column_right',
			'common/footer',
			'common/column_left',
			'common/header'
		);
		
		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
  	}
	  public function mail()
	  {

		$this->load->model('account/voucher');
	
		$this->model_account_voucher->send_voucher_email(121);
		echo " Mail Sent";

	  }
}
?>