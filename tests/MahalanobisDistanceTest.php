<?php

use PHPUnit\Framework\TestCase;
use Arig\MahalanobisDistance\MahalanobisDistance;

final class MahalanobisDistanceTest extends TestCase
{
    public function testCalculate(): void
    {
        $point = [3.5, 4.5];
        $data = [
            [2, 3],
            [3, 4],
            [4, 5],
            [5, 6],
        ];

        $this->assertEquals(
            0.0,
            MahalanobisDistance::calculate($point, $data)
        );
    }

    public function testCholesky(): void
    {
        $matrix = [
            [18, 22, 54, 42],
            [22, 70, 86, 62],
            [54, 86, 174, 134],
            [42, 62, 134, 106],
        ];

        var_dump(MahalanobisDistance::cholesky($matrix));

//        $this->assertEquals(
//            [
//                [-4.503599627370496e+15, 9.007199254740992e+15, -4.503599627370496e+15],
//                [9.007199254740992e+15, -1.8014398509481984e+16, 9.007199254740992e+15],
//                [-4.503599627370496e+15, 9.007199254740992e+15, -4.503599627370496e+15],
//            ],
//            MahalanobisDistance::cholesky($matrix)
//        );
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
