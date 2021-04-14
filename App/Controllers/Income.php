<?php

namespace App\Controllers;

use \App\Auth;
use \Core\View;
use \App\Models\Operation;

/**
 * Add an income to the database into income table
 * 
 * PHP version 7.0
 */
class Income extends Authenticated {
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
     * Show new income page
     * 
     * @return void
     */
    public function newAction() {
        View::renderTemplate('Income/new.html');
    }

    /**
     * Save income in databse
     * 
     * @return void
     */
    public function createAction() {
        $income = new Operation($_POST);

        if($income -> saveIncome($this -> user)) {
            $this -> redirect('/income/success');
        } else {
            View::renderTemplate('Income/new.html',[
                'income' => $income
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