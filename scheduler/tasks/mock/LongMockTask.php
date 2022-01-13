<?php

namespace common\modules\scheduler\tasks\mock;

/**
 * Class TaskAirport
 * @package common\modules\scheduler\tasks
 */
class LongMockTask extends ShortMockTask
{
    const ITERATIONS_COUNT = 7;
    const TIME_TO_SLEEP_FOR_ITERATION = 10;
}
