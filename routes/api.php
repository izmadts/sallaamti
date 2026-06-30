// GitHub Auto Deploy Webhook
// This route must be OUTSIDE auth middleware so GitHub can access it without login
Route::post('/deploy', [App\Http\Controllers\DeployController::class, 'handle']);