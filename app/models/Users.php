<?php

use Phalcon\Validation;
use Phalcon\Validation\Validator\Email as EmailValidator;
use Phalcon\Mvc\Model\Query;
use Phalcon\Di;

class Users extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $username;

    /**
     *
     * @var string
     */
    public $password;

    /**
     *
     * @var string
     */
    public $email;

    /**
     * Validations and business logic
     *
     * @return boolean
     */
    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            'email',
            new EmailValidator(
                [
                    'model' => $this,
                    'message' => 'Please enter a correct email address',
                ]
            )
        );

        return $this->validate($validator);
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("public");
        $this->setSource("users");
        $this->hasMany('id', 'Orders', 'user_id', ['alias' => 'Orders']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'users';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Users[]|Users|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Users|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public function all()
    {
        $query = new Query(
            'SELECT * FROM Users',
            $this->di
        );

        return $query->execute();
    }

    public function allUserEmails()
    {
        $query = new Query(
            'SELECT email FROM Users',
            $this->di
        );

        return $query->execute();
    }


    public function getIdFromEmail($userEmail)
    {
        $query = new Query(
            "SELECT * FROM Users where email = '" . $userEmail . "'",
            $this->di
        );

        $user = $query->execute();

        return $user[0]->id;
    }

    public function getUserId() {}



}
