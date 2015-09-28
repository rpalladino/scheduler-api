<?php

namespace spec\Scheduler\Web\Radar\Responder;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use DateTime;
use Aura\Payload\Payload;
use Scheduler\Domain\Model\Shift\Shift;
use Scheduler\Domain\Model\User\NullUser;
use Scheduler\Domain\Model\User\User;
use Scheduler\Web\Resource\ShiftResource;
use Scheduler\Web\Resource\UserResource;
use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;

class ShiftResponderSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new ShiftResource(new UserResource()));
    }

    function examples()
    {
        return [
            "employee" => new NullUser(),
            "manager" => new User(1, "John Williamson", "manager", "jwilliamson@gmail.com"),
            "start" => new DateTime("10:30 AM"),
            "end" => new DateTime("2:30 PM"),
            "break" => 0.5,
        ];
    }

    function it_can_respond_with_a_resource_item_on_success()
    {
        extract($this->examples());

        $payload = new Payload();
        $payload->setStatus(Payload::SUCCESS);
        $payload->setOutput(new Shift(76543, $manager, $employee, $break, $start, $end));

        $response = $this(new Request(), new Response(), $payload);

        $response->shouldImplement(\Psr\Http\Message\ResponseInterface::class);
        $response->getStatusCode()->shouldBe(200);
    }
}
