<?php

namespace spec\Scheduler\REST\Resource;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Scheduler\Domain\Model\Shift\HoursWorkedSummary;

class HoursWorkedSummaryResourceSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Scheduler\REST\Resource\HoursWorkedSummaryResource');
    }

    function it_can_transform_a_single_instance()
    {
        $start = new \DateTimeImmutable();
        $end = $start->modify("+6 days");
        $summary = new HoursWorkedSummary($start, $end, 6.5);

        $result = $this->transform($summary);

        $result->shouldBeArray();
        $result->shouldHaveKeyWithValue("start", $start->format(DATE_RFC2822));
        $result->shouldHaveKeyWithValue("end", $end->format(DATE_RFC2822));
        $result->shouldHaveKeyWithValue("hours", 6.5);
    }

    function it_can_return_a_summary_item()
    {
        $start = new \DateTimeImmutable();
        $end = $start->modify("+6 days");
        $summary = new HoursWorkedSummary($start, $end, 6.5);

        $result = $this->item($summary);

        $result->shouldHaveKey('summary');
        $result["summary"]->shouldHaveKeyWithValue("start", $start->format(DATE_RFC2822));
        $result["summary"]->shouldHaveKeyWithValue("end", $end->format(DATE_RFC2822));
        $result["summary"]->shouldHaveKeyWithValue("hours", 6.5);
    }
}
