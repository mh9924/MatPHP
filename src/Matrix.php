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

    public function distance($other)
    {
        if ((sizeof($this->mx) != 2 && sizeof($this->mx) != 3) || sizeof($this->mx[0]) != 1)
            die("First matrix must be 2d or 3d vector to find distance.");

        if ((sizeof($other->mx) != 2 && sizeof($other->mx) != 3) || sizeof($other->mx[0]) != 1)
            die("Second matrix must be 2d or 3d vector to find distance.");


        if (sizeof($this->mx) != sizeof($other->mx))
            die("Cannot find distance between a 2d vector and 3d vector. Both must be 2d OR 3d.");

        if (sizeof($this->mx) == 2)
            return sqrt(($other->mx[0][0] - $this->mx[0][0])**2 + ($other->mx[1][0] - $this->mx[1][0])**2);
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

    public function findTrace()
    {
        if ($this->numrows == $this->numcols) {
            $sum = 0;

            for ($i = 0; $i < $this->numrows; $i++)
                $sum += $this->mx[$i][$i];

            return $sum;
        }

        die("Matrix must be square to get trace.");
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

    public function findCharacteristicEquation()
    {
        $matrix = new Matrix($this->mx);
        $negTrace = -($matrix->findTrace());

        $characteristicEquation = "";

        echo "\n";
        $characteristicEquation .= "x^" . sizeof($this->mx[0]) . " + " . $negTrace . "x^" . (sizeof($this->mx[0])-1) . " ";

        for ($k = sizeof($matrix->mx)-1; $k >= 1; $k--)
        {
            $identity = $matrix->findIdentity();

            for ($i = 0; $i < sizeof($matrix->mx); $i++)
                $identity->mx[$i][$i] *= $negTrace;

            $matrixPlusIdentity = $matrix->add($identity);
            $matrix = $this->mul($matrixPlusIdentity);

            $negTrace = -($matrix->findTrace() / (sizeof($this->mx) - $k + 1));

            $characteristicEquation .= " + " . $negTrace . (($k - 1 == 0 ? "" : "x^" . ($k - 1))) . " ";
        }

        return $characteristicEquation;
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

    public function findLargestEigenvalue()
    {
        $matrix = new Matrix($this->mx);

        $epsilon = 1.0e-15;
        $m = 50;
        $i = 0;

        $vector = [];

        for ($j = 0; $j < sizeof($matrix->mx); $j++)
            $vector[$j][0] = 2;

        $y = new Matrix($vector);
        $x = $matrix->mul($y);

        $sum = 0;

        do {
            for ($j = 0; $j < sizeof($matrix->mx); $j++)
                $sum += $vector[$j][0] ** 2;

            $normalized = sqrt($sum);

            for ($j = 0; $j < sizeof($matrix->mx); $j++)
                $x->mx[$j][0] /= $normalized;

            $y = $x;

            $x = $matrix->mul($y);
            $yt = $y->findTranspose();

            $numerator = $yt->mul($x);
            $denominator = $yt->mul($y);

            $u = $numerator->mx[0][0] / $denominator->mx[0][0];

            for ($j = 0; $j < sizeof($matrix->mx); $j++)
                $y->mx[$j][0] *= $u;

            $r = $y->sub($x);

            $sum2 = 0;

            for ($j = 0; $j < sizeof($matrix->mx); $j++)
                $sum2 += $r->mx[$j][0] ** 2;

            $normalized2 = sqrt($sum2);
            $i++;
        } while ($normalized2 > $epsilon && $i < $m);

        return $u;
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