<?php

namespace Arig\MahalanobisDistance;

use Arig\MahalanobisDistance\Exceptions\NonSquareMatrixException;
use Arig\MahalanobisDistance\Exceptions\SingularMatrixException;
use InvalidArgumentException;

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

        $covarianceMat = covariance_matrix($data);
        $meanVector = self::meanVector($data);

        $xMinusMu = vector_sub($x, $meanVector);

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

    public static function forwardSubstitution(array $L, array $b): array
    {
        $m = count($b);
        $x = array_fill(0, $m, 0);

        for ($i = 0; $i < $m; $i++) {
            $x[$i] = ($b[$i] - dot(array_slice($L[$i], 0, $i), array_slice($x, 0, $i))) / $L[$i][$i];
        }

        return $x;
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
