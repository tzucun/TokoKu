<?php
class Product {
    public $id;
    public $name;
    public $description;
    public $price;
    public $stock;
    public $image;

    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
?>