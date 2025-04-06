<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\DTOs\RoleCollectionDTO;
use App\DTOs\RoleDTO;
use App\Http\Controllers\API\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * @OA\Tag(
 *     name="Admin Roles",
 *     description="API Endpoints for role management (admin only)"
 * )
 */
class RoleController extends BaseController
{
    /**
     * Display a listing of roles.
     *
     * @OA\Get(
     *     path="/api/v1/admin/roles",
     *     operationId="listRoles",
     *     tags={"Admin Roles"},
     *     summary="Get list of roles",
     *     description="Returns a list of all roles with their permissions",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="admin"),
     *                 @OA\Property(property="guard_name", type="string", example="web"),
     *                 @OA\Property(property="permissions", type="array", @OA\Items(type="object"))
     *             )),
     *             @OA\Property(property="message", type="string", example="")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="This action is unauthorized.")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Role::with('permissions');
        
        // Get paginated results or all results
        if ($request->has('per_page')) {
            $perPage = $request->input('per_page');
            $roles = $query->paginate($perPage);
            $roleCollectionDto = RoleCollectionDTO::fromPaginator($roles);
        } else {
            $roles = $query->get();
            $roleCollectionDto = RoleCollectionDTO::fromCollection($roles);
        }
        
        return $this->sendResponse($roleCollectionDto->toArray());
    }
    
    /**
     * Store a newly created role.
     *
     * @OA\Post(
     *     path="/api/v1/admin/roles",
     *     operationId="storeRole",
     *     tags={"Admin Roles"},
     *     summary="Create a new role",
     *     description="Creates a new role with optional permissions",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="editor"),
     *             @OA\Property(property="permissions", type="array", @OA\Items(type="string", example="edit-posts"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=3),
     *                 @OA\Property(property="name", type="string", example="editor"),
     *                 @OA\Property(property="guard_name", type="string", example="web"),
     *                 @OA\Property(property="permissions", type="array", @OA\Items(type="object"))
     *             ),
     *             @OA\Property(property="message", type="string", example="Role created successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="This action is unauthorized.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation Error."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }
        
        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);
        
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }
        
        $roleDto = RoleDTO::fromRole($role);
        
        return $this->sendResponse($roleDto->toArray(), 'Role created successfully.', 201);
    }
    
    /**
     * Display the specified role.
     *
     * @OA\Get(
     *     path="/api/v1/admin/roles/{role}",
     *     operationId="showRole",
     *     tags={"Admin Roles"},
     *     summary="Get specific role details",
     *     description="Returns the specified role details including permissions",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="role",
     *         in="path",
     *         description="Role ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="admin"),
     *                 @OA\Property(property="guard_name", type="string", example="web"),
     *                 @OA\Property(property="permissions", type="array", @OA\Items(type="object"))
     *             ),
     *             @OA\Property(property="message", type="string", example="")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="This action is unauthorized.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [Spatie\\Permission\\Models\\Role] 1")
     *         )
     *     )
     * )
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $role = Role::with('permissions')->findOrFail($id);
        
        $roleDto = RoleDTO::fromRole($role);
        
        return $this->sendResponse($roleDto->toArray());
    }
    
    /**
     * Update the specified role.
     *
     * @OA\Put(
     *     path="/api/v1/admin/roles/{role}",
     *     operationId="updateRole",
     *     tags={"Admin Roles"},
     *     summary="Update a role",
     *     description="Updates a specific role's name and permissions",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="role",
     *         in="path",
     *         description="Role ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="editor"),
     *             @OA\Property(property="permissions", type="array", @OA\Items(type="string", example="edit-posts"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="editor"),
     *                 @OA\Property(property="guard_name", type="string", example="web"),
     *                 @OA\Property(property="permissions", type="array", @OA\Items(type="object"))
     *             ),
     *             @OA\Property(property="message", type="string", example="Role updated successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="This action is unauthorized.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [Spatie\\Permission\\Models\\Role] 1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation Error."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255|unique:roles,name,' . $id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }
        
        $role = Role::findOrFail($id);
        
        // Prevent updating 'admin' role name
        if ($role->name === 'admin' && $request->name !== 'admin') {
            return $this->sendError('You cannot change the name of the admin role.', [], 403);
        }
        
        // Update name
        $role->update(['name' => $request->name]);
        
        // Update permissions if provided
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }
        
        // Refresh the role to load updated permissions
        $role = Role::with('permissions')->findOrFail($id);
        
        $roleDto = RoleDTO::fromRole($role);
        
        return $this->sendResponse($roleDto->toArray(), 'Role updated successfully.');
    }
    
    /**
     * Remove the specified role.
     *
     * @OA\Delete(
     *     path="/api/v1/admin/roles/{role}",
     *     operationId="deleteRole",
     *     tags={"Admin Roles"},
     *     summary="Delete a role",
     *     description="Deletes a specific role",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="role",
     *         in="path",
     *         description="Role ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Role deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Cannot delete system roles.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [Spatie\\Permission\\Models\\Role] 1")
     *         )
     *     )
     * )
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $role = Role::findOrFail($id);
        
        // Prevent deleting protected roles
        if (in_array($role->name, ['admin', 'user'])) {
            return $this->sendError('You cannot delete the ' . $role->name . ' role.', [], 403);
        }
        
        $role->delete();
        
        return $this->sendResponse([], 'Role deleted successfully.');
    }
} 