<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Illuminate\Support\Str;

/**
 *  ApiRequestLogging class for creating log files.
 *
 *  @package App\Http\Middleware
 */
class ApiRequestLogging
{
    private $logger;

    public function __construct(Request $request)
    {
        $this->logger = $this->getLogger($request);
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $this->logger->info('Incoming request:');
        $this->logger->info($request);

        $request->hooksLogger = $this->logger;

        return $next($request);
    }

    /**
     * Function for make log file
     *
     * @param  Request  $request
     * @return Logger
     */
    private function getLogger(Request $request)
    {
        $requestName = str_replace("/","_",$request->path());
        $filePath = $requestName . '_logging.log';
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
