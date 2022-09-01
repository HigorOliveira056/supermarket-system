<?php
namespace App\Controllers;

use App\Domain\Product;
use App\Repository\ProductRepository;
use App\Repository\CategoryProductsRepository;
use App\Helpers\RequestFactory as Request;
use App\Helpers\Json;


class ProductController extends BaseController {
    static public function show (array $params) : Json {
        $id = (int) $params['id'];
        $repository = new ProductRepository;
        $product = $repository->get($id);

        if (is_null($product))
            return new Json(['message' => 'Nenhum produto encontrado','error' => true]);

        return $product->toJson();
    }

    static public function showAll () : Json {
       $repository = new ProductRepository;
       $collection = $repository->getAll();
       $response = [];
       foreach ($collection as $product) {
           $response[] = (string) $product->toJson();
       }
       return new Json($response);
    }

    static public function save () : Json {
        $request = new Request;
        $product = new Product;
        $rules = $product->rules();
        $errors = self::validate($rules, $request);
        if (count($errors) > 0) {
            return new Json($errors);
        }

        $product->category_id = (int) $request->get('category_id');
        $product->name = $request->get('name');
        $product->description = $request->get('description');
        $product->price = $request->get('price');

        $repository = new ProductRepository;
        $save = $repository->save($product);

        if (!$save) return new Json(['error' => true, 'message' => 'Não foi possível salvar o produto']);
        
        return new Json(['error' => false, 'message' => 'Produto salvo com suceso']);
    }

    static public function update (array $params) : Json {
        $request = new Request;
        $product = new Product;
        $rules = $product->rules();
        $errors = self::validate($rules, $request);
        if (count($errors) > 0) {
            return new Json($errors);
        }

        $product->id = $params['id'];
        $product->category_id = $request->get('category_id');
        $product->name = $request->get('name');
        $product->description = $request->get('description');
        $product->price = $request->get('price');

        $repository = new ProductRepository;
        $update = $repository->update($product);

        if (!$update) return new Json(['error' => true, 'message' => 'Não foi possível atualizar o produto']);
        
        return new Json(['error' => false, 'message' => 'Produto atualizado com suceso']);
    }

    static public function delete (array $params) : Json {
        $repository = new ProductRepository;
        $product = new Product;
        $product->id = $params['id'];
        $delete = $repository->delete($product);

        if ($delete) return new Json(['error' => false, 'message' => 'Produto deletado com suceso']);

        return new Json(['error' => true, 'message' => 'Não foi possível deletar o produto']);
    }

}