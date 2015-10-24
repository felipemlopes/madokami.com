<?php
/**
 * Created by PhpStorm.
 * User: oscar
 * Date: 24/10/2015
 * Time: 03:28
 */

namespace Madokami\VirusTotal;


use Carbon\Carbon;

class ApiThrottler {

    protected $requests = [ ];

    public function throttle($batchCount = 1) {
        $rateLimit = config('virustotal.requests_per_minute');
        $rate = 0;

        $now = Carbon::now();

        // Carbon instance for 1 minute in the past
        $rateCutoff = new Carbon('-1 minute');

        foreach($this->requests as $index => $request) {
            /** @var Carbon $then */
            $then = $request->time;

            if($then->lt($rateCutoff)) {
                // Request is over a minute old so remove it
                unset($this->requests[$index]);
            }
            else {
                $rate += $request->count;
            }
        }

        if(($rate + $batchCount) > $rateLimit) {
            $debt = ($rate + $batchCount) - $rateLimit;

            foreach($this->requests as $request) {
                $debt -= $request->count;

                if($debt <= 0) {
                    /** @var Carbon $then */
                    $then = $request->time;

                    $delta = $now->diffInSeconds($then);

                    $secondsToWait = (60 - $delta);

                    if($secondsToWait > 0) {
                        sleep($secondsToWait);
                    }

                    break;
                }
            }
        }

        $this->requests[] = (object) [
            'count' => $batchCount,
            'time' => Carbon::now(),
        ];
    }

}