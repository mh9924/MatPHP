<?php
/*
 * @author    harrism
*/

namespace MatPHP;


class Matrix
{
    private $mx = array();
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

    public function findIdentity(){
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

    public function findTranspose(){
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

    public function scl($other){
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
        $x = $y = 0;
        foreach ($this->mx as $row) {
            $r = array();
            foreach ($row as $element) {
                $r[] = $other * $this->mx[$x][$y];
                $y++;
            }
            $matrix[] = $r;
            $y = 0;
            $x++;
        }
        return new Matrix($matrix);
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
?>