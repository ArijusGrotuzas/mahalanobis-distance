<?php

namespace Arig\MahalanobisDistance;

use InvalidArgumentException;

/**
 *
 */
class MahalanobisDistance
{
    /**
     * @param array<float|int> $point
     * @param array<float|int> $data
     * @return float
     */
    public static function calculate(array $point, array $data): float
    {
        $covarianceMat = MahalanobisDistance::covarianceMatrix($data);
        $meanVector = self::meanVector($data);

        // Calculate the difference between the point and the mean
        $pointMinusMean = array_map(function (float $x, float $y): array {
            return [$x - $y];
        }, $point, $meanVector);

        $pointMinusMeanT = array_column($pointMinusMean, 0);

        $inverseCovariance = self::findInverse($covarianceMat);
        $leftTerm = self::matrixMul($inverseCovariance, $pointMinusMean);

        return sqrt(self::dot($pointMinusMeanT, array_column($leftTerm, 0)));
    }

    public static function covarianceMatrix(array $data): array
    {
        $keys = array_keys($data);

        return array_map(function (array $dataX, int $keyX) use ($data, $keys): array {
            $meanX = array_sum($dataX) / count($dataX);

            return array_map(function (array $dataY, int $keyY) use ($keyX, $dataX, $meanX): float {
                $meanY = array_sum($dataY) / count($dataY);

                if ($keyX === $keyY) {
                    return self::variance($dataX, $meanX);
                }

                return self::covariance($dataX, $dataY, $meanX, $meanY);
            }, $data, $keys);
        }, $data, $keys);
    }

    public static function variance(array $data, float $mean): float
    {
        return array_sum(array_map(fn(float $var): float => pow($var - $mean, 2), $data)) / count($data);
    }

    public static function covariance(array $dataX, array $dataY, float $meanX, float $meanY): float
    {
        return array_sum(array_map(fn(float $varX, float $varY): float => ($varX - $meanX) * ($varY - $meanY), $dataX, $dataY)) / count($dataX);
    }

    public static function meanVector($data): array
    {
        return array_map(fn(array $varData): float => array_sum($varData) / count($varData), $data);
    }

    public static function findInverse($matrix): array
    {
        $m = count($matrix);
        $n = count($matrix[0]);

        // Check if the matrix is square
        if ($m !== $n) {
            throw new InvalidArgumentException("Matrix is not square. Cannot find inverse.");
        }

        $identity = self::createIdentityMatrix($m);
        $augmentedMatrix = array_merge($matrix, $identity);

        $h = 0;
        $k = 0;

        while ($h < $m && $k < $n) {
            // Find the pivot row
            $iMax = $h;
            for ($i = $h + 1; $i < $m; $i++) {
                if (abs($augmentedMatrix[$i][$k]) > abs($augmentedMatrix[$iMax][$k])) {
                    $iMax = $i;
                }
            }

            // If pivot is zero, no unique solution exists
            if ($augmentedMatrix[$iMax][$k] == 0) {
                throw new InvalidArgumentException("Matrix is singular. Cannot find inverse.");
            }

            // Swap rows
            list($augmentedMatrix[$h], $augmentedMatrix[$iMax]) = array($augmentedMatrix[$iMax], $augmentedMatrix[$h]);

            // Normalize pivot row
            $pivot = $augmentedMatrix[$h][$k];
            for ($j = 0; $j < $n * 2; $j++) {
                $augmentedMatrix[$h][$j] /= $pivot;
            }

            // Eliminate other rows
            for ($i = 0; $i < $m; $i++) {
                if ($i !== $h) {
                    $f = $augmentedMatrix[$i][$k];
                    for ($j = 0; $j < $n * 2; $j++) {
                        $augmentedMatrix[$i][$j] -= $augmentedMatrix[$h][$j] * $f;
                    }
                }
            }

            // Move to the next pivot
            $h++;
            $k++;
        }

        // Extract the inverse matrix
        $inverse = array_map(function ($row) use ($n) {
            return array_slice($row, $n);
        }, $augmentedMatrix);

        return $inverse;
    }

    public static function createIdentityMatrix($size): array
    {
        $identity = array_fill(0, $size, array_fill(0, $size * 2, 0));

        for ($i = 0; $i < $size; $i++) {
            $identity[$i][$i + $size] = 1;
        }

        return $identity;
    }

    public static function matrixMul(array $matrixA, array $matrixB): array
    {
        // TODO: check if both matrices have the same amount of columns

        $res = [];

        foreach ($matrixA as $rowKey => $rowA) {
            $resRow = [];

            foreach (array_keys($matrixB[0]) as $colIdxB) {
                $resRow[$colIdxB] = self::dot($rowA, array_column($matrixB, $colIdxB));
            }

            $res[$rowKey] = $resRow;
        }

        return $res;
    }

    public static function dot(array $vectorA, array $vectorB): float
    {
        return array_sum(array_map(fn(float $valA, float $valB) => $valA * $valB, $vectorA, $vectorB));
    }

    public static function vectorMatrixMul(array $vector, array $matrix): array
    {
        return array_map(fn(array $row): float => self::dot($vector, $row), $matrix);
    }

    public static function inverseMatrix(array $matrix, array $data): array
    {
        $n = count($matrix);
        $originMat = $matrix;

        // Augment the matrix with the identity matrix
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $matrix[$i][] = ($i == $j) ? 1 : 0;
            }
        }

        // Perform Gauss-Jordan elimination
        for ($i = 0; $i < $n; $i++) {
            $pivot = $matrix[$i][$i];

            if ($pivot === 0.0) {
                throw new InvalidArgumentException("Matrix is not invertible");
            }

            // Scale the row to make the pivot 1
            for ($j = 0; $j < 2 * $n; $j++) {
                $matrix[$i][$j] /= $pivot;
            }

            // Eliminate other rows
            for ($k = 0; $k < $n; $k++) {
                if ($k != $i) {
                    $factor = $matrix[$k][$i];
                    for ($j = 0; $j < 2 * $n; $j++) {
                        $matrix[$k][$j] -= $factor * $matrix[$i][$j];
                    }
                }
            }
        }

        // Extract the inverse matrix from the augmented matrix
        $inverseMatrix = [];
        for ($i = 0; $i < $n; $i++) {
            $inverseMatrix[] = array_slice($matrix[$i], $n);
        }

        return $inverseMatrix;
    }
}
