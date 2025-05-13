<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use App\Services\FeedbackNotificationService;

class FeedbackServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(FeedbackNotificationService::class, function ($app) {
            return new FeedbackNotificationService();
        });

        $this->mergeConfigFrom(
            __DIR__.'/../../config/feedback.php', 'feedback'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publication des configurations
        $this->publishes([
            __DIR__.'/../../config/feedback.php' => config_path('feedback.php'),
        ], 'config');

        // Publication des vues
        $this->publishes([
            __DIR__.'/../../resources/views/emails' => resource_path('views/emails'),
            __DIR__.'/../../resources/views/admin/feedback' => resource_path('views/admin/feedback'),
        ], 'views');

        // Charger les vues
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'feedback');

        // Enregistrer les routes
        $this->registerRoutes();
    }

    /**
     * Enregistre les routes du module de feedback
     */
    protected function registerRoutes(): void
    {
        Route::prefix('api')
            ->middleware('api')
            ->group(function () {
                Route::post('/feedback', [\App\Http\Controllers\FeedbackController::class, 'store'])
                    ->middleware(\App\Http\Middleware\FeedbackRateLimiter::class);

                // Routes protégées par authentification
                Route::middleware(['auth:sanctum'])->group(function () {
                    Route::get('/admin/feedbacks', [\App\Http\Controllers\FeedbackController::class, 'index']);
                    Route::get('/admin/feedbacks/{id}', [\App\Http\Controllers\FeedbackController::class, 'show']);
                    Route::patch('/admin/feedbacks/{id}/resolve', [\App\Http\Controllers\FeedbackController::class, 'markAsResolved']);
                    Route::get('/admin/feedback-stats', [\App\Http\Controllers\FeedbackController::class, 'getStats']);
                    
                    // Routes pour les réponses
                    Route::post('/admin/feedbacks/{id}/respond', [\App\Http\Controllers\FeedbackResponseController::class, 'respond']);
                    Route::get('/admin/feedbacks/{id}/responses', [\App\Http\Controllers\FeedbackResponseController::class, 'getResponses']);
                });
            });

        // Routes web pour l'administration
        Route::prefix('admin')
            ->middleware(['web', 'auth']) // Middleware d'authentification
            ->group(function () {
                Route::get('/feedbacks', [\App\Http\Controllers\Admin\AdminFeedbackController::class, 'index'])
                    ->name('admin.feedbacks.index');
                Route::get('/feedbacks/dashboard', [\App\Http\Controllers\Admin\AdminFeedbackController::class, 'dashboard'])
                    ->name('admin.feedbacks.dashboard');
                Route::get('/feedbacks/{id}', [\App\Http\Controllers\Admin\AdminFeedbackController::class, 'show'])
                    ->name('admin.feedbacks.show');
                Route::get('/feedbacks/{id}/respond', [\App\Http\Controllers\Admin\AdminFeedbackController::class, 'showResponseForm'])
                    ->name('admin.feedbacks.respond.form');
                Route::post('/feedbacks/{id}/respond', [\App\Http\Controllers\Admin\AdminFeedbackController::class, 'respond'])
                    ->name('admin.feedbacks.respond');
                Route::post('/feedbacks/{id}/resolve', [\App\Http\Controllers\Admin\AdminFeedbackController::class, 'markAsResolved'])
                    ->name('admin.feedbacks.resolve');
                Route::get('/feedbacks/export', [\App\Http\Controllers\Admin\AdminFeedbackController::class, 'export'])
                    ->name('admin.feedbacks.export');
            });
    }
}