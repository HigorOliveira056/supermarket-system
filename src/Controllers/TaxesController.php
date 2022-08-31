<?php
namespace App\Controllers;

use App\Domain\Taxes;
use App\Repository\TaxesRepository;
use App\Services\RequestFactory as Request;
use App\Services\Json;


class TaxesController {
    static public function show (array $params) : Json {
        $id = (int) $params['id'];
        $repository = new TaxesRepository;
        $taxes = $repository->get($id);

        if (is_null($taxes))
            return new Json(['message' => 'Nenhuma taxa encontrada','error' => true]);

        return $taxes->toJson();
    }

    static public function showAll () : Json {
       $repository = new TaxesRepository;
       $collection = $repository->getAll();
       return new Json($collection->toArray());
    }

    static public function save () : Json {
        $request = new Request;
        $taxes = new Taxes;
        $errors = $taxes->rules($request);
        if (count($errors) > 0) {
            return new Json($errors);
        }

        $taxes->name = $request->get('name');
        $taxes->percentual = $request->get('percentual');

        $repository = new TaxesRepository;
        $save = $repository->save($taxes);

        if ($save) return new Json(['error' => false, 'message' => 'Taxa salva com suceso']);

        return new Json(['error' => true, 'message' => 'Não foi possível salvar a taxa']);
    }

    static public function update (array $params) : Json {
        $repository = new TaxesRepository;
        $request = new Request;
        $taxes = new Taxes;
        $errors = $taxes->rules($request);
        if (count($errors) > 0) {
            return new Json($errors);
        }

        $taxes->id = $params['id'];
        $taxes->name = $request->get('name');
        $taxes->percentual = $request->get('percentual');

        $repository = new TaxesRepository;
        $update = $repository->update($taxes);

        if ($update) return new Json(['error' => false, 'message' => 'Taxa atualizada com suceso']);

        return new Json(['error' => true, 'message' => 'Não foi possível atualizar a taxa']);
    }

    static public function delete (array $params) : Json {
        $repository = new TaxesRepository;
        $taxes = new Taxes;
        $taxes->id = $params['id'];
        $delete = $repository->delete($taxes);

        if ($delete) return new Json(['error' => false, 'message' => 'Taxa deletada com suceso']);

        return new Json(['error' => true, 'message' => 'Não foi possível deletar a taxa']);
    }

}