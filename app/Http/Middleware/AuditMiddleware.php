<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuditLogController;

class AuditMiddleware
{

    public function handle(Request $request, Closure $next)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Only log if the user exists and is not an Admin
        if ($user && strtolower($user->role) !== 'admin') {
            // Get the route name to use as the action
            $action = $request->route() ? $request->route()->getName() : 'Unknown Action';

            // Extract the patient ID if it exists in the request
            $patientId = $request->has('patient_id') ? $request->patient_id : null;

            // Define the details for the log
            $details = "Accessed route: " . $action;
            $extraData = ['url' => $request->fullUrl()];

            // Add patient ID to details if available
            if ($patientId) {
                $extraData['patient_id'] = $patientId;
            }

            // Log the action using the static method
            AuditLogController::log('Route Accessed', $details, $extraData);
        }

        return $next($request);
    }
}
