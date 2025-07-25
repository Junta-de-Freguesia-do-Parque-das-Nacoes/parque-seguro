<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Log;
use Throwable;
use JsonException;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        ValidationException::class,
        \Intervention\Image\Exception\NotSupportedException::class,
        JsonException::class,
        InvalidFormatException::class,
    ];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function report(Throwable $exception)
    {
        if ($this->shouldReport($exception)) {
            Log::error($exception);
        }

        parent::report($exception);
    }

    public function render($request, Throwable $e)
    {
        // 429 Too Many Requests personalizado
        if ($e instanceof ThrottleRequestsException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Demasiados pedidos consecutivos. Por favor, aguarde alguns segundos antes de tentar novamente.'
                ], 429);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', '⚠️ Está a tentar demasiado depressa. Por favor, aguarde um pouco e tente novamente.');
        }

        // 404 personalizado
        if ($this->isHttpException($e) && $e->getStatusCode() === 404) {
            return response()->view('layouts/basic', [
                'content' => view('errors.404')
            ], 404);
        }

        return parent::render($request, $e);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $request->expectsJson()
            ? response()->json(['error' => 'Não autenticado.'], 401)
            : redirect()->guest(route('login'));
    }

    protected function invalidJson($request, ValidationException $exception)
    {
        return response()->json([
            'message' => 'Erro de validação.',
            'errors' => $exception->errors(),
        ], 422);
    }
}
