<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class ControllerSaleVoucherTheme extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('sale/voucher_theme');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/voucher_theme');
		

		$this->getList();
	}

	public function insert() {
		$this->load->language('sale/voucher_theme');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/voucher_theme');
		print_r($this->request->post); 
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			
			$this->model_sale_voucher_theme->addVoucherTheme($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			$this->redirect(HTTPS_SERVER . 'index.php?route=sale/voucher_theme&token=' . $this->session->data['token'] . $url);
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('sale/voucher_theme');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/voucher_theme');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			
			$this->model_sale_voucher_theme->editVoucherTheme($this->request->get['voucher_theme_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->redirect(HTTPS_SERVER . 'index.php?route=sale/voucher_theme&token=' . $this->session->data['token'] . $url);
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('sale/voucher_theme');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/voucher_theme');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $voucher_theme_id) {
				$this->model_sale_voucher_theme->deleteVoucherTheme($voucher_theme_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('sale/voucher_theme', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'vtd.name';
		}
		
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}
		
		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}
	

		$url = '';
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
	    $this->document->breadcrumbs[] = array(
			'href'      => HTTPS_SERVER . 'index.php?route=common/home&token=' . $this->session->data['token'],
			'text'      => $this->language->get('text_home'),
		   'separator' => FALSE
		);

		$this->document->breadcrumbs[] = array(
			'href'      => HTTPS_SERVER . 'index.php?route=sale/voucher_theme&token=' . $this->session->data['token'] . $url,
			'text'      => $this->language->get('heading_title'),
		   'separator' => ' :: '
		);
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}


		


		$this->data['button_copy'] = $this->language->get('button_copy');
		$this->data['button_insert'] = $this->language->get('button_insert');
		$this->data['button_delete'] = $this->language->get('button_delete');
		$this->data['button_filter'] = $this->language->get('button_filter');

		$this->data['insert'] = HTTPS_SERVER . 'index.php?route=sale/voucher_theme/insert&token=' . $this->session->data['token'] . $url;
		$this->data['copy'] = HTTPS_SERVER . 'index.php?route=sale/voucher_theme/copy&token=' . $this->session->data['token'] . $url;
		$this->data['delete'] = HTTPS_SERVER . 'index.php?route=sale/voucher_theme/delete&token=' . $this->session->data['token'] . $url;
		$this->data['heading_title'] = $this->language->get('heading_title');
	
// echo " ok " ;
		$this->data['voucher_themes'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$voucher_theme_total = $this->model_sale_voucher_theme->getTotalVoucherThemes();
		$results = $this->model_sale_voucher_theme->getVoucherThemes($filter_data);
		
// echo "Results ok "; exit;

		foreach ($results as $result) {
			$action = array();
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => HTTPS_SERVER . 'index.php?route=sale/voucher_theme/edit&token=' . $this->session->data['token'] .'&voucher_theme_id=' . $result['voucher_theme_id'] . $url
			);
			$this->data['voucher_themes'][] = array(
				'voucher_theme_id' => $result['voucher_theme_id'],
				'name'             => $result['name'],
				'selected'   => isset($this->request->post['selected']) && in_array($result['voucher_id'], $this->request->post['selected']),
				'actions'             => $action
				//'edit'             => $this->url->link('sale/voucher_theme/edit', 'user_token=' . $this->session->data['user_token'] . '&voucher_theme_id=' . $result['voucher_theme_id'] . $url, true)
			);
		
			
		}
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		// echo "data ok "; exit;
		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = '#';
		//$data['sort_name'] = $this->url->link('sale/voucher_theme', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		// echo "data sort name 	ok "; exit;
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $voucher_theme_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = '#';
		$this->data['pagination'] = $pagination->render();
		$data['sort'] = $sort;
		$data['order'] = $order;

		$this->template = 'sale/voucher_theme_list.tpl';
		//$this->template = 'catalog/product_list.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
		//echo "data ok "; exit;
		//print_r($data); 
		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
		//$this->response->setOutput($this->load->view('sale/voucher_theme_list', $data));
	}

	protected function getForm() {
		$this->load->model('tool/image');
		$data['text_form'] = !isset($this->request->get['voucher_theme_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}

		if (isset($this->error['image'])) {
			$data['error_image'] = $this->error['image'];
		} else {
			$data['error_image'] = '';
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$this->document->breadcrumbs = array();

   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=common/home&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('text_home'),
			'separator' => FALSE
   		);

   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=sale/voucher_theme&token=' . $this->session->data['token'] . $url,
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
		   );
		if (isset($this->request->get['voucher_theme_id'])) {
			$this->data['action'] =HTTPS_SERVER . 'index.php?route=sale/voucher_theme/edit&voucher_theme_id='.$this->request->get['voucher_theme_id'].'&token=' . $this->session->data['token'] . $url;
		
		} else {
			$this->data['action'] = HTTPS_SERVER . 'index.php?route=sale/voucher_theme/insert&token=' . $this->session->data['token'] . $url;
			
		}
		
		$this->data['cancel'] = HTTPS_SERVER . 'index.php?route=sale/voucher_theme/&token=' . $this->session->data['token'] . $url;
		

		if (isset($this->request->get['voucher_theme_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$voucher_theme_info = $this->model_sale_voucher_theme->getVoucherTheme($this->request->get['voucher_theme_id']);
			$this->data['image1'] =  $this->model_tool_image->resize($voucher_theme_info['image'], 100, 100);;
			$this->data['name'] = $voucher_theme_info['name'];
			$this->data['image'] = $voucher_theme_info['image'];
			$this->data['voucher_theme_id'] = $voucher_theme_info['voucher_theme_id'];
		
		}
		
		$this->data['user_token'] = $this->session->data['token'];
		$this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		if (isset($this->request->post['voucher_theme_description'])) {
			$this->data['voucher_theme_description'] = $this->request->post['voucher_theme_description'];
		} elseif (isset($this->request->get['voucher_theme_id'])) {
			$this->data['voucher_theme_description'] = $this->model_sale_voucher_theme->getVoucherThemeDescriptions($this->request->get['voucher_theme_id']);
		} else {
			$this->data['voucher_theme_description'] = array();
		}
		
		if (isset($this->request->post['image'])) {
			$this->data['image'] = $this->request->post['image'];
		} elseif (!empty($voucher_theme_info)) {
			$this->data['image'] = $voucher_theme_info['image'];
		} else {
			$this->data['image'] = '';
		}

		$this->data['entry_name'] = $this->language->get('entry_name');
		$this->data['entry_description'] = $this->language->get('entry_description');
		$this->data['entry_image'] = $this->language->get('entry_image');

		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['button_copy'] = $this->language->get('button_copy');
		$this->data['button_insert'] = $this->language->get('button_insert');
		$this->data['button_delete'] = $this->language->get('button_delete');
		$this->data['button_filter'] = $this->language->get('button_filter');
			$this->data['button_cancel'] = "Cancel";
		
    	$this->data['entry_to_email'] = 
    	$this->data['entry_theme'] = $this->language->get('entry_theme');
    	$this->data['entry_message'] = $this->language->get('entry_message');
		$this->load->model('tool/image');
		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$this->data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($voucher_theme_info) && is_file(DIR_IMAGE . $voucher_theme_info['image'])) {
			$this->data['thumb'] = $this->model_tool_image->resize($voucher_theme_info['image'], 100, 100);
		} else {
			$this->data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}
		
		$this->data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		$this->load->model('localisation/length_class');
		$this->data['length_classes'] = $this->model_localisation_length_class->getLengthClasses();
    	$length_info = $this->model_localisation_length_class->getLengthClassDescriptionByUnit($this->config->get('config_length_class'));
		$this->template = 'sale/voucher_theme_form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
// 		$this->response->setOutput($this->load->view('sale/voucher_theme_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'sale/voucher_theme')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		//print_r($this->request->post);
		if ((strlen($this->request->post['voucher_theme_name']) < 3) || (strlen($this->request->post['voucher_theme_name']) > 32)) {
			$this->error['name']= $this->language->get('error_name');
		}
			// foreach ($this->request->post['voucher_theme_description'] as $language_id => $value) {
			// 	if ((strlen($value['name']) < 3) || (strlen($value['name']) > 32)) {
			// 		$this->error['name'][$language_id] = $this->language->get('error_name');
			// 	}
			// }

		// if (!$this->request->post['image']) {
		// 	$this->error['image'] = $this->language->get('error_image');
		// }

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'sale/voucher_theme')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$this->load->model('sale/voucher');

		foreach ($this->request->post['selected'] as $voucher_theme_id) {
			$voucher_total = $this->model_sale_voucher->getTotalVouchersByVoucherThemeId($voucher_theme_id);

			if ($voucher_total) {
				$this->error['warning'] = sprintf($this->language->get('error_voucher'), $voucher_total);
			}
		}

		return !$this->error;
	}
}