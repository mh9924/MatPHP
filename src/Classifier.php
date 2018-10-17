<?php

namespace MatPHP;


class Classifier
{

    public $class1Covariance;
    public $class1Determinant;
    public $class1Inverse;
    public $class1Mean;

    public $class2Covariance;
    public $class2Determinant;
    public $class2Inverse;
    public $class2Mean;

    private $ready;


    public function classify(array $vector)
    {
        if (!$this->ready)
        {
            print("Please train on datasets first.");
            return;
        }

        $vector = new Matrix([[$vector[0]],
            [$vector[1]]]);

        /* Find discriminant value for first class. */
        $meanDifference = $vector->sub($this->class1Mean);


        $meanDifferenceTranspose = $meanDifference->findTranspose();
        $scaledTranspose = $meanDifferenceTranspose->scl(-0.5);

        $scaledTransposeTimesInverse = $scaledTranspose->mul($this->class1Inverse);

        $firstFactor = $scaledTransposeTimesInverse->mul($meanDifference);
        $secondFactor = 0.5 * log($this->class1Determinant);

        $discriminant1 = $firstFactor->mx[0][0] - $secondFactor;

        /* Find discriminant value for second class. */
        $meanDifference = $vector->sub($this->class2Mean);


        $meanDifferenceTranspose = $meanDifference->findTranspose();
        $scaledTranspose = $meanDifferenceTranspose->scl(-0.5);

        $scaledTransposeTimesInverse = $scaledTranspose->mul($this->class2Inverse);

        $firstFactor = $scaledTransposeTimesInverse->mul($meanDifference);
        $secondFactor = 0.5 * log($this->class2Determinant);

        $discriminant2 = $firstFactor->mx[0][0] - $secondFactor;

        if ($discriminant1 > $discriminant2)
            return [1, [$discriminant1, $discriminant2]];

        if ($discriminant2 > $discriminant1)
            return [2, [$discriminant1, $discriminant2]];
    }

    public function train(array $class1Vectors, array $class2Vectors)
    {
        $class1Matrices = [];
        $class2Matrices = [];

        foreach ($class1Vectors as $class1Vector)
            $class1Matrices[] = new Matrix([[$class1Vector[0]],
                [$class1Vector[1]]]);

        foreach ($class2Vectors as $class2Vector)
            $class2Matrices[] = new Matrix([[$class2Vector[0]],
                [$class2Vector[1]]]);

        $class1 = new MatrixSet($class1Matrices);
        $class2 = new MatrixSet($class2Matrices);

        $this->class1Mean = $class1->findMean();
        $this->class2Mean = $class2->findMean();

        $this->class1Covariance = $class1->findCovariance();
        $this->class2Covariance = $class2->findCovariance();

        $covariance1 = clone $this->class1Covariance;
        $this->class1Determinant = $covariance1->findDeterminant();

        $covariance2 = clone $this->class2Covariance;
        $this->class2Determinant = $covariance2->findDeterminant();

        $covariance3 = clone $this->class1Covariance;
        $covariance3->invert();
        $this->class1Inverse = $covariance3;

        $covariance4 = clone $this->class2Covariance;
        $covariance4->invert();
        $this->class2Inverse = $covariance4;

        $this->ready = true;

    }

    public function fromTextFile($filename)
    {
        $class1Vectors = [];
        $class2Vectors = [];

        $data = file_get_contents(__DIR__ . "/../sampledata/{$filename}");

        $lines = explode("\r", $data);

        for ($i = 0; $i < sizeof($lines); $i++)
        {
            $vectors = explode("\t", $lines[$i]);
            $class1Vectors[] = [$vectors[0], $vectors[1]];
            $class2Vectors[] = [$vectors[2], $vectors[3]];
        }

        return [$class1Vectors, $class2Vectors];
    }

}