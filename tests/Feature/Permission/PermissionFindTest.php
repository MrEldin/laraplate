<?php

namespace Tests\Feature\Permission;

use PHPOpenSourceSaver\Fractal\Manager;
use PHPOpenSourceSaver\Fractal\Resource\Collection;
use PHPOpenSourceSaver\Fractal\Resource\Item;
use Laraplate\Api\V1\Transformers\PermissionTransformer;
use Laraplate\Entities\Permission\Models\Permission;
use Laraplate\Serializers\CustomSerializer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PermissionFindTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function it_should_get_one_permission()
    {
        //ARRANGE
        $permissionData = Permission::factory()->create();

        //ACT
        $response = $this->get(
            url('/api/permissions', [Permission::ID => $permissionData->{Permission::ID}]),
            $this->getRequestHeaders()
        );

        $manager = new Manager;
        $manager->setSerializer(new CustomSerializer);
        $resource = new Item($permissionData, new PermissionTransformer());

        //ASSERT
        $this->assertEquals($manager->createData($resource)->toJson(), $response->getContent());
    }
}
