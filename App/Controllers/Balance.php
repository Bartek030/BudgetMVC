<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Operation;

/**
 * Budget balance controller
 * 
 * PHP version 7.0
 */
class Balance extends Authenticated {
    /**
     * Show new balance page
     * 
     * @return void
     */
    public function otherAction() {
        View::renderTemplate('Balance/other_period.html');
    }

    /**
     * Show balance from custom period of time on the page
     * 
     * @return void
     */
    public function showAction() {
        $balance = new Operation($_POST);
        $balance -> balanceTime = $this -> route_params['period'];

        $incomeData = Operation::getIncomeData($balance, $_SESSION['user_id']);
        $expenseData = Operation::getExpenseData($balance, $_SESSION['user_id']);

        $incomeSummary = static::operationSummary($incomeData);
        $expenseSummary = static::operationSummary($expenseData);
        $balanceSummary = $incomeSummary - $expenseSummary;

        View::renderTemplate('Balance/results.html', [
            'incomeData' => $incomeData,
            'expenseData' => $expenseData,
            'incomeSummary' => $incomeSummary,
            'expenseSummary' => $expenseSummary,
            'balanceSummary' => $balanceSummary
        ]);
    }

    /**
     * Count summary balance of operations
     * 
     * @param array Array of operation data
     * 
     * @return float summary balance of operations
     */
    protected static function operationSummary($operationArray) {
        $summary = 0;
        foreach($operationArray as $amount ) {
            $summary = $summary + $amount['amount'];
        }
        return $summary;
    }
}