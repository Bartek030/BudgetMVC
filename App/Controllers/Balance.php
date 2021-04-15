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
     * Before filter - called before each action method
     * 
     * @return void
     */
    protected function before() {
        parent::before();
        $this -> user = Auth::getUser();
    }

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

        $incomeData = Operation::getIncomeData($balance, $this -> user);
        $expenseData = Operation::getExpenseData($balance, $this -> user);

    }
}