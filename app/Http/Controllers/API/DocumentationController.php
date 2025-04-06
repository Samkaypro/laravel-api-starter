<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Laravel API Starter",
 *      description="A modern RESTful API starter kit built with Laravel, featuring authentication, roles, permissions, and comprehensive documentation. The API provides endpoints for user management, authentication, social login, and role-based access control.",
 *      @OA\Contact(
 *          email="samkaypro@gmail.com",
 *          name="Samuel Kayode",
 *          url="https://samkaypro.github.io"
 *      ),
 *      @OA\License(
 *          name="MIT",
 *          url="https://opensource.org/licenses/MIT"
 *      ),
 *      @OA\ExternalDocumentation(
 *          description="Find more information here",
 *          url="https://github.com/samkaypro/laravel-api-starter"
 *      )
 * )
 * 
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="API Server"
 * )
 * 
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT",
 *      description="Use a JWT token to access this API. You can obtain a token through the login or register endpoints."
 * )
 *
 * @OA\Components(
 *      @OA\Schema(
 *          schema="ApiResponse",
 *          @OA\Property(property="success", type="boolean", example=true),
 *          @OA\Property(property="data", type="object"),
 *          @OA\Property(property="message", type="string", example="Operation successful")
 *      ),
 *      @OA\Schema(
 *          schema="ValidationError",
 *          @OA\Property(property="success", type="boolean", example=false),
 *          @OA\Property(property="message", type="string", example="Validation Error."),
 *          @OA\Property(property="errors", type="object")
 *      )
 * )
 * 
 * @OA\Tag(
 *     name="Authentication",
 *     description="API Endpoints for user authentication"
 * )
 * 
 * @OA\Tag(
 *     name="User Profile",
 *     description="API Endpoints for user profile management"
 * )
 * 
 * @OA\Tag(
 *     name="Admin Users",
 *     description="API Endpoints for user management (admin only)"
 * )
 * 
 * @OA\Tag(
 *     name="Admin Roles",
 *     description="API Endpoints for role management (admin only)"
 * )
 * 
 * @OA\Tag(
 *     name="Password Reset",
 *     description="API Endpoints for password reset functionality"
 * )
 */
class DocumentationController extends Controller
{
    /**
     * Display API documentation.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect('/api/documentation');
    }
} 


// <?php

// namespace App\OpenApi;


// class OpenApiDefinitions
// {
//     // This class doesn't need any content - it's just a container for annotations
// }