<?php

namespace App\Controllers;

use \App\Auth;
use \Core\View;

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
        View::renderTemplate('Income/new.html', [
            'user' => $this -> user
        ]);
    }
}