<?php

namespace App\Http\Controllers\API;

use App\Constants\AuthConstants;
use App\Constants\UserConstants;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Http\Traits\Access;
use App\Http\Traits\HttpResponses;
use App\Models\User;
use App\Events\UserRegistered;
use Illuminate\Http\JsonResponse;


class UserController extends Controller
{
    use Access;
    use HttpResponses;

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $users = User::all(); // Obtener todos los usuarios desde la base de datos
        return response()->json(['data' => $users], 200);
    }

        /**
     * @param UserRequest $request
     * @return JsonResponse
     */
    public function store(UserRequest $request): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',

        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
        ]);

        event(new UserRegistered($user));

        return response()->json([
            'message'=>'User Created Successfully',$user
        ],200);
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        if (!$this->canAccess($user)) {
            return $this->error([], AuthConstants::PERMISSION);
        }

        return $this->success(new UserResource($user));
    }

    /**
     * @param UserRequest $request
     * @param Product $product
     * @return JsonResponse
     */
    public function update(UserRequest $request, $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
        
            $user->fill($request->only(['name', 'email']));
        
            if ($request->filled('password')) {
                $user->password = bcrypt($request->password);
            }
        
            $user->save();
        
            return response()->json([
                'message'=>'User update Successfully',$user
            ],200);

        } catch (\Exception $e) {

            return response()->json([
                'error' => 'User Error update ',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();
        
        return response()->json([
            'message'=>'User delete Successfully',$user
        ],200);
    }
}
