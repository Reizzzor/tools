<?php

namespace Support;

use Carbon\Carbon;
use ErrorException;
use Reizzzor\Tools\Exceptions\DateRangeLimitException;
use InvalidArgumentException;

abstract class Reporter
{
    public const TIMEFRAME_BLANK = 'blank';
    public const TIMEFRAME_DAY = 'day';
    public const TIMEFRAME_HOUR = 'hour';
    private const TIMEFRAMES = [
        self::TIMEFRAME_BLANK,
        self::TIMEFRAME_DAY,
        self::TIMEFRAME_HOUR,
    ];

    /**
     * Max size of date range in days to build report for.
     *
     * @var int
     */
    protected $dateRangeLimit;

    /** @var Carbon $since */
    protected $since;

    /** @var Carbon $since */
    protected $till;

    /** @var string $timeframe */
    protected $timeframe;

    /**
     * @throws DateRangeLimitException
     * @throws ErrorException
     */
    protected function validateDateRange()
    {
        if (isset($this->dateRangeLimit, $this->since, $this->till)) {
            if ($this->since()->diffInDays($this->till()) > $this->dateRangeLimit()) {
                throw new DateRangeLimitException(sprintf('Date range must be under %s days', $this->dateRangeLimit()));
            }
        }
    }

    /**
     * @return array
     * @throws DateRangeLimitException
     * @throws ErrorException
     */
    public function dateRange(): array
    {
        $this->validateDateRange();

        return [$this->since(), $this->till()];
    }

    /**
     * @param int|null $limit
     *
     * @return $this|int
     */
    public function dateRangeLimit(int $limit = null)
    {
        if (is_null($limit)) {
            return $this->dateRangeLimit;
        }

        $this->dateRangeLimit = $limit;

        return $this;
    }

    /**
     * @param Carbon|null $datetime
     *
     * @return $this|Carbon
     * @throws ErrorException
     */
    public function since(Carbon $datetime = null)
    {
        if (is_null($datetime)) {
            if (!$this->since) {
                throw new ErrorException('Start time is not set');
            }

            if ($this->timeframe() === self::TIMEFRAME_HOUR) {
                return $this->since->copy()->minute(0)->second(0);
            } elseif ($this->timeframe() === self::TIMEFRAME_DAY) {
                return $this->since->copy()->startOfDay();
            } elseif ($this->timeframe() === self::TIMEFRAME_BLANK) {
                return $this->since->copy();
            }
        }

        $this->since = $datetime->copy();

        return $this;
    }

    /**
     * @param Carbon|null $datetime
     *
     * @return $this|Carbon
     * @throws ErrorException
     */
    public function till(Carbon $datetime = null)
    {
        if (is_null($datetime)) {
            if (!$this->till) {
                throw new ErrorException('End time is not set');
            }

            if ($this->timeframe() === self::TIMEFRAME_HOUR) {
                return $this->till->copy()->minute(59)->second(59);
            } elseif ($this->timeframe() === self::TIMEFRAME_DAY) {
                return $this->till->copy()->endOfDay();
            } elseif ($this->timeframe() === self::TIMEFRAME_BLANK) {
                return $this->till->copy();
            }
        }

        $this->till = $datetime->copy();

        return $this;
    }

    /**
     * @param null $timeframe
     *
     * @return $this|string
     * @throws ErrorException
     */
    public function timeframe($timeframe = null)
    {
        if (is_null($timeframe)) {
            if (!$this->timeframe) {
                throw new ErrorException('Timeframe is not set');
            }

            return $this->timeframe;
        }

        if (!in_array($timeframe, self::TIMEFRAMES, true)) {
            throw new InvalidArgumentException('Invalid timeframe passed');
        }

        $this->timeframe = $timeframe;

        return $this;
    }
}
