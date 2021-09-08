<?php

namespace App\Tests;

use App\Service\Calculator;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    /**
     * Check that the addition service method works great
     *
     * @return void
     */
    public function testAddition(): void
    {
        // We take number 1 & 2
        // The addition method is supposed to return number 3
        $calculator = new Calculator();
        $result = $calculator->addition(1, 2); //3
        
        // La méthode assertEquals prends 3 arguments :
        // 1/ $excepted : la valeur à laquelle on s'attends
        // 2/ $actual : la valeur actuellement retournée par la méthode du service
        // 3/ $message : message retourné en cas d'échec
        $this->assertEquals(3, $result);
        //$this->assertTrue(true);
    }
}
