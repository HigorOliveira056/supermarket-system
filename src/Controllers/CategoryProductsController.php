<?php
namespace App\Controllers;

use App\Domain\CategoryProducts;
use App\Domain\CategoryProductsTaxes;
use App\Repository\CategoryProductsRepository;
use App\Helpers\RequestFactory as Request;
use App\Helpers\Json;


class CategoryProductsController {
    static public function show (array $params) : Json {
        $id = (int) $params['id'];
        $repository = new CategoryProductsRepository;
        $category = $repository->get($id);

        if (is_null($category))
            return new Json(['message' => 'Nenhuma categoria encontrada','error' => true]);

        $taxesCollection = $repository->oneToMany($category);
        $taxes = [];
        foreach ($taxesCollection->toArray() as $item) {
            $taxes[] = (string) $item->toJson();
        }
        $response = [
            'entity' => (string) $category->toJson(),
            'taxe' => $taxes,
        ];
        return new Json($response);
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

        $taxes = explode(';', $request->get('taxes_id', ''));
        
        if (count($taxes) < 1 || empty($taxes[0])) return new Json(['error' => true, 'message' => 'Ao menos uma taxa deverá ser informada']);

        $category->name = $request->get('name');
        $category->description = $request->get('description', '');

        $repository = new CategoryProductsRepository;
        $save = $repository->save($category);

        if (!$save) return new Json(['error' => true, 'message' => 'Não foi possível salvar a categoria']);

        $category_id = $repository->getInsertedId();
        
        foreach ($taxes as $tax) {
            $category_taxes = new CategoryProductsTaxes;
            $category_taxes->category_id = $category_id;
            $category_taxes->taxe_id = $tax;
            self::saveTaxe($category_taxes);
        }
    
        return new Json(['error' => false, 'message' => 'Categoria salva com suceso']);
    }

    static protected function saveTaxe (CategoryProductsTaxes $category_taxes) : bool {
        $repository = new CategoryProductsRepository;
        try {
            $save_taxe = $repository->saveTaxe($category_taxes);
        }catch (\Exception $e) {
            return false;
        }
        return true;
    }

    static public function update (array $params) : Json {
        $repository = new CategoryProductsRepository;
        $request = new Request;
        $category = new CategoryProducts;
        $errors = $category->rules($request);
        if (count($errors) > 0) {
            return new Json($errors);
        }
        $taxes = explode(';', $request->get('taxes_id', ''));
        
        if (count($taxes) < 1 || empty($taxes[0])) return new Json(['error' => true, 'message' => 'Ao menos uma taxa deverá ser informada']);

        $category->id = $params['id'];
        $category->name = $request->get('name');
        $category->description = $request->get('description');

        $repository = new CategoryProductsRepository;
        $update = $repository->update($category);
        
        if (!$update) return new Json(['error' => true, 'message' => 'Não foi possível atualizar a categoria']);

        $repository->deleteOneToMany($category);

        foreach ($taxes as $tax) {
            $category_taxes = new CategoryProductsTaxes;
            $category_taxes->category_id = $category->id;
            $category_taxes->taxe_id = $tax;
            self::saveTaxe($category_taxes);
        }
        return new Json(['error' => false, 'message' => 'Categoria atualizada com suceso']);
    }

    static public function delete (array $params) : Json {
        $repository = new CategoryProductsRepository;
        $category = new CategoryProducts;
        $category->id = $params['id'];
        $delete = $repository->delete($category);

        if ($delete) return new Json(['error' => false, 'message' => 'Categoria deletada com suceso']);

        return new Json(['error' => true, 'message' => 'Não foi possível deletar a categoria']);
    }

}