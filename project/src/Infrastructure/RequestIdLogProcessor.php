<?php

namespace Viktorprogger\YiisoftInform\Infrastructure;

class RequestIdLogProcessor
{
    public function __construct(private readonly RequestId $requestId)
    {
    }

    public function __invoke(array $record)
    {
        $record['context']['request_id'] = $this->requestId->getValue();

        return $record;
    }
}
