<?php

namespace BoggleSolver;

class BoggleSolverTest extends \PHPUnit_Framework_TestCase
{
    public $lessWords = array(
        "ABCD", "ABC", "ABBA", "ABABA", "ABQU", "XYZ", "ABBBBBBBBBBBBBBBB", "AB"
    );

    public function testLoadDictBuildsDictionary()
    { 
        $solverMock = $this->getMockBuilder('\BoggleSolver\BoggleSolver')
                           ->setMethods(array('getWords'))
                           ->getMock();
                           
        $solverMock->expects($this->once())
                   ->method('getWords')
                   ->will($this->returnValue($this->lessWords));
                   
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
        
        $this->assertEquals("ABC\nDEF\nGHI\n", $result);
    }
    
    public function testFindWords()
    {
        $solverMock = $this->getMockBuilder('\BoggleSolver\BoggleSolver')
                           ->setMethods(array('getWords'))
                           ->getMock();
                           
        $solverMock->expects($this->once())
                   ->method('getWords')
                   ->will($this->returnValue($this->lessWords));
                   
        $solverMock->boardLookup = array('A' => 1);
        $solverMock->size = 3;

        $solverMock->loadBoard("ABCABDABA");
        
        $result = $solverMock->findWords();
        
        $expected = array(
            "ABC", "ABCD", "ABBA", "ABABA",
        );
        
        $this->assertEquals($expected, $result);
    }
}