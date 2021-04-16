<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class ModelAccountVoucher extends Model {

    public function addVoucher($order_id, $voucher) {
        foreach ($voucher as $data) {
            $seed = str_split('abcdefghijklmnopqrstuvwxyz'
                    . 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                    . '0123456789'); // and any other characters
            shuffle($seed); // probably optional since array_is randomized; this may be redundant
            $code = '';
            foreach (array_rand($seed, 10) as $k)
                $code .= $seed[$k];
            $this->db->query("INSERT INTO " . DB_PREFIX . "voucher SET order_id = '" . (int) $order_id . "', code = '" . $this->db->escape($code) . "', from_name = '" . $this->db->escape($data['from_name']) . "', from_email = '" . $this->db->escape($data['from_email']) . "', to_name = '" . $this->db->escape($data['to_name']) . "', to_email = '" . $this->db->escape($data['to_email']) . "', voucher_theme_id = '" . (int) $data['voucher_theme_id'] . "', message = '" . $this->db->escape($data['message']) . "', amount = '" . (float) $data['amount'] . "', status = '1', date_added = NOW()");
            $label_id = $this->db->getLastId();
            $new_voucher = $this->db->query("INSERT INTO " . DB_PREFIX . "order_voucher SET order_id = '" . (int) $order_id . "', voucher_id = '" . $this->db->escape($voucherid) . "', code = '" . $this->db->escape($code) . "', from_name = '" . $this->db->escape($data['from_name']) . "', from_email = '" . $this->db->escape($data['from_email']) . "', to_name = '" . $this->db->escape($data['to_name']) . "', to_email = '" . $this->db->escape($data['to_email']) . "', voucher_theme_id = '" . (int) $data['voucher_theme_id'] . "', message = '" . $this->db->escape($data['message']) . "', amount = '" . (float) $data['amount'] . "'");
        }

        return $label_id;
    }

    public function disableVoucher($order_id) {
        $this->db->query("UPDATE " . DB_PREFIX . "voucher SET status = '0' WHERE order_id = '" . (int) $order_id . "'");
    }

    public function getVoucher($code) {

        $status = true;
        $voucher_query = $this->db->query("SELECT *, vtd.name AS theme FROM " . DB_PREFIX . "voucher v LEFT JOIN " . DB_PREFIX . "voucher_theme vt ON (v.voucher_theme_id = vt.voucher_theme_id) LEFT JOIN " . DB_PREFIX . "voucher_theme_description vtd ON (vt.voucher_theme_id = vtd.voucher_theme_id) WHERE v.code = '" . $this->db->escape($code) . "' AND vtd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND v.status = '1'");

        if ($voucher_query->num_rows) {
            $status = true;
            if ($voucher_query->row['order_id']) {
                $implode = array();
                foreach ($this->config->get('config_complete_status') as $order_status_id) {
                    $implode[] = "'" . (int) $order_status_id . "'";
                }

                $order_query = $this->db->query("SELECT order_id FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int) $voucher_query->row['order_id'] . "' ");

                if (!$order_query->num_rows) {
                    $status = false;
                }

                // $order_voucher_query = $this->db->query("SELECT order_voucher_id FROM `" . DB_PREFIX . "order_voucher` WHERE order_id = '" . (int)$voucher_query->row['order_id'] . "' AND voucher_id = '" . (int)$voucher_query->row['voucher_id'] . "'");
                // 	print_r($order_voucher_query);
                // if (!$order_voucher_query->num_rows) {
                // 	$status = false;
                // }
            }

            $voucher_history_query = $this->db->query("SELECT SUM(amount) AS total FROM `" . DB_PREFIX . "voucher_history` vh WHERE vh.voucher_id = '" . (int) $voucher_query->row['voucher_id'] . "' GROUP BY vh.voucher_id");

            if ($voucher_history_query->num_rows) {
                $amount = $voucher_query->row['amount'] + $voucher_history_query->row['total'];
            } else {
                $amount = $voucher_query->row['amount'];
            }

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
    }

    public function getTotal($total) {
        if (isset($this->session->data['voucher'])) {
            $this->load->language('extension/total/voucher', 'voucher');

            $voucher_info = $this->getVoucher($this->session->data['voucher']);

            if ($voucher_info) {
                $amount = min($voucher_info['amount'], $total['total']);

                if ($amount > 0) {
                    $total['totals'][] = array(
                        'code' => 'voucher',
                        'title' => sprintf($this->language->get('voucher')->get('text_voucher'), $this->session->data['voucher']),
                        'value' => -$amount,
                        'sort_order' => $this->config->get('total_voucher_sort_order')
                    );

                    $total['total'] -= $amount;
                } else {
                    unset($this->session->data['voucher']);
                }
            } else {
                unset($this->session->data['voucher']);
            }
        }
    }

    public function confirm($order_info, $order_total) {
        $code = '';

        $start = strpos($order_total['title'], '(') + 1;
        $end = strrpos($order_total['title'], ')');

        if ($start && $end) {
            $code = substr($order_total['title'], $start, $end - $start);
        }

        if ($code) {
            $voucher_info = $this->getVoucher($code);

            if ($voucher_info) {
                $this->db->query("INSERT INTO `" . DB_PREFIX . "voucher_history` SET voucher_id = '" . (int) $voucher_info['voucher_id'] . "', order_id = '" . (int) $order_info['order_id'] . "', amount = '" . (float) $order_total['value'] . "', date_added = NOW()");
            } else {
                return $this->config->get('config_fraud_status_id');
            }
        }
    }

    public function unconfirm($order_id) {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "voucher_history` WHERE order_id = '" . (int) $order_id . "'");
    }

    public function send_voucher_email($voucher_id) {

        $voucher_code = $this->db->query("SELECT `code`,`order_id` FROM `voucher` WHERE `voucher_id` =$voucher_id
		");
        $voucher_info = $this->getVoucher($voucher_code->row ['code']);
        if ($voucher_info) {
            if ($voucher_info['order_id']) {
                $order_id = $voucher_info['order_id'];
            } else {
                $order_id = $voucher_code->row ['order_id'];
            }
            $this->load->model('checkout/order');
            $this->load->model('account/voucher_theme');
            $order_info = $this->model_checkout_order->getOrder($order_id);

            // If voucher belongs to an order
            if ($order_info) {
                $this->load->model('localisation/language');
                $language = new Language($order_info['language_code']);
                $language->load($order_info['language_code']);
                $language->load('mail/voucher');
                $template = new Template();
                // HTML Mail
                $template->data['title'] = sprintf($language->get('text_subject'), $voucher_info['from_name']);
                $template->data['text_greeting'] = sprintf($language->get('text_greeting'), $this->currency->format($voucher_info['amount'], (!empty($order_info['currency_code']) ? $order_info['currency_code'] : $this->config->get('config_currency')), (!empty($order_info['currency_value']) ? $order_info['currency_value'] : $this->currency->getValue($this->config->get('config_currency')))));
                $template->data['text_from'] = sprintf($language->get('text_from'), $voucher_info['from_name']);
                $template->data['text_message'] = $language->get('text_message');
                $template->data['text_redeem'] = sprintf($language->get('text_redeem'), $voucher_info['code']);
                $template->data['text_footer'] = $language->get('text_footer');

                $voucher_theme_info = $this->model_account_voucher_theme->getVoucherTheme($voucher_info['voucher_theme_id']);

                if ($voucher_theme_info && is_file(DIR_IMAGE . $voucher_theme_info['image'])) {
                    $template->data['image'] = HTTP_CATALOG . 'image/' . $voucher_theme_info['image'];
                } else {
                    $template->data['image'] = '';
                }

                $template->data['store_name'] = $order_info['store_name'];
                $template->data['store_url'] = $order_info['store_url'];
                $template->data['message'] = nl2br($voucher_info['message']);

                $mail = new Mail($this->config->get('config_mail_engine'));
                $mail->parameter = $this->config->get('config_mail_parameter');
                $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
                $mail->smtp_username = $this->config->get('config_mail_smtp_username');
                $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
                $mail->smtp_port = $this->config->get('config_mail_smtp_port');
                $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
                $mail->setTo($voucher_info['to_email']);
                $mail->setFrom($this->config->get('config_email'));
                $mail->setSender(html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
                $mail->setSubject(sprintf($language->get('text_subject'), html_entity_decode($voucher_info['from_name'], ENT_QUOTES, 'UTF-8')));
                /* code to check Template */
                if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mail/voucher.tpl')) {
                    $html = $template->fetch($this->config->get('config_template') . '/template/mail/voucher.tpl');
                } else {
                    $html = $template->fetch('default/template/mail/voucher.tpl');
                }

                $subject = sprintf($language->get('text_greeting'), $this->currency->format($voucher_info['amount'], (!empty($order_info['currency_code']) ? $order_info['currency_code'] : $this->config->get('config_currency')), (!empty($order_info['currency_value']) ? $order_info['currency_value'] : $this->currency->getValue($this->config->get('config_currency')))));

                $mail->setSubject($subject);
                $mail->setTo($voucher_info['to_email']);
                $mail->setHtml($html);
                $mail->send();

                /* code to check Template */

                // If voucher does not belong to an order
            } else {
                $this->language->load('mail/voucher');

                $data['title'] = sprintf($this->language->get('text_subject'), $voucher_info['from_name']);

                $data['text_greeting'] = sprintf($this->language->get('text_greeting'), $this->currency->format($voucher_info['amount'], $this->config->get('config_currency')));
                $data['text_from'] = sprintf($this->language->get('text_from'), $voucher_info['from_name']);
                $data['text_message'] = $this->language->get('text_message');
                $data['text_redeem'] = sprintf($this->language->get('text_redeem'), $voucher_info['code']);
                $data['text_footer'] = $this->language->get('text_footer');

                $voucher_theme_info = $this->model_account_voucher_theme->getVoucherTheme($voucher_info['voucher_theme_id']);

                if ($voucher_theme_info && is_file(DIR_IMAGE . $voucher_theme_info['image'])) {
                    $data['image'] = HTTP_CATALOG . 'image/' . $voucher_theme_info['image'];
                } else {
                    $data['image'] = '';
                }

                $data['store_name'] = $this->config->get('config_name');
                $data['store_url'] = HTTP_CATALOG;
                $data['message'] = nl2br($voucher_info['message']);

                $mail = new Mail($this->config->get('config_mail_engine'));
                $mail->parameter = $this->config->get('config_mail_parameter');
                $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
                $mail->smtp_username = $this->config->get('config_mail_smtp_username');
                $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
                $mail->smtp_port = $this->config->get('config_mail_smtp_port');
                $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

                $mail->setTo($voucher_info['to_email']);
                $mail->setFrom($this->config->get('config_email'));
                $mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
                $mail->setSubject(html_entity_decode(sprintf($this->language->get('text_subject'), $voucher_info['from_name']), ENT_QUOTES, 'UTF-8'));
                //$mail->setHtml($this->load->view('mail/voucher', $data));
                $mail->send();
            }
        }
    }

}
