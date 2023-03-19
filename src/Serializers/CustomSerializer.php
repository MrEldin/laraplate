<?php

namespace Laraplate\Serializers;

use League\Fractal\Serializer\ArraySerializer;

/**
 * Class CustomSerializer
 * @package App\Cpl\Serializers
 */
class CustomSerializer extends ArraySerializer
{
    /**
     * Serialize a collection.
     *
     * @param string $resourceKey
     * @param array  $data
     *
     * @return array
     */
    public function collection(?string $resourceKey, array $data): array
    {
        if ($resourceKey === false){
            return $data;
        }

        return ['data' => $data];
    }

    /**
     * Serialize an item.
     *
     * @param string $resourceKey
     * @param array  $data
     *
     * @return array
     */
    public function item(?string $resourceKey, array $data): array
    {
        if ($resourceKey === false){
            return $data;
        }

        return ['data' => $data];
    }
}
