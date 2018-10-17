<?php
/*
 * @author    harrism
*/

namespace MatPHP;

include "../src/MatrixSet.php";
include "../src/Classifier.php";

class MatrixTests
{
    public function test1(){

        /* Tests of basic matrix arithmetic and finding scalar, identity,
        and transpose matrix. */

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

        $m8 = new Matrix([[1, 2, -1, -4],
                        [2, 3, -1, -11],
                        [-2, 0, -3, 22]]);

        $sm1 = new Matrix([[1,4,8,2],
            [6,3,2,1],
            [5,3,9,2],
            [2,7,4,7]]);

        $sm2 = new Matrix([[6,2,5],
            [6,1,2],
            [4,9,3]]);

        echo "Addition:\n";
        $m0->add($m1)->echoOut();
        $m2->add($m3)->echoOut();

        echo "Subtraction:\n";
        $m0->sub($m1)->echoOut();
        $m2->sub($m3)->echoOut();

        echo "Multiplication:\n";
        $m1->mul($m2)->echoOut();
        $m3->mul($m4)->echoOut();

        echo "Scalar multiplication:\n";
        $m0->scl(5)->echoOut();
        $m1->scl(5)->echoOut();

        echo "Find identity matrix:\n";
        $sm1->findIdentity()->echoOut();
        $sm2->findIdentity()->echoOut();

        echo "Transpose matrix:\n";
        $m1->findTranspose()->echoOut();
        $m2->findTranspose()->echoOut();
    }

    public function test2()
    {
        /* Tests of inverting, finding determinant (converts to row-echelon consequently),
        and finding sum, mean, covariance of a set of vectors. */

        $m5 = new Matrix([[3],
            [5]]);

        $m6 = new Matrix([[7],
            [3]]);

        $m7 = new Matrix([[3],
            [8]]);

        $m8 = new Matrix([[9, 11, 18],
            [6, 9, 12],
            [4, 1, 5]]);

        $m8->echoOut();

        echo "^ Inverted:";

        $m8->invert();
        $m8->echoOut();

        $m8 = new Matrix([[1, 2],
            [4,3]]);

        $m8->echoOut();

        echo "^ Row echelon form / Determinant:";

        $det = $m8->findDeterminant();
        $m8->echoOut();
        echo $det;

        echo "Find sum, mean, covariance of a set of matrices:";

        $ms = new MatrixSet([$m5, $m6, $m7]);

        $ms->findSum()->echoOut();
        $ms->findMean()->echoOut();

        $covarianceMatrix = $ms->findCovariance();

        $covarianceMatrix->echoOut();

        $det = $covarianceMatrix->findDeterminant();
        $covarianceMatrix->echoOut();

        echo $det;
    }

    public function test3()
    {
        /* Test our classifier on sample data.
        Solve linear equations using Gauss-Jordan elimination. */

        $c = new Classifier();
        $vectorSets = $c->fromTextFile("data1.txt");
        $c->train($vectorSets[0], $vectorSets[1]);

        echo "Class 1, class 2 mean vectors:\n";

        $c->class1Mean->echoOut();
        $c->class2Mean->echoOut();

        echo "Class 1, class 2 covariance matrices:\n";

        $c->class1Covariance->echoOut();
        $c->class2Covariance->echoOut();

        echo "Class 1, class 2 determinants:\n";

        echo $c->class1Determinant . "\n";
        echo $c->class2Determinant . "\n";

        echo "Class 1, class 2 inverses:\n";

        $c->class1Inverse->echoOut();
        $c->class2Inverse->echoOut();

        $result1 = $c->classify([-1.8925273441455, 2.1188537162636]);
        $result2 = $c->classify([-0.32412683001818, -0.88570603142727]);

        $classification1 = $result1[0];
        $classification2 = $result2[0];

        echo "m1 was classed as Class " . $classification1;
        echo " because g1(...) = {$result1[1][0]} and g2(...) = {$result1[1][1]}";
        echo "\n";
        echo "m2 was classed as Class " . $classification2;
        echo " because g1(...) = {$result2[1][0]} and g2(...) = {$result2[1][1]}";

    }
}

$t = new MatrixTests();
// $t->test1();
// $t->test2();
$t->test3();
?>