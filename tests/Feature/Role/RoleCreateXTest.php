<?php

namespace Tests\Feature\Role;

use Illuminate\Http\Response;
use Laraplate\Entities\Role\Models\Role;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RoleCreateXTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }


    /**
     * @param $data
     * @param $message
     * @param $type
     */
    #[Test]
    #[DataProvider('agencyCreateInvalidData')]
    public function it_should_fail_create_role_on_invalid_request($data, $message, $type)
    {
        //ARRANGE
        $role = Role::factory()->create();

        $roleData = $role->toArray();
        $roleData[$type] = $data[$type];

        //ACT
        $response = $this->post(
            url('/api/roles'),
            $roleData,
            $this->getRequestHeaders()
        );

        //ASSERT
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertEquals($message, $response->getOriginalContent()['errors']->get($type)[0]);
    }

    public static function agencyCreateInvalidData()
    {
        return [
            #0 Empty role name
            [
                [
                    Role::NAME => ''
                ],
                'The name field is required.',
                Role::NAME
            ]
        ];
    }
}
