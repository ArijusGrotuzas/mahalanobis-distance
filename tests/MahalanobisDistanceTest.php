<?php

use PHPUnit\Framework\TestCase;
use Arig\MahalanobisDistance\MahalanobisDistance;

final class MahalanobisDistanceTest extends TestCase
{
    public function testCalculate(): void
    {
        $point = [3.5, 4.5];
        $data = [
            [2, 3, 4, 5],
            [3, 4, 5, 6]
        ];

        $this->assertEquals(
            0.0,
            MahalanobisDistance::calculate($point, $data)
        );
    }

    public function testCovarianceMatrix(): void
    {
        $data = [
            'x' => [0, 1, 2],
            'y' => [2, 1, 0]
        ];

        $this->assertEquals(
            [
                [1.0, -1.0],
                [-1.0, 1.0]
            ],
            covariance_matrix($data)
        );

        $data = [
            'x' => [-2.1, -1, 4.3],
            'y' => [3, 1.1, 0.12]
        ];

        $this->assertEquals(
            [
                [11.71, -4.286],
                [-4.286, 2.1441]
            ],
            covariance_matrix($data)
        );
    }

    public function testVariance(): void
    {
        $data = [2.0, 4.0, 6.0, 8.0, 10.0];

        $this->assertEquals(10.0, variance($data));
    }

    public function testCovariance(): void
    {
        $x = [1, 2, 3, 4, 5];
        $y = [2, 4, 6, 8, 10];

        $this->assertEquals(5.0, covariance($x, $y));
    }

    public function testCholeskySimple(): void {
        $matrix = [
            [1, -2],
            [2, 5]
        ];

        $this->assertEquals(
            [
                [1, 0],
                [2, 1],
            ],
            MahalanobisDistance::cholesky($matrix)
        );
    }

    public function testCholeskyMedium(): void
    {
        $matrix = [
            [18, 22, 54, 42],
            [22, 70, 86, 62],
            [54, 86, 174, 134],
            [42, 62, 134, 106],
        ];

        $this->assertEquals(
            [
                [4.24264069, 0.0, 0.0, 0.0],
                [5.18544973, 6.5659052, 0.0, 0.0],
                [12.72792205, 3.0460385, 1.64974233, 0.0],
                [9.89949493, 1.62455387, 1.84971102, 1.39262127]
            ],
            MahalanobisDistance::cholesky($matrix)
        );
    }

    public function testForwardSubstitution(): void
    {
        $matrix = [
            [2, 0, 0],
            [3, 1, 0],
            [1, 2, 1],
        ];
        $vector = [4, 7, 6];

        $this->assertEquals(
            [2, 1, 2],
            MahalanobisDistance::forwardSubstitution($matrix, $vector)
        );
    }
}
