<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        if (request()->header('X-localization')) {
            App::setLocale(request()->header('X-localization'));
        }

        // print sql query log
        if (env('APP_ENV') == 'dev') {
            DB::listen(function ($query) {
                Log::info(
                    $query->sql,
                    $query->bindings,
                    $query->time
                );
            });
        }

        // where like query
        Builder::macro('whereLike', function (Builder $builder, $attributes, string $searchTerm) {
            return $builder->where(function (Builder $query) use ($attributes, $searchTerm) {
                foreach (Arr::wrap($attributes) as $attribute) {
                    $query->when(
                        str_contains($attribute, '.'),
                        function (Builder $query) use ($attribute, $searchTerm) {
                            [$relationName, $relationAttribute] = explode('.', $attribute);

                            $query->orWhereHas($relationName, function (Builder $query) use ($relationName, $relationAttribute, $searchTerm) {
                                $query->where("$relationAttribute", 'LIKE', "%{$searchTerm}%");
                            });
                        },
                        function (Builder $query) use ($attribute, $searchTerm) {
                            $query->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
                        }
                    );
                }
            });
            return $this;
        });

        // where relation query
        Builder::macro('whereRelation', function (Builder $builder, $attributes, string $value) {
            return $builder->where(function (Builder $query) use ($attributes, $value) {
                foreach (Arr::wrap($attributes) as $attribute) {
                    $query->when(
                        str_contains($attribute, '.'),
                        function (Builder $query) use ($attribute, $value) {
                            [$relationName, $relationAttribute] = explode('.', $attribute);

                            $query->orWhereHas($relationName, function (Builder $query) use ($relationName, $relationAttribute, $value) {
                                $query->where("$relationName.$relationAttribute", $value);
                            });
                        },
                        function (Builder $query) use ($attribute, $value) {
                            $query->orWhere($attribute, $value);
                        }
                    );
                }
            });
            return $this;
        });


        // make response
        Response::macro('data', function ($data) {
            return Response::json($data, config('httpstatuses.OK'));
        });
        Response::macro('notFound', function ($message) {
            return Response::json(['message' => $message], config('httpstatuses.NotFound'));
        });
        Response::macro('validation', function ($errors, $message) {
            return Response::json(['errors' => $errors, 'message' => $message], config('httpstatuses.NotAcceptable'));
        });
        Response::macro('success', function ($message) {
            return Response::json(['message' => $message], config('httpstatuses.OK'));
        });
        Response::macro('error', function ($message) {
            return Response::json(['message' => $message], config('httpstatuses.BadRequest'));
        });
    }
}
