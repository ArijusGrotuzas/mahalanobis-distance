<?php

use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
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

        // TODO: Assert with delta instead
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
}