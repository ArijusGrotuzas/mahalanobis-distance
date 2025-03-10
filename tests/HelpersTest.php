<?php

use PHPUnit\Framework\TestCase;

final class HelpersTest extends TestCase
{
    public function testCovarianceMatrix(): void
    {
        $data = [
            'x' => [-2.1, -1, 4.3],
            'y' => [3, 1.1, 0.12]
        ];

        $expected = [
            [11.71, -4.286],
            [-4.286, 2.1441]
        ];

        $actual = covariance_matrix($data);

        foreach ($expected as $i => $row) {
            foreach ($row as $j => $value) {
                $this->assertEqualsWithDelta(
                    $value,
                    $actual[$i][$j],
                    0.001,
                    "Failed asserting that $i, $j element: {$actual[$i][$j]} is equal to $value"
                );
            }
        }
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