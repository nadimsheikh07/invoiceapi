<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = Sale::query();
        $query->with('customer');
        $columns = ['comments'];

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
            'customer_id' => [
                'required',
            ],
        ]);
        if ($validator->fails()) {
            return response()->validation($validator->errors(), __('response.errors.validation'));
        }
        $input = $request->all();
        Sale::create($input);
        $message = __('response.messages.success', ['name' => __('module.sale.title')]);
        return response()->success($message);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function show(Sale $sale)
    {
        return response()->data($sale);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sale $sale)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => [
                'required',
            ],
        ]);
        if ($validator->fails()) {
            return response()->validation($validator->errors(), __('response.errors.validation'));
        }
        $input = $request->all();
        $sale->update($input);
        $message = __('response.messages.update', ['name' => __('module.sale.title')]);
        return response()->success($message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sale $sale)
    {
        $sale->delete();
        $message = __('response.messages.delete', ['name' => __('module.sale.title')]);
        return response()->success($message);
    }
}
