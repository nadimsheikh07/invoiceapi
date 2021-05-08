<?php

namespace App\Http\Controllers;

use App\Mail\UserForgotPassword;
use App\Models\User;
use App\Rules\IsValidPassword;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = User::query();
        $query->with('role');

        $columns = ['name', 'email'];

        if (request('search')) {
            $query->whereLike($query, $columns, request('search'));
        }

        if (request('filters')) {
            $filters = json_decode(request('filters'), true);
            if ($filters) {
                foreach ($filters as  $filter) {
                    switch ($filter['name']) {
                        case 'created_at':
                        case 'updated_at':
                            $fieldName = $filter['name'];
                            $query->whereDate($fieldName, Carbon::parse($filter['value']));
                            break;

                        default:
                            $query->whereLike($query, $filter['name'], $filter['value']);
                            break;
                    }
                }
            }
        }

        if (request('startDate')) {
            $startDate = Carbon::parse(request('startDate'));
            $query->whereDate('created_at', '>=', $startDate->format('Y-m-d'));
        }

        if (request('endDate')) {
            $toDate = Carbon::parse(request('endDate'));
            $query->whereDate('created_at', '<=', $toDate->format('Y-m-d'));
        }

        if (request('orderBy') && request('orderDirection')) {
            $query->orderBy(request('orderBy'), request('orderDirection'));
        } else {
            $query->orderBy("created_at", "DESC");
        }

        if (request('pageSize')) {
            $data = $query->paginate(request('pageSize'));
        } else {
            $data = $query->get();
        }

        return response()->data($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required',
            'name' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique(User::class)
            ]
        ]);
        if ($validator->fails()) {
            return response()->validation($validator->errors(), __('response.errors.validation'));
        }
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        User::create($input);
        $message = __('response.messages.success', ['name' => __('userModule.users.title')]);
        return response()->success($message);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $query = User::with(['role'])->where('id', $user->id)->first();
        return response()->data($query);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required',
            'name' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique(User::class)->ignore($user->id)
            ]
        ]);
        if ($validator->fails()) {
            return response()->validation($validator->errors(), __('response.errors.validation'));
        }
        $input = $request->all();
        $user->update($input);
        $message = __('response.messages.update', ['name' => __('userModule.users.title')]);
        return response()->success($message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        $message = __('response.messages.delete', ['name' => __('userModule.users.title')]);
        return response()->success($message);
    }

    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique(User::class)
            ],
            'password' => ['required', new IsValidPassword()],
        ]);
        if ($validator->fails()) {
            return response()->validation($validator->errors(), __('response.errors.validation'));
        }
        $input = $request->all();

        $input['role_id'] = 1;
        $input['password'] = Hash::make($input['password']);

        User::create($input);
        $message = __('response.messages.success', ['name' => __('userModule.users.title')]);
        return response()->success($message);
    }

    public function signin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => ['required', new IsValidPassword()],
            'remember_me' => 'boolean'
        ]);
        if ($validator->fails()) {
            return response()->validation($validator->errors(), __('response.errors.validation'));
        }

        $input = $request->all();

        $user = User::where('email', $input['email'])->first();

        if ($user) {
            if (!Hash::check($input['password'], $user->password)) {
                return response()->json(['error' => __('response.messages.invalid_username_password')], 404);
            }
            $response['accessToken'] =  $user->createToken('userToken')->plainTextToken;
            $response['message'] = 'Login Successfully';
            $response['permissions'] = $user->role->permission_codes;
            return response()->data($response);
        } else {
            $message = __('response.messages.invalid_username_password', ['name' => __('userModule.users.title')]);
            return response()->notFound($message);
        }
    }

    public function signout(Request $request)
    {
        $request->user()->token()->revoke();
        $message = __('response.messages.logged_out_success', ['name' => __('userModule.users.title')]);
        return response()->success($message);
    }

    public function profile(Request $request)
    {
        $data = $request->user();
        return $this->show($data);
    }

    public function updatePassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'password' => [
                'required',
                'confirmed',
                new IsValidPassword()
            ],
        ]);
        if ($validator->fails()) :
            return response()->validation($validator->errors(), __('response.errors.validation'));
        endif;
        $input = $request->all();

        $data = ['password' => Hash::make($input['password'])];

        $user->update($data);

        $message = __('response.messages.update', ['name' => __('fields.password')]);
        return response()->success($message);
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
        ]);
        if ($validator->fails()) :
            return response()->validation($validator->errors(), __('response.errors.validation'));
        endif;
        $input = $request->all();

        Mail::send(new UserForgotPassword($input['email']));

        $message = __('response.messages.send', ['name' => __('fields.email')]);
        return response()->success($message);
    }

    public function test($id)
    {
        $user = User::where('id', $id)->with('reference.reference.reference.reference')->first();
        return response()->data($user);
    }
}
