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
        $covarianceMat = covariance_matrix($data);
        $meanVector = mean_vector($data);

        $L = self::cholesky($covarianceMat);

        $xMinusMu = vector_sub($x, $meanVector);

        $z = self::forwardSubstitution($L, $xMinusMu);

        return sqrt(dot($z, $z));
    }

    public static function cholesky(array $A): array
    {
        $m = count($A);
        $l = array_fill(0, $m, array_fill(0, $m, 0));

        for ($i = 0; $i < $m; $i++) {
            for ($k = 0; $k < ($i + 1); $k++) {
                $sum = 0;
                for ($j = 0; $j < $k; $j++) {
                    $sum += $l[$i][$j] * $l[$k][$j];
                }

                $l[$i][$k] = ($i == $k) ? sqrt($A[$i][$i] - $sum) :
                    (1.0 / $l[$k][$k] * ($A[$i][$k] - $sum));
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
}
