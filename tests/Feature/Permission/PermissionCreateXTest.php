<?php

namespace Tests\Feature\Permission;

use Illuminate\Http\Response;
use Laraplate\Entities\Permission\Models\Permission;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PermissionCreateXTest extends TestCase
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
    public function it_should_fail_create_permission_on_invalid_request($data, $message, $type)
    {
        //ARRANGE
        $permission = Permission::factory()->create();

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

    public static function agencyCreateInvalidData()
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
