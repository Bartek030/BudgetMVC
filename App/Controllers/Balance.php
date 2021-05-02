<?php

namespace App\Controllers;

use \App\Auth;
use \Core\View;
use \App\Models\Operation;

/**
 * Show the budget balance from choosen period of time
 * 
 * PHP version 7.0
 */
class Balance extends Authenticated {
    /**
     * Show new balance page
     * 
     * @return void
     */
    public function newAction() {
        View::renderTemplate('Balance/new.html');
    }

    /**
     * Show balance on the page
     * 
     * @return void
     */
    public function showAction() {
        $balance = new Operation($_POST);

        $incomeData = Operation::getIncomeData($balance, $_SESSION['user_id']);
        $expenseData = Operation::getExpenseData($balance, $_SESSION['user_id']);

        $incomeSummary = static::operatioinSummary($incomeData);
        $expenseSummary = static::operatioinSummary($expenseData);
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
    protected static function operatioinSummary($operationArray) {
        $summary = 0;
        foreach($operationArray as $amount ) {
            $summary = $summary + $amount['amount'];
        }
        return $summary;
    }
}