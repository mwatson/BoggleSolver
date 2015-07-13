<?php

namespace BoggleSolver;

class BoggleSolver
{
    public $dict = array();

    public $size = 0;
    public $board = array();
    public $boardLookup = array();

    public $lastDictTime;
    public $lastSolveTime;

    // for now, 3 - 11 letter words only
    public static $minLen = 3;
    public static $maxLen = 11;

    public static $dictFile = "/../resources/dict.txt";

    public static $dirs = array(
        'n', 'ne', 'e', 'se', 's', 'sw', 'w', 'nw',
    );

    // $dictFile should be relative to this file
    public function __construct($dictFile = "")
    {
        if ($dictFile) {
            static::$dictFile = $dictFile;
        }
    }

    public function getWords()
    {
        return explode("\r\n", $this->getDictFileContents());
    }

    public function loadDict()
    {
        $start = microtime(true);

        $this->dict = array();
        $words = $this->getWords();

        // Create a boggle friendly lookup table. This is basically a half-assed
        // search tree so we know when we can stop checking for words on the
        // current path.
        foreach ($words as $word) {

            // no need for words that can't be on the board
            if (!isset($this->boardLookup[$word[0]])) {
                continue;
            }

            $wordLen = strlen($word);

            // max length is mostly here for memory reasons
            if ($wordLen < static::$minLen) {
                continue;
            }
            if ($wordLen > static::$maxLen) {
                continue;
            }
            if ($wordLen > $this->size * $this->size) {
                continue;
            }

            $ptr = &$this->dict;

            for ($i = 0; $i < $wordLen; $i++) {
                $letter = $word[$i];
                if ($letter == 'Q' && isset($words[$i + 1]) && $word[$i + 1] == 'U') {
                    $letter = 'Qu';
                    $i++;
                }
                if (!isset($ptr[$letter])) {
                    $ptr[$letter] = array();
                }
                $ptr = &$ptr[$letter];
            }
            $ptr[] = $word;
        }

        unset($words);

        $this->lastDictTime = microtime(true) - $start;
    }

    public function loadBoard($board)
    {
        // the board can be any string, non alpha chars will be stripped
        $board = strtoupper(preg_replace("|[^A-Za-z]|", '', $board));

        $this->size = 0;

        if (strpos($board, 'QU') !== false) {
            $board = str_replace('QU', 'Q', $board);
        }

        // currently only 3x3 and 4x4 are suported
        switch (strlen($board)) {
            case 9:
                $this->size = 3;
                break;
            case 16:
                $this->size = 4;
                break;
            default:
                throw new BoggleException("Unknown board size of " . strlen($board));
        }

        for ($i = 0; $i < strlen($board); $i++) {
            $letter = $board[$i] == 'Q' ? 'Qu' : $board[$i];
            $this->board[] = new BoggleTile($letter, $i);
            $this->boardLookup[$board[$i]] = true;
        }

        $r = 1;
        for ($i = 0; $i < sizeof($this->board); $i++) {
            if ($r < $this->size) {
                $this->board[$i]->e = &$this->board[$i + 1];
            }
            if ($r > 1) {
                $this->board[$i]->w = &$this->board[$i - 1];
            }

            if ($i - $this->size >= 0) {
                $this->board[$i]->n = &$this->board[$i - $this->size];
                if ($r < $this->size) {
                    $this->board[$i]->ne = &$this->board[($i - $this->size) + 1];
                }
                if ($r > 1) {
                    $this->board[$i]->nw = &$this->board[$i - $this->size - 1];
                }
            }

            if ($i + $this->size < $this->size * $this->size) {
                $this->board[$i]->s = &$this->board[$i + $this->size];
                if ($r < $this->size) {
                    $this->board[$i]->se = &$this->board[$i + $this->size + 1];
                }
                if ($r > 1) {
                    $this->board[$i]->sw = &$this->board[$i + $this->size - 1];
                }
            }

            if ($r == $this->size) {
                $r = 0;
            }

            $r++;
        }

        // we can load the dictionary now
        $this->loadDict();
    }

    public function displayBoard($lineEnd = "\n")
    {
        $rowPtr = &$this->board[0];

        $board = "";
        while ($rowPtr !== null) {
            $colPtr = $rowPtr;
            while ($colPtr !== null) {
                $board .= $colPtr->letter;
                if ($colPtr->letter != "Qu") {
                    $board .= " ";
                }
                $colPtr = &$colPtr->e;
            }
            $board .= $lineEnd;
            $rowPtr = $rowPtr->s;
        }

        return $board;
    }

    public function findWords()
    {
        $start = microtime(true);

        $ptr = &$this->board[0];
        $dir = "e";

        $words = array();

        while (1) {

            $ptr->visited = true;

            $words = array_merge(
                $words,
                $this->findWordsFromOneTile($ptr, null, $words)
            );

            $ptr->visited = false;

            if ($ptr->$dir === null && $ptr->s === null) {
                break;
            }

            if ($ptr->$dir !== null) {
                $ptr = &$ptr->$dir;
            } else if ($ptr->s !== null) {
                $ptr = &$ptr->s;
                $dir = $dir == "e" ? "w" : "e";
            }
        }

        $this->lastSolveTime = microtime(true) - $start;

        return $words;
    }

    public function findWordsFromOneTile($boardPtr, $dictPtr = null, &$words = array(), $path = array())
    {
        if ($dictPtr === null) {
            $dictPtr = &$this->dict;
        }

        $curLetter = $boardPtr->letter;

        if (!isset($dictPtr[$curLetter])) {
            return array();
        }

        $path[] = $boardPtr->id;

        $dictPtr = &$dictPtr[$curLetter];

        if (isset($dictPtr[0])) {
            if (!isset($words[$dictPtr[0]])) {
                $words[$dictPtr[0]] = array();
            }
            $words[$dictPtr[0]][] = $path;
        }

        foreach (static::$dirs as $dir) {
            if ($boardPtr->$dir === null) {
                continue;
            }
            if ($boardPtr->$dir->visited) {
                continue;
            }

            // if we're going diagonal, make sure the two adjacent tiles
            // haven't already been pathed to each other
            if (strlen($dir) == 2) {
                $d1 = substr($dir, 0, 1);
                $d2 = substr($dir, 1, 1);
                if ($boardPtr->$d1->pathTo == $boardPtr->$d2->id ||
                    $boardPtr->$d2->pathTo == $boardPtr->$d1->id
                ) {
                    continue;
                }
            }

            // set up some flags so we don't backtrack to this tile
            $boardPtr->$dir->visited = true;
            $boardPtr->$dir->pathTo = $boardPtr->id;
            $boardPtr->pathTo = $boardPtr->$dir->id;

            $newWords = $this->findWordsFromOneTile($boardPtr->$dir, $dictPtr, $words, $path);
            $words = array_merge($words, $newWords);

            // unset those flags since we're picking a new path after this
            $boardPtr->$dir->visited = false;
            $boardPtr->$dir->pathTo = -1;
            $boardPtr->pathTo = -1;
        }

        return $words;
    }

    // @codeCoverageIgnoreStart
    public function getDictFileContents()
    {
        return file_get_contents(__DIR__ . static::$dictFile);
    }
    // @codeCoverageIgnoreEnd
}
