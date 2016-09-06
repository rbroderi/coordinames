<?php
/*
The MIT License (MIT)

Copyright (c) 2016 krowinski

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
//namespace BCMathExtended;
    /**
     * @param int|string $number
     * @return string
     */
    function bcceil($number)
    {
        $number = (string)$number;
        if (true === checkIsFloat($number) and true === checkIsFloatCleanZeros($number))
        {
            $result = 1;
            if (true === isNegative($number))
            {
                --$result;
            }
            $number = bcadd($number, $result, 0);
        }
        return checkNumber($number);
    }
    /**
     * @param int|string $number
     * @return bool
     */
    function checkIsFloat($number)
    {
        return false !== strpos($number, '.');
    }
    /**
     * @param int|string $number
     * @return bool
     */
    function checkIsFloatCleanZeros(&$number)
    {
        return false !== strpos($number = rtrim(rtrim($number, '0'), '.'), '.');
    }
    /**
     * @param $number
     * @return bool
     */
    function isNegative($number)
    {
        return 0 === strncmp('-', $number, 1);
    }
    /**
     * @param int|string $number
     * @return int|string
     */
    function checkNumber($number)
    {
        $number = str_replace('+', '', filter_var($number, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
        if ('-0' === $number || !is_numeric($number))
        {
            return '0';
        }
        return $number;
    }
    /**
     * @param int|string $number
     * @param int $precision
     * @return string
     */
    function bcround($number, $precision = 0)
    {
        $number = (string)$number;
        if (true === checkIsFloat($number))
        {
            if (true === isNegative($number))
            {
                return bcsub($number, '0.' . str_repeat('0', $precision) . '5', $precision);
            }
            return bcadd($number, '0.' . str_repeat('0', $precision) . '5', $precision);
        }
        return checkNumber($number);
    }
    /**
     * @param int|string $number
     * @return string
     */
    function bcfloor($number)
    {
        $number = (string)$number;
        if (true === checkIsFloat($number) and true === checkIsFloatCleanZeros($number))
        {
            $result = 0;
            if (true === isNegative($number))
            {
                --$result;
            }
            $number = bcadd($number, $result, 0);
        }
        return checkNumber($number);
    }
/*
function bcfloor($number)
{
    if ($number[0] != '-')
    {
        return bcadd($number, 0, 0);
    }

    return bcsub($number, 1, 0);
}
*/
    /**
     * @param int|string $number
     * @return string
     */
    function bcabs($number)
    {
        $number = (string)$number;
        if (true === isNegative($number))
        {
            $number = substr($number, 1);
        }
        return checkNumber($number);
    }
    /**
     * @param int|string $min
     * @param int|string $max
     * @return string
     */
    function bcrand($min, $max)
    {
        $max = (string)$max;
        $min = (string)$min;
        $difference = bcadd(bcsub($max, $min), 1);
        $rand_percent = bcdiv(mt_rand(), mt_getrandmax(), 8);
        return bcadd($min, bcmul($difference, $rand_percent, 8), 0);
    }
    /**
     * @param int|string,...
     * @return null|int|string
     */
    function bcmax()
    {
        $max = null;
        foreach (func_get_args() as $value)
        {
            if (null === $max)
            {
                $max = $value;
            }
            else
            {
                if (bccomp($max, $value) < 0)
                {
                    $max = $value;
                }
            }
        }
        return $max;
    }
    /**
     * @param int|string,...
     * @return null|int|string
     */
    function bcmin()
    {
        $min = null;
        foreach (func_get_args() as $value)
        {
            if (null === $min)
            {
                $min = $value;
            }
            else
            {
                if (bccomp($min, $value) > 0)
                {
                    $min = $value;
                }
            }
        }
        return $min;
    }