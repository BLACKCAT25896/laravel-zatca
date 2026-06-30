<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'success' => true,
        'message' => 'Laravel ZATCA API',
        'version' => '1.0.0',
        'endpoints' => [
            'businesses' => '/api/businesses',
            'suppliers' => '/api/suppliers',
            'invoices' => '/api/invoices',
            'tax_reports' => '/api/reports',
        ],
    ]);
});
