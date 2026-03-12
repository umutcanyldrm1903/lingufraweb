<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Arr;
use Illuminate\Session\TokenMismatchException;
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
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'UnAuthenticated'], 401);
        }
        $guard = Arr::get($exception->guards(), '0');
        switch ($guard) {
            case 'admin':
                $response = Redirect()->guest('/admin/login');
                break;
            case 'sanctum':
                $response = response()->json(['status' => 'error', 'message' => 'UnAuthenticated'], 401);
                break;
            default:
                $response = Redirect()->guest('/login');
        }
        return $response;
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof AccessPermissionDeniedException || $exception instanceof DemoModeEnabledException) {
            return $exception->render($request);
        }

        if ($exception instanceof PostTooLargeException) {
            logger()->warning('Post too large', [
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'content_length' => $request->server('CONTENT_LENGTH'),
            ]);

            $message = __('Yuklenen dosya cok buyuk. Daha kucuk bir dosya deneyin.');
            if ($request->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => $message], 413);
            }

            return redirect()->back()->with([
                'messege' => $message,
                'alert-type' => 'error',
            ]);
        }

        if ($exception instanceof TokenMismatchException) {
            logger()->warning('CSRF token mismatch', [
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referer' => $request->headers->get('referer'),
            ]);

            $message = __('Oturum suresi doldu. Sayfayi yenileyip tekrar deneyin.');
            if ($request->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => $message], 419);
            }

            return redirect()->back()->with([
                'messege' => $message,
                'alert-type' => 'error',
            ]);
        }

        return parent::render($request, $exception);
    }
}
