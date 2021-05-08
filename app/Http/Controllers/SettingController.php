<?php

namespace App\Http\Controllers;

use App\Helpers\SettingHelper;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = Setting::query();

        $columns = ['code','value'];

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

        $setting = [];
        foreach ($data as  $value) {
            $setting[$value->code] = $value->value;
        }
        if (isset($setting['maintenance_mode'])) {
            $setting['maintenance_mode'] = $setting['maintenance_mode'] ? true : false;
        }

        return response()->data($setting);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function show($code)
    {
        $query = SettingHelper::getSetting($code);
        return response()->json($query);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'contact' => 'required',
        ]);
        if ($validator->fails()) :
            return response()->json(['errors' => $validator->errors(), 'message' => __('response.errors.validation')], $this->NotAcceptable);
        endif;
        $input = $request->all();
        Setting::truncate();

        $data = [
            'name' => $input['name'],
            'email' => $input['email'],
            'contact' => $input['contact'],
            'distributor_role' => $input['distributor_role'],
            'promotor_role' => $input['promotor_role'],
            'franchise_role' => $input['franchise_role'],
            'distributor_commission' => $input['distributor_commission'],
            'distributor_fixed_commission' => $input['distributor_fixed_commission'],
            'level_1_commission' => $input['level_1_commission'],
            'level_2_commission' => $input['level_2_commission'],
            'level_3_commission' => $input['level_3_commission'],
            'level_4_commission' => $input['level_4_commission'],
        ];
        foreach ($data as $key => $value) {
            $insertData = [
                'code' => $key,
                'value' => $value,
            ];
            Setting::create($insertData);
        }

        $message = __('response.messages.update', ['name' => __('response.heading.setting')]);
        return response()->success($message);
    }
}
