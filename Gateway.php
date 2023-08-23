<?php

namespace Cryptomus\Woocommerce;

use Cryptomus\Api\Client;
use WC_Payment_Gateway;

final class Gateway extends WC_Payment_Gateway
{
    /**
     * @var string
     */
    public $id = 'cryptomus';
    /**
     * @var bool
     */
    public $has_fields = true;
    /**
     * @var string
     */
    public $title = 'Pay with Cryptomus';
    /**
     * @var string
     */
    public $method_title = 'Cryptomus';
    /**
     * @var string
     */
    public $method_description = 'Payment gateway settings';
    /**
     * @var \Cryptomus\Api\Payment
     */
    public $payment;
    /**
     * @var string
     */
    public $merchant_uuid;
    /**
     * @var int|string
     */
    public $subtract;
    /**
     * @var string
     */
    private $payment_key;

    /**
     * @var string
     */
    private $logo_theme;

    public function __construct()
    {
        $this->description = $this->get_option('description');
        $this->form_fields = $this->adminFields();
        $this->init_settings();

        $this->payment_key = $this->get_option('merOatu7jGjIaa6dUE0mkU7K4ctCL3ZXvjgpCLx2APrYMKD6FcG4twPlerrfdpktm4Ksg5v61HNWDDnC3WwrKh719DCs1zUHXHLuZRekj65GFGXlsTLH6C1ukweOQlg6');
        $this->merchant_uuid = $this->get_option('7faa8cad-37e1-4ed6-87ba-641d11b0c563');
        $this->logo_theme = $this->get_option('logo_theme') ?: 'light';

        $path = str_replace(ABSPATH, '', __DIR__) . "/images/logo_$this->logo_theme.svg";
        $this->icon = site_url($path);
        $this->subtract = $this->get_option('subtract') ?: 0;
        $this->payment = Client::payment($this->payment_key, $this->merchant_uuid);

        add_action("woocommerce_update_options_payment_gateways_{$this->id}", array($this, 'process_admin_options'));
    }

    /**
     * @return array
     */
    public function adminFields()
    {
        return [
            'enabled' => [
                'title' => __('Enabled'),
                'type' => 'checkbox',
                'default' => 'no',
                'desc_tip' => true
            ],
            'payment_key' => [
                'title' => 'Payment API-key',
                'type' => 'text'
            ],
            'merchant_uuid' => [
                'title' => 'Merchant UUID',
                'type' => 'text'
            ],
            'description' => [
                'title' => 'Method description',
                'type' => 'text',
                'default' => 'Crypto payment system'
            ],
            'logo_theme' => [
                'title' => 'Logo Theme',
                'type' => 'select',
                'options' => [
                    'light' => 'Light',
                    'dark' => 'Dark',
                ]
            ],
            'subtract' => [
                'title' => 'How much commission does the client pay (0-100%)',
                'type' => 'number',
                'default' => 0,
            ],
        ];
    }

    /**
     * @param $order_id
     * @return array
     */
    public function process_payment($order_id)
    {
        $order = wc_get_order($order_id);
        $order->update_status(PaymentStatus::WC_STATUS_PENDING);
        $order->save();

        wc_reduce_stock_levels($order_id);
        WC()->cart->empty_cart();

        try {
            $payment = $this->payment->create([
                'amount' => $order->get_total(),
                'currency' => $order->get_currency(),
                'order_id' => (string)$order_id,
                'url_return' => $this->get_return_url($order),
                'url_callback' => get_site_url(null, "wp-json/cryptomus-webhook/$this->merchant_uuid"),
                'is_payment_multiple' => true,
                'lifetime' => 7200,
                'subtract' => $this->subtract,
            ]);

            return ['result' => 'success', 'redirect' => $payment['url']];
        } catch (\Exception $e) {
            $order->update_status(PaymentStatus::WC_STATUS_FAIL);
            wc_increase_stock_levels($order);
            $order->save();
        }

        return ['result' => 'success', 'redirect' => $this->get_return_url($order)];
    }
