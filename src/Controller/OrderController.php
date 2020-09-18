<?php

namespace App\Controller;

use App\Database;
use App\Event\OrderEvent;
use App\Model\Order;
use Symfony\Component\EventDispatcher\EventDispatcher;

class OrderController
{

    protected $database;
    protected $dispatcher;

    public function __construct(Database $database,EventDispatcher $dispatcher)
    {
        $this->database = $database;
        $this->dispatcher = $dispatcher;
    }

    public function displayOrderForm()
    {
        require __DIR__ . '/../../views/form.html.php';
    }

    /**
     * GERER LE FORMULAIRE DE COMMANDE
     * ----------
     * Une fois que le formulaire est soumis, on veut :
     * 1) Extraire les données de la requête (communément admis comme étant le boulot d'un controller)
     * 2) Demander à un service d'enregistrer la commande (aussi admis comme étant le boulot classique d'un controller)
     * 3) Envoyer des emails (Heu ?)
     * 4) Envoyer des SMS (Ha ?)
     * 5) Faire des logs (Hu ?)
     */
    public function handleOrder()
    {
        // Extraction des données du POST et création d'un objet Order (voir src/Model/Order.php)
        $order = new Order;
        $order->setProduct($_POST['product'])
            ->setQuantity($_POST['quantity'])
            ->setEmail($_POST['email'])
            ->setPhoneNumber($_POST['phone']);

        // send to dispatcher the event before insert in db
        $this->dispatcher->dispatch(new OrderEvent($order), 'order.before_insert');

        // Enregistrement en base de données :
        // voir src/Database.php
        $this->database->insertOrder($order);

        // send to dispatcher the event after insert in db
        $this->dispatcher->dispatch(new OrderEvent($order), 'order.after_insert');

    }
}
