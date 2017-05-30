<?php

namespace MathTools;


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
            echo("Could not create object instance - Not a valid matrix.");
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

    public function add($other){
        if(gettype($other) == "integer" || gettype($other) == "double"){
            // Scalar

        }
        elseif($other instanceof Matrix){
            if($other->getDimensions() == $this->getDimensions()){
                $x = 0;
                $y = 0;
                $matrix = array();
                foreach($this->mx as $row){
                    $r = array();
                    foreach($row as $element) {
                        array_push($r, $element + $other->mx[$x][$y]);
                        $y++;
                    }
                    array_push($matrix, $r);
                    $y = 0;
                    $x++;
                }
                return new Matrix($matrix);
            }
        }
    }

    public function echoOut(){
        foreach($this->mx as $row) {
            echo "|";
            foreach($row as $element) {
                echo " {$element} ";
            }
            echo "|\n";
        }
    }
}
function main(){
    $m1 = new Matrix([[5,2],
        [4,9],
        [10,3]]);
    $m2 = new Matrix([[5,2],
        [4,9],
        [10,3]]);

    echo $m1->add($m2)->echoOut();
}

main();
?>