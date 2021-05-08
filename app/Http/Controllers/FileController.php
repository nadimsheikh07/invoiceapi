<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FileController extends Controller
{
    public function image(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'file' => 'required',
        ]);
        if ($validator->fails()) :
            return response()->validation($validator->errors(), __('response.errors.validation'));
        endif;

        $extension = $request->file('file')->getClientOriginalExtension();
        $imageName =  time() . '.' . $extension;

        $request->file('file')->storeAs('public', $imageName);

        if (env('APP_ENV') == 'production') {
            $url = asset("public" . Storage::url($imageName));
        } else {
            $url = asset(Storage::url($imageName));
        }

        $message = __('response.messages.success', ['name' => __('response.heading.file')]);
        return response()->data([
            'message' => $message,
            'url' => $url,
        ]);
    }

    public function download()
    {
        $url = request('url');

        return Storage::download($url);
    }
}
