<?php

namespace Arig\MahalanobisDistance;

use Arig\MahalanobisDistance\Exceptions\NonSquareMatrixException;
use Arig\MahalanobisDistance\Exceptions\SingularMatrixException;
use InvalidArgumentException;

// TODO: Provide doc strings for functions

/**
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
    public static function calculate(array $x, array $data): float
    {
        // TODO: Provide input validation
        // TODO: Make comments about the computation
        // TODO: Input validation for covariance matrix
        $covarianceMat = covariance_matrix($data);
        $meanVector = self::meanVector($data);

        $xMinusMu = vector_sub($x, $meanVector);

        // TODO: Input validation for Cholesky decomposition
        // TODO: Input validation for forward substitution
        $L = self::cholesky($covarianceMat);
        $z = self::forwardSubstitution($L, $xMinusMu);

        return sqrt(dot($z, $z));
    }

    /**
     * @param array<array<float|int>> $matrix
     * @return array<array<float>>
     */
    public static function cholesky(array $matrix): array
    {
        $size = count($matrix);
        $l = array_fill(0, $size, array_fill(0, $size, 0.0));

        for ($i = 0; $i < $size; $i++) {
            for ($k = 0; $k < ($i + 1); $k++) {
                $sum = 0;
                for ($j = 0; $j < $k; $j++) {
                    $sum += $l[$i][$j] * $l[$k][$j];
                }

                $l[$i][$k] = ($i == $k) ? round(sqrt($matrix[$i][$i] - $sum), 8) : round(1.0 / $l[$k][$k] * ($matrix[$i][$k] - $sum), 8);
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
            $b[$i] = ($x[$i] - dot(array_slice($a[$i], 0, $i), array_slice($b, 0, $i))) / $a[$i][$i];
        }

        return $b;
    }

    /**
     * @param array<array<float|int>> $data
     * @return array
     */
    private static function meanVector(array $data): array
    {
        return array_map(fn (array $variable): float => array_sum($variable) / count($variable), $data);
    }
}
