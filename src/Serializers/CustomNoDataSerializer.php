<?php

namespace Laraplate\Serializers;

use League\Fractal\Serializer\ArraySerializer;

/**
 * Class CustomIncludeSerializer
 * @package App\Cpl\Serializers
 */
class CustomNoDataSerializer extends ArraySerializer
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
        return $data;
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
        return $data;
    }
}
