<?php

namespace Tests\Feature\Role;

use PHPOpenSourceSaver\Fractal\Manager;
use PHPOpenSourceSaver\Fractal\Resource\Collection;
use PHPOpenSourceSaver\Fractal\Resource\Item;
use Laraplate\Api\V1\Transformers\RoleTransformer;
use Laraplate\Entities\Role\Models\Role;
use Laraplate\Serializers\CustomSerializer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RoleFindTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function it_should_get_one_role()
    {
        //ARRANGE
        $roleData = Role::factory()->create();

        //ACT
        $response = $this->get(
            url('/api/roles', [Role::ID => $roleData->{Role::ID}]),
            $this->getRequestHeaders()
        );

        $manager = new Manager;
        $manager->setSerializer(new CustomSerializer);
        $resource = new Item($roleData, new RoleTransformer());

        //ASSERT
        $this->assertEquals($manager->createData($resource)->toJson(), $response->getContent());
    }
}
