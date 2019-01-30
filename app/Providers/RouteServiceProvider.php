<?php

namespace CodeShopping\Providers;

use CodeShopping\Models\ChatGroupInvitation;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;


use CodeShopping\Common\OnlyTrashed;
use CodeShopping\Models\Category;
use CodeShopping\Models\Product;
use CodeShopping\Models\User;

class RouteServiceProvider extends ServiceProvider
{
    use OnlyTrashed;

    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'CodeShopping\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();


        // Consultar Product pelo id ou pelo slug
        Route::bind('product', function($value){
            /** @var Collection $collection */
            $query = Product::query();
            $request = app(Request::class);
            $query = $this->onlyTrashedIfRequested($request, $query);
            $collection = $query->whereId($value)->orWhere('slug', $value)->get();
            return $collection->first();
        });

        // Consultar Category pelo id ou pelo slug
        Route::bind('category', function($value){
            /** @var Collection $collection */
            $collection = Category::whereId($value)->orWhere('slug', $value)->get();
            return $collection->first();
        });

        // Consultar Users pelo id ou pelo slug
        Route::bind('user', function ($value) {
            /** @var Collection $collection */
            $query = User::query();
            $request = app(Request::class);
            $query = $this->onlyTrashedIfRequested($request, $query);
            return $query->find($value);
        });

        // Consultar Convites pelo pelo slug
        Route::bind('invitation_slug', function ($value) {
            return ChatGroupInvitation::where('slug', $value)->first();
        });



    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }
}
