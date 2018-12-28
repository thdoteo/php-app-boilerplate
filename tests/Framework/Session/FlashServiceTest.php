<?php

namespace Tests\Framework\Session;

use Framework\Session\ArraySession;
use Framework\Session\FlashService;
use PHPUnit\Framework\TestCase;

class FlashServiceTest extends TestCase
{
    /**
     * @var ArraySession
     */
    private $session;

    /**
     * @var FlashService
     */
    private $flash;

    public function setUp()
    {
        $this->session = new ArraySession();
        $this->flash = new FlashService($this->session);
    }

    public function testDeleteFlashAfterGet()
    {
        $this->flash->success('Bravo');
        $this->assertEquals('Bravo', $this->flash->get('success'));
        $this->assertNull($this->session->get('flash'));
        $this->assertEquals('Bravo', $this->flash->get('success'));
        $this->assertEquals('Bravo', $this->flash->get('success'));
    }
}