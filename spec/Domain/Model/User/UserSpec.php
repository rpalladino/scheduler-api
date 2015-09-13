<?php

namespace spec\Scheduler\Domain\Model\User;

use DateTimeImmutable as DateTime;
use DateTimeInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UserSpec extends ObjectBehavior
{
    private function example_arguments()
    {
        return [
            "id" => 1,
            "name" => "Richard Roma",
            "role" => "employee",
            "email" => "ricky@roma.com",
            "phone" => "312-331-3322",
            "created" => new DateTime("2015-02-13 16:30:00"),
            "updated" => new DateTime("2015-02-13 16:30:00")
        ];
    }

    function let()
    {
        extract($this->example_arguments());
        $this->beConstructedWith($id, $name, $role, $email, $phone, $created, $updated);
    }

    function it_has_named_constructor_for_an_employee_named_with_email()
    {
        $this->beConstructedThrough("employeeNamedWithEmail", ["Richard Roma", "ricky@roma.com"]);
        $this->getRole()->shouldReturn("employee");
        $this->getName()->shouldReturn("Richard Roma");
        $this->getEmail()->shouldReturn("ricky@roma.com");
    }

    function it_has_named_constructor_for_a_manager_named_with_email()
    {
        $this->beConstructedThrough("managerNamedWithEmail", ["John Williamson", "jwilliamson@gmail"]);
        $this->getRole()->shouldReturn("manager");
        $this->getName()->shouldReturn("John Williamson");
        $this->getEmail()->shouldReturn("jwilliamson@gmail");
    }

    function it_has_an_id()
    {
        $this->getId()->shouldReturn(1);
    }

    function it_allows_id_to_be_null()
    {
        extract($this->example_arguments());
        $id = null;
        $this->beConstructedWith($id, $name, $role, $email, $phone, $created, $updated);
        $this->getId()->shouldReturn(null);
    }

    function it_throws_when_id_is_not_an_integer_when_specified()
    {
        $this->shouldThrow(\InvalidArgumentException::class)
             ->during("__construct", ["1", "Richard Roma", "employee", "ricky@roma.com"]);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn("Richard Roma");
    }

    function it_has_a_role()
    {
        $this->getRole()->shouldReturn("employee");
    }

    function its_role_must_be_either_employee_or_manager()
    {
        extract($this->example_arguments());
        $this->shouldThrow(\InvalidArgumentException::class)
             ->during('__construct', [$id, $name, "supervisor", $email]);
    }

    function it_can_have_an_email()
    {
        $this->beConstructedWith(1, "Richard Roma", "employee", "ricky@roma.com", null);
        $this->getEmail()->shouldReturn("ricky@roma.com");
    }

    function it_can_have_a_phone()
    {
        $this->beConstructedWith(1, "Richard Roma", "employee", null, "312-333-2222");
        $this->getPhone()->shouldReturn("312-333-2222");
    }

    function it_must_have_either_phone_or_email_defined()
    {
        extract($this->example_arguments());
        $this->shouldThrow(\InvalidArgumentException::class)
             ->during('__construct', [$id, $name, $role, null, null]);
    }

    function it_has_a_created_time()
    {
        $this->getCreated()->shouldHaveType(DateTimeInterface::class);
    }

    function it_has_a_updated_time()
    {
        $this->getUpdated()->shouldHaveType(DateTimeInterface::class);
    }

    function it_can_be_constructed_without_created_or_updated_times()
    {
        $this->beConstructedWith(1, "Richard Roma", "employee", "ricky@roma.com");
        $this->getCreated()->shouldHaveType(DateTimeInterface::class);
        $this->getUpdated()->shouldHaveType(DateTimeInterface::class);
    }

    function it_can_be_authenticated()
    {
        $this->shouldNotBeAuthenticated();
        $this->authenticate();
        $this->shouldBeAuthenticated();
    }
}
