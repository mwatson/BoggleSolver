BoggleSolver
----

This is a PHP class that solves Boggle boards. I made it for fun.

## Installing

If you use composer:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/mwatson/BoggleSolver.git"
        }
    ],
    "require": {
        "mwatson/BoggleSolver": "0.3.0"
    }
}
```

## Usage

```php
$boggle = new \BoggleSolver\BoggleSolver();

try {
    $boggle->loadBoard(
        "A M T O".
        "L N S T".
        "L X T G".
        "E T A N"
    );
} catch (\BoggleSolver\BoggleException $e) {
    die("exiting on error: " . $e->getMessage());
}

// retrieve the list of words
$words = $boggle->findWords();
````

`BoggleSolver::findWords()` will return an array where every valid word is the key 
and the value is one or more arrays describing the path you take on the board to 
replicate finding this word.

Example (in formatted JSON, for clarity):

```json
{
    "ANTS":[
        [0,5,2,6],
        [0,5,10,6],
        [14,15,10,6]
    ],
    "ANTA":[
        [0,5,10,14]
    ]    
}
```

The numbers in the array represent each tile ID.

See the files in the `examples` directory for more info.

## Tests

If you have composer and make installed, you can run the following:

```
composer install
make tests
```

The `make coverage` command will also build coverage maps in HTML. `make clean` 
will delete the coverage directory.

## License

&copy; Mike Watson

Released under the [MIT license](http://opensource.org/licenses/MIT). See the 
`LICENSE` file.
