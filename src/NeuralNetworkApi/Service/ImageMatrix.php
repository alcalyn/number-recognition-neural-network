<?php

namespace Alcalyn\NeuralNetworkApi\Service;

class ImageMatrix {

    /**
     * Extract a number from sample image to input matrix.
     *
     * @param resource $img
     * @param int $offsetX
     * @param int $offsetY
     *
     * @return float[]
     */
    public function sampleImageToMatrix($img, $offsetX, $offsetY)
    {
        $matrix = [];

        for ($y = 0; $y < 14; $y++) {
            for ($x = 0; $x < 14; $x++) {
                $rgb = imagecolorat($img, $offsetX + $x * 2, $offsetY + $y * 2);
                $grey = ($rgb & 0xFF) / 255;
                $matrix[$y * 14 + $x] = $this->correctPixel($grey);
            }
        }

        return $matrix;
    }

    /**
     * Converts an image to matrix.
     *
     * @param resource $img
     *
     * @return float[]
     */
    public function imageToMatrix($img)
    {
        $matrix = [];

        for ($y = 0; $y < 14; $y++) {
            for ($x = 0; $x < 14; $x++) {
                $rgb = imagecolorat($img, $x, $y);
                $grey = ($rgb & 0xFF) / 255;
                $matrix[$y * 14 + $x] = 1 - $grey;
            }
        }

        return $matrix;
    }

    /**
     * Add contrast to fix jpeg noise.
     *
     * @param float $grey
     *
     * @return float
     */
    public function correctPixel($grey)
    {
        if ($grey < 0.25) {
            return 0.0;
        }

        if ($grey < 0.75) {
            return ($grey - 0.25) * 2;
        }

        return 1.0;
    }

    /**
     * Check whether a matrix contains a number.
     *
     * @param array $matrix
     *
     * @return boolean
     */
    public function matrixIsEmpty(array $matrix)
    {
        $whiteDot = 0;

        foreach ($matrix as $x) {
            if (($x >= 0.2) && (++$whiteDot > 3)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Generate expected output for a number.
     *
     * @param int $n
     *
     * @return float[]
     */
    public function number($n)
    {
        $out = [];

        for ($i = 0; $i < 10; $i++) {
            if ($i === $n) {
                $out [] = 1.0;
            } else {
                $out [] = 0.0;
            }
        }

        return $out;
    }

    /**
     * Render a matrix to string to preview the number as image.
     *
     * @param float[] $matrix
     *
     * @return string
     */
    public function toString(array $matrix)
    {
        $s = '';

        foreach (array_chunk($matrix, 14) as $y) {
            foreach ($y as $x) {
                $s .= $x >= 0.5 ? 'o' : ' ';
            }

            $s .= '|' . PHP_EOL;
        }

        return $s;
    }

    /**
     * Check whether network output matches the expected output.
     *
     * @param float[] $out
     * @param float[] $expected
     *
     * @return boolean
     */
    public function isSuccess(array $out, array $expected)
    {
        for ($i = 0; $i < count($out); $i++) {
            if ($expected[$i] && $out[$i] < 0.5) {
                return false;
            }

            if (!$expected[$i] && $out[$i] > 0.5) {
                return false;
            }
        }

        return true;
    }
}
