<?php

route('api')->group(function () {
    // Business endpoints
    Route::apiResource('businesses', 'BusinessController');

    // Supplier endpoints
    Route::apiResource('suppliers', 'SupplierController');

    // Invoice endpoints
    Route::apiResource('invoices', 'InvoiceController');
    Route::post('invoices/{invoice}/finalize', 'InvoiceController@finalize');
    Route::post('invoices/{invoice}/submit', 'InvoiceController@submitToZatca');
    Route::get('invoices/{invoice}/xml', 'InvoiceController@getXml');
    Route::get('invoices/{invoice}/qrcode', 'InvoiceController@getQrCode');

    // Tax Report endpoints
    Route::get('reports/tax-summary', 'TaxReportController@taxSummary');
    Route::post('reports/tax-settlement', 'TaxReportController@taxSettlement');
    Route::post('tax-declarations/{declaration}/submit', 'TaxReportController@submitDeclaration');
    Route::get('tax-declarations/{declaration}', 'TaxReportController@getDeclaration');
});
