<?php

class X implements IteratorAggregate {
    public function getIterator(){
        yield from [1,2,3,4,5];
    }
    public function getGenerator(){
        foreach ($this as $j => $each){
            echo "getGenerator(): yielding: {$j} => {$each}\n";
            $val = (yield $j => $each);
            yield; // ignore foreach's next()
            echo "getGenerator(): received: {$j} => {$val}\n";
        }
    }
}
$x = new X;

foreach ($x as $i => $val){
    echo "getIterator(): {$i} => {$val}\n";
}
echo "\n";

$gen = $x->getGenerator();
foreach ($gen as $j => $val){
    echo "getGenerator(): sending:  {$j} => {$val}\n";
    $gen->send($val);
}