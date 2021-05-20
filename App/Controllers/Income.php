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
    public function newAction($isSnackbar = false, $snackbarText = '', $snackbarType = '') {
        $incomeCategories = Categories::getIncomeCategories($_SESSION['user_id']);
        View::renderTemplate('Income/new.html', [
            'incomeCategories' => $incomeCategories,
            'isSnackbar' => $isSnackbar,
            'snackbarText' => $snackbarText,
            'snackbarType' => $snackbarType
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
            $this -> newAction(true, 'Przychód został dodany', 'success');
        } else {
            $this -> newAction(true, 'Uzupełnij poprawnie dane, aby dodać przychód', 'fault');
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