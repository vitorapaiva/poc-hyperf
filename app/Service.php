<?php declare(strict_types=1);

namespace App;

use Hyperf\DbConnection\Db;

class Service
{
    public function __construct(
        private Db $database,
    ) {}

    public function index($request)
    {
        try {
            $validatedData = $request->all();

            if (empty($validatedData['userId']) || empty($validatedData['userType'])) {
                throw new \Exception('campos invalidos');
            }

            $user = $this->database::table('entities')
                ->where('entity_identifier', $validatedData['userId'])
                ->where('entity_Type', $validatedData['userType'])
                ->first();

            $items = $this->database::table('keys')
                ->where('entity_id', $user->id)->get();

            return $this->flatKeyTypes($validatedData['userType'], array_values($items->toArray()));
        } catch (\Throwable $exception) {
            var_dump($exception->getMessage(), $exception->getLine());
        }
    }


    private function flatKeyTypes(string $userType, array $items): array
    {
        $types = ['random', 'phone', 'email', 'cpf'];
        if ($userType === 'company') {
            $types = ['random', 'phone', 'email', 'cnpj'];
        }

        $keyArray = array_map(
            static fn ($type) => ['type' => 'unregistered_key', 'attributes' => ['keyType' => $type]],
            $types
        );

        $countKey = 0;
        $newKeyArray = [];
        $hasRandom = false;

        // new
        $key_type_ids = collect($items)->pluck('key_type_id')->unique();
        $keyTypes = $this->database->table('key_types')->whereIn('id', $key_type_ids)->get()->keyBy('id');

        foreach ($keyArray as $attributes) {
            if (
                ($userType === 'person' && count($items) < 5)
                || ($userType === 'company' && count($items) < 10)
            ) {
                $newKeyArray[$countKey] = $attributes;
            }
            foreach ($items as $key) {
                //$keyType = $this->database->table('key_types')->where('id', $key->key_type_id)->first();
                $keyType = $keyTypes->get($key->key_type_id);

                if ($keyType->name === $attributes['attributes']['keyType']) {
                    $newKeyArray[$countKey] = $key;
                    ++$countKey;
                    if ($keyType->name === 'random') {
                        $hasRandom = true;
                    }
                }
            }
            ++$countKey;
        }

        if (
            $hasRandom === true
            && (
                ($userType === 'person' && count($items) < 5)
                || ($userType === 'company' && count($items) < 10)
            )
        ) {
            $newKeyArray[] = ['type' => 'unregistered_key', 'attributes' => ['keyType' => 'random']];
        }

        $order = ['cpf', 'cnpj', 'phone', 'email', 'random'];

        return ['data' => array_values($newKeyArray)];
    }
}