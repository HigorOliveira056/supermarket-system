<?php
namespace App\Domain;

use App\Helpers\GenericCollection;
use App\Helpers\Json;

class Sales extends Entity {
    protected int $id;
    protected int $seller_id;
    protected int $client_id;

    protected Seller $seller;
    protected Client $client;

    private GenericCollection $sales_products;

    public function __construct () {
        $this->sales_products = new GenericCollection(SalesProduct::class);
    }

    public function addProduct (SalesProduct $product) : void {
        $this->sales_products->add($product);
    }

    public function removeProduct (SalesProduct $product) : void {
        $this->sales_products = $this->sales_products->filter(function ($item) use ($product) {
            return $item !== $product;
        });
    }

    public function getProducts () : GenericCollection {
        return clone $this->sales_products;
    }

    protected function calculateTotalPrice() : float {
        $sales = $this->sales_products->toArray();
        return array_reduce($sales, function ($acc, $item) {
            return $item->getTotalPrice() + $acc;
        });
    }

    protected function calculateTotalQuantityProducts() : int {
        $sales = $this->sales_products->toArray();
        return array_reduce($sales, function ($acc, $item) {
            return $item->quantity + $acc;
        });
    }

    public function toJson () : Json {
        return new Json([
            'id' => $this->id,
            'seller' => (string) $this->seller->toJson(),
            'client' => (string) $this->client->toJson(),
            'total_price' => $this->calculateTotalPrice(),
            'quantity' => $this->calculateTotalQuantityProducts(),
        ]);
    }

}