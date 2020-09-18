<?php

namespace App\Listener;

use App\Logger;
use App\Texter\Sms;
use App\Event\OrderEvent;
use App\Texter\SmsTexter;

class OrderListenerSms
{
    protected $logger;

    protected $texter;

    public function __construct(SmsTexter $texter, Logger $logger)
    {
        $this->texter = $texter;
        $this->logger = $logger;
    }
    public function sendSmsToCustomer(OrderEvent $event)
    {
        $order = $event->getOrder();
        // Après enregistrement on veut aussi envoyer un SMS au client
        // voir src/Texter/Sms.php et /src/Texter/SmsTexter.php
        $sms = new Sms();
        $sms->setNumber($order->getPhoneNumber())
            ->setText("Merci pour votre commande de {$order->getQuantity()} {$order->getProduct()} !");
        $this->texter->send($sms);

        // Après SMS au client, on veut logger ce qui se passe :
        // voir src/Logger.php
        $this->logger->log("SMS de confirmation envoyé à {$order->getPhoneNumber()} !");
    }

    public function sendSmsToStock(OrderEvent $event)
    {
        $order = $event->getOrder();
        $sms = new Sms();
        $sms->setNumber('0606060606')
            ->setText("Commande confirmée merci de préparer: {$order->getQuantity()} {$order->getProduct()} !");
        $this->texter->send($sms);
        $this->logger->log("SMS de confirmation envoyé au stock");
    }
}
