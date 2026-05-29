<?php

namespace App\Logging;

use Illuminate\Log\Logger;

/**
 * Laravel "tap" class that bolts the PII scrubber onto any standard
 * channel without rewriting it as a monolog driver.
 *
 * Usage in config/logging.php:
 *     'single' => [
 *         'driver' => 'single',
 *         ...
 *         'tap' => [App\Logging\AddPiiScrubber::class],
 *     ],
 */
class AddPiiScrubber
{
    public function __invoke(Logger $logger): void
    {
        $processor = new PiiScrubbingProcessor();
        foreach ($logger->getHandlers() as $handler) {
            $handler->pushProcessor($processor);
        }
    }
}
