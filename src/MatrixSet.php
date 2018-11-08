<?php
/*
 * @author    harrism
*/

namespace MatPHP;
require_once "Matrix.php";

class MatrixSet
{
    public $mxs = array();

    public function __construct($matrices = [])
    {
        foreach ($matrices as $matrix)
        {
            if ($matrix instanceof Matrix)
                $this->mxs[] = $matrix;
            else
                die("Not a valid set of matrix objects.");
        }
    }

    public function findSum()
    {
        $sum = $this->mxs[0];

        for ($m = 1; $m < sizeof($this->mxs); $m++)
            $sum = $sum->add($this->mxs[$m]);

        return $sum;
    }

    public function findMean()
    {
        $sum = $this->findSum();

        return $sum->scl(1/sizeof($this->mxs));
    }

    public function findCovariance()
    {
        if ($this->mxs[0]->getNumCols() != 1)
            die("Must be n by 1 vectors to find covariance.");

        $productSet = new MatrixSet();
        $meanMatrix = $this->findMean();

        foreach ($this->mxs as $mx)
        {
            $diffMatrix = $mx->sub($meanMatrix);
            $product = $diffMatrix->mul($diffMatrix->findTranspose());
            $productSet->mxs[] = $product;
        }

        return $productSet->findMean();
    }

    public static function vectorsFromTextFile($filename)
    {
        $matrixSet = [];

        $data = file_get_contents(__DIR__ . "/../sampledata/{$filename}");

        $lines = explode("\r", $data);

        for ($i = 0; $i < sizeof($lines); $i++)
        {
            $vectors = explode("\t", $lines[$i]);

            $matrix = [];

            for ($j = 0; $j < sizeof($vectors); $j++)
            {
                $matrix[] = [$vectors[$j]];
            }

            $matrixSet[] = new Matrix($matrix);
        }

        return new MatrixSet($matrixSet);
    }

}
?>