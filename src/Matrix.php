<?php
/*
 * @author    harrism
*/

namespace MatPHP;


class Matrix
{
    public $mx = array();
    private $size = 0;

    public function __construct($matrix)
    {
        if($this->isValidMatrix($matrix))
        {
            $this->mx = $matrix;
            $this->numrows = sizeof($this->mx);
            $this->numcols = sizeof($this->mx[0]);
            $this->size = $this->numrows * $this->numcols;
        }
        else
            die("Could not create object instance - Not a valid matrix.");
    }

    private function isValidMatrix($matrix)
    {
        if(!is_array($matrix) || !is_array($matrix[0]))
            return false;

        // More checking needed

        return true;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getNumRows()
    {
        return sizeof($this->mx);
    }

    public function getNumCols()
    {
        return sizeof($this->mx[0]);
    }

    public function getDimensions()
    {
        return $this->numrows . "x" . $this->numcols;
    }

    public function add($other)
    {
        return $this->performOperation($other, "+");
    }

    public function sub($other)
    {
        return $this->performOperation($other, "-");
    }

    public function mul($other)
    {
        return $this->performOperation($other, "*");
    }

    public function scl($other)
    {
        return $this->performOperation($other, "x");
    }

    private function performOperation($other, $operation)
    {
        if ($other instanceof Matrix)
        {
            if ($this->getDimensions() == $other->getDimensions() && $operation != "*")
                return $this->handleAdditive($other, $operation);
            elseif (sizeof($this->mx[0]) == sizeof($other->mx) && $operation == "*")
                return $this->handleMultiplicative($other);
        }
        elseif (is_numeric($other) && $operation == "x")
                return $this->handleScalar($other);

        echo "ERROR:";
        $this->echoOut();
        $other->echoOut();
        die("Can't possibly perform '{$operation}' operation on these matrices. Check that dimensions are correct.");
    }

    private function handleAdditive($other, $operation)
    {
        $x = 0;

        foreach ($this->mx as $row)
        {
            $r = array();
            $y = 0;

            foreach ($row as $element)
            {
                if ($operation == "+")
                    $r[] = $element + $other->mx[$x][$y];
                elseif ($operation == "-")
                    $r[] = $element - $other->mx[$x][$y];
                $y++;
            }

            $matrix[] = $r;
            $x++;
        }

        return new Matrix($matrix);
    }

    private function handleMultiplicative($other)
    {
        $matrix = [];

        for ($i = 0; $i < sizeof($this->mx); $i++)
            for ($j = 0; $j < sizeof($other->mx[0]); $j++)
                for ($k = 0; $k < sizeof($other->mx); $k++)
                    $matrix[$i][$j] += $this->mx[$i][$k] * $other->mx[$k][$j];

        return new Matrix($matrix);
    }

    private function handleScalar($other)
    {
        $matrix = [];

        for ($i=0; $i<$this->numrows; $i++)
        {
            $r = [];
            $y = 0;

            for ($j=0; $j<$this->numcols; $j++)
            {
                $r[] = $other * $this->mx[$i][$y];
                $y++;
            }

            $matrix[] = $r;

        }

        return new Matrix($matrix);
    }

    public function findIdentity()
    {
        $matrix = [];

        if ($this->numrows == $this->numcols)
        {
            for ($i = 0; $i < $this->numrows; $i++)
            {
                for ($j = 0; $j < $this->numcols; $j++)
                    $matrix[$i][$j] = 0;

                $matrix[$i][$i] = 1;
            }

            return new Matrix($matrix);
        }

        die("Matrix must be square to get identity.");
    }

    public function findTranspose()
    {
        /* Finds the transpose of the matrix.
        Returns a new matrix. */

        $matrix = [];

        for ($i = 0; $i < $this->numrows; $i++)
            for ($j = 0; $j < $this->numcols; $j++)
                $matrix[$j][$i] = $this->mx[$i][$j];

        return new Matrix($matrix);
    }


    public function findDeterminant()
    {
        /* Finds the determinant of the matrix.
        Note: Consequently converts the matrix to row-echelon form.
        Returns a numeric value.
        */

        if ($this->numcols != $this->numrows)
            die("Matrix must be square to find determinant.");

        $r = 0;

        for ($j = 0; $j < $this->numrows; $j++)
        {
            // Find pivot index
            $pivotIndex = $j;

            for ($p = $j+1; $p < $this->numrows; $p++)
                if (abs($this->mx[$p][$j]) > abs($this->mx[$pivotIndex][$j]))
                    $pivotIndex = $p;

            if ($this->mx[$pivotIndex][$j] == 0)
                return 0;

            if ($pivotIndex > $j) # Interchange rows
            {
                $temp = $this->mx[$pivotIndex];
                $this->mx[$pivotIndex] = $this->mx[$j];
                $this->mx[$j] = $temp;
                $r++;
            }

            for ($i = $j+1; $i < $this->numrows; $i++)
            {
                $frac = $this->mx[$i][$j] / $this->mx[$j][$j];
                $rowJ = $this->mx[$j];

                for ($rowJIndex = 0; $rowJIndex < sizeof($rowJ); $rowJIndex++)
                {
                    $rowJ[$rowJIndex] *= $frac;
                    $this->mx[$i][$rowJIndex] -= $rowJ[$rowJIndex];
                }
            }

            $det = pow(-1, $r);

            for ($rowNo = 0; $rowNo < $this->numrows; $rowNo++)
                $det *= $this->mx[$rowNo][$rowNo];

            return $det;
        }
    }

    public function findConditionNumber()
    {
        $matrix = new Matrix($this->mx);
        $inverseMatrix = new Matrix($this->mx);
        $inverseMatrix->invert();

        $matrix->normalize();
        $inverseMatrix->normalize();

        $maxRowSum = $matrix->findMaxRowSum();
        $inverseMaxRowSum = $inverseMatrix->findMaxRowSum();

        return $maxRowSum * $inverseMaxRowSum;
    }

    public function invert()
    {
        if ($this->numcols != $this->numrows)
            die("Matrix must be square to invert.");

        $e = 1;
        $identity = $this->findIdentity();
        $this->augment($identity);

        for ($j = 0; $j < $this->numrows; $j++)
        {
            // Find pivot index
            $pivotIndex = $j;

            for ($p = $j+1; $p < $this->numrows; $p++)
                if (abs($this->mx[$p][$j]) > abs($this->mx[$pivotIndex][$j]))
                    $pivotIndex = $p;

            if ($this->mx[$pivotIndex][$j] == 0)
                return 0;

            if ($pivotIndex > $j) # Interchange rows
            {
                $temp = $this->mx[$pivotIndex];
                $this->mx[$pivotIndex] = $this->mx[$j];
                $this->mx[$j] = $temp;
            }

            $this->pivotAugmented($j, $j);
        }

        foreach ($this->mx as &$row)
        {
            for ($i = 0; $i < $this->numcols / 2; $i++)
                array_shift($row);
        }
    }

    public function augment($other)
    {
        for ($i = 0; $i < $this->numrows; $i++)
        {
            for ($j = 0; $j < $other->numcols; $j++)
                $this->mx[$i][] = $other->mx[$i][$j];
            $this->numcols++;
        }
    }

    public function normalize()
    {
        for ($i = 0; $i < sizeof($this->mx); $i++)
            for ($j = 0; $j < sizeof($this->mx[0]); $j++)
                $this->mx[$i][$j] = abs($this->mx[$i][$j]);
    }

    private function pivotAugmented($a, $b)
    {
        for ($i = 0; $i < $this->numrows; $i++)
        {
            $factor = $this->mx[$i][$b] / $this->mx[$a][$b];

            for ($j = 0; $j < $this->numcols; $j++)
                if ($i != $a && $j != $b)
                    $this->mx[$i][$j] -= $factor * $this->mx[$a][$j];
        }

        for ($i = 0; $i < $this->numrows; $i++)
            if ($i != $a)
                $this->mx[$i][$b] = 0;

        for ($j = 0; $j < $this->numcols; $j++)
            if ($j != $b)
                $this->mx[$a][$j] /= $this->mx[$a][$b];

        $this->mx[$a][$b] = 1;
    }

    private function findMaxRowSum()
    {
        $max = 0;

        for ($i = 0; $i < sizeof($this->mx); $i++)
        {
            $sum = array_sum($this->mx[$i]);

            if ($sum > $max)
                $max = $sum;
        }

        return $max;
    }

    public function echoOut()
    {
        echo "\n";

        foreach($this->mx as $row)
        {
            echo "[";
            foreach($row as $element)
                echo " {$element} ";
            echo "]\n";
        }

        echo "\n";
    }

}
?>