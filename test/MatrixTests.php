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

        /* Evaluate the discriminant for the
        sample data that was used to train. */

        echo "\n\n";
        echo "Class 1 classifications (" . sizeof($vectorSets[0]) . " vectors):\n";

        for ($i = 0; $i < sizeof($vectorSets[0]); $i++)
        {
            $classification = $c->classify($vectorSets[0][$i]);
            if ($classification[0] == 2)
            {
                $vector = $vectorSets[0][$i];
                echo "[{$vector[0]}, {$vector[1]}] was classed incorrectly.\n";
                echo "Value of g1: {$classification[1][0]}\n";
                echo "Value of g2: {$classification[1][1]}\n";
                echo "\n";
            }
        }

        echo "Class 2 classifications (" . sizeof($vectorSets[1]) . " vectors):\n";

        for ($i = 0; $i < sizeof($vectorSets[1]); $i++)
        {
            $classification = $c->classify($vectorSets[1][$i]);
            if ($classification[0] == 1)
            {
                $vector = $vectorSets[1][$i];
                echo "[{$vector[0]}, {$vector[1]}] was classed incorrectly.\n";
                echo "Value of g1: {$classification[1][0]}\n";
                echo "Value of g2: {$classification[1][1]}\n";
                echo "\n";
            }
        }

        echo "\n\n";
        echo "Tests for boundary plotting with epsilon error = 0.1:\n";

        $testVector = [-1.2, 0.96];
        $testClassification = $c->classify($testVector);
        echo "g1: " . $testClassification[1][0] . "\n";
        echo "g2: " . $testClassification[1][1] . "\n";
        echo "|g1 - g2|: " . abs($testClassification[1][0] - $testClassification[1][1]) ."\n";

        echo "\n\n";
        echo "Solving a linear system\n";

        $systemA = [
            [0, 1, 3, -1, 1, 0, -1, -1],
            [5, 0, 2, 0, -1, 3, 1, 1],
            [2, -2, 2, -1, -1, 2, 3, 1],
            [1, 1, 0, 3, 2, 1, -1, 0],
            [4, 1, 2, 3, -2, 2, 2, 1],
            [-1, -3, -2, 2, 0, 2, 4, 1],
            [3, 5, -1, 1, 1, 3, 0, -2],
            [1, 0, 1, 1, 0, 2, 2, 1]
        ];

        $systemB = [
            [1],
            [2],
            [2],
            [-2],
            [1],
            [7],
            [14],
            [6]
        ];

        $systemAMatrix = new Matrix($systemA);
        $systemBMatrix = new Matrix($systemB);

        $systemAMatrix->invert();

        echo "(first find inverse of A):\n";
        $systemAMatrix->echoOut();

        echo "Multiply by nx1 RHS to get solution:\n";

        $systemXMatrix = $systemAMatrix->mul($systemBMatrix);
        $systemXMatrix->echoOut();

        echo "Find condition number:\n";

        $systemAMatrix = new Matrix($systemA);
        echo $systemAMatrix->findConditionNumber();

        $systemAMatrix = new Matrix($systemA);
        echo $systemAMatrix->findDeterminant();

    }

    public function test4()
    {

        $matrixSet = MatrixSet::vectorsFromTextFile("data2.txt");

        echo "Mean of vectors: ";

        $matrixSet->findMean()->echoOut();

        echo "Covariance of vectors: ";

        $covariance = $matrixSet->findCovariance();
        $covariance->echoOut();

        echo "Trace of covariance matrix: ";

        $trace = $covariance->findTrace();
        echo $trace;

        echo "\n";
        echo "Determinant of covariance matrix: ";

        $det = $covariance->findDeterminant();
        echo $det;

        $covariance = $matrixSet->findCovariance();

        echo "\n";
        echo "Eigenvalues of covariance matrix: ";

        $e1 = $trace / 2 + sqrt($trace**2 / 4 - $det);

        $e2 = $trace / 2 - sqrt($trace**2 / 4 - $det);

        echo "{$e1} {$e2}";

        echo "\n";
        echo "Eigenvectors: ";

        $e3x = 1 / sqrt(1 + (($e1 - $covariance->mx[0][0]) / $covariance->mx[0][1])**2 );
        $e3y = (($e1 - $covariance->mx[0][0]) / $covariance->mx[0][1]) / sqrt(1 + (($e1 - $covariance->mx[0][0]) / $covariance->mx[0][1])**2);

        $e4x = 1 / sqrt(1 + (($e2 - $covariance->mx[0][0]) / $covariance->mx[0][1])**2 );
        $e4y = (($e2 - $covariance->mx[0][0]) / $covariance->mx[0][1]) / sqrt(1 + (($e2 - $covariance->mx[0][0]) / $covariance->mx[0][1])**2);


        echo "\n";
        echo "{$e3x} {$e3y}";
        echo "\n\n";
        echo "{$e4x} {$e4y}";

        $px = new Matrix([
            [0, 0, 0, 0, 728/3],
            [1, 0, 0, 0, -758/3],
            [0, 1, 0, 0, -190],
            [0, 0, 1, 0, 161/6],
            [0, 0, 0, 1, 73/6]]);

        $px->findCharacteristicCoefficients();
        $px->echoOut();


    }
}

$t = new MatrixTests();
// $t->test1();
// $t->test2();
// $t->test3();
$t->test4();
?>