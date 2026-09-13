<?php

namespace Tests\Feature\Permission;

use PHPOpenSourceSaver\Fractal\Manager;
use PHPOpenSourceSaver\Fractal\Resource\Collection;
use Laraplate\Api\V1\Transformers\PermissionTransformer;
use Laraplate\Entities\Permission\Models\Permission;
use Laraplate\Serializers\CustomSerializer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PermissionIndexTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function it_should_get_all_permissions()
    {
        //ARRANGE
        Permission::truncate();
        $permissionData = Permission::factory()->count(2)->create();

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
