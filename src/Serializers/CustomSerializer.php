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
    public function collection($resourceKey, array $data)
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
    public function item($resourceKey, array $data)
    {
        if ($resourceKey === false){
            return $data;
        }

        return ['data' => $data];
    }
}
