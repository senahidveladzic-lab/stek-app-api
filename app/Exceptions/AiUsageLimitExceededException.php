<?php

namespace App\Exceptions;

use Exception;

class AiUsageLimitExceededException extends Exception
{
    public function __construct(
        public readonly int $limit,
        public readonly int $used
    ) {
        parent::__construct('AI usage limit reached.');
    }
}
