<?php
/**
 * This file is part of library-template
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 * @author Rahal Aboulfeth <rahal.aboulfeth@gmail.com>
 */

/**
 * This is a basic benchmark test for Engine...
 */
include 'vendor/autoload.php';

$engine = new \Youniwemi\StringTemplate\Engine();
$template = "These are {foo} and {bar}. Those are {goo.b} and {goo.v}  {goo.e%E} %";
$vars = array(
    'foo' => 'bar',
    'baz' => 'friend',
    'goo' => array('a' => 'b', 'c' => 'd' , 'e' => 12.4 )
);
$replace = function () use ($engine, $template, $vars) {
    $engine->render($template, $vars);
};


$varsSearch = array(
    '{foo}', '{baz}', '{goo.a}', '{goo.c}' , '{goo.e%E}'
);
$varsReplace = array(
    'bar', 'friend', 'b', 'd' , '12.4'
);

$strReplace = function () use ($template, $varsSearch, $varsReplace) {
    str_replace($varsSearch, $varsReplace, $template);
};

function benchmark($f, $title = '', $iterations = 100000)
{
    static $firstTime = 0;
    echo "\n\n======", $title, "=======\n";
    $start = microtime(true);
    for ($i = 0; $i < $iterations; $i++) {
        $f();
    }
    $time = microtime(true) - $start;
    echo 'Time: ', $time, "\n";
    if ($firstTime) {
        echo 'Factor: ', sprintf("%d.3&times;", $time / $firstTime);
        echo ', Reverse Factor: ', sprintf("%d.3&times;", $firstTime / $time), "\n";
    } else {
        $firstTime = $time;
    }
    echo 'Average: ', $time / $iterations, "\n";
    echo 'MemoryPeak: ', memory_get_peak_usage(), ' (meaningful only if you run one benchmark at time)';
}

benchmark($replace, 'Engine benchmark');
benchmark($strReplace, 'StrReplace benchmark');
