<?php

namespace App;

/**
 * Application configuration
 *
 * PHP version 7.0
 */
class Config
{

    /**
     * Database host
     * @var string
     */
    const DB_HOST = 'localhost';

    /**
     * Database name
     * @var string
     */
    const DB_NAME = 'personalbudget';

    /**
     * Database user
     * @var string
     */
    const DB_USER = 'root';

    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = '';

    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = true;

    /**
     * Secret key for hashing
     * @var boolean
     */
    const SECRET_KEY = '';

    /**
     * Mail host
     * @var string
     */
    const MAIL_HOST = '';

    /**
     * Mail user
     * @var string
     */
    const MAIL_USER = '';

    /**
     * Mail password
     * @var string
     */
    const MAIL_PASSWORD = '';

    /**
     * Mail name
     * @var string
     */
    const MAIL_NAME= '';
}
