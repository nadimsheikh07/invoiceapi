<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = Role::query();

        $columns = ['name'];

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
            'name' => [
                'required',
                Rule::unique(Role::class)
            ],
        ]);
        if ($validator->fails()) {
            return response()->validation($validator->errors(), __('response.errors.validation'));
        }
        $input = $request->all();
        $role = Role::create($input);

        $permissions = [];
        if ($input['permissions']) {
            $permissions = json_decode($input['permissions'], 1);
        }
        $role->rolePermissions()->detach();
        $role->rolePermissions()->attach($permissions);
        $message = __('response.messages.success', ['name' => __('response.heading.role')]);
        return response()->success($message);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        $query = $role->with(['rolePermissions'])->where('id', $role->id)->first();
        $query->permissions = $query->rolePermissions->pluck('id');
        return response()->data($query);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                Rule::unique(Role::class)->ignore($role->id)
            ],
        ]);
        if ($validator->fails()) {
            return response()->validation($validator->errors(), __('response.errors.validation'));
        }
        $input = $request->all();
        $role->update($input);

        $permissions = [];
        if ($input['permissions']) {
            $permissions = json_decode($input['permissions'], 1);
        }
        
        $role->rolePermissions()->detach();
        $role->rolePermissions()->attach($permissions);
        $message = __('response.messages.update', ['name' => __('response.heading.role')]);
        return response()->success($message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role->delete();
        $message = __('response.messages.delete', ['name' => __('response.heading.role')]);
        return response()->success($message);
    }
}
