<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity;
use App\Model\Keys;
use App\Model\KeyType;
use App\Model\Status;

class GetKeyListController extends AbstractController
{
    public function __construct(
        Entity  $entityModel,
        Keys    $keysModel,
        KeyType $keyTypeModel,
        Status  $statusModel
    )
    {
        parent::__construct();
        $this->entityModel = $entityModel;
        $this->keysModel = $keysModel;
        $this->keyTypeModel = $keyTypeModel;
        $this->statusModel = $statusModel;
    }

    public function index(): array
    {
        try {
            $validatedData = $this->request->all();

            if (empty($validatedData['userId']) || empty($validatedData['userType'])) {
                throw new \Exception('campos invalidos');
            }

            $user = $this->entityModel::where('entity_identifier', $validatedData['userId'])
                ->where('entity_Type', $validatedData['userType'])
                ->first();

            $items = $this->keysModel::where('entity_id', $user->id)->get();

            return $this->flatKeyTypes($validatedData['userType'], array_values($items->toArray()));

        } catch (\Throwable $exception) {
            var_dump($exception);
            die();
        }
    }

    private function flatKeyTypes(string $userType, array $items): array
    {
        $types = ['random', 'phone', 'email', 'cpf'];
        if ($userType === 'company') {
            $types = ['random', 'phone', 'email', 'cnpj'];
        }

        $keyArray = array_map(
            static fn($type) => ['type' => 'unregistered_key', 'attributes' => ['keyType' => $type,]],
            $types
        );

        $countKey = 0;
        $newKeyArray = [];
        $hasRandom = false;

        foreach ($keyArray as $attributes) {
            if (
                ($userType === 'person' && count($items) < 5) ||
                ($userType === 'company' && count($items) < 10)
            ) {
                $newKeyArray[$countKey] = $attributes;
            }
            foreach ($items as $key) {
                $keyType = $this->keyTypeModel::where('id', $key['key_type_id'])->first();

                if ($keyType->name === $attributes['attributes']['keyType']) {
                    $newKeyArray[$countKey] = $key;
                    $countKey++;
                    if ($keyType->name === 'random') {
                        $hasRandom = true;
                    }
                }
            }
            $countKey++;
        }

        if (
            $hasRandom === true &&
            (
                ($userType === 'person' && count($items) < 5) ||
                ($userType === 'company' && count($items) < 10)
            )
        ) {
            $newKeyArray[] = ['type' => 'unregistered_key', 'attributes' => ['keyType' => 'random',]];
        }


        $order = ['cpf', 'cnpj', 'phone', 'email', 'random'];

        usort($newKeyArray, function ($a, $b) use ($order) {
            $pos_a = array_search($a['attributes']['keyType'], $order);
            $pos_b = array_search($b['attributes']['keyType'], $order);
            return $pos_a - $pos_b;
        });

        return ['data' => array_values($newKeyArray)];
    }

}
