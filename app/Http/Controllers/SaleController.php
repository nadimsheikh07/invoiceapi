<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use niklasravnsborg\LaravelPdf\Facades\Pdf as LaravelPdf;
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
        $query->with(['customer', 'company']);
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

        $items = json_decode($input['items'], true);

        $total = 0;

        if ($items) {
            foreach ($items as $value) {
                $total += ($value['quantity'] * $value['price']);
            }
        }

        if ($input['total_tax']) {
            $total += $input['total_tax'];
        }
        if ($input['total_discount']) {
            $total -= $input['total_discount'];
        }

        $input['total'] = $total;


        $sale = Sale::create($input);

        $sale->items()->delete();
        $sale->items()->createMany($items);

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
        $query = Sale::with(['customer', 'company', 'items'])->where('id', $sale->id)->first();
        return response()->json($query);
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

        $items = json_decode($input['items'], true);

        $total = 0;

        if ($items) {
            foreach ($items as $value) {
                $total += ($value['quantity'] * $value['price']);
            }
        }

        if ($input['total_tax']) {
            $total += $input['total_tax'];
        }
        if ($input['total_discount']) {
            $total -= $input['total_discount'];
        }

        $input['total'] = $total;


        $sale->update($input);

        $sale->items()->delete();
        $sale->items()->createMany($items);


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


    public function showPdf(Sale $sale)
    {
        $query = Sale::with(['customer', 'company', 'items'])->where('id', $sale->id)->first();
        // return $query;

        $pdf = LaravelPdf::loadView("pdf/sales", $query, [], [
            'mode'             => 'utf-8',
            'format'           => 'A4',
            'author'           => '',
            'subject'          => '',
            'keywords'         => '',
            'creator'          => '',
            'display_mode'     => 'fullpage',
        ]);

        return $pdf->stream('document.pdf');
    }
}
