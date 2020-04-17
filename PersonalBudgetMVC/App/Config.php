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
    const DB_NAME = 'aplikacjabudzetowamvc';
	//const DB_NAME = 'kailp_aplikacjabudzetowamvc';

    /**
     * Database user
     * @var string
     */
    const DB_USER = 'usermvc';
	//const DB_USER = 'kailp_budzetmvc';
    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = 'waltornia';
	//const DB_PASSWORD = 'Waltornia123';
    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = true;
	
	/**
     * Secret key for hashing
     * @var boolean
     */
    const SECRET_KEY = 'mKtiTyXBksXGintgy7nz9DscCplydf6T';
}
