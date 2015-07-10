<?php

namespace BoggleSolver;

class BoggleTile
{
    // it's easiest to use cardinal directions
    public $n = null;
    public $ne = null;
    public $e = null;
    public $se = null;
    public $s = null;
    public $sw = null;
    public $w = null;
    public $nw = null;

    public $id = -1;

    public $letter = "";
    public $visited = false;
    public $pathTo = null;

    public function __construct($letter, $id = -1)
    {
        $this->letter = $letter;
        if ($id >= 0) {
            $this->id = $id;
        }
    }
}

