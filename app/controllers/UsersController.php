<?php

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Http\Response;

use Users;

class UsersController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
//        $this->persistent->parameters = null;
    }

    /**
     * Searches for users
     */
    public function searchAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'Users', $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = [];
        }
        $parameters["order"] = "id";

        $users = Users::find($parameters);
        if (count($users) == 0) {
            $this->flash->notice("The search did not find any users");

            $this->dispatcher->forward([
                "controller" => "users",
                "action" => "index"
            ]);

            return;
        }

        $paginator = new Paginator([
            'data' => $users,
            'limit'=> 10,
            'page' => $numberPage
        ]);

        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Displays the creation form
     */
    public function newAction()
    {

    }

    /**
     * Edits a user
     *
     * @param string $user_id
     */
    public function editAction($user_id)
    {
        if (!$this->request->isPost()) {

            $user = Users::findFirstByuser_id($user_id);
            if (!$user) {
                $this->flash->error("user was not found");

                $this->dispatcher->forward([
                    'controller' => "users",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->user_id = $user->user_id;

            $this->tag->setDefault("user_id", $user->user_id);
            $this->tag->setDefault("username", $user->username);
            $this->tag->setDefault("password", $user->password);
            $this->tag->setDefault("email", $user->email);
        }
    }

    /**
     * Creates a new user
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "users",
                'action' => 'index'
            ]);

            return;
        }

        $password = $this->request->getPost("password");

        $user = new Users();
        $user->username = $this->request->getPost("username");
        $user->password = $this->security->hash($password);
        $user->email = $this->request->getPost("email", "email");

        if (!$user->save()) {
            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "users",
                'action' => 'new'
            ]);

            return;
        }

        $this->flash->success("user was created successfully");

        $this->dispatcher->forward([
            'controller' => "users",
            'action' => 'index'
        ]);
    }

    /**
     * Saves a user edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "users",
                'action' => 'index'
            ]);

            return;
        }

        $user_id = $this->request->getPost("user_id");
        $user = Users::findFirstByuser_id($user_id);

        if (!$user) {
            $this->flash->error("user does not exist " . $user_id);

            $this->dispatcher->forward([
                'controller' => "users",
                'action' => 'index'
            ]);

            return;
        }

        $user->username = $this->request->getPost("username");
        $user->password = $this->request->getPost("password");
        $user->email = $this->request->getPost("email", "email");


        if (!$user->save()) {

            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "users",
                'action' => 'edit',
                'params' => [$user->user_id]
            ]);

            return;
        }

        $this->flash->success("user was updated successfully");

        $this->dispatcher->forward([
            'controller' => "users",
            'action' => 'index'
        ]);
    }

    /**
     * Deletes a user
     *
     * @param string $user_id
     */
    public function deleteAction($user_id)
    {
        $user = Users::findFirstByuser_id($user_id);
        if (!$user) {
            $this->flash->error("user was not found");

            $this->dispatcher->forward([
                'controller' => "users",
                'action' => 'index'
            ]);

            return;
        }

        if (!$user->delete()) {

            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "users",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("user was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "users",
            'action' => "index"
        ]);
    }

    public function allAction()
    {
        $users = new Users();

        $response = new Response();
        $response
            ->setJsonContent($users->all())
            ->send();

        return $response;
    }

    public function emailsAction()
    {
        $users = new Users();

        $response = new Response();
        $response
            ->setJsonContent($users->allUserEmails())
            ->send();

        return $response;
    }

}
