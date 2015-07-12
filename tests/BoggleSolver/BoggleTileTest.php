<?php

namespace BoggleSolver;

class BoggleTileTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $tile = new BoggleTile('A', 0);
        
        $this->assertEquals('A', $tile->letter);
        $this->assertEquals(0, $tile->id);
    }
}