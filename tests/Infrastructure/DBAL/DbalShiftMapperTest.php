<?php

namespace Scheduler\Test\Infrastructure\DBAL;

use DateTime;
use Scheduler\Domain\Model\Shift\Shift;
use Scheduler\Domain\Model\User\NullUser;
use Scheduler\Infrastructure\DBAL\DbalShiftMapper;
use Scheduler\Infrastructure\DBAL\DbalUserMapper;

class DbalShiftMapperTest extends DbalTestCase
{
    private $shiftMapper;
    private $userMapper;

    function setUp()
    {
        parent::setUp();
        $this->userMapper = new DbalUserMapper($this->getDbalConnection());
        $this->shiftMapper = new DbalShiftMapper(
            $this->getDbalConnection(),
            $this->userMapper
        );
    }

    /**
     * @test
     */
    function itCanFindAShift()
    {
        $shift = $this->shiftMapper->find(1);

        $this->assertEquals("John Williamson", $shift->getManager()->getName());
        $this->assertEquals("Richard Roma", $shift->getEmployee()->getName());
        $this->assertEquals(0.5, $shift->getBreak());
        $this->assertEquals("2015-09-07 17:30:00", $shift->getStartTime()->format('Y-m-d H:i:s'));
        $this->assertEquals("2015-09-07 23:30:00", $shift->getEndTime()->format('Y-m-d H:i:s'));
        $this->assertEquals("2015-09-03 15:11:23", $shift->getCreated()->format('Y-m-d H:i:s'));
        $this->assertEquals("2015-09-03 15:11:23", $shift->getUpdated()->format('Y-m-d H:i:s'));
    }

    /**
     * @test
     * @dataProvider shiftsByEmployeeId
     */
    function itCanFindShiftsByEmployeeId($employee_id, $count)
    {
        $shifts = $this->shiftMapper->findShiftsByEmployeeId($employee_id);

        $this->assertEquals($count, count($shifts));
        foreach ($shifts as $shift) {
            $this->assertEquals($employee_id, $shift->getEmployee()->getId());
        }
    }

    public function shiftsByEmployeeId()
    {
        return [
            [
                "employee_id" => 2,
                "count" => 2
            ],
            [
                "employee_id" => 3,
                "count" => 3
            ]
        ];
    }

    /**
     * @test
     * @dataProvider shiftsInTimePeriod
     */
    function itCanFindShiftsInTimePeriod($start, $end, $count)
    {
        $shifts = $this->shiftMapper->findShiftsInTimePeriod($start, $end);
        $this->assertEquals($count, count($shifts));
    }

    public function shiftsInTimePeriod()
    {
        return [
            [
                "start" => new DateTime("2015-09-07 5:30 PM"),
                "end" => new DateTime("2015-09-07 11:30 PM"),
                "count" => 2
            ],
            [
                "start" => new DateTime("2015-09-07 5:30 PM"),
                "end" => new DateTime("2015-09-08 11:30 PM"),
                "count" => 4
            ],
            [
                "start" => new DateTime("2015-09-09"),
                "end" => new DateTime("2015-09-10"),
                "count" => 1
            ],
            [
                "start" => new DateTime("2015-09-01"),
                "end" => new DateTime("2015-09-15"),
                "count" => 7
            ]
        ];
    }

    /**
     * @test
     * @dataProvider shiftsInTimePeriodForEmployee
     */
    public function itCanFindShiftsInTimePeriodByEmployeeId($start, $end, $employeeId, $count)
    {
        $shifts = $this->shiftMapper->findShiftsInTimePeriodByEmployeeId($start, $end, $employeeId);
        $this->assertEquals($count, count($shifts));
    }

    public function shiftsInTimePeriodForEmployee()
    {
        return [
            [
                "start" => new DateTime("2015-09-07 5:30 PM"),
                "end" => new DateTime("2015-09-07 11:30 PM"),
                "employee_id" => 2,
                "count" => 1
            ],
            [
                "start" => new DateTime("2015-09-07 5:30 PM"),
                "end" => new DateTime("2015-09-07 11:30 PM"),
                "employee_id" => 3,
                "count" => 1
            ],
            [
                "start" => new DateTime("2015-09-07 5:30 PM"),
                "end" => new DateTime("2015-09-08 11:30 PM"),
                "employee_id" => 2,
                "count" => 2
            ],
            [
                "start" => new DateTime("2015-09-07 5:30 PM"),
                "end" => new DateTime("2015-09-08 11:30 PM"),
                "employee_id" => 3,
                "count" => 2
            ],
            [
                "start" => new DateTime("2015-09-09"),
                "end" => new DateTime("2015-09-10"),
                "employee_id" => 2,
                "count" => 0
            ],
            [
                "start" => new DateTime("2015-09-09"),
                "end" => new DateTime("2015-09-10"),
                "employee_id" => 3,
                "count" => 1
            ],
            [
                "start" => new DateTime("2015-09-01"),
                "end" => new DateTime("2015-09-15"),
                "employee_id" => null,
                "count" => 0
            ]
        ];
    }

    /**
     * @test
     */
    function itCanFindOpenShifts()
    {
        $openShifts = $this->shiftMapper->findOpenShifts();

        $this->assertEquals(2, count($openShifts));
        foreach ($openShifts as $shift) {
            $this->assertTrue($shift->getEmployee() instanceof NullUser);
        }
    }

    /**
     * @test
     */
    function itCanInsertAShift()
    {
        $initialRowCount = $this->getConnection()->getRowCount('shifts');

        $manager = $this->userMapper->find(1);
        $employee = $this->userMapper->find(2);
        $break = 0.5;
        $start = new DateTime("7:00 PM");
        $end = new DateTime("11:00 PM");
        $shift = new Shift(null, $manager, $employee, $break, $start, $end);
        $insertedId = $this->shiftMapper->insert($shift);

        $this->assertEquals($initialRowCount + 1, $this->getConnection()->getRowCount('shifts'));
        $this->shiftMapper->clean();
        $inserted = $this->shiftMapper->find($insertedId);
        $this->assertEquals("John Williamson", $inserted->getManager()->getName());
        $this->assertEquals("Richard Roma", $inserted->getEmployee()->getName());
        $this->assertTrue(0.5 === $inserted->getBreak());
        $this->assertEquals($start, $inserted->getStartTime());
        $this->assertEquals($end, $inserted->getEndTime());
    }

    /**
     * @test
     */
    function itCanUpdateAShift()
    {
        $aShift = $this->shiftMapper->find(1);
        $this->assertEquals(2, $aShift->getEmployee()->getId());

        $shelly = $this->userMapper->find(3);
        $shiftToUpdate = $aShift->assignTo($shelly);
        $this->shiftMapper->update($shiftToUpdate);

        $this->shiftMapper->clean();
        $updatedShift = $this->shiftMapper->find(1);
        $this->assertEquals(3, $updatedShift->getEmployee()->getId());
        $this->assertTrue($aShift->getUpdated() < $updatedShift->getUpdated());
    }
}
