<?php
namespace App\Controllers;

use App\Domain\CategoryProducts;
use App\Repository\CategoryProductsRepository;
use App\Services\RequestFactory as Request;
use App\Services\Json;


class CategoryProductsController {
    static public function show (array $params) : Json {
        $id = (int) $params['id'];
        $repository = new CategoryProductsRepository;
        $category = $repository->get($id);

        if (is_null($category))
            return new Json(['message' => 'Nenhum produto encontrado','error' => true]);

        return $category->toJson();
    }

    static public function showAll () : Json {
       $repository = new CategoryProductsRepository;
       $collection = $repository->getAll();
       return new Json($collection->toArray());
    }

    static public function save () : Json {
        $request = new Request;
        $category = new CategoryProducts;
        $errors = $category->rules($request);
        if (count($errors) > 0) {
            return new Json($errors);
        }

        $category->name = $request->get('name');
        $category->description = $request->get('description');

        $repository = new CategoryProductsRepository;
        $save = $repository->save($category);

        if ($save) return new Json(['error' => false, 'message' => 'Produto salvo com suceso']);

        return new Json(['error' => true, 'message' => 'Não foi possível salvar o produto']);
    }

    static public function update (array $params) : Json {
        $repository = new CategoryProductsRepository;
        $request = new Request;
        $category = new CategoryProducts;
        $errors = $category->rules($request);
        if (count($errors) > 0) {
            return new Json($errors);
        }

        $category->id = $params['id'];
        $category->name = $request->get('name');
        $category->description = $request->get('description');

        $repository = new CategoryProductsRepository;
        $update = $repository->update($category);

        if ($update) return new Json(['error' => false, 'message' => 'Produto atualizado com suceso']);

        return new Json(['error' => true, 'message' => 'Não foi possível atualizar o produto']);
    }

    static public function delete (array $params) : Json {
        $repository = new CategoryProductsRepository;
        $category = new CategoryProducts;
        $category->id = $params['id'];
        $delete = $repository->delete($category);

        if ($delete) return new Json(['error' => false, 'message' => 'Produto deletado com suceso']);

        return new Json(['error' => true, 'message' => 'Não foi possível deletar o produto']);
    }

}