<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Laravel\Socialite\Two\InvalidStateException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (InvalidStateException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('notifications.errors.sm_session_invalid'),
                ], 422);
            }

            return redirect()
                ->route('login')
                ->with('error', __('notifications.errors.sm_session_invalid'));
        });
    }
}
