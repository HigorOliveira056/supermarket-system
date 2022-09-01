<?php
namespace App\Domain;

use App\Helpers\Json;

class SalesProduct extends Entity {
    protected int $sales_id;
    protected int $product_id;
    protected int $quantity;

    protected Sales $sales;
    protected Product $product;

    public function insertProduct(Sales $sales, Product $product, int $quantity) : void {
        $this->sales = $sales;
        $this->sales_id = $sales->id;
        $this->product = $product;
        $this->product_id = $product->id;
        $this->quantity = $quantity;
    }

    public function getTotalPrice () : float {
        return $this->quantity * $this->product->getPrice();
    }

    public function toJson () : Json {
        return new Json([
            'sales' => (string) $this->sales->toJson(),
            'product' => (string) $this->product->toJson(),
            'quantity' => $this->quantity,
            'total_price' => $this->getTotalPrice(),
        ]);
    }

}