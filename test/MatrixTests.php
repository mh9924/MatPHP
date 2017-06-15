<?php
/*
 * @author    harrism
*/

namespace MatPHP;

include "../src/Matrices.php";

class MatrixTests
{
    public function main(){

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

        $m5 = new Matrix([[3, 5],
            [8, 2],
            [1, 6],
            [5, 2]]);

        $m6 = new Matrix([[7, 3],
            [2, 5],
            [6, 8],
            [9, 0]]);

        $m7 = new Matrix([[3, 8],
            [1, 8],
            [0, 5],
            [2, 3]]);

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

        echo "Find sum/mean of a set of matrices:";
        $ms = new Matrices([$m5, $m6, $m7]);
        $ms->findSum()->echoOut();
        $ms->findMean()->echoOut();

        $sm1->findRowEchelonForm()->echoOut();
        echo($sm1->findDeterminant());
    }
}

$t = new MatrixTests();
$t->main();
?>