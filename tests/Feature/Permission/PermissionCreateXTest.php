<?php

namespace Tests\Feature\Permission;

use Illuminate\Http\Response;
use Laraplate\Entities\Permission\Models\Permission;
use Tests\TestCase;

class PermissionCreateXTest extends TestCase
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
    public function it_should_fail_create_permission_on_invalid_request($data, $message, $type)
    {
        //ARRANGE
        $permission = factory(Permission::class)->create();

        $permissionData = $permission->toArray();
        $permissionData[$type] = $data[$type];

        //ACT
        $response = $this->post(
            url('/api/permissions'),
            $permissionData,
            $this->getRequestHeaders()
        );

        //ASSERT
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertEquals($message, $response->getOriginalContent()['errors']->get($type)[0]);
    }

    public function agencyCreateInvalidData()
    {
        return [
            #0 Empty permission name
            [
                [
                    Permission::NAME => ''
                ],
                'The name field is required.',
                Permission::NAME
            ]
        ];
    }
}
