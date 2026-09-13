<?php

namespace Tests\Feature\User;

use PHPOpenSourceSaver\Fractal\Manager;
use PHPOpenSourceSaver\Fractal\Resource\Collection;
use PHPOpenSourceSaver\Fractal\Resource\Item;
use Laraplate\Api\V1\Transformers\UserTransformer;
use Laraplate\Entities\User\Models\User;
use Laraplate\Serializers\CustomSerializer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserFindTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function it_should_get_one_user()
    {
        //ARRANGE
        $userData = User::factory()->create();

        //ACT
        $response = $this->get(
            url('/api/users', [User::ID => $userData->{User::ID}]),
            $this->getRequestHeaders()
        );

        $manager = new Manager;
        $manager->setSerializer(new CustomSerializer);
        $resource = new Item($userData, new UserTransformer());

        //ASSERT
        $this->assertEquals($manager->createData($resource)->toJson(), $response->getContent());
    }
}
