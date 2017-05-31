<?php
/*
 * @author    harrism
*/

namespace MatPHP;
require_once "Matrix.php";

class Matrices
{
    private $mxs = array();
    private $size = 0;

    public function __construct($matrices){
        foreach ($matrices as $matrix) {
            if ($matrix instanceof Matrix) {
                $this->mxs[] = $matrix;
            } else {
                die("Not a valid set of matrix objects.");
            }
            $this->size = sizeof($this->mxs);
        }
    }

    public function getSize(){
        return $this->size;
    }

    public function findSum(){
        $m = 1;
        $sum = $this->mxs[0];
        while ($m < $this->size){
            $sum = $sum->add($this->mxs[$m]);
            $m++;
        }
        return $sum;
    }

    public function findMean(){
        return $this->findSum()->scl(1/$this->size);
    }
}
?>