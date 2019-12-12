<?php
use Phalcon\Mvc\Model\Query;
use Phalcon\Di;

class Orders extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $order_id;

    /**
     *
     * @var integer
     */
    public $user_id;

    /**
     *
     * @var string
     */
    public $products;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("public");
        $this->setSource("orders");
        $this->belongsTo('user_id', 'Users', 'id', ['alias' => 'Users']);
        $this->hasManyToMany(
            "id",
            "OrderProducts",
            "order_id",
            "product_id",
            "Products",
            "id",
            array('alias' => 'products')
        );
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'orders';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Orders[]|Orders|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Orders|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public function all()
    {
        $query = new Query(
            'SELECT * FROM Orders',
            $this->di
        );

        return $query->execute();
    }

}
