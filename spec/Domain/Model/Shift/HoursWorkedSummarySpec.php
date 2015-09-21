<?php

namespace spec\Scheduler\Domain\Model\Shift;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use DateTimeImmutable;

class HoursWorkedSummarySpec extends ObjectBehavior
{
    function let()
    {
        $start = new DateTimeImmutable();
        $end = $start->modify("+6 days");
        $hours = 6.5;
        $this->beConstructedWith($start, $end, $hours);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Scheduler\Domain\Model\Shift\HoursWorkedSummary');
    }

    function it_has_a_start_date()
    {
        $this->getStartDate()->shouldBeLike(new DateTimeImmutable());
    }

    function it_has_an_end_date()
    {
        $this->getEndDate()->shouldBeLike(new DateTimeImmutable("+6 days"));
    }

    function it_has_total_of_hours_worked()
    {
        $this->getTotalHours()->shouldBe(6.5);
    }
}
