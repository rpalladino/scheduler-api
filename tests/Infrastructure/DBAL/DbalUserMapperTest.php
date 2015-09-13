<?php

namespace Scheduler\Test\Infrastructure\DBAL;

use Scheduler\Domain\Model\User\NullUser;
use Scheduler\Domain\Model\User\User;
use Scheduler\Infrastructure\DBAL\DbalUserMapper;

class DbalUserMapperTest extends DbalTestCase
{
    private $userMapper;

    function setUp()
    {
        parent::setUp();
        $this->userMapper = new DbalUserMapper($this->getDbalConnection());
    }

    /**
     * @test
     */
    function itCanFindAUser()
    {
        $user = $this->userMapper->find(1);

        $this->assertEquals("John Williamson", $user->getName());
        $this->assertEquals("manager", $user->getRole());
        $this->assertEquals("jwilliamson@gmail.com", $user->getEmail());
        $this->assertEquals("312-332-1233", $user->getPhone());
        $this->assertEquals("2015-07-03 08:44:05", $user->getCreated()->format('Y-m-d H:i:s'));
        $this->assertEquals("2015-09-06 13:24:57", $user->getUpdated()->format('Y-m-d H:i:s'));
    }

    /**
     * @test
     */
    function itReturnsNullUserIfNotFound()
    {
        $user = $this->userMapper->find(76543);

        $this->assertTrue($user instanceof NullUser);
    }

    /**
     * @test
     */
    function itCanFindUsersByRole()
    {
        $users = $this->userMapper->findByRole("employee");

        $this->assertEquals(2, count($users));
        foreach ($users as $user) {
            $this->assertEquals("employee", $user->getRole());
        }
    }

    /**
     * @test
     */
    function itCanInsertAUser()
    {
        $this->assertEquals(3, $this->getConnection()->getRowCount('users'));

        $user = new User(null, "David Moss", "employee", "dmoss@hotmail.com");
        $id = $this->userMapper->insert($user);

        $this->assertEquals(4, $this->getConnection()->getRowCount('users'));
        $this->userMapper->clean();
        $moss = $this->userMapper->find($id);
        $this->assertEquals("David Moss", $moss->getName());
        $this->assertEquals("employee", $moss->getRole());
        $this->assertEquals("dmoss@hotmail.com", $moss->getEmail());
    }
}
