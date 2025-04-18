<?php

namespace App\Providers;

use Filament\Support\Assets\Js;
use Filament\Support\Assets\Css;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Gate;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;
use Filament\Support\Facades\FilamentAsset;
use Filament\Notifications\Livewire\Notifications;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            LoginResponse::class,
            \App\Http\Responses\LoginResponse::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Notifications::alignment(Alignment::Left);

        FilamentAsset::register([
            Css::make('font-awesome-icons', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css'),
        ]);

        // FilamentView::registerRenderHook(
        //     PanelsRenderHook::TOPBAR_START,
        //     fn (): string => Blade::render('pms.top-bar'),
        // );
     
        FilamentView::registerRenderHook('panels::body.end', fn(): string => Blade::render("@vite('resources/js/flat.js')"));
    }
}
