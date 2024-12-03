<?php

namespace Arig\MahalanobisDistance;

use Arig\MahalanobisDistance\Exceptions\NonSquareMatrixException;
use Arig\MahalanobisDistance\Exceptions\SingularMatrixException;
use InvalidArgumentException;

/**
 * @see https://www.machinelearningplus.com/statistics/mahalanobis-distance/
 */
class MahalanobisDistance
{
    /**
     * @param array<float|int> $x
     * @param array<array<float|int>> $data
     * @return float
     */
    public static function calculate(array $x, array $data): float
    {
        $covarianceMat = covariance_matrix($data);
        $meanVector = mean_vector($data);

        $xMinusMu = vector_sub($x, $meanVector);
        $inverseCovarianceMatrix = self::inverse($covarianceMat);
        $leftTerm = self::matrixMul($inverseCovarianceMatrix, $xMinusMu);

        return sqrt(dot($leftTerm, vector_transpose($xMinusMu)));
    }

    public static function inverse($matrix): array
    {
        $m = count($matrix);
        $n = count($matrix[0]);

        // Check if the matrix is square
        if ($m !== $n) {
            throw new NonSquareMatrixException("Matrix is not square. Cannot find inverse.");
        }

        $identity = identity_matrix($m);
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
                throw new SingularMatrixException("Matrix is singular. Cannot find inverse.");
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

    public static function matrixMul(array $matrixA, array $matrixB): array
    {
        // TODO: check if both matrices have the same amount of columns

        $res = [];

        foreach ($matrixA as $rowKey => $rowA) {
            $resRow = [];

            foreach (array_keys($matrixB[0]) as $colIdxB) {
                $resRow[$colIdxB] = dot($rowA, array_column($matrixB, $colIdxB));
            }

            $res[$rowKey] = $resRow;
        }

        return $res;
    }

    public static function vectorMatrixMul(array $vector, array $matrix): array
    {
        return array_map(fn(array $row): float => dot($vector, $row), $matrix);
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
