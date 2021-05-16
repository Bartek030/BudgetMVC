<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Operation;
use \App\Models\Categories;

/**
 * Income controller
 * 
 * PHP version 7.0
 */
class Income extends Authenticated {
    /**
     * Show new income page
     * 
     * @return void
     */
    public function newAction() {
        $incomeCategories = Categories::getIncomeCategories($_SESSION['user_id']);
        View::renderTemplate('Income/new.html', [
            'incomeCategories' => $incomeCategories
        ]);
    }

    /**
     * Save income in databse
     * 
     * @return void
     */
    public function createAction() {
        $income = new Operation($_POST);

        if($income -> saveIncome($_SESSION['user_id'])) {
            $this -> redirect('/income/success');
        } else {
            $incomeCategories = Categories::getIncomeCategories($_SESSION['user_id']);
            View::renderTemplate('Income/new.html',[
                'income' => $income,
                'incomeCategories' => $incomeCategories
            ]);
        }
    }

    /**
     * Show the income success page
     * 
     * @return void
     */
    public function successAction() {
        View::renderTemplate('Income/success.html');
    }
}