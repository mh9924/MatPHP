<?php
/**
 * Created by PhpStorm.
 * User: xmatt
 * Date: 11/8/2018
 * Time: 9:36 PM
 */

namespace MatPHP;

ini_set('memory_limit', -1);

include "../src/MatrixSet.php";


class TSPTests
{

    public function test1()
    {
        // EXHAUSTIVE SEARCH
        /*
        $vectorSet = MatrixSet::vectorsFromTextFile("data3.txt");
        $solutions = $this->permutations($vectorSet->mxs);

        $permutationDistances = [];
        $i = 0;

        foreach ($solutions as $solution)
        {
            if ($i % 100000 == 0)
                echo $i . PHP_EOL;
            $i++;

            $solutionMatrixSet = new MatrixSet($solution);
            $permutationDistances[] = $solutionMatrixSet->findTotalDistance(true);
        }
        */

        // RANDOM SEARCH
        $vectorSet = MatrixSet::vectorsFromTextFile("data3.txt")->mxs;
        $permutationSolutions = [];
        $permutationDistances = [];

        for ($i = 0; $i < 1000000; $i++)
        {
            shuffle($vectorSet);
            $solutionMatrixSet = new MatrixSet($vectorSet);
            $permutationSolutions[] = $solutionMatrixSet;
            $permutationDistances[] = $solutionMatrixSet->findTotalDistance(true);
        }

        $max = max($permutationDistances);
        $min = min($permutationDistances);
        $avg = array_sum($permutationDistances) / count($permutationDistances);
        $sd = $this->standardDeviation($permutationDistances);

        $maxTrip = $permutationSolutions[array_search($max, $permutationDistances)];
        $minTrip = $permutationSolutions[array_search($min, $permutationDistances)];

        echo "Maximum distance: " . $max;

        echo " (" . $maxTrip->findTotalDistance(true) . ")";

        foreach ($maxTrip->mxs as $city)
            echo $city->echoOut();

        echo "Minimum distance: " . $min;

        echo " (" . $minTrip->findTotalDistance(true) . ")";

        foreach ($minTrip->mxs as $city)
            echo $city->echoOut();

        echo "Average distance: " . $avg . PHP_EOL;
        echo "Standard deviation of distance: " . $sd . PHP_EOL;

        $step_size = 0.1;

        foreach ($permutationDistances as $index => $value) {
            $data[$index] = round($value / $step_size) * $step_size;
        }

        // SIMULATED ANNEALING

        echo "\n\n";

        $vectorSet = MatrixSet::vectorsFromTextFile("data3.txt")->mxs;
        $annealingSolutions = [];
        $annealingDistances = [];

        for ($i = 0; $i < 50; $i++)
        {
            $temperature = 100000;
            $coolingRate = 0.005;

            shuffle($vectorSet);

            $currentSol = new MatrixSet($vectorSet);
            $bestSol = new MatrixSet($currentSol->mxs);

            while ($temperature > 1)
            {
                $newSol = new MatrixSet($currentSol->mxs);

                $randPos = rand(0, sizeof($newSol->mxs)-1);
                $randPos2 = rand(0, sizeof($newSol->mxs)-1);

                while ($randPos == $randPos2)
                    $randPos2 = rand(0, sizeof($newSol->mxs)-1);

                $swapCity1 = $newSol->mxs[$randPos];
                $swapCity2 = $newSol->mxs[$randPos2];

                $newSol->mxs[$randPos2] = $swapCity1;
                $newSol->mxs[$randPos] = $swapCity2;

                $currentDis = $currentSol->findTotalDistance(true);
                $neighborDis = $newSol->findTotalDistance(true);

                $randDec = mt_rand();

                if ($this->acceptanceP($currentDis, $neighborDis, $temperature) > $randDec)
                    $bestSol = $currentSol->mxs;

                $temperature *= 1 - $coolingRate;

            }

            $annealingSolutions[] = $bestSol;
            $annealingDistances[] = $bestSol->findTotalDistance(true);
        }

        $max = max($annealingDistances);
        $min = min($annealingDistances);
        $avg = array_sum($annealingDistances) / count($annealingDistances);
        $sd = $this->standardDeviation($annealingDistances);

        $maxTrip = $annealingSolutions[array_search($max, $annealingDistances)];
        $minTrip = $annealingSolutions[array_search($min, $annealingDistances)];

        echo "Maximum distance: " . $max;

        echo " (" . $maxTrip->findTotalDistance(true) . ")";

        foreach ($maxTrip->mxs as $city)
            echo $city->echoOut();

        echo "Minimum distance: " . $min;

        echo " (" . $minTrip->findTotalDistance(true) . ")";

        foreach ($minTrip->mxs as $city)
            echo $city->echoOut();

        echo "Average distance: " . $avg . PHP_EOL;
        echo "Standard deviation of distance: " . $sd . PHP_EOL;


        $file = fopen("random.txt", "w");
        foreach ($permutationDistances as $permutationDistance)
            fwrite($file, $permutationDistance . "\n");

        $file = fopen("annealing.txt", "w");
        foreach ($annealingDistances as $annealingDistance)
            fwrite($file, $annealingDistance . "\n");
    }

    private function acceptanceP($currentDis, $newDis, $temperature)
    {
        if ($newDis < $currentDis)
            return 1.0;

        return exp(($currentDis - $newDis) / $temperature);
    }

    private function standardDeviation($arr)
    {
        $numElements = count($arr);

        $variance = 0.0;

        $average = array_sum($arr)/$numElements;

        foreach($arr as $i)
            $variance += pow(($i - $average), 2);

        return (float)sqrt($variance/$numElements);
    }

}

$t = new TSPTests();
$t->test1();