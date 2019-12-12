<?php
// TESTING CONTROLLER
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;

class AboutController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        echo 'testindex';
//        $this->persistent->parameters = null;
    }


    public function testAction()
    {
        echo 'testfunctioncustom';
//        $users = Users::find('all');
//        var_dump($users);
    }
}
