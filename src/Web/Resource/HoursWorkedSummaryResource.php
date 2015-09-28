<?php

namespace Scheduler\Web\Resource;

use Scheduler\Domain\Model\Shift\HoursWorkedSummary;

class HoursWorkedSummaryResource
{

    public function transform(HoursWorkedSummary $summary)
    {
        return [
            "start" => $summary->getStartDate()->format(DATE_RFC2822),
            "end" => $summary->getEndDate()->format(DATE_RFC2822),
            "hours" => $summary->getTotalHours()
        ];
    }

    public function item(HoursWorkedSummary $summary)
    {
        return ["summary" => $this->transform($summary)];
    }
}
