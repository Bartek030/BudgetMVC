<?php

namespace App\Controllers;

use App\Mail;
use App\Auth;
use \Core\View;

/**
 * Login controller
 * 
 * PHP version 7.0
 */

class Contact extends \Core\Controller {
    /**
     * Show the login page
     * 
     * @return void
     */
    public function sendMessageAction() {

        $to = 'bartlomiejswies@gmail.com';
        $subject = $_POST['chatName'] . ' Kontakt ze strony budzet osobisty';
        $text = $_POST['chatEmail'] . '<br>' . $_POST['message'];
        $html = $_POST['chatEmail'] . '<br>' . $_POST['message'];
        $name = 'Bartek';

        Mail::send($to, $subject, $text, $html, $name);
        View::renderTemplate('Contact/success.html');
    }
}