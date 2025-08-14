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
            // Filter untuk hanya menampilkan GET routes dan mengecualikan internal routes
            return !str_starts_with($route['uri'], '_') &&
                   !str_starts_with($route['uri'], 'sanctum') &&
                   !str_starts_with($route['uri'], 'api/user') &&
                   in_array('GET', $route['methods']) &&
                   !in_array('HEAD', $route['methods']);
        });

        return view('home', compact('routes'));
    }
}
