<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;

/**
 * Items controller (example)
 *
 * PHP version 7.0
 */
class Monthlybalance extends Authenticated
{
    /**
     * Items index
     *
     * @return void
     */
    public function indexAction()
    {
		unset($_SESSION['ok']);
		unset($_SESSION['ok2']);
		$monthlybalance = new User($_POST);
		$args=[];

		$args['rowIncomes'] = $monthlybalance->incomesmonthlybalance();
		$args['rowExpenses'] = $monthlybalance->expensesmonthlybalance();
		$args['sumrowIncomes'] = $monthlybalance->sumincomesmonthlybalance();
		$args['sumrowExpenses'] = $monthlybalance->sumexpensesmonthlybalance();

        View::renderTemplate('Monthlybalance/index.html', $args);

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
