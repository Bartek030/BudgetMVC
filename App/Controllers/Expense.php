<?php

namespace App\Controllers;

use \App\Auth;
use \Core\View;
use \App\Models\Operation;

/**
 * Add an expense to the database into expense table
 * 
 * PHP version 7.0
 */
class Expense extends Authenticated {
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
     * Show new expense page
     * 
     * @return void
     */
    public function newAction() {
        View::renderTemplate('Expense/new.html');
    }

    /**
     * Save expense in databse
     * 
     * @return void
     */
    public function createAction() {
        $expense = new Operation($_POST);

        if($expense -> saveExpense($this -> user)) {
            $this -> redirect('/expense/success');
        } else {
            View::renderTemplate('Expense/new.html',[
                'expense' => $expense
            ]);
        }
    }

    /**
     * Show the expense success page
     * 
     * @return void
     */
    public function successAction() {
        View::renderTemplate('Expense/success.html');
    }
}