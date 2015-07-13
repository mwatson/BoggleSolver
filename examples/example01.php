<?php
header('Content-Type: text/plain; charset=utf-8');

// if you just use composer:
//include(__DIR__ . '/../vendor/autoload.php');

// if you don't have an autoloader for some reason
include(__DIR__ . '/../src/BoggleTile.php');
include(__DIR__ . '/../src/BoggleException.php');
include(__DIR__ . '/../src/BoggleSolver.php');

// the board can be a string with whatever weird formatting
$boggleBoard = "A M T O".
               "L N S T".
               "L X T G".
               "E T A N";

// initialize BoggleSolver
$boggle = new \BoggleSolver\BoggleSolver();

// load the board (this is the only thing that throws an exception)
try {
    $boggle->loadBoard($boggleBoard);
} catch (\BoggleSolver\BoggleException $e) {
    die("exiting on error: " . $e->getMessage());
}

// find your words!
$words = $boggle->findWords();
$numWords = sizeof($words);

echo "Boggle Board:\n\n";

// this can be used to print the board (even if the input string was not formatted right)
echo $boggle->displayBoard();

echo "\n";

echo "Found {$numWords} words:\n\n";

foreach ($words as $word => $paths) {
    echo "{$word}\n";
}

echo "\n";

// lastSolveTime will hold the time it took to solve
echo "Dictionary built in {$boggle->lastDictTime} seconds\n";
echo "Board solved in {$boggle->lastSolveTime} seconds\n";
