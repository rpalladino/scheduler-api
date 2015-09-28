<?php

namespace spec\Scheduler\Application\Service;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use DateTime;
use Aura\Payload\Payload;
use Scheduler\Domain\Model\Shift\Shift;
use Scheduler\Domain\Model\Shift\ShiftCoworkersFinder;
use Scheduler\Domain\Model\Shift\ShiftMapper;
use Scheduler\Domain\Model\User\User;

class GetShiftSpec extends ObjectBehavior
{
    function let(ShiftMapper $shiftMapper)
    {
        $coworkersFinder = new ShiftCoworkersFinder($shiftMapper->getWrappedObject());
        $this->beConstructedWith($shiftMapper, $coworkersFinder);
    }

    function it_gets_a_shift($shiftMapper)
    {
        $currentUser = new User(12346, "Shelly Levene", "employee", "oldguy@aol.com");
        $currentUser->authenticate();
        $manager = new User(12345, "John Williamson", "manager", "john@abc.com");

        $start = new DateTime("10:30 AM");
        $end = new DateTime("1:30 PM");
        $userShift = new Shift(76543, $manager, $currentUser, 0.5, $start, $end);

        $shiftMapper->find(76543)->willReturn($userShift);
        $shiftMapper->findShiftsInTimePeriod($start, $end)->willReturn([
            $userShift
        ]);

        $withCoworkers = false;
        $payload = $this($currentUser, 76543, $withCoworkers);

        $payload->shouldHaveType(Payload::class);
        $payload->getStatus()->shouldBe(Payload::SUCCESS);
        $payload->getOutput()->shouldHaveType(Shift::class);
        $payload->getOutput()->shouldNotHaveCoworkers();
    }

    function it_gets_a_shift_with_coworkers($shiftMapper)
    {
        $currentUser = new User(12346, "Shelly Levene", "employee", "oldguy@aol.com");
        $currentUser->authenticate();
        $coworker = new User(12347, "Richard Roma", "employee", "ricky@roma.com");
        $manager = new User(12345, "John Williamson", "manager", "john@abc.com");

        $start = new DateTime("10:30 AM");
        $end = new DateTime("1:30 PM");
        $userShift = new Shift(76543, $manager, $currentUser, 0.5, $start, $end);
        $coworkerShift = new Shift(76544, $manager, $coworker, 0.5, $start, $end);

        $shiftMapper->find(76543)->willReturn($userShift);
        $shiftMapper->findShiftsInTimePeriod($start, $end)->willReturn([
            $userShift,
            $coworkerShift
        ]);

        $withCoworkers = true;
        $payload = $this($currentUser, 76543, $withCoworkers);

        $payload->shouldHaveType(Payload::class);
        $payload->getStatus()->shouldBe(Payload::SUCCESS);
        $payload->getOutput()->shouldHaveType(Shift::class);
        $payload->getOutput()->getCoworkers()->shouldBeArray();
        $payload->getOutput()->getCoworkers()->shouldContain(
            $coworkerShift->getEmployee()
        );
    }

    function it_does_not_allow_unauthenticated_access()
    {
        $currentUser = new User(1, "John Williamson", "manager", "john@abc.com");

        $payload = $this($currentUser, 1, true);

        $payload->getStatus()->shouldBe(Payload::NOT_AUTHENTICATED);
    }

    function it_responds_with_not_found_when_shift_not_found()
    {
        $currentUser = new User(1, "John Williamson", "manager", "john@abc.com");
        $currentUser->authenticate();

        $payload = $this($currentUser, 54321, true);

        $payload->getStatus()->shouldBe(Payload::NOT_FOUND);
    }
}
