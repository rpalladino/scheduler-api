<?php

namespace spec\Scheduler\Application\Service;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use DateTime;
use Aura\Payload_Interface\PayloadInterface;
use Scheduler\Domain\Model\Shift\Shift;
use Scheduler\Domain\Model\Shift\ShiftMapper;
use Scheduler\Domain\Model\User\User;

class GetShiftsInTimePeriodSpec extends ObjectBehavior
{
    function let(ShiftMapper $shiftMapper)
    {
        $this->beConstructedWith($shiftMapper);
    }

    function it_is_invokable()
    {
        expect(method_exists($this->getWrappedObject(), '__invoke'))->toBe(true);
    }

    function it_gets_shifts_in_time_period(ShiftMapper $shiftMapper)
    {
        $manager = User::managerNamedWithEmail("John Williamson", "jwilliamson@gmail.com");
        $shifts = [
            Shift::withManagerAndTimes(
                $manager,
                new DateTime('2015-09-06 08:30 AM'),
                new DateTime('2015-09-06 01:30 PM')
            ),
            Shift::withManagerAndTimes(
                $manager,
                new DateTime('2015-09-06 01:00 PM'),
                new DateTime('2015-09-06 07:00 PM')
            )
        ];
        $start = new DateTime('2015-09-06 00:00:00');
        $end = new DateTime('2015-09-07 00:00:00');
        $shiftMapper->findShiftsInTimePeriod($start, $end)->willReturn($shifts);
        $this->beConstructedWith($shiftMapper);

        $payload = $this($start, $end);

        $payload->shouldImplement(PayloadInterface::class);
        $shifts = $payload->getOutput()->getWrappedObject()["shifts"];
        expect($shifts)->toHaveCount(2);
        expect($shifts)->toContain($shifts[0]);
        expect($shifts)->toContain($shifts[1]);
    }
}
