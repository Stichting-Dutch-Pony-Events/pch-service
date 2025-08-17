<?php

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class KernelTest extends KernelTestCase
{
    public function testKernel()
    {
        self::bootKernel();
        $this->assertTrue(self::$booted);
    }
}