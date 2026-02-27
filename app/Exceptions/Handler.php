<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\ConnectionException;
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

    /**
     * Show a clear message when the database is unreachable (e.g. MySQL not running).
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof ConnectionException || $this->isDatabaseConnectionError($e)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Database unavailable. Start MySQL (e.g. XAMPP) and try again.'], 503);
            }
            return response(
                '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Database unavailable</title></head><body style="font-family:sans-serif;max-width:520px;margin:2rem auto;padding:1rem;"><h1>Database unavailable</h1><p>Could not connect to the database. Please:</p><ol><li>Start MySQL (e.g. open XAMPP Control Panel and start MySQL).</li><li>Ensure <code>.env</code> has correct <code>DB_HOST</code>, <code>DB_DATABASE</code>, <code>DB_USERNAME</code>, <code>DB_PASSWORD</code>.</li><li>Refresh this page.</li></ol><p><a href="' . ($request->getSchemeAndHttpHost() . '/') . '">Try again</a></p></body></html>',
                503,
                ['Content-Type' => 'text/html; charset=UTF-8']
            );
        }

        return parent::render($request, $e);
    }

    private function isDatabaseConnectionError(Throwable $e): bool
    {
        $message = $e->getMessage();
        return str_contains($message, 'Connection refused')
            || str_contains($message, 'SQLSTATE[HY000] [2002]')
            || str_contains($message, 'No connection could be made')
            || str_contains($message, 'could not find driver');
    }
}
