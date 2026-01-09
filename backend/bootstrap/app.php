<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use App\Application\Auth\Exceptions\InvalidCredentialsException;
use App\Infrastructure\Http\Middleware\TraceIdMiddleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Sanctum (SPA auth via cookies) support on API routes.
        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
        ]);

        // Trace ID for all requests (incluye API).
        $middleware->append(TraceIdMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Throwable $e, Request $request) {
            $isApi = $request->is('api/*') || $request->expectsJson();

            if (! $isApi) {
                return null;
            }

            $traceId = $request->attributes->get(TraceIdMiddleware::ATTRIBUTE);

            if (! is_string($traceId) || ! Str::isUuid($traceId)) {
                $traceId = (string) Str::uuid();
            }

            if ($e instanceof ValidationException) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'errors' => (object) $e->errors(),
                    'trace_id' => $traceId,
                ], 422);
            }

            if ($e instanceof InvalidCredentialsException) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'errors' => (object) [],
                    'trace_id' => $traceId,
                ], 422);
            }

            if ($e instanceof AuthenticationException) {
                return response()->json([
                    'message' => 'No autenticado.',
                    'errors' => (object) [],
                    'trace_id' => $traceId,
                ], 401);
            }

            if ($e instanceof AuthorizationException) {
                return response()->json([
                    'message' => 'No autorizado.',
                    'errors' => (object) [],
                    'trace_id' => $traceId,
                ], 403);
            }

            $status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;

            // Para 401/403/etc (auth middleware), el mensaje suele venir del exception.
            $message = $e->getMessage() !== '' ? $e->getMessage() : 'Error.';

            if ($status === 500 && ! config('app.debug')) {
                $message = 'Error interno del servidor.';
            }

            return response()->json([
                'message' => $message,
                'errors' => (object) [],
                'trace_id' => $traceId,
            ], $status);
        });
    })->create();
