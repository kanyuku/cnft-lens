<?php

use App\Http\Controllers\Api\NftController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get("/user", function (Request $request) {
    return $request->user();
})->middleware("auth:sanctum");

Route::prefix("nfts")->group(function () {
    Route::get("/policy/{policy_id}", [NftController::class, "policy"]);
    Route::get("/policy/{policy_id}/stats", [NftController::class, "stats"]);
    Route::get("/asset/{asset_id}", [NftController::class, "asset"]);
    Route::get("/asset/{asset_id}/history", [NftController::class, "history"]);
});
