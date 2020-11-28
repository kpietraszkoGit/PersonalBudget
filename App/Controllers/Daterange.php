<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;

/**
 * Items controller (example)
 *
 * PHP version 7.0
 */
class Daterange extends Authenticated
{

    /**
     * Items index
     *
     * @return void
     */
    public function indexAction()
    {
		$daterange = new User($_POST);
		$daterange2 = new User($_POST);
		$args=[];
		
		$args['rowIncomes'] = $daterange->incomesdaterange();
		$args['rowExpenses'] = $daterange->expensesdaterange();
		$args['sumrowIncomes'] = $daterange->sumincomesdaterange();
		$args['sumrowExpenses'] = $daterange->sumexpensesdaterange();
		$daterange2->takerangefrom();
		$daterange2->takerangeto();
		
        View::renderTemplate('Daterange/index.html', $args);
    }

	public function createAction()
    {

    }
    /**
     * Add a new item
     *
     * @return void
     */
    public function newAction()
    {
        echo "new action";
    }

    /**
     * Show an item
     *
     * @return void
     */
    public function showAction()
    {
        echo "show action";
    }
}
