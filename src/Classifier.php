<?php

namespace MatPHP;


class Classifier
{

    private $class1Covariance;
    private $class1Determinant;
    private $class1Inverse;
    private $class1Mean;

    private $class2Covariance;
    private $class2Determinant;
    private $class2Inverse;
    private $class2Mean;


    public function classify(array $vector): int
    {

    }

    public function train(array $class1Vectors, array $class2Vectors): void
    {
        $class1Matrices = [];
        $class2Matrices = [];

        foreach ($class1Vectors as $class1Vector)
            $class1Matrices[] = new Matrix([$class1Vector]);

        foreach ($class2Vectors as $class2Vector)
            $class2Matrices[] = new Matrix([$class2Vector]);

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



    }

}