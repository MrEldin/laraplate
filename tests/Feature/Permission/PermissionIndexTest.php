<?php

namespace Tests\Feature\Permission;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use Laraplate\Api\V1\Transformers\PermissionTransformer;
use Laraplate\Entities\Permission\Models\Permission;
use Laraplate\Serializers\CustomSerializer;
use Tests\TestCase;

class PermissionIndexTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_should_get_all_permissions()
    {
        //ARRANGE
        Permission::truncate();
        $permissionData = factory(Permission::class, 2)->create();

        //ACT
        $response = $this->get(
            url('/api/permissions'),
            $this->getRequestHeaders()
        );

        $manager = new Manager;
        $manager->setSerializer(new CustomSerializer);
        $resource = new Collection($permissionData, new PermissionTransformer());

        //ASSERT
        $this->assertEquals($manager->createData($resource)->toJson(), $response->getContent());
    }
}
