<?php

use PHPUnit\Framework\TestCase;
use Arig\MahalanobisDistance\MahalanobisDistance;

final class MahalanobisDistanceTest extends TestCase
{
    public function testCalculate(): void
    {
        $point = [1, 2, 3];
        $data = [
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9],
        ];

        $this->assertEquals(
            0.0,
            MahalanobisDistance::calculate($point, $data)
        );
    }

    public function testVectorMatrixMul(): void
    {
        $vector = [1, 2, 3];
        $matrix = [
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9],
        ];

        $this->assertEquals(
            [14.0, 32.0, 50.0],
            MahalanobisDistance::vectorMatrixMul($vector, $matrix)
        );
    }

    public function testInverseMatrix(): void
    {
        $matrix = [
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9],
        ];

        $this->assertEquals(
            [
                [-4.503599627370496e+15, 9.007199254740992e+15, -4.503599627370496e+15],
                [9.007199254740992e+15, -1.8014398509481984e+16, 9.007199254740992e+15],
                [-4.503599627370496e+15, 9.007199254740992e+15, -4.503599627370496e+15],
            ],
            MahalanobisDistance::inverseMatrix($matrix)
        );
    }
}
