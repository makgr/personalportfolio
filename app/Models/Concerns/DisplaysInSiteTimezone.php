<?php

namespace App\Models\Concerns;

use DateTimeInterface;
use Illuminate\Support\Carbon;

/**
 * Timestamps are stored in UTC (config app.timezone) but read back in the
 * site timezone chosen in Settings (config app.display_timezone).
 * Date-only casts are left untouched so they never shift a day.
 */
trait DisplaysInSiteTimezone
{
    /**
     * @param  mixed  $value
     */
    protected function asDateTime($value): Carbon
    {
        return parent::asDateTime($value)->setTimezone(config('app.display_timezone'));
    }

    /**
     * @param  mixed  $value
     */
    protected function asDate($value): Carbon
    {
        return parent::asDateTime($value)->startOfDay();
    }

    /**
     * @param  mixed  $value
     */
    public function fromDateTime($value): mixed
    {
        if (empty($value)) {
            return $value;
        }

        // Carbon values (including ones read back in the site timezone) are written as UTC
        $date = $value instanceof DateTimeInterface
            ? Carbon::instance($value)->setTimezone(config('app.timezone'))
            : parent::asDateTime($value);

        return $date->format($this->getDateFormat());
    }
}
