<?php

namespace BoggleSolver;

class BoggleSolverTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadDictBuildsDictionary()
    {
        $solverMock = $this->getMockBuilder('\BoggleSolver\BoggleSolver')
                           ->setConstructorArgs(array('dict.txt'))
                           ->setMethods(array('getWords'))
                           ->getMock();

        $lessWords = array(
            "ABCD", "ABC", "ABBA", "ABABA", "ABQU", "XYZ", "ABBBBBBBBBBBBBBBB", "ABBBBBBBBB", "AB",
        );

        $solverMock->expects($this->once())
                   ->method('getWords')
                   ->will($this->returnValue($lessWords));

        $solverMock->boardLookup = array('A' => 1);
        $solverMock->size = 3;

        $solverMock->loadDict();

        $expectedDict = array(
            'A' => array(
                'B' => array(
                    'C' => array(
                        "ABC",
                        'D' => array(
                            "ABCD",
                        ),
                    ),
                    'B' => array(
                        'A' => array(
                            "ABBA",
                        ),
                    ),
                    'A' => array(
                        'B' => array(
                            'A' => array(
                                "ABABA",
                            ),
                        ),
                    ),
                    'Qu' => array(
                        "ABQU"
                    ),
                ),
            ),
        );

        $this->assertEquals($expectedDict, $solverMock->dict);

        // lazy
        $solverMock->unloadDict();

        $this->assertEquals(array(), $solverMock->dict);
    }

    public function testLoadBoardLoadsBoard()
    {
        $exampleBoard = "A B C\nX Y Z\nQu R S";

        $solverMock = $this->getMockBuilder('\BoggleSolver\BoggleSolver')
                           ->setMethods(array('loadDict'))
                           ->getMock();

        $solverMock->expects($this->once())
                   ->method('loadDict');

        $solverMock->loadBoard($exampleBoard);

        $expectedLookup = array(
            'A', 'B', 'C', 'X', 'Y', 'Z', 'Q', 'R', 'S'
        );

        $this->assertEquals(3, $solverMock->size);

        $this->assertEquals($expectedLookup, array_keys($solverMock->boardLookup));
    }

    public function testLoadBoardLoadsBoardOfFourByFour()
    {
        $exampleBoard = "A B C D\nX Y Z A\nQu R S T\nO K A Y";

        $solverMock = $this->getMockBuilder('\BoggleSolver\BoggleSolver')
                           ->setMethods(array('loadDict'))
                           ->getMock();

        $solverMock->expects($this->once())
                   ->method('loadDict');

        $solverMock->loadBoard($exampleBoard);

        $this->assertEquals(4, $solverMock->size);

    }

    public function testLoadBoardThrowsExceptionOnUnknownBoardSize()
    {
        $exampleBoard = "A";

        $this->setExpectedException('\BoggleSolver\BoggleException');

        $solverMock = $this->getMockBuilder('\BoggleSolver\BoggleSolver')
                           ->setMethods(array('loadDict'))
                           ->getMock();

        $solverMock->expects($this->never())
                   ->method('loadDict');

        $solverMock->loadBoard($exampleBoard);
    }

    public function testDisplayBoardReturnsBoard()
    {
        $solverMock = $this->getMockBuilder('\BoggleSolver\BoggleSolver')
                           ->setMethods(array('loadDict'))
                           ->getMock();

        $solverMock->expects($this->once())
                   ->method('loadDict');

        $solverMock->loadBoard("ABCDEFGHI");

        $result = $solverMock->displayBoard();

        $this->assertEquals("A B C \nD E F \nG H I \n", $result);
    }

    public function testFindWords()
    {
        $solverMock = $this->getMockBuilder('\BoggleSolver\BoggleSolver')
                           ->setMethods(array('getWords'))
                           ->getMock();

        $words = array('XXX', 'ALL', 'XXXXLA');

        $solverMock->expects($this->once())
                   ->method('getWords')
                   ->will($this->returnValue($words));

        $solverMock->boardLookup = array('A' => 1);
        $solverMock->size = 3;

        $solverMock->loadBoard("X X A L L X L L X");

        // X X A
        // L L X
        // L L X

        $result = $solverMock->findWords();

        $expected = array(
            'XXX' => array(
                array(0, 1, 5),
                array(1, 5, 8),
                array(5, 1, 0),
                array(8, 5, 1),
            ),
            'ALL' => array(
                array(2, 4, 7),
                array(2, 4, 6),
                array(2, 4, 3),
            ),
        );

        $this->assertEquals($expected, $result);
    }

    public function testGetWords()
    {
        $solverMock = $this->getMockBuilder('\BoggleSolver\BoggleSolver')
                           ->setMethods(array('getDictFileContents'))
                           ->getMock();

        $solverMock->expects($this->once())
                   ->method('getDictFileContents')
                   ->will($this->returnValue("XYZ\r\nABC\r\nQRS"));

        $result = $solverMock->getWords();

        $this->assertEquals(array('XYZ', 'ABC', 'QRS'), $result);
    }
}
