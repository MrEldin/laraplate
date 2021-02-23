<?php
namespace Laraplate\Api\V1\Controllers;
use Dingo\Api\Routing\Helpers;
use Illuminate\Routing\Controller as BaseController;

/**
 * This class should be parent class for other API controllers
 * Class ApiController
 */
class Controller extends BaseController
{
    use Helpers;
}
