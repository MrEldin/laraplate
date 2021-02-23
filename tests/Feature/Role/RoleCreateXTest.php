<?php

namespace Tests\Feature\Role;

use Illuminate\Http\Response;
use Laraplate\Entities\Role\Models\Role;
use Tests\TestCase;

class RoleCreateXTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }


    /**
     * @test
     * @dataProvider agencyCreateInvalidData
     * @param $data
     * @param $message
     * @param $type
     */
    public function it_should_fail_create_role_on_invalid_request($data, $message, $type)
    {
        //ARRANGE
        $role = factory(Role::class)->create();

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

    public function agencyCreateInvalidData()
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
