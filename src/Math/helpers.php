<?php

if (! function_exists('variance')) {
    function variance(array $data, float|int $mean): float|int
    {
        return array_sum(array_map(fn (float|int $element): float|int => pow($element - $mean, 2), $data)) / count($data);
    }
}

if (! function_exists('covariance')) {
    function covariance(array $dataX, array $dataY, float|int $meanX, float|int $meanY): float|int
    {
        return array_sum(array_map(fn (float|int $x, float|int $y): float|int => ($x - $meanX) * ($y - $meanY), $dataX, $dataY)) / count($dataX);
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

if (! function_exists('mean_vector')) {
    /**
     * @param array<array<float|int>> $data
     * @return array
     */
    function mean_vector(array $data): array
    {
        return array_map(fn (array $variable): float => array_sum($variable) / count($variable), $data);
    }
}

if (! function_exists('covariance_matrix')) {
    function covariance_matrix(array $data): array
    {
        $keys = array_keys($data);

        return array_map(function (array $dataX, int $keyX) use ($data, $keys): array {
            $meanX = array_sum($dataX) / count($dataX);

            return array_map(function (array $dataY, int $keyY) use ($keyX, $dataX, $meanX): float {
                $meanY = array_sum($dataY) / count($dataY);

                if ($keyX === $keyY) {
                    return variance($dataX, $meanX);
                }

                return covariance($dataX, $dataY, $meanX, $meanY);
            }, $data, $keys);
        }, $data, $keys);
    }
}

if (! function_exists('vector_sub')) {
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