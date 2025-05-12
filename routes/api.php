<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\SPSTController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\ImportExportController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Routes pour les clients
Route::apiResource('clients', ClientController::class);
Route::post('/clients/{client}/toggle-favorite', [ClientController::class, 'toggleFavorite']);

// Routes pour les employés
Route::apiResource('employees', EmployeeController::class);
Route::post('/employees/import', [EmployeeController::class, 'import']);
Route::get('/employees/export/{format}', [EmployeeController::class, 'export']);
Route::get('/employees/stats', [EmployeeController::class, 'stats']);

// Routes pour les visites
Route::apiResource('visits', VisitController::class);
Route::post('/visits/export/{format}', [VisitController::class, 'export']);

// Routes pour les rendez-vous
Route::apiResource('appointments', AppointmentController::class);
Route::post('/appointments/export/{format}', [AppointmentController::class, 'export']);

// Routes pour les SPST
Route::apiResource('spsts', SPSTController::class);
Route::get('/spsts/notifications', [SPSTController::class, 'getNotifications']);
Route::put('/spsts/notifications/{notificationId}/read', [SPSTController::class, 'markNotificationAsRead']);
Route::get('/spsts/services', [SPSTController::class, 'getServices']);
Route::post('/spsts/services/{serviceId}/request', [SPSTController::class, 'requestService']);
Route::get('/spsts/visits', [SPSTController::class, 'getVisits']);

// Routes pour les documents
Route::apiResource('documents', DocumentController::class);
Route::get('/documents/{document}/download', [DocumentController::class, 'download']);

// Routes pour les notifications
Route::apiResource('notifications', NotificationController::class);
Route::put('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

// Routes pour les statistiques
Route::post('/statistics/visits', [StatisticsController::class, 'visits']);
Route::post('/statistics/appointments', [StatisticsController::class, 'appointments']);
Route::post('/statistics/dashboard', [StatisticsController::class, 'dashboard']);

// Routes pour l'import/export
Route::post('/import-export/import', [ImportExportController::class, 'import']);
Route::post('/import-export/export/{type}/{format}', [ImportExportController::class, 'export']);
Route::get('/import-export/templates', [ImportExportController::class, 'getTemplates']);
Route::get('/import-export/templates/{templateId}/{format}', [ImportExportController::class, 'downloadTemplate']);
Route::post('/import-export/validate', [ImportExportController::class, 'validate']);
Route::get('/import-export/history', [ImportExportController::class, 'getHistory']);

// Routes pour la communication
Route::post('/contact/manager', [CommunicationController::class, 'contactManager']);
Route::post('/contact/assistant', [CommunicationController::class, 'contactAssistant']);
Route::post('/contact/rrh', [CommunicationController::class, 'contactRRH']);
Route::post('/incidents/declare', [CommunicationController::class, 'declareIncident']);