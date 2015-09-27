<?php

namespace spec\Scheduler\REST\Resource;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Scheduler\Domain\Model\Shift\Shift;
use Scheduler\Domain\Model\User\User;
use Scheduler\REST\Resource\UserResource;

class ShiftResourceSpec extends ObjectBehavior
{
    private function examples()
    {
        return [
            "manager" => new User(76543, "John Williamson", "manager", "jwilliamson@gmail.com"),
            "employee" => new User(65432, "Richard Roma", "employee", "ricky@roma.com", "312-322-2211"),
            "start" => new \DateTimeImmutable("2015-12-24 07:00:00"),
            "end" => new \DateTimeImmutable("2015-12-24 12:00:00")
        ];
    }

    function let()
    {
        $this->beConstructedWith(new UserResource());
    }

    function it_can_transform_a_single_shift()
    {
        extract($this->examples());
        $shift = new Shift(54321, $manager, $employee, 0.5, $start, $end);

        $result = $this->transform($shift);

        $result->shouldBeArray();
        $result->shouldHaveKeyWithValue('id', 54321);
        $result->shouldHaveKeyWithValue('break', 0.5);
        $result->shouldHaveKeyWithValue('start_time', $start->format(DATE_RFC2822));
        $result->shouldHaveKeyWithValue('end_time', $end->format(DATE_RFC2822));
        $result->shouldHaveKey('manager');
        $result['manager']->shouldHaveKeyWithValue("id", 76543);
        $result['manager']->shouldHaveKeyWithValue("name", "John Williamson");
        $result['manager']->shouldHaveKeyWithValue("email", "jwilliamson@gmail.com");
        $result['manager']->shouldHaveKeyWithValue("phone", null);
        $result->shouldHaveKey('employee');
        $result['employee']->shouldHaveKeyWithValue("id", 65432);
        $result['employee']->shouldHaveKeyWithValue("name", "Richard Roma");
        $result['employee']->shouldHaveKeyWithValue("email", "ricky@roma.com");
        $result['employee']->shouldHaveKeyWithValue("phone", "312-322-2211");
    }

    function it_can_transform_a_single_shift_with_coworkers()
    {
        extract($this->examples());
        $shift = new Shift(54321, $manager, $employee, 0.5, $start, $end);
        $shelly = new User(12347, "Shelly Levene", "employee", "oldguy@aol.com");
        $shift = $shift->withCoworkers([$shelly]);

        $result = $this->transform($shift);

        $result->shouldBeArray();
        $result->shouldHaveKey('coworkers');
        $result['coworkers']->shouldHaveCount(1);
        $result['coworkers'][0]->shouldHaveKeyWithValue('id', $shelly->getId());
        $result['coworkers'][0]->shouldHaveKeyWithValue('name', $shelly->getName());
        $result['coworkers'][0]->shouldHaveKeyWithValue('email', $shelly->getEmail());
    }

    function it_can_return_a_shift_item()
    {
        extract($this->examples());
        $shift = new Shift(54321, $manager, $employee, 0.5, $start, $end);

        $result = $this->item($shift);

        $result->shouldHaveKey('shift');
        $result['shift']->shouldHaveKeyWithValue('id', 54321);
        $result['shift']->shouldHaveKeyWithValue('start_time', $start->format(DATE_RFC2822));
        $result['shift']->shouldHaveKeyWithValue('end_time', $end->format(DATE_RFC2822));
        $result['shift']->shouldHaveKey('manager');
        $result['shift']->shouldHaveKey('employee');
    }

    public function it_can_return_a_shift_collection()
    {
        extract($this->examples());
        $oneDay = new \DateInterval('P1D');
        $twoDays = new \DateInterval('P2D');
        $shifts = [
            new Shift(54321, $manager, $employee, 0.5, $start, $end),
            new Shift(54321, $manager, $employee, 0.5, $start->add($oneDay), $end->add($oneDay)),
            new Shift(54321, $manager, $employee, 0.5, $start->add($twoDays), $end->add($twoDays))
        ];

        $result = $this->collection($shifts);

        $result->shouldHaveKey("shifts");
        $result["shifts"]->shouldHaveCount(3);
        foreach ($result["shifts"] as $shift) {
            $shift->shouldHaveKey('id');
            $shift->shouldHaveKey('start_time');
            $shift->shouldHaveKey('end_time');
            $shift->shouldHaveKey('break');
            $shift->shouldHaveKey('manager');
            $shift->shouldHaveKey('employee');
        }
    }
}
