<?php

namespace Tests\Feature\User;

use Illuminate\Http\Response;
use Laraplate\Entities\User\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserCreateXTest extends TestCase
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
    public function it_should_fail_create_user_on_invalid_request($data, $message, $type)
    {
        //ARRANGE
        $user = User::factory()->create();

        $userData = $user->toArray();
        $userData[$type] = $data[$type];

        //ACT
        $response = $this->post(
            url('/api/users'),
            $userData,
            $this->getRequestHeaders()
        );

        //ASSERT
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertEquals($message, $response->getOriginalContent()['errors']->get($type)[0]);
    }

    public static function agencyCreateInvalidData()
    {
        return [
            #0 Empty user name
            [
                [
                    User::FIRST_NAME => ''
                ],
                'The first name field is required.',
                User::FIRST_NAME
            ],
            #0 Empty user last name
            [
                [
                    User::LAST_NAME => ''
                ],
                'The last name field is required.',
                User::LAST_NAME
            ],
            #0 Empty user password
            [
                [
                    User::PASSWORD => ''
                ],
                'The password field is required.',
                User::PASSWORD
            ]
        ];
    }
}
