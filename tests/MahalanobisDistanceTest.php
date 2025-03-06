<?php

use Arig\MahalanobisDistance\MahalanobisDistance;
use PHPUnit\Framework\TestCase;

final class MahalanobisDistanceTest extends TestCase
{
    public function testCalculate(): void
    {
        $point = [4, 5];
        $data = [
            [2, 3, 4, 5, 6],
            [3, 5, 4, 6, 8]
        ];

        $this->assertEqualsWithDelta(
            0.24343,
            MahalanobisDistance::calculate($point, $data),
            0.00001,
            "The calculated distance is not similar to the expected distance."
        );
    }

    public function testCholeskySimple(): void
    {
        $matrix = [
            [1, -2],
            [2, 5]
        ];

        $this->assertEquals(
            [
                [1, 0],
                [2, 1],
            ],
            MahalanobisDistance::cholesky($matrix),
            "The calculated Cholesky decomposition doesn't match the expected decomposition."
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

        $expected = [
            [4.24264069, 0.0, 0.0, 0.0],
            [5.18544973, 6.5659052, 0.0, 0.0],
            [12.72792205, 3.0460385, 1.64974233, 0.0],
            [9.89949493, 1.62455387, 1.84971102, 1.39262127]
        ];

        $actual = MahalanobisDistance::cholesky($matrix);

        foreach ($expected as $i => $row) {
            foreach ($row as $j => $value) {
                $this->assertEqualsWithDelta(
                    $value,
                    $actual[$i][$j],
                    0.00001,
                    "Failed asserting that $i, $j element: {$actual[$i][$j]} is equal to $value"
                );
            }
        }
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
            MahalanobisDistance::forwardSubstitution($matrix, $vector),
            "The calculated forward substitution doesn't match the expected result."
        );
    }
}
