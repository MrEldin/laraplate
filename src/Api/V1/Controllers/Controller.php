<?php
namespace Laraplate\Api\V1\Controllers;
use Dingo\Api\Routing\Helpers;
use Illuminate\Routing\Controller as BaseController;
/**
 * @SWG\Swagger(
 *   basePath="/api",
 *   @SWG\Info(
 *     title="SMATRLYJOBS API DOCUMENTATION",
 *     version="1.0.0",
 *   ),
 *     @SWG\SecurityScheme(
 *         securityDefinition="jwt",
 *         in="header",
 *         name= "Authorization",
 *         type="apiKey",
 *         description="add jwt token with Bearer ",
 *         flow="implicit",
 *         authorizationUrl="/auth/login",
 *         scopes={"all": "full access with token."}
 *     )
 *  )
 * This class should be parent class for other API controllers
 * Class ApiController
 */
class Controller extends BaseController
{
    use Helpers;
}
