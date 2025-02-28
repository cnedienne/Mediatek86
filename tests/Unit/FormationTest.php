<?php

namespace App\tests\Unit;
use PHPUnit\Framework\TestCase; 
use App\Entity\Formation;

class FormationTest extends TestCase
{
    public function testGetPublishedAtString()
    {
        $formation = new Formation();
        $date = new \DateTime("2025-02-27");
        $formation->setPublishedAt($date);
        $this->assertEquals("27/02/2025", $formation->getPublishedAtString());
    }

}