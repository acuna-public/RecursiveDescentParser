# RecursiveDescentParser
A Recursive descent parser (https://en.wikipedia.org/wiki/Recursive_descent_parser) PHP implementation

**Usage**


    $parser = new Parser ('(2 + (5 * 4) + 3) * 6');
    echo $parser->result; // 150
