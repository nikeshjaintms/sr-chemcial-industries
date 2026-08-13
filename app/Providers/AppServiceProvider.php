<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        \Illuminate\Support\Facades\View::composer('partials.header', function ($view) {
            $menuCategories = \App\Models\Category::whereNull('parent_id')
                ->where('status', true)
                ->with([
                    'children' => function ($q) {
                        $q->where('status', true)->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
                    },
                    'children.children' => function ($q) {
                        $q->where('status', true)->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
                    },
                    'children.products' => function ($q) {
                        $q->where('status', true)->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
                    },
                    'products' => function ($q) {
                        $q->where('status', true)->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
                    }
                ])
                ->orderBy('sort_order', 'asc')
                ->orderBy('name', 'asc')
                ->get();

            $targetOrder = [
                'GACL Products' => 1,
                'Organic Products' => 2,
                'DMCC Products' => 3,
                'GNFC' => 4,
                'GNFC Products' => 4,
                'Industrial Solvents & Commodities' => 5,
            ];

            $menuCategories = $menuCategories->sortBy(function ($cat) use ($targetOrder) {
                return $targetOrder[$cat->name] ?? $cat->sort_order ?? 99;
            })->values();

            $view->with('menuCategories', $menuCategories);
        });
    }
}
