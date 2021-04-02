<?php

class ControllerSaleVoucher extends Controller {

    private $error = array();

    public function index() {
        $this->load->language('sale/voucher');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('sale/voucher');

        $this->getList();
    }

    public function insert() {

        $this->load->language('sale/voucher');

        $this->document->title = $this->language->get('heading_title');
        $this->load->model('sale/voucher');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_sale_voucher->addVoucher($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            $this->redirect(HTTPS_SERVER . 'index.php?route=sale/voucher&token=' . $this->session->data['token'] . $url);
        }

        $this->getForm();
    }

    public function update_old() {

        $this->load->language('sale/voucher');

        $this->document->title = $this->language->get('heading_title');

        $this->load->model('sale/voucher');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            
            $this->model_sale_voucher->editVoucher($this->request->get['voucher_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_name'])) {
                $url .= '&filter_name=' . $this->request->get['filter_name'];
            }

            if (isset($this->request->get['filter_model'])) {
                $url .= '&filter_model=' . $this->request->get['filter_model'];
            }

            if (isset($this->request->get['filter_price'])) {
                $url .= '&filter_price=' . $this->request->get['filter_price'];
            }

            if (isset($this->request->get['filter_quantity'])) {
                $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
            }

            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            $this->redirect(HTTPS_SERVER . 'index.php?route=sale/voucher&token=' . $this->session->data['token'] . $url);
        }

        $this->getForm();
    }

    public function update() {
		$this->language->load('sale/voucher');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/voucher');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_sale_voucher->editVoucher($this->request->get['voucher_id'], $this->request->post);

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

            $this->redirect(HTTPS_SERVER . 'index.php?route=sale/voucher&token=' . $this->session->data['token'] . $url);
			// $this->redirect($this->url->link('sale/voucher', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

    public function delete() {
        $this->load->language('sale/voucher');

        $this->document->title = $this->language->get('heading_title');

        $this->load->model('sale/voucher');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $voucher_id) {
                $this->model_sale_voucher->deleteVoucher($voucher_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            $this->redirect(HTTPS_SERVER . 'index.php?route=sale/voucher&token=' . $this->session->data['token'] . $url);
        }

        $this->getList();
    }

    private function validateForm() {
        if (!$this->user->hasPermission('modify', 'sale/voucher')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if ((strlen($this->request->post['code']) < 3) || (strlen($this->request->post['code']) > 10)) {
            $this->error['code'] = $this->language->get('error_code');
        }
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

        if ($this->request->post['amount'] < 1) {
            $this->error['amount'] = $this->language->get('error_amount');
        }
//   	echo " All well "; exit;

        if (!$this->error) {
            return TRUE;
        } else {
            if (!isset($this->error['warning'])) {
                $this->error['warning'] = $this->language->get('error_required_data');
            }
            return FALSE;
        }
    }

    public function copy() {
        $this->load->language('sale/voucher');

        $this->document->title = $this->language->get('heading_title');

        $this->load->model('sale/voucher');

        if (isset($this->request->post['selected']) && $this->validateCopy()) {
            foreach ($this->request->post['selected'] as $voucher_id) {
                $this->model_sale_voucher->copyProduct($voucher_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_name'])) {
                $url .= '&filter_name=' . $this->request->get['filter_name'];
            }

            if (isset($this->request->get['filter_model'])) {
                $url .= '&filter_model=' . $this->request->get['filter_model'];
            }

            if (isset($this->request->get['filter_price'])) {
                $url .= '&filter_price=' . $this->request->get['filter_price'];
            }

            if (isset($this->request->get['filter_quantity'])) {
                $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
            }

            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            $this->redirect(HTTPS_SERVER . 'index.php?route=sale/voucher&token=' . $this->session->data['token'] . $url);
        }

        $this->getList();
    }

    private function getList() {
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'v.date_added';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'DESC';
        }

        if (isset($this->request->get['page'])) {
            $page = (int) $this->request->get['page'];
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
        //$pagi = new Pagination();
        //$pagi->total = $product_total;
        //$pagi->limit = $this->config->get('config_admin_limit');
        $this->document->breadcrumbs = array();

        $this->document->breadcrumbs[] = array(
            'href' => HTTPS_SERVER . 'index.php?route=common/home&token=' . $this->session->data['token'],
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        );

        $this->document->breadcrumbs[] = array(
            'href' => HTTPS_SERVER . 'index.php?route=sale/voucher&token=' . $this->session->data['token'] . $url,
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['insert'] = HTTPS_SERVER . 'index.php?route=sale/voucher/insert&token=' . $this->session->data['token'] . $url;
        $this->data['copy'] = HTTPS_SERVER . 'index.php?route=sale/voucher/copy&token=' . $this->session->data['token'] . $url;
        $this->data['delete'] = HTTPS_SERVER . 'index.php?route=sale/voucher/delete&token=' . $this->session->data['token'] . $url;
//		$this->data['products'] = array();
        $data = array();

        $filter_data = array(
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_admin_limit'),
            'limit' => $this->config->get('config_admin_limit')
        );
        $product_total = $this->model_sale_voucher->getTotalVouchers($filter_data);

        $results = $this->model_sale_voucher->getVouchers($filter_data);

        foreach ($results as $result) {
            if ($result['order_id']) {
                $order_href = HTTPS_SERVER . 'index.php?route=sale/voucher/&token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id'];
            } else {
                $order_href = '';
            }
            $action = array();

            $action[] = array(
                'text' => $this->language->get('text_edit'),
                'href' => HTTPS_SERVER . 'index.php?route=sale/voucher/update&token=' . $this->session->data['token'] . '&voucher_id=' . $result['voucher_id'] . $url
            );

            $this->data['vouchers'][] = array(
                'voucher_id' => $result['voucher_id'],
                'code' => $result['code'],
                'from' => $result['from_name'],
                'to' => $result['to_name'],
                'amount' => $result['amount'],
                'theme' => $result['theme'],
                'status' => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
                'date' => $result['date_added'],
                'selected' => isset($this->request->post['selected']) && in_array($result['voucher_id'], $this->request->post['selected']),
                'action' => $action
            );
        }

        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['text_list'] = $this->language->get('text_list');
        $this->data['text_send'] = $this->language->get('text_send');
        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');
        $this->data['text_no_results'] = $this->language->get('text_no_results');
        $this->data['text_image_manager'] = $this->language->get('text_image_manager');
        $this->data['code'] = $this->language->get('column_name');
        $this->data['From'] = $this->language->get('column_model');
        $this->data['To'] = $this->language->get('column_price');
        $this->data['Amount'] = $this->language->get('column_quantity');
        $this->data['column_status'] = $this->language->get('column_status');
        $this->data['column_action'] = $this->language->get('column_action');

        $this->data['button_copy'] = $this->language->get('button_copy');
        $this->data['button_insert'] = $this->language->get('button_insert');
        $this->data['button_delete'] = $this->language->get('button_delete');
        $this->data['button_filter'] = $this->language->get('button_filter');

        $this->data['token'] = $this->session->data['token'];

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

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . $this->request->get['filter_name'];
        }

        if (isset($this->request->get['filter_model'])) {
            $url .= '&filter_model=' . $this->request->get['filter_model'];
        }

        if (isset($this->request->get['filter_price'])) {
            $url .= '&filter_price=' . $this->request->get['filter_price'];
        }

        if (isset($this->request->get['filter_quantity'])) {
            $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $this->data['sort_name'] = HTTPS_SERVER . 'index.php?route=sale/voucher&token=' . $this->session->data['token'] . '&sort=pd.name' . $url;
        $this->data['sort_model'] = HTTPS_SERVER . 'index.php?route=sale/voucher&token=' . $this->session->data['token'] . '&sort=p.model' . $url;
        $this->data['sort_price'] = HTTPS_SERVER . 'index.php?route=sale/voucher&token=' . $this->session->data['token'] . '&sort=p.price' . $url;
        $this->data['sort_quantity'] = HTTPS_SERVER . 'index.php?route=sale/voucher&token=' . $this->session->data['token'] . '&sort=p.quantity' . $url;
        $this->data['sort_status'] = HTTPS_SERVER . 'index.php?route=sale/voucher&token=' . $this->session->data['token'] . '&sort=p.status' . $url;
        $this->data['sort_order'] = HTTPS_SERVER . 'index.php?route=sale/voucher&token=' . $this->session->data['token'] . '&sort=p.sort_order' . $url;

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . $this->request->get['filter_name'];
        }

        if (isset($this->request->get['filter_model'])) {
            $url .= '&filter_model=' . $this->request->get['filter_model'];
        }

        if (isset($this->request->get['filter_price'])) {
            $url .= '&filter_price=' . $this->request->get['filter_price'];
        }

        if (isset($this->request->get['filter_quantity'])) {
            $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $product_total;
        $pagination->limit = $this->config->get('config_admin_limit');
        $pagination->text = $this->language->get('text_pagination');
        $pagination->page = $page;
        $pagination->url = HTTPS_SERVER . 'index.php?route=sale/voucher&token=' . $this->session->data['token'] . $url . '&page={page}';

        $this->data['pagination'] = $pagination->render();
        $this->data['results'] = sprintf($this->language->get('text_pagination'), ($voucher_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($voucher_total - $this->config->get('config_limit_admin'))) ? $voucher_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $voucher_total, ceil($voucher_total / $this->config->get('config_limit_admin')));

        $this->template = 'sale/voucher.tpl';

        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }

    protected function getForm() {
        $this->load->model('sale/voucher_theme');
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');

		$this->data['entry_code'] = $this->language->get('entry_code');
		$this->data['entry_from_name'] = $this->language->get('entry_from_name');
		$this->data['entry_from_email'] = $this->language->get('entry_from_email');
		$this->data['entry_to_name'] = $this->language->get('entry_to_name');
		$this->data['entry_to_email'] = $this->language->get('entry_to_email');
		$this->data['entry_theme'] = $this->language->get('entry_theme');
		$this->data['entry_message'] = $this->language->get('entry_message');
		$this->data['entry_amount'] = $this->language->get('entry_amount');
		$this->data['entry_status'] = $this->language->get('entry_status');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		$this->data['tab_general'] = $this->language->get('tab_general');
		$this->data['tab_voucher_history'] = $this->language->get('tab_voucher_history');

		if (isset($this->request->get['voucher_id'])) {
			$this->data['voucher_id'] = $this->request->get['voucher_id'];
		} else {
			$this->data['voucher_id'] = 0;
		}

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->error['code'])) {
			$this->data['error_code'] = $this->error['code'];
		} else {
			$this->data['error_code'] = '';
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

		if (isset($this->error['amount'])) {
			$this->data['error_amount'] = $this->error['amount'];
		} else {
			$this->data['error_amount'] = '';
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

		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
            'href' => HTTPS_SERVER . 'index.php?route=common/home&token=' . $this->session->data['token'],
			// 'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
            'href' => HTTPS_SERVER . 'index.php?route=sale/voucher&token=' . $this->session->data['token'] . $url,
			// 'href'      => $this->url->link('sale/voucher', 'token=' . $this->session->data['token'] . $url, 'SSL'),
			'separator' => ' :: '
		);

		if (!isset($this->request->get['voucher_id'])) {
            $this->data['action'] = HTTPS_SERVER . 'index.php?route=sale/voucher/insert&token=' . $this->session->data['token'] . $url;
			// $this->data['action'] = $this->url->link('sale/voucher/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
            $this->data['action'] = HTTPS_SERVER . 'index.php?route=sale/voucher/update&token=' . $this->session->data['token'] . '&voucher_id=' . $this->request->get['voucher_id'] . $url;
			// $this->data['action'] = $this->url->link('sale/voucher/update', 'token=' . $this->session->data['token'] . '&voucher_id=' . $this->request->get['voucher_id'] . $url, 'SSL');
		}

        $this->data['cancel'] = HTTPS_SERVER . 'index.php?route=sale/voucher&token=' . $this->session->data['token'] . $url;
		// $this->data['cancel'] = $this->url->link('sale/voucher', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['voucher_id']) && (!$this->request->server['REQUEST_METHOD'] != 'POST')) {
			$voucher_info = $this->model_sale_voucher->getVoucher($this->request->get['voucher_id']);
		}

		$this->data['token'] = $this->session->data['token'];

		if (isset($this->request->post['code'])) {
			$this->data['code'] = $this->request->post['code'];
		} elseif (!empty($voucher_info)) {
			$this->data['code'] = $voucher_info['code'];
		} else {
			$this->data['code'] = '';
		}

		if (isset($this->request->post['from_name'])) {
			$this->data['from_name'] = $this->request->post['from_name'];
		} elseif (!empty($voucher_info)) {
			$this->data['from_name'] = $voucher_info['from_name'];
		} else {
			$this->data['from_name'] = '';
		}

		if (isset($this->request->post['from_email'])) {
			$this->data['from_email'] = $this->request->post['from_email'];
		} elseif (!empty($voucher_info)) {
			$this->data['from_email'] = $voucher_info['from_email'];
		} else {
			$this->data['from_email'] = '';
		}

		if (isset($this->request->post['to_name'])) {
			$this->data['to_name'] = $this->request->post['to_name'];
		} elseif (!empty($voucher_info)) {
			$this->data['to_name'] = $voucher_info['to_name'];
		} else {
			$this->data['to_name'] = '';
		}

		if (isset($this->request->post['to_email'])) {
			$this->data['to_email'] = $this->request->post['to_email'];
		} elseif (!empty($voucher_info)) {
			$this->data['to_email'] = $voucher_info['to_email'];
		} else {
			$this->data['to_email'] = '';
		}

		$this->load->model('sale/voucher_theme');

		$this->data['voucher_themes'] = $this->model_sale_voucher_theme->getVoucherThemes();

		if (isset($this->request->post['voucher_theme_id'])) {
			$this->data['voucher_theme_id'] = $this->request->post['voucher_theme_id'];
		} elseif (!empty($voucher_info)) {
			$this->data['voucher_theme_id'] = $voucher_info['voucher_theme_id'];
		} else {
			$this->data['voucher_theme_id'] = '';
		}

		if (isset($this->request->post['message'])) {
			$this->data['message'] = $this->request->post['message'];
		} elseif (!empty($voucher_info)) {
			$this->data['message'] = $voucher_info['message'];
		} else {
			$this->data['message'] = '';
		}

		if (isset($this->request->post['amount'])) {
			$this->data['amount'] = $this->request->post['amount'];
		} elseif (!empty($voucher_info)) {
			$this->data['amount'] = $voucher_info['amount'];
		} else {
			$this->data['amount'] = '';
		}

		if (isset($this->request->post['status'])) {
			$this->data['status'] = $this->request->post['status'];
		} elseif (!empty($voucher_info)) {
			$this->data['status'] = $voucher_info['status'];
		} else {
			$this->data['status'] = 1;
		}

		$this->template = 'sale/voucher_form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
		// $this->response->setOutput($this->render());		
	}

    private function getForm_old() {

        $this->load->model('sale/voucher_theme');
        $this->data['voucher_themes'] = $this->model_sale_voucher_theme->getVoucherThemes();
        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['entry_code'] = $this->language->get('entry_code');
        $this->data['entry_from_name'] = $this->language->get('entry_from_name');
        $this->data['entry_from_email'] = $this->language->get('entry_from_email');
        $this->data['entry_to_name'] = $this->language->get('entry_to_name');
        $this->data['entry_to_email'] = $this->language->get('entry_to_email');
        $this->data['entry_theme'] = $this->language->get('entry_theme');
        $this->data['entry_message'] = $this->language->get('entry_message');
        $this->data['entry_amount'] = $this->language->get('entry_amount');
        $this->data['entry_status'] = $this->language->get('entry_status');
        $this->data['button_insert'] = "Save";
        $this->data['button_cancel'] = "Cancel";

        $this->data['tab_general'] = $this->language->get('tab_general');
        $this->data['tab_voucher_history'] = $this->language->get('tab_voucher_history');

        if (isset($this->request->get['voucher_id'])) {
			$this->data['voucher_id'] = $this->request->get['voucher_id'];
		} else {
			$this->data['voucher_id'] = 0;
		}

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }
        $url = '';

        $this->document->breadcrumbs = array();

        $this->document->breadcrumbs[] = array(
            'href' => HTTPS_SERVER . 'index.php?route=common/home&token=' . $this->session->data['token'],
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        );

        $this->document->breadcrumbs[] = array(
            'href' => HTTPS_SERVER . 'index.php?route=sale/voucher&token=' . $this->session->data['token'] . $url,
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        if (!isset($this->request->get['voucher_id'])) {
            $this->data['action'] = HTTPS_SERVER . 'index.php?route=sale/voucher/insert&token=' . $this->session->data['token'] . $url;
        } else {
            $this->data['action'] = HTTPS_SERVER . 'index.php?route=sale/voucher/update&token=' . $this->session->data['token'] . '&voucher_id=' . $this->request->get['voucher_id'] . $url;
        }

        $this->data['cancel'] = HTTPS_SERVER . 'index.php?route=sale/voucher&token=' . $this->session->data['token'] . $url;

        $this->data['token'] = $this->session->data['token'];

        if (isset($this->request->get['voucher_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $voucher_info = $this->model_sale_voucher->getVoucher($this->request->get['voucher_id']);
            $this->data['voucher'] = $voucher_info;
        }

        $this->load->model('localisation/language');
        $this->data['languages'] = $this->model_localisation_language->getLanguages();
        $this->load->model('localisation/length_class');
        $this->data['length_classes'] = $this->model_localisation_length_class->getLengthClasses();
        $length_info = $this->model_localisation_length_class->getLengthClassDescriptionByUnit($this->config->get('config_length_class'));
        $this->template = 'sale/voucher_form.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }

    private function validateDelete() {
        if (!$this->user->hasPermission('modify', 'sale/voucher')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    private function validateCopy() {
        if (!$this->user->hasPermission('modify', 'sale/voucher')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function category() {
        $this->load->model('sale/voucher');

        if (isset($this->request->get['category_id'])) {
            $category_id = $this->request->get['category_id'];
        } else {
            $category_id = 0;
        }

        $product_data = array();

        $results = $this->model_catalog_product->getProductsByCategoryId($category_id);

        foreach ($results as $result) {
            $product_data[] = array(
                'product_id' => $result['product_id'],
                'name' => $result['name'],
                'model' => $result['model']
            );
        }

        $this->load->library('json');

        $this->response->setOutput(Json::encode($product_data));
    }

    public function related() {
        $this->load->model('sale/voucher');

        if (isset($this->request->post['product_related'])) {
            $products = $this->request->post['product_related'];
        } else {
            $products = array();
        }

        $product_data = array();

        foreach ($products as $product_id) {
            $product_info = $this->model_catalog_product->getProduct($product_id);

            if ($product_info) {
                $product_data[] = array(
                    'product_id' => $product_info['product_id'],
                    'name' => $product_info['name'],
                    'model' => $product_info['model']
                );
            }
        }

        $this->load->library('json');

        $this->response->setOutput(Json::encode($product_data));
    }

    public function send() {
        $this->language->load('sale/voucher');

        $json = array();

        if (!$this->user->hasPermission('modify', 'sale/voucher')) {
            $json['error'] = $this->language->get('error_permission');
        } elseif (isset($this->request->get['voucher_id'])) {
            $this->load->model('sale/voucher');

            $this->model_sale_voucher->sendVoucher($this->request->get['voucher_id']);

            $json['success'] = $this->language->get('text_sent');
        }

        $this->response->setOutput(json_encode($json));
    }
    
    public function history() {
		$this->language->load('sale/voucher');

		$this->load->model('sale/voucher');

		$this->data['text_no_results'] = $this->language->get('text_no_results');

		$this->data['column_order_id'] = $this->language->get('column_order_id');
		$this->data['column_customer'] = $this->language->get('column_customer');
		$this->data['column_amount'] = $this->language->get('column_amount');
		$this->data['column_date_added'] = $this->language->get('column_date_added');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}  

		$this->data['histories'] = array();

		$results = $this->model_sale_voucher->getVoucherHistories($this->request->get['voucher_id'], ($page - 1) * 10, 10);

		foreach ($results as $result) {
			$this->data['histories'][] = array(
				'order_id'   => $result['order_id'],
				'customer'   => $result['customer'],
				'amount'     => $this->currency->format($result['amount'], $this->config->get('config_currency')),
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$history_total = $this->model_sale_voucher->getTotalVoucherHistories($this->request->get['voucher_id']);

		$pagination = new Pagination();
		$pagination->total = $history_total;
		$pagination->page = $page;
		$pagination->limit = 10; 
        $pagination->url = HTTPS_SERVER . 'index.php?route=sale/voucher/insert&token=' . $this->session->data['token'] . '&voucher_id=' . $this->request->get['voucher_id'] . '&page={page}' ;
		// $pagination->url = $this->url->link('sale/voucher/history', 'token=' . $this->session->data['token'] . '&voucher_id=' . $this->request->get['voucher_id'] . '&page={page}', 'SSL');

		$this->data['pagination'] = $pagination->render();
        
		$this->template = 'sale/voucher_history.tpl';		

        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
		// $this->response->setOutput($this->render());
	}

}

?>