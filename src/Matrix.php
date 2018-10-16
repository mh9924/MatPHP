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
        return $this->numrows;
    }

    public function getNumCols()
    {
        return $this->numcols;
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
            elseif ($this->numcols == $other->numrows && $operation == "*")
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
        foreach ($this->mx as $row)
        {
            $r = [];

            for ($i=0; $i<$other->numcols; $i++)
            {
                $sum = 0;
                $x = 0;

                foreach ($row as $element)
                {
                    $dot = $element * $other->mx[$x][$i];
                    $sum = $sum + $dot;
                    $x++;
                }

                $r[] = $sum;
            }

            $matrix[] = $r;
        }

        return new Matrix($matrix);
    }

    private function handleScalar($other)
    {
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
        for ($i = 0; $i < $this->numrows; $i++)
        {
            for ($j = 0; $j < $this->numcols; $j++)
                $matrix[$j][$i] = $this->mx[$i][$j];
        }

        return new Matrix($matrix);
    }

    public function findDeterminant()
    {
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
                $det = $det * $this->mx[$rowNo][$rowNo];

            return $det;
        }
    }

    public function invert()
    {
        if ($this->numcols != $this->numrows)
            die("Matrix must be square to invert.");

        $e = 1;
        $identity = $this->findIdentity();

        for ($j = 0; $j < $this->numrows; $j++)
        {

        }
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