<?php

namespace spec\Scheduler\Domain\Model\Shift;

use DateTimeImmutable as DateTime;
use DateTimeInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Scheduler\Domain\Model\User\NullUser;
use Scheduler\Domain\Model\User\User;

class ShiftSpec extends ObjectBehavior
{
    private function example_arguments()
    {
        return [
            "id" => 1,
            "manager" => User::managerNamedWithEmail("John Williamson", "jwilliamson@gmail.com"),
            "employee" => User::employeeNamedWithEmail("Richard Roma", "ricky@roma.com"),
            "break" => 0.5,
            "start" => new DateTime("2015-12-24 16:30:00"),
            "end" => new DateTime("2015-12-24 23:00:00"),
            "created" => new DateTime("2015-02-13 16:30:00"),
            "updated" => new DateTime("2015-02-13 16:30:00")
        ];
    }

    function let()
    {
        extract($this->example_arguments());
        $this->beConstructedWith($id, $manager, $employee, $break, $start, $end, $created, $updated);
    }

    function it_has_a_named_constructor_for_creating_with_manager_and_times()
    {
        extract($this->example_arguments());
        $this->beConstructedThrough("withManagerAndTimes", [$manager, $start, $end]);
        $this->getManager()->shouldBe($manager);
        $this->getStartTime()->shouldBe($start);
        $this->getEndTime()->shouldBe($end);
        $this->getBreak()->shouldBe(0.0);
    }

    function it_has_an_id()
    {
        $this->getId()->shouldReturn(1);
    }

    function it_allows_id_to_be_null()
    {
        extract($this->example_arguments());
        $this->beConstructedWith(null, $manager, $employee, $break, $start, $end, $created, $updated);
        $this->getId()->shouldReturn(null);
    }

    function it_throws_when_id_is_not_an_integer_when_specified()
    {
        extract($this->example_arguments());
        $this->shouldThrow(\InvalidArgumentException::class)
             ->during("__construct", ["1", $manager, $employee, $break, $start, $end]);
    }

    function it_has_a_manager()
    {
        $manager = $this->example_arguments()["manager"];
        $this->getManager()->shouldBeLike($manager);
    }

    function it_has_an_employee()
    {
        $employee = $this->example_arguments()["employee"];
        $this->getEmployee()->shouldBeLike($employee);
    }

    function it_allows_employee_to_be_null()
    {
        extract($this->example_arguments());
        $this->beConstructedThrough("withManagerAndTimes", [$manager, $start, $end]);
        $this->getEmployee()->shouldBeLike(new NullUser());
    }

    function it_can_assign_to_a_different_employee_immutably()
    {
        $shelly = User::employeeNamedWithEmail("Sheldon Levene", "oldguy@aol.com");
        $modified = $this->assignTo($shelly);
        $modified->getEmployee()->shouldBe($shelly);
        $this->shouldNotBe($modified);
    }

    function it_has_a_break()
    {
        $this->getBreak()->shouldBe(0.5);
    }

    function it_throws_when_break_is_not_a_float()
    {
        extract($this->example_arguments());
        $this->shouldThrow(\InvalidArgumentException::class)
             ->during("__construct", [$id, $manager, $employee, "0.5", $start, $end]);
    }

    function it_has_a_start_time()
    {
        $this->getStartTime()->shouldHaveType(DateTimeInterface::class);
    }

    function it_can_change_start_time_immutably()
    {
        $newStart = new DateTime("2015-12-24 19:00:00");
        $modified = $this->changeStartTime($newStart);
        $modified->getStartTime()->shouldBe($newStart);
        $this->shouldNotBe($modified);
    }

    function it_has_an_end_time()
    {
        $this->getEndTime()->shouldHaveType(DateTimeInterface::class);
    }

    function it_can_change_end_time_immutably()
    {
        $newEnd = new DateTime("2015-12-25 00:00:00");
        $modified = $this->changeEndTime($newEnd);
        $modified->getEndTime()->shouldBe($newEnd);
        $this->shouldNotBe($modified);
    }

    function it_can_calculate_hours_worked()
    {
        $this->getHours()->shouldReturn(6.0);
    }

    function it_has_a_created_time()
    {
        $this->getCreated()->shouldHaveType(DateTimeInterface::class);
    }

    function it_has_an_updated_time()
    {
        $this->getUpdated()->shouldHaveType(DateTimeInterface::class);
    }

    function it_changes_updated_time_when_modified()
    {
        $shelly = User::employeeNamedWithEmail("Sheldon Levene", "oldguy@aol.com");
        $modified = $this->assignTo($shelly);
        $modified->getUpdated()->shouldNotBe($this->getUpdated());

        $newStart = new DateTime("2015-12-24 19:00:00");
        $modified = $this->changeStartTime($newStart);
        $modified->getUpdated()->shouldNotBe($this->getUpdated());

        $newEnd = new DateTime("2015-12-25 00:00:00");
        $modified = $this->changeEndTime($newEnd);
        $modified->getUpdated()->shouldNotBe($this->getUpdated());
    }

    function it_can_be_constructed_without_created_or_updated_times()
    {
        extract($this->example_arguments());
        $this->beConstructedWith($id, $manager, $employee, $break, $start, $end);
        $this->getCreated()->shouldHaveType(DateTimeInterface::class);
        $this->getUpdated()->shouldHaveType(DateTimeInterface::class);
    }
}
