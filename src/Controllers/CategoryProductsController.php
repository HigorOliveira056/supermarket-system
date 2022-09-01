<?php
namespace App\Controllers;

use App\Domain\CategoryProducts;
use App\Domain\Taxes;
use App\Repository\CategoryProductsRepository;
use App\Helpers\RequestFactory as Request;
use App\Helpers\Json;


class CategoryProductsController extends BaseController {
    static public function show (array $params) : Json {
        $id = (int) $params['id'];
        $repository = new CategoryProductsRepository;
        $category = $repository->get($id);

        if (is_null($category))
            return new Json(['message' => 'Nenhuma categoria encontrada','error' => true]);

        return $category->toJson();
    }

    static public function showAll () : Json {
        $repository = new CategoryProductsRepository;
        $collection = $repository->getAll();
        $response = [];
        foreach ($collection as $category) {
            $response[] = (string) $category->toJson();
        }
        return new Json($response);
    }

    static public function save () : Json {
        $request = new Request;
        $category = new CategoryProducts;
        $rules = $category->rules();
        $errors = self::validate($rules, $request);
        if (count($errors) > 0) {
            return new Json($errors);
        }

        $taxes = explode(';', $request->get('taxes_id'));
        
        if (empty($taxes[0])) return new Json(['error' => true, 'message' => 'Ao menos uma taxa deverá ser informada']);

        $category->name = $request->get('name');
        $category->description = $request->get('description', '');
        
        foreach ($taxes as $id_tax) {
            $tax = new Taxes;
            $tax->id = (int) $id_tax;
            $category->addTax($tax);
        }

        $repository = new CategoryProductsRepository;
        $save = $repository->save($category);

        if (!$save) return new Json(['error' => true, 'message' => 'Não foi possível salvar a categoria']);
    
        return new Json(['error' => false, 'message' => 'Categoria salva com suceso']);
    }

    static public function update (array $params) : Json {
        $repository = new CategoryProductsRepository;
        $request = new Request;
        $category = new CategoryProducts;
        $rules = $category->rules();
        $errors = self::validate($rules, $request);
        if (count($errors) > 0) {
            return new Json($errors);
        }
        $taxes = explode(';', $request->get('taxes_id'));
        
        if (empty($taxes[0])) return new Json(['error' => true, 'message' => 'Ao menos uma taxa deverá ser informada']);

        foreach ($taxes as $id_tax) {
            $tax = new Taxes;
            $tax->id = (int) $id_tax;
            $category->addTax($tax);
        }

        $category->id = $params['id'];
        $category->name = $request->get('name');
        $category->description = $request->get('description');

        $repository = new CategoryProductsRepository;
        $update = $repository->update($category);
        
        if (!$update) return new Json(['error' => true, 'message' => 'Não foi possível atualizar a categoria']);

        return new Json(['error' => false, 'message' => 'Categoria atualizada com suceso']);
    }

    static public function delete (array $params) : Json {
        $repository = new CategoryProductsRepository;
        $category = new CategoryProducts;
        $category->id = $params['id'];
        $delete = $repository->delete($category);

        if (!$delete) return new Json(['error' => true, 'message' => 'Não foi possível deletar a categoria']);
        
        return new Json(['error' => false, 'message' => 'Categoria deletada com suceso']);
    }

}