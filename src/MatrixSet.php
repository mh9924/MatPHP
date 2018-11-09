<?php
/*
 * @author    harrism
*/

namespace MatPHP;
require_once "Matrix.php";

class MatrixSet
{
    public $mxs = array();
    public $fitness = 0;

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

    public function findTotalDistance($roundTrip = false)
    {
        $totalDistance = 0;

        for ($m = 0; $m < sizeof($this->mxs) - 2; $m++)
            $totalDistance += $this->mxs[$m]->distance($this->mxs[$m + 1]);

        if ($roundTrip)
            $totalDistance += $this->mxs[$m]->distance($this->mxs[0]);

        return $totalDistance;
    }

    public function getFitness()
    {
        if ($this->fitness == 0)
            $this->fitness = 1/$this->findTotalDistance();

        return $this->fitness;
    }

    public static function createIndividual($cities)
    {

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