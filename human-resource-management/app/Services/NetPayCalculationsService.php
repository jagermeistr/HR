<?php

namespace App\Services;

class NetPayCalculationsService
{
    /**
     * Create a new class instance.
     */
    public $gross_salary;
    
    public function __construct($gross_salary)
    {
        $this->gross_salary = (float) $gross_salary;
    }

    public function getNssfDeduction(): float
    {
        $max = 4320;
        return min($this->gross_salary * 0.06, $max);
    }

    public function getShifDeduction(): float
    {
        return max($this->gross_salary * 0.0275, 300);    
    }

    public function getAhlDeduction(): float
    {
        return $this->gross_salary * 0.015;
    }

    public function getPaye(): float
    {
        $level1 = [288000 / 12, 0.1];   // 24,000
        $level2 = [388000 / 12, 0.25];  // 32,333
        $level3 = [6000000 / 12, 0.3];  // 500,000
        $level4 = [9600000 / 12, 0.325]; // 800,000
        $level5 = [INF, 0.35];

        $taxableIncome = $this->getTaxableIncome();
        $paye = 0;

        if ($taxableIncome <= $level1[0]) {
            $paye = $taxableIncome * $level1[1];
        } elseif ($taxableIncome <= $level2[0]) {
            $paye = ($level1[0] * $level1[1]) + (($taxableIncome - $level1[0]) * $level2[1]);
        } elseif ($taxableIncome <= $level3[0]) {
            $paye = ($level1[0] * $level1[1]) + (($level2[0] - $level1[0]) * $level2[1]) + (($taxableIncome - $level2[0]) * $level3[1]);
        } elseif ($taxableIncome <= $level4[0]) {
            $paye = ($level1[0] * $level1[1]) + (($level2[0] - $level1[0]) * $level2[1]) + (($level3[0] - $level2[0]) * $level3[1]) + (($taxableIncome - $level3[0]) * $level4[1]);
        } else {
            $paye = ($level1[0] * $level1[1]) + (($level2[0] - $level1[0]) * $level2[1]) + (($level3[0] - $level2[0]) * $level3[1]) + (($level4[0] - $level3[0]) * $level4[1]) + (($taxableIncome - $level4[0]) * $level5[1]);
        }

        $relief = 2400;
        // $insuranceRelief = 0.15 * $this->getShifDeduction();
        // $relief += $insuranceRelief;
        
        $paye -= $relief;
        $paye = max($paye, 0);
        
        return $paye; // THIS WAS MISSING!
    }

    public function getDeductions(): float
    {
        return $this->getNssfDeduction() + $this->getShifDeduction() + $this->getAhlDeduction() + $this->getPaye();
    }

    public function getTaxableIncome(): float
    {
        return $this->gross_salary - ($this->getNssfDeduction() + $this->getShifDeduction() + $this->getAhlDeduction());
    }

    public function getNetPay(): float
    {
        return $this->gross_salary - $this->getDeductions();
    }

    // Optional: Add a method to get all breakdown data
    public function getBreakdown(): array
    {
        return [
            'gross_salary' => $this->gross_salary,
            'nssf' => $this->getNssfDeduction(),
            'shif' => $this->getShifDeduction(),
            'ahl' => $this->getAhlDeduction(),
            'paye' => $this->getPaye(),
            'total_deductions' => $this->getDeductions(),
            'net_pay' => $this->getNetPay(),
            'taxable_income' => $this->getTaxableIncome(),
        ];
    }
}