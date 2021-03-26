<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Illuminate\Support\Str;

class ApiRequestLogging
{
    /** @var Monolog\Logger **/
    private $logger;
    public function __construct()
    {
        $this->logger = $this->getLogger();
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $this->logger->info('Incoming request:');
        $this->logger->info($request);
        $request->hooksLogger = $this->logger;
        return $next($request);
    }
    private function getLogger()
    {
        $dateString = now()->format('m_d_Y');
        $filePath = 'web_hooks_' . $dateString . '.log';
        $dateFormat = "m/d/Y H:i:s";
        $output = "[%datetime%] %channel%.%level_name%: %message%\n";
        $formatter = new LineFormatter($output, $dateFormat);
        $stream = new StreamHandler(storage_path('logs/' . $filePath), Logger::DEBUG);
        $stream->setFormatter($formatter);
        $processId = Str::random(5);
        $logger = new Logger($processId);
        $logger->pushHandler($stream);

        return $logger;
    }
}
