<?php
header('Content-Type: text/plain; charset=utf-8');

// if you just use composer:
//include(__DIR__ . '/../vendor/autoload.php');

// if you don't have an autoloader for some reason
include(__DIR__ . '/../src/BoggleTile.php');
include(__DIR__ . '/../src/BoggleException.php');
include(__DIR__ . '/../src/BoggleSolver.php');

// testing a board with a Qu tile
$boggleBoard = "IZLEERCVADJQuITDI";

$boggle = new \BoggleSolver\BoggleSolver();

try {
    $boggle->loadBoard($boggleBoard);
} catch (\BoggleSolver\BoggleException $e) {
    die("exiting on error: " . $e->getMessage());
}

$words = $boggle->findWords();

echo "Boggle Board:\n\n";
echo $boggle->displayBoard();

echo "\n";

foreach ($words as $word => $paths) {
    echo $word . "\n";
}

echo "\n";
