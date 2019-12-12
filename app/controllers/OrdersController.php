<?php

//namespace app\controller;

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Mvc\Model\Query;
use Phalcon\Di;
use Orders;

class OrdersController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
//        $this->persistent->parameters = null;
        $orders = new Orders();

        $this->view->orders = $orders->all();
    }

    /**
     * Searches for orders
     */
    public function searchAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'Orders', $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = [];
        }
        $parameters["order"] = "order_id";

        $orders = Orders::find($parameters);
        if (count($orders) == 0) {
            $this->flash->notice("The search did not find any orders");

            $this->dispatcher->forward([
                "controller" => "orders",
                "action" => "index"
            ]);

            return;
        }

        $paginator = new Paginator([
            'data' => $orders,
            'limit' => 10,
            'page' => $numberPage
        ]);

        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Displays the creation form
     */
    public function newAction()
    {
//        $products = new Products();
//        $users = new Users();
//
//        $this->view->products = $products->all();
//        $this->view->users = $users->all();
    }

    /**
     * Edits a order
     *
     * @param string $order_id
     */
    public function editAction($order_id)
    {
        if (!$this->request->isPost()) {

            $order = Orders::findFirstByorder_id($order_id);
            if (!$order) {
                $this->flash->error("order was not found");

                $this->dispatcher->forward([
                    'controller' => "orders",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->order_id = $order->order_id;

            $this->tag->setDefault("order_id", $order->order_id);
            $this->tag->setDefault("user_id", $order->user_id);
            $this->tag->setDefault("products", $order->products);

        }
    }

    /**
     * Creates a new order
     */
    public function createAction()
    {
        $user = new Users();
        $user_id = $user->getIdFromEmail($this->request->getPost("user_id"));
        $products = explode(",", substr($this->request->getPost("products")[0], 0, -1));

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "orders",
                'action' => 'index'
            ]);

            return;
        }

        $order = new Orders();
        $order->user_id = $user_id;
        $order->products = json_encode($products);

        if (!$order->save()) {
            foreach ($order->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "orders",
                'action' => 'new'
            ]);

            return;
        }

        foreach ($products as $product){
            $order_products = new OrderProducts();

            $productInstance = new Products();
            $product_id = $productInstance->getIdFromProductName($product);

            $order_products->order_id = $order->id;
            $order_products->product_id = $product_id;

            $order_products->save();
        }

        $this->flash->success("order was created successfully");

        $this->dispatcher->forward([
            'controller' => "orders",
            'action' => 'index'
        ]);
    }

    /**
     * Saves a order edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "orders",
                'action' => 'index'
            ]);

            return;
        }

        $order_id = $this->request->getPost("order_id");
        $order = Orders::findFirstByorder_id($order_id);

        if (!$order) {
            $this->flash->error("order does not exist " . $order_id);

            $this->dispatcher->forward([
                'controller' => "orders",
                'action' => 'index'
            ]);

            return;
        }

        $order->userId = $this->request->getPost("user_id");
        $order->products = $this->request->getPost("products");


        if (!$order->save()) {

            foreach ($order->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "orders",
                'action' => 'edit',
                'params' => [$order->order_id]
            ]);

            return;
        }

        $this->flash->success("order was updated successfully");

        $this->dispatcher->forward([
            'controller' => "orders",
            'action' => 'index'
        ]);
    }

    /**
     * Deletes a order
     *
     * @param string $order_id
     */
    public function deleteAction($order_id)
    {
        $order = Orders::findFirstByorder_id($order_id);
        if (!$order) {
            $this->flash->error("order was not found");

            $this->dispatcher->forward([
                'controller' => "orders",
                'action' => 'index'
            ]);

            return;
        }

        if (!$order->delete()) {

            foreach ($order->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "orders",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("order was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "orders",
            'action' => "index"
        ]);
    }


}
