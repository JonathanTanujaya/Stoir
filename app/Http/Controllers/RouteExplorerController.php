<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class RouteExplorerController extends Controller
{
    public function index()
    {
        $routes = collect(Route::getRoutes())->map(function ($route) {
            return [
                'uri' => $route->uri(),
                'methods' => $route->methods(),
                'name' => $route->getName(),
                'action' => $route->getActionName(),
            ];
        })->filter(function ($route) {
            return !str_starts_with($route['uri'], '_') &&
                   !str_starts_with($route['uri'], 'sanctum') &&
                   !str_starts_with($route['uri'], 'api/user');
        });

        return view('home', compact('routes'));
    }
}
