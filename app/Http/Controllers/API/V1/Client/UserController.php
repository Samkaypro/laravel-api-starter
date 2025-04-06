<?php

namespace App\Http\Controllers\API\V1\Client;

use App\DTOs\UserProfileDTO;
use App\Http\Controllers\API\BaseController;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\User\UpdateProfilePictureRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * @OA\Tag(
 *     name="User Profile",
 *     description="API Endpoints for user profile management"
 * )
 */
class UserController extends BaseController
{
    /**
     * Get authenticated user profile
     *
     * @OA\Get(
     *     path="/api/v1/user",
     *     operationId="getUserProfile",
     *     tags={"User Profile"},
     *     summary="Get authenticated user profile",
     *     description="Returns authenticated user profile data including roles and permissions",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com"),
     *                 @OA\Property(property="profile_picture", type="string", nullable=true),
     *                 @OA\Property(property="roles", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="permissions", type="array", @OA\Items(type="string"))
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
     *     )
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $userProfileDto = UserProfileDTO::fromUser($user);
        
        return $this->sendResponse($userProfileDto->toArray());
    }
    
    /**
     * Update user profile
     *
     * @OA\Put(
     *     path="/api/v1/user",
     *     operationId="updateUserProfile",
     *     tags={"User Profile"},
     *     summary="Update user profile information",
     *     description="Updates the authenticated user's profile information",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="User profile updated successfully.")
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
     * @param UpdateProfileRequest $request
     * @return JsonResponse
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        
        // Update user profile
        $updateData = $request->validated();
        
        // Exclude profile picture if present in validated data
        unset($updateData['profile_picture']);
        
        $user->update($updateData);
        
        // Return user profile data using DTO
        $userProfileDto = UserProfileDTO::fromUser($user);
        
        return $this->sendResponse($userProfileDto->toArray(), 'User profile updated successfully.');
    }
    
    /**
     * Update user password
     *
     * @OA\Put(
     *     path="/api/v1/user/password",
     *     operationId="updateUserPassword",
     *     tags={"User Profile"},
     *     summary="Update user password",
     *     description="Updates the authenticated user's password",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"current_password","password","password_confirmation"},
     *             @OA\Property(property="current_password", type="string", format="password", example="current_password"),
     *             @OA\Property(property="password", type="string", format="password", example="new_password"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="new_password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Password updated successfully.")
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
     * @param UpdatePasswordRequest $request
     * @return JsonResponse
     */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $user = $request->user();
        
        $user->update([
            'password' => Hash::make($request->password),
        ]);
        
        return $this->sendResponse([], 'Password updated successfully.');
    }
    
    /**
     * Upload user profile picture
     *
     * @OA\Post(
     *     path="/api/v1/user/profile-picture",
     *     operationId="uploadProfilePicture",
     *     tags={"User Profile"},
     *     summary="Upload profile picture",
     *     description="Uploads a profile picture for the authenticated user",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="profile_picture",
     *                     type="file",
     *                     format="file",
     *                     description="Profile picture file (jpeg, png, jpg, gif)"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Profile picture uploaded successfully.")
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
     * @param UpdateProfilePictureRequest $request
     * @return JsonResponse
     */
    public function uploadProfilePicture(UpdateProfilePictureRequest $request): JsonResponse
    {
        $user = $request->user();
        
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if it exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            
            // Store the new profile picture
            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $user->profile_picture = $path;
            $user->save();
            
            // Return user profile data
            $userProfileDto = UserProfileDTO::fromUser($user);
            
            return $this->sendResponse($userProfileDto->toArray(), 'Profile picture uploaded successfully.');
        }
        
        return $this->sendError('No profile picture provided.', [], 422);
    }
    
    /**
     * Delete user profile picture
     *
     * @OA\Delete(
     *     path="/api/v1/user/profile-picture",
     *     operationId="deleteProfilePicture",
     *     tags={"User Profile"},
     *     summary="Delete profile picture",
     *     description="Deletes the profile picture for the authenticated user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Profile picture deleted successfully.")
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
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No profile picture to delete."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteProfilePicture(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->profile_picture) {
            return $this->sendError('No profile picture to delete.', [], 404);
        }
        
        // Delete the profile picture from storage
        Storage::disk('public')->delete($user->profile_picture);
        
        // Update user record
        $user->profile_picture = null;
        $user->save();
        
        // Return user profile data
        $userProfileDto = UserProfileDTO::fromUser($user);
        
        return $this->sendResponse($userProfileDto->toArray(), 'Profile picture deleted successfully.');
    }
} 