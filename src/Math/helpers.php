<?php

if (! function_exists('variance')) {
    /**
     * @param array<float|int> $data
     * @return float
     */
    function variance(array $data): float
    {
        $n = count($data);
        $mean = array_sum($data) / $n;

        return round(array_sum(array_map(fn (float|int $element): float|int => pow($element - $mean, 2), $data)) / ($n - 1), 8);
    }
}

if (! function_exists('covariance')) {
    /**
     * @param array<float|int> $dataX
     * @param array<float|int> $dataY
     * @return float
     */
    function covariance(array $dataX, array $dataY): float
    {
        $nX = count($dataX);
        $nY = count($dataY);

        if ($nX !== $nY) {
            throw new InvalidArgumentException('The number of elements in the two arrays must be equal.');
        }

        $meanX = array_sum($dataX) / $nX;
        $meanY = array_sum($dataY) / $nY;

        return round(array_sum(array_map(fn (float|int $x, float|int $y): float|int => ($x - $meanX) * ($y - $meanY), $dataX, $dataY)) / ($nX - 1), 8);
    }
}

if (! function_exists('dot')) {
    function dot(array $vectorA, array $vectorB): float|int
    {
        return array_sum(array_map(fn (float|int $a, float|int $b): float|int => $a * $b, $vectorA, $vectorB));
    }
}

if (! function_exists('identity_matrix')) {
    function identity_matrix(int $size): array
    {
        $identity = array_fill(0, $size, array_fill(0, $size, 0));

        for ($i = 0; $i < $size; $i++) {
            $identity[$i][$i] = 1;
        }

        return $identity;
    }
}

if (! function_exists('covariance_matrix')) {
    /**
     * @param array<array<float|int>> $data
     * @return array<array<float>>
     */
    function covariance_matrix(array $data): array
    {
        $size = count($data);
        $keys = array_keys($data);

        $emptyCov = array_fill(0, $size, array_fill(0, $size, 0));

        return array_map(function (array $_, string $varA) use ($data, $keys): array {
            return array_map(function (int $_, string $varB) use ($data, $varA): float {

                if ($varA === $varB) {
                    return variance($data[$varA]);
                }

                return covariance($data[$varA], $data[$varB]);
            }, $_, $keys);
        }, $emptyCov, $keys);
    }
}

if (! function_exists('vector_sub')) {
    /**
     * @param array<float|int> $vectorA
     * @param array<float|int> $vectorB
     * @return array<float|int>
     */
    function vector_sub(array $vectorA, array $vectorB): array
    {
        return array_map(function (float|int $a, float|int $b): float|int {
            return $a - $b;
        }, $vectorA, $vectorB);
    }
}

if (! function_exists('vector_transpose')) {
    function vector_transpose(array $vector): array
    {
        return array_map(function (array $element): array {
            return [$element];
        }, $vector);
    }
}