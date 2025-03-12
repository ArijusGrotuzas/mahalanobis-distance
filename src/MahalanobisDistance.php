<?php

namespace Arig\MahalanobisDistance;

use Arig\MahalanobisDistance\Exceptions\InvalidDatasetSizeException;
use Arig\MahalanobisDistance\Exceptions\NonSquareMatrixException;
use Arig\MahalanobisDistance\Exceptions\UnequalVectorException;
use InvalidArgumentException;

/**
 * The Mahalanobis distance is a measure of the distance between a point P and a distribution D.
 * The class computes the mahalanobis distance by finding the product between inverse covariance matrix and the
 * difference between the point and the mean, by using Cholesky decomposition and forward substitution.
 *
 * @see https://www.machinelearningplus.com/statistics/mahalanobis-distance/
 * @see https://rosettacode.org/wiki/Cholesky_decomposition
 */
class MahalanobisDistance
{
    /**
     * @param array<float|int> $x
     * @param array<array<float|int>> $data
     * @return float
     */
    public static function mahalanobis(array $x, array $data): float
    {
        $covarianceMat = self::covarianceMatrix($data);
        $meanVector = self::meanVector($data);

        $xMinusMu = self::vectorSub($x, $meanVector);

        // TODO: Add input validation for Cholesky decomposition
        // TODO: Add input validation for Forward substitution
        $L = self::cholesky($covarianceMat);
        $z = self::forwardSubstitution($L, $xMinusMu);

        return sqrt(self::vectorDot($z, $z));
    }

    /**
     * Cholesky decomposition computed using the Cholesky-Banachiewicz algorithm.
     *
     * @param array<array<float|int>> $matrix
     * @return array<array<float>>
     * @see https://en.wikipedia.org/wiki/Cholesky_decomposition#The_Cholesky%E2%80%93Banachiewicz_and_Cholesky%E2%80%93Crout_algorithms
     */
    public static function cholesky(array $matrix): array
    {
        // TODO: Add additional input validation
        self::isSquareMatrix($matrix);

        $size = count($matrix);
        $l = array_fill(0, $size, array_fill(0, $size, 0.0));

        for ($i = 0; $i < $size; $i++) {
            for ($j = 0; $j < ($i + 1); $j++) {
                $sum = 0;

                for ($k = 0; $k < $j; $k++) {
                    $sum += $l[$i][$k] * $l[$j][$k];
                }

                $l[$i][$j] = ($i == $j) ? round(sqrt($matrix[$i][$i] - $sum), 8) : round(1.0 / $l[$j][$j] * ($matrix[$i][$j] - $sum), 8);
            }
        }

        return $l;
    }

    /**
     * Solve the equation Ax = b for x, assuming A is a triangular matrix.
     *
     * @param array<array<float|int>> $a
     * @param array<float|int> $x
     * @return array<float|int>
     */
    public static function forwardSubstitution(array $a, array $x): array
    {
        $m = count($x);
        $b = array_fill(0, $m, 0);

        for ($i = 0; $i < $m; $i++) {
            $b[$i] = ($x[$i] - self::vectorDot(array_slice($a[$i], 0, $i), array_slice($b, 0, $i))) / $a[$i][$i];
        }

        return $b;
    }

    /**
     * @param array<array<float|int>> $data
     * @return array<array<float>>
     */
    private static function covarianceMatrix(array $data): array
    {
        self::subArraysOfSameLength($data);

        $size = count($data);
        $keys = array_keys($data);

        $emptyCov = array_fill(0, $size, array_fill(0, $size, 0));

        return array_map(function (array $_, string $varA) use ($data, $keys): array {
            return array_map(function (int $_, string $varB) use ($data, $varA): float {

                if ($varA === $varB) {
                    return self::variance($data[$varA]);
                }

                return self::covariance($data[$varA], $data[$varB]);
            }, $_, $keys);
        }, $emptyCov, $keys);
    }

    /**
     * @param array<float|int> $data
     * @param bool $sample
     * @return float
     */
    private static function variance(array $data, bool $sample = false): float
    {
        // TODO: Add sample variance calculation

        $n = count($data);
        $mean = array_sum($data) / $n;

        return round(array_sum(array_map(fn(float|int $element): float|int => pow($element - $mean, 2), $data)) / ($n - 1), 8);
    }

    /**
     * @param array<float|int> $dataX
     * @param array<float|int> $dataY
     * @return float
     */
    private static function covariance(array $dataX, array $dataY): float
    {
        self::twoVectorsEqualLength($dataX, $dataY);

        $nX = count($dataX);
        $nY = count($dataY);

        $meanX = array_sum($dataX) / $nX;
        $meanY = array_sum($dataY) / $nY;

        return round(array_sum(array_map(fn(float|int $x, float|int $y): float|int => ($x - $meanX) * ($y - $meanY), $dataX, $dataY)) / ($nX - 1), 8);
    }

    /**
     * @param array<array<float|int>> $data
     * @return array
     */
    private static function meanVector(array $data): array
    {
        self::subArraysOfSameLength($data);

        return array_map(fn(array $variable): float => array_sum($variable) / count($variable), $data);
    }

    /**
     * @param array<float|int> $vectorA
     * @param array<float|int> $vectorB
     * @return array<float|int>
     */
    private static function vectorSub(array $vectorA, array $vectorB): array
    {
        self::twoVectorsEqualLength($vectorA, $vectorB);

        return array_map(function (float|int $a, float|int $b): float|int {
            return $a - $b;
        }, $vectorA, $vectorB);
    }

    /**
     * @param array<float|int> $vectorA
     * @param array<float|int> $vectorB
     * @return float|int
     */
    private static function vectorDot(array $vectorA, array $vectorB): float|int
    {
        self::twoVectorsEqualLength($vectorA, $vectorB);

        return array_sum(array_map(fn(float|int $a, float|int $b): float|int => $a * $b, $vectorA, $vectorB));
    }

    /**
     * @param array<float|int> $vectorA
     * @param array<float|int> $vectorB
     * @return void
     * @throws UnequalVectorException
     */
    private static function twoVectorsEqualLength(array $vectorA, array $vectorB): void
    {
        if (count($vectorA) !== count($vectorB)) {
            throw new UnequalVectorException("The number of elements in the two arrays must be equal.");
        }
    }

    /**
     * @param array<array<float|int>> $data
     * @return void
     * @throws InvalidDatasetSizeException
     */
    private static function subArraysOfSameLength(array $data): void
    {
        $firstSubArrayLength = count($data[0]);

        foreach ($data as $subArray) {
            if (count($subArray) !== $firstSubArrayLength) {
                throw new InvalidDatasetSizeException("All sub-arrays must have the same number of columns.");
            }
        }
    }

    /**
     * @param array<array<float|int>> $matrix
     * @throws InvalidArgumentException
     * @return void
     */
    private static function isSquareMatrix(array $matrix): void
    {
        $size = count($matrix);

        foreach ($matrix as $row) {
            if (count($row) !== $size) {
                throw new NonSquareMatrixException("The matrix is not square.");
            }
        }
    }
}
