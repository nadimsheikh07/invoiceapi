<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = Company::query();
        $columns = ['name', 'contact', 'email', 'pancard', 'gstin', 'address', 'bank_name', 'account_number', 'account_type', 'swift_code', 'ifsc_code', 'bank_address', 'sign_image', 'status'];

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

        if (request('selected')) {
            $value = request('selected');
            $query->orderByRaw(DB::raw("FIELD(id, $value) DESC"));
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
            ],
            'email' => [
                'required',
                'email',
                Rule::unique(Company::class)
            ],
        ]);
        if ($validator->fails()) {
            return response()->validation($validator->errors(), __('response.errors.validation'));
        }
        $input = $request->all();
        Company::create($input);
        $message = __('response.messages.success', ['name' => __('module.company.title')]);
        return response()->success($message);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company)
    {
        return response()->data($company);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Company $company)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
            ],
            'email' => [
                'required',
                'email',
                Rule::unique(Company::class)->ignore($company->id)
            ],
        ]);
        if ($validator->fails()) {
            return response()->validation($validator->errors(), __('response.errors.validation'));
        }
        $input = $request->all();
        $company->update($input);
        $message = __('response.messages.update', ['name' => __('module.company.title')]);
        return response()->success($message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        $company->delete();
        $message = __('response.messages.delete', ['name' => __('module.company.title')]);
        return response()->success($message);
    }
}
