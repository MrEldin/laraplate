<?php

namespace Tests\Feature\Permission;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Laraplate\Api\V1\Transformers\PermissionTransformer;
use Laraplate\Entities\Permission\Models\Permission;
use Laraplate\Serializers\CustomSerializer;
use Tests\TestCase;

class PermissionFindTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_should_get_one_permission()
    {
        //ARRANGE
        $permissionData = factory(Permission::class)->create();

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
