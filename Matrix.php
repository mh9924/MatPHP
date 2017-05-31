<?php
/*
 * @author    harrism
*/

namespace MatPHP;


class Matrix
{
    private $mx;
    private $numcols = 0;
    private $numrows = 0;
    private $size = 0;

    public function __construct($matrix) {
        if($this->isValidMatrix($matrix)) {
            $this->mx = $matrix;
            $this->numrows = sizeof($this->mx);
            $this->numcols = sizeof($this->mx[0]);
            $this->size = $this->numrows * $this->numcols;
        } else{
            die("Could not create object instance - Not a valid matrix.");
        }
    }

    private function isValidMatrix($matrix) {
        if(!is_array($matrix) || !is_array($matrix[0])) {
            return false;
        }
        for ($i=1;sizeof($matrix[$i-1])==sizeof($matrix[$i]);$i++) {
            if ($i+1 == sizeof($matrix)) {
                    return true;
                }
        }
        return false;
    }

    public function getSize(){
        return $this->size;
    }

    public function getNumRows(){
        return $this->numrows;
    }

    public function getNumCols(){
        return $this->numcols;
    }

    public function getDimensions(){
        return $this->numrows . "x" . $this->numcols;
    }

    public function getIdentity(){
        if ($this->numrows == $this->numcols) {
            for ($i = 0; $i < $this->numrows; $i++) {
                for ($j = 0; $j < $this->numcols; $j++) {
                    $matrix[$i][$j] = 0;
                }
                $matrix[$i][$i] = 1;
            }
            return new Matrix($matrix);
        }
        die("Matrix must be square to get identity.");
    }

    public function getTranspose(){
        for ($i = 0; $i < $this->numrows; $i++) {
            for ($j = 0; $j < $this->numcols; $j++) {
                $matrix[$j][$i] = $this->mx[$i][$j];
            }
        }
        return new Matrix($matrix);
    }

    public function add($other){
        return $this->performOperation($other, "+");
    }

    public function sub($other){
        return $this->performOperation($other, "-");
    }

    public function mul($other){
        return $this->performOperation($other, "*");
    }

    public function div($other){
        return $this->performOperation($other, "x");
    }

    private function performOperation($other, $operation){
        if ($other instanceof Matrix) {
            if ($this->getDimensions() == $other->getDimensions() && $operation != "*") {
                return $this->handleAdditive($other, $operation);
            } elseif ($this->numcols == $other->numrows && $operation == "*") {
                return $this->handleMultiplicative($other);
            }
        } elseif (is_numeric($other) && $operation == "x") {
                return $this->handleScalar($other);
        }
        die("Can't possibly perform operation on these matrices. Check that dimensions are correct.");
    }

    private function handleAdditive($other, $operation){
        $x = $y = 0;
        foreach ($this->mx as $row) {
            $r = array();
            foreach ($row as $element) {
                if ($operation == "+") {
                    $r[] = $element + $other->mx[$x][$y];
                } elseif ($operation == "-") {
                    $r[] = $element - $other->mx[$x][$y];
                }
                $y++;
            }
            $matrix[] = $r;
            $y = 0;
            $x++;
        }
        return new Matrix($matrix);
    }

    private function handleMultiplicative($other){
        $x = 0;
        foreach ($this->mx as $row) {
            $r = array();
            for ($i=0;$i<$other->numcols;$i++) {
                $sum = 0;
                foreach ($row as $element) {
                    $dot = $element * $other->mx[$x][$i];
                    $sum = $sum + $dot;
                    $x++;
                }
                $x = 0;
                $r[] = $sum;
            }
            $matrix[] = $r;
        }
        return new Matrix($matrix);
    }

    private function handleScalar($other){
        // Implement
    }

    public function echoOut(){
        echo "\n";
        foreach($this->mx as $row) {
            echo "[";
            foreach($row as $element) {
                echo " {$element} ";
            }
            echo "]\n";
        }
        echo "\n";
    }
}

function main(){


    $m0 = new Matrix([[3,5],
                    [8,2],
                    [1,6],
                    [5,2]]);

    $m1 = new Matrix([[7,3],
                        [2,5],
                        [6,8],
                        [9,0]]);

    $m2 = new Matrix([[7,4,9],
                    [8,1,5]]);

    $m3 = new Matrix([[1,2,3],
                    [4,5,6]]);

    $m4 = new Matrix([[7,8],
                    [9,10],
                    [11,12]]);

    $sm1 = new Matrix([[1,4,8,2],
                        [6,3,2,1],
                        [5,3,9,2],
                        [2,7,4,7]]);

    $sm2 = new Matrix([[6,2,5],
                        [6,1,2],
                        [4,9,3]]);

    echo $m0->add($m1)->echoOut();
    echo $m2->add($m3)->echoOut();
    echo $m1->mul($m2)->echoOut();
    echo $m3->mul($m4)->echoOut();
    echo $sm1->getIdentity()->echoOut();
    echo $sm2->getIdentity()->echoOut();
    echo $m1->getTranspose()->echoOut();
    echo $m2->getTranspose()->echoOut();

}

if (!count(debug_backtrace())) {
    main();
    }
?>
