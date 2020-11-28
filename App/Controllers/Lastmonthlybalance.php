<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;

/**
 * Items controller (example)
 *
 * PHP version 7.0
 */
class Lastmonthlybalance extends Authenticated
{

    /**
     * Items index
     *
     * @return void
     */
    public function indexAction()
    {
		$lastmonthlybalance = new User($_POST);
		$args=[];
		
		$args['rowIncomes'] = $lastmonthlybalance->incomeslastmonthlybalance();
		$args['rowExpenses'] = $lastmonthlybalance->expenseslastmonthlybalance();
		$args['sumrowIncomes'] = $lastmonthlybalance->sumincomeslastmonthlybalance();
		$args['sumrowExpenses'] = $lastmonthlybalance->sumexpenseslastmonthlybalance();
		
        View::renderTemplate('Lastmonthlybalance/index.html', $args);

    }
	//to chyba nie trzeba
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
