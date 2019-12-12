<?php

use Phalcon\Mvc\Model\Query;
use Phalcon\Di;

class Products extends \Phalcon\Mvc\Model
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
    public $product_name;

    /**
     *
     * @var string
     */
    public $price;

    /**
     *
     * @var string
     */
    public $sale;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("public");
        $this->setSource("products");
        $this->hasManyToMany(
            "id",
            "OrderProducts",
            "product_id",
            "order_id",
            "Orders",
            "id",
            array('alias' => 'orders')
        );
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'products';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Products[]|Products|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Products|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public function all()
    {
        $query = new Query(
            'SELECT * FROM Products',
            $this->di
        );

        return $query->execute();
    }

    public function getIdFromProductName($productName)
    {
        $query = new Query(
            "SELECT * FROM Products where product_name = '" . $productName . "'",
            $this->di
        );

        $product = $query->execute();

        return $product[0]->id;
    }

    public function getId() {}

}
