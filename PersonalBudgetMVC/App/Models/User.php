<?php

namespace App\Models;

use PDO;
use \App\Token;
use \Core\View;
use \App\Mail;
use \App\Auth; 

/**
 * Example user model
 *
 * PHP version 7.0
 */
class User extends \Core\Model
{

     /**
     * Error messages
     *
     * @var array
     */
    public $errors = [];

    /**
     * Class constructor
     *
     * @param array $data  Initial property values (optional)
     *
     * @return void
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

  /**
   * Save the user model with the current property values
   *
   * @return void
   */
  public function save()
  {
	$this->validate();

    if (empty($this->errors)) {  
	
		$password_hash = password_hash($this->password, PASSWORD_DEFAULT);

		$sql = 'INSERT INTO users (username, password, email)
				VALUES (:username, :password, :email)';

		$db = static::getDB();
		$stmt = $db->prepare($sql);

		$stmt->bindValue(':username', $this->name, PDO::PARAM_STR);
		$stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
		$stmt->bindValue(':password', $password_hash, PDO::PARAM_STR);
		
		$stmt->execute();

		$_SESSION['last_id'] = $db->lastInsertId();

		return true;
	}

    return false;
  }
  
  
  /*add from default table*/
   public function addDefaultTableIncomes()
  {

    if (empty($this->errors)) {  
		
		$last_id = $_SESSION['last_id'];
		//$user_id = 25; //pobrac tutaj zmienna sesyjna last_id z góry
		
		$db = static::getDB();
		
		$sql = "INSERT INTO incomes_category_assigned_to_users (user_id, name) SELECT '$last_id', name FROM incomes_category_default";
		
		$stmt = $db->prepare($sql);
		

		return $stmt->execute();
	}

    return false;
  }
  
  
   public function addDefaultTableExpenses()
  {

    if (empty($this->errors)) {  
		
		$last_id = $_SESSION['last_id'];
		
		$db = static::getDB();
		
		$sql = "INSERT INTO expenses_category_assigned_to_users (user_id, name) SELECT '$last_id', name FROM expenses_category_default";

		$stmt = $db->prepare($sql);

		return $stmt->execute();
	}

    return false;
  }
  
  
   public function addDefaultTablePayment()
  {

    if (empty($this->errors)) {  
		
		$last_id = $_SESSION['last_id'];
		
		$db = static::getDB();
		
		$sql = "INSERT INTO payment_methods_assigned_to_users (user_id, name) SELECT '$last_id', name FROM payment_methods_default";

		$stmt = $db->prepare($sql);

		return $stmt->execute();
	}

    return false;
  }
  
  /**
     * Validate current property values, adding valiation error messages to the errors array property
     *
     * @return void
     */
    public function validate()
    {
       // Name
       if ($this->name == '') {
           $this->errors[] = 'Imię jest wymagane';
       }

       // email address
       if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
           $this->errors[] = 'Niepoprawny e-mail';
       }
        if (static::emailExists($this->email, $this->id ?? null)) {
            $this->errors[] = 'Adres e-mail jest już zajęty';
        }

		// Password
		 if (isset($this->password)) {
			 
			if (strlen($this->password) < 6) {
				$this->errors[] = 'Hasło musi zawierać co najmniej 6 znaków';
			}

			if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
				$this->errors[] = 'Hasło wymaga co najmniej jednej litery';
			}

			if (preg_match('/.*\d+.*/i', $this->password) == 0) {
				$this->errors[] = 'Hasło wymaga co najmniej jednej cyfry';
			}
		 }
    }
	
    /**
     * See if a user record already exists with the specified email
     *
     * @param string $email email address to search for
     * @param string $ignore_id Return false anyway if the record found has this ID
     *
     * @return boolean  True if a record already exists with the specified email, false otherwise
     */
    public static function emailExists($email, $ignore_id = null)
    {
        $user = static::findByEmail($email);

        if ($user) {
            if ($user->id != $ignore_id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Find a user model by email address
     *
     * @param string $email email address to search for
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findByEmail($email)
    {
        $sql = 'SELECT * FROM users WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Authenticate a user by email and password.
     *
     * @param string $email email address
     * @param string $password password
     *
     * @return mixed  The user object or false if authentication fails
     */
    public static function authenticate($email, $password)
    {
        $user = static::findByEmail($email);

        if ($user) {
            if (password_verify($password, $user->password)) {
                return $user;
            }
        }

        return false;
    }
	
	/**
     * Find a user model by ID
     *
     * @param string $id The user ID
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findByID($id)
    {
        $sql = 'SELECT * FROM users WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }
	
	/**
     * Remember the login by inserting a new unique token into the remembered_logins table
     * for this user record
     *
     * @return boolean  True if the login was remembered successfully, false otherwise
     */
    public function rememberLogin()
    {
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->remember_token = $token->getValue();

        $this->expiry_timestamp = time() + 60 * 60 * 24 * 30;  // 30 days from now

        $sql = 'INSERT INTO remembered_logins (token_hash, user_id, expires_at)
                VALUES (:token_hash, :user_id, :expires_at)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $this->expiry_timestamp), PDO::PARAM_STR);

        return $stmt->execute();
    }
	
	/**
     * Update the user's profile
     *
     * @param array $data Data from the edit profile form
     *
     * @return boolean  True if the data was updated, false otherwise
     */
    public function updateProfile($data)
    {
        $this->name = $data['name'];
        $this->email = $data['email'];

        // Only validate and update the password if a value provided
        if ($data['password'] != '') {
            $this->password = $data['password'];
        }

        $this->validate();

        if (empty($this->errors)) {

            $sql = 'UPDATE users
                    SET username = :username,
                        email = :email';

            // Add password if it's set
            if (isset($this->password)) {
                $sql .= ', password = :password_hash';
            }

            $sql .= "\nWHERE id = :id";

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':username', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

            // Add password if it's set
            if (isset($this->password)) {
                $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
                $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            }

            return $stmt->execute();
        }

        return false;
    }
	
		/**
	   *Zapisywanie przychodu
	   *
	   * @return void
	   */
	  public function saveIncome()
	  {

		$user_id = $_SESSION['user_id'];

		if (empty($this->errors)) {  
					
			$sql = "INSERT INTO incomes (id, user_id, amount, date_of_income, income_comment,  income_category_assigned_to_user_id) SELECT NULL, '$user_id', :amount, :day, :comment, id FROM incomes_category_assigned_to_users WHERE name=:category AND user_id='$user_id'";
					
			$db = static::getDB();
			$stmt = $db->prepare($sql);

			$stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
			$stmt->bindValue(':day', $this->day, PDO::PARAM_STR);
			$stmt->bindValue(':category', $this->category, PDO::PARAM_STR);
			$stmt->bindValue(':comment', $this->comment, PDO::PARAM_STR);

			return $stmt->execute();
		}

		return false;
	  }
	  
		/**
	   *Zapisywanie wydatek
	   *
	   * @return void
	   */
	  public function saveExpense()
	  {

		$user_id = $_SESSION['user_id'];

		if (empty($this->errors)) {  			
			
			$sql = "INSERT INTO expenses (id, user_id, amount, date_of_expense, expense_comment,  expense_category_assigned_to_user_id, payment_method_assigned_to_user_id) VALUES (NULL, '$user_id', :amount, :day, :comment, (SELECT id FROM expenses_category_assigned_to_users WHERE name=:category AND user_id='$user_id'), (SELECT id FROM payment_methods_assigned_to_users WHERE name=:pay AND user_id='$user_id'))";
					
			$db = static::getDB();
			$stmt = $db->prepare($sql);

			$stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
			$stmt->bindValue(':day', $this->day, PDO::PARAM_STR);
			$stmt->bindValue(':pay', $this->pay, PDO::PARAM_STR);
			$stmt->bindValue(':category', $this->category, PDO::PARAM_STR);
			$stmt->bindValue(':comment', $this->comment, PDO::PARAM_STR);

			return $stmt->execute();
		}

		return false;
	  }
	  
		/**
	   *Zapisywanie wydatek
	   *
	   * @return void
	   */
	  public function incomesmonthlybalance()
	  { 
	    $user_id = $_SESSION['user_id'];

		if (empty($this->errors)) { 
		
			$sql = "SELECT user_id, FORMAT(SUM(amount),2) AS SUM, income_category_assigned_to_user_id, (SELECT name FROM  incomes_category_assigned_to_users WHERE incomes_category_assigned_to_users.id=incomes. income_category_assigned_to_user_id) AS nameCategory FROM incomes WHERE MONTH(date_of_income) = MONTH(CURDATE()) AND YEAR(date_of_income) = YEAR(CURDATE()) AND user_id='$user_id' GROUP BY nameCategory  ORDER BY SUM DESC";
			
			
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->execute();
			
			$rowIncomes = $stmt->fetchAll();
			
			return $rowIncomes;
		}
		
		return false;
	  }
	  
	  
	  public function expensesmonthlybalance()
	  { 
	    $user_id = $_SESSION['user_id'];

		if (empty($this->errors)) { 
		
			$sql = "SELECT user_id, FORMAT(SUM(amount),2) AS SUM, expense_category_assigned_to_user_id, (SELECT name FROM  expenses_category_assigned_to_users WHERE expenses_category_assigned_to_users.id=expenses. expense_category_assigned_to_user_id) AS nameCategory FROM expenses WHERE MONTH(date_of_expense) = MONTH(CURDATE()) AND YEAR(date_of_expense) = YEAR(CURDATE()) AND user_id='$user_id' GROUP BY nameCategory ORDER BY SUM DESC";
			
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->execute();
			
			$rowExpenses = $stmt->fetchAll();
			
			return $rowExpenses;
		}
		
		return false;
	  }
	  
	  public function sumincomesmonthlybalance()
	  { 
	    $user_id = $_SESSION['user_id'];

		if (empty($this->errors)) { 
		
			$sql = "SELECT user_id, FORMAT(SUM(amount),2) AS SUM FROM incomes WHERE MONTH(date_of_income) = MONTH(CURDATE()) AND YEAR(date_of_income) = YEAR(CURDATE()) AND user_id='$user_id'";
			
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->execute();
			
			$sumrowIncomes = $stmt->fetchAll();

			return $sumrowIncomes;
		}
		
		 return false;
	   }
	   
	  public function sumexpensesmonthlybalance()
	  { 
	    $user_id = $_SESSION['user_id'];

		if (empty($this->errors)) { 
		
			$sql = "SELECT user_id, FORMAT(SUM(amount),2) AS SUM FROM expenses WHERE MONTH(date_of_expense) = MONTH(CURDATE()) AND YEAR(date_of_expense) = YEAR(CURDATE()) AND user_id='$user_id'";
			
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->execute();
			
			$sumrowExpenses = $stmt->fetchAll();
			//$_SESSION['wydatek'] = $sumrowExpenses;
			
			return $sumrowExpenses;
		}
		
		 return false;
	   }


	public function incomeslastmonthlybalance()
	{ //poprzedni miesiac	
		$user_id = $_SESSION['user_id'];

		if (empty($this->errors)) { 
		
			$sql = "SELECT user_id, FORMAT(SUM(amount),2) AS SUM, income_category_assigned_to_user_id, (SELECT name FROM  incomes_category_assigned_to_users WHERE incomes_category_assigned_to_users.id=incomes. income_category_assigned_to_user_id) AS nameCategory FROM incomes WHERE MONTH(date_of_income) = MONTH(CURDATE())-1 AND YEAR(date_of_income) = YEAR(CURDATE()) AND user_id='$user_id' GROUP BY  nameCategory ORDER BY SUM DESC";
					
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->execute();
			
			$rowIncomes = $stmt->fetchAll();
			
			return $rowIncomes;
		}

		return false;
	}
	  
	public function expenseslastmonthlybalance()
	{ 
	    $user_id = $_SESSION['user_id'];

		if (empty($this->errors)) { 
		
			$sql = "SELECT user_id, FORMAT(SUM(amount),2) AS SUM, expense_category_assigned_to_user_id, (SELECT name FROM  expenses_category_assigned_to_users WHERE expenses_category_assigned_to_users.id=expenses. expense_category_assigned_to_user_id) AS nameCategory FROM expenses WHERE MONTH(date_of_expense) = MONTH(CURDATE())-1 AND YEAR(date_of_expense) = YEAR(CURDATE()) AND user_id='$user_id' GROUP BY nameCategory ORDER BY SUM DESC";
			
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->execute();
			
			$rowExpenses = $stmt->fetchAll();
			
			return $rowExpenses;
		}
		
		return false;
	}
	  
	public function sumincomeslastmonthlybalance()
	{ 
	    $user_id = $_SESSION['user_id'];

		if (empty($this->errors)) { 
		
			$sql = "SELECT user_id, FORMAT(SUM(amount),2) AS SUM FROM incomes WHERE MONTH(date_of_income) = MONTH(CURDATE())-1 AND YEAR(date_of_income) = YEAR(CURDATE()) AND user_id='$user_id'";
			
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->execute();
			
			$sumrowIncomes = $stmt->fetchAll();

			return $sumrowIncomes;
		}
		
		 return false;
	}
	   
	 public function sumexpenseslastmonthlybalance()
	{ 
	    $user_id = $_SESSION['user_id'];

		if (empty($this->errors)) { 
		
			$sql = "SELECT user_id, FORMAT(SUM(amount),2) AS SUM FROM expenses WHERE MONTH(date_of_expense) = MONTH(CURDATE())-1 AND YEAR(date_of_expense) = YEAR(CURDATE()) AND user_id='$user_id'";
			
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->execute();
			
			$sumrowExpenses = $stmt->fetchAll();
			
			return $sumrowExpenses;
		}
		
		 return false;
	}
	
	
	public function incomesyearbalance()
	{ 
		$user_id = $_SESSION['user_id'];

		if (empty($this->errors)) { 
		
			$sql = "SELECT user_id, FORMAT(SUM(amount),2) AS SUM, income_category_assigned_to_user_id, (SELECT name FROM  incomes_category_assigned_to_users WHERE incomes_category_assigned_to_users.id=incomes. income_category_assigned_to_user_id) AS nameCategory FROM incomes WHERE YEAR(date_of_income) = YEAR(CURDATE()) AND user_id='$user_id' GROUP BY  nameCategory ORDER BY SUM DESC";
					
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->execute();
			
			$rowIncomes = $stmt->fetchAll();
			
			return $rowIncomes;
		}

		return false;
	}
	  
	public function expensesyearbalance()
	{ 
	    $user_id = $_SESSION['user_id'];

		if (empty($this->errors)) { 
		
			$sql = "SELECT user_id, FORMAT(SUM(amount),2) AS SUM, expense_category_assigned_to_user_id, (SELECT name FROM  expenses_category_assigned_to_users WHERE expenses_category_assigned_to_users.id=expenses. expense_category_assigned_to_user_id) AS nameCategory FROM expenses WHERE YEAR(date_of_expense) = YEAR(CURDATE()) AND user_id='$user_id' GROUP BY nameCategory ORDER BY SUM DESC";
			
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->execute();
			
			$rowExpenses = $stmt->fetchAll();
			
			return $rowExpenses;
		}
		
		return false;
	}
	  
	public function sumincomesyearbalance()
	{ 
	    $user_id = $_SESSION['user_id'];

		if (empty($this->errors)) { 
		
			$sql = "SELECT user_id, FORMAT(SUM(amount),2) AS SUM FROM incomes WHERE YEAR(date_of_income) = YEAR(CURDATE()) AND user_id='$user_id'";
			
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->execute();
			
			$sumrowIncomes = $stmt->fetchAll();

			return $sumrowIncomes;
		}
		
		 return false;
	}
	   
	 public function sumexpensesyearbalance()
	{ 
	    $user_id = $_SESSION['user_id'];

		if (empty($this->errors)) { 
		
			$sql = "SELECT user_id, FORMAT(SUM(amount),2) AS SUM FROM expenses WHERE YEAR(date_of_expense) = YEAR(CURDATE()) AND user_id='$user_id'";
			
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->execute();
			
			$sumrowExpenses = $stmt->fetchAll();
			
			return $sumrowExpenses;
		}
		
		 return false;
	}
	  
	 
	public static function takerangefrom()
	{ 
		$fromDay = $_POST['day1'];
		$_SESSION['fromDay'] = $fromDay;

		return $fromDay;
	}

	public static function takerangeto()
	{ 
		$toDay = $_POST['day2'];
		$_SESSION['toDay'] = $toDay;
		
		return $toDay;
	}

	public function incomesdaterange()
	{ 
		$user_id = $_SESSION['user_id'];
		
		$beginDay = static::takerangefrom();
		$endDay = static::takerangeto();

		if (empty($this->errors)) { 
		
			$sql = "SELECT user_id, FORMAT(SUM(amount),2) AS SUM, income_category_assigned_to_user_id, (SELECT name FROM  incomes_category_assigned_to_users WHERE incomes_category_assigned_to_users.id=incomes. income_category_assigned_to_user_id) AS nameCategory FROM incomes WHERE (date_of_income >= '$beginDay' AND date_of_income <= '$endDay') AND user_id='$user_id' GROUP BY nameCategory ORDER BY SUM DESC";
					
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->execute();
			
			$rowIncomes = $stmt->fetchAll();
			
			return $rowIncomes;
		}

		return false;
	}

	
	public function expensesdaterange()
	{ 
		$user_id = $_SESSION['user_id'];
		
		$beginDay = static::takerangefrom();
		$endDay = static::takerangeto();

		if (empty($this->errors)) { 
		
			$sql = "SELECT user_id, FORMAT(SUM(amount),2) AS SUM, expense_category_assigned_to_user_id, (SELECT name FROM  expenses_category_assigned_to_users WHERE expenses_category_assigned_to_users.id=expenses. expense_category_assigned_to_user_id) AS nameCategory FROM expenses WHERE (date_of_expense >= '$beginDay' AND date_of_expense <= '$endDay') AND user_id='$user_id' GROUP BY nameCategory ORDER BY SUM DESC";
					
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->execute();
			
			$rowExpenses = $stmt->fetchAll();
			
			return $rowExpenses;
		}

		return false;
	}
	
	public function sumincomesdaterange()
	{ 
	    $user_id = $_SESSION['user_id'];
		
		$beginDay = static::takerangefrom();
		$endDay = static::takerangeto();

		if (empty($this->errors)) { 
		
			$sql = "SELECT user_id, FORMAT(SUM(amount),2) AS SUM FROM incomes WHERE (date_of_income >= '$beginDay' AND date_of_income <= '$endDay') AND user_id='$user_id'";
			
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->execute();
			
			$sumrowIncomes = $stmt->fetchAll();

			return $sumrowIncomes;
		}
		
		 return false;
	}
	   
	 public function sumexpensesdaterange()
	{ 
	    $user_id = $_SESSION['user_id'];
		
		$beginDay = static::takerangefrom();
		$endDay = static::takerangeto();

		if (empty($this->errors)) { 
		
			$sql = "SELECT user_id, FORMAT(SUM(amount),2) AS SUM FROM expenses WHERE (date_of_expense >= '$beginDay' AND date_of_expense <= '$endDay') AND user_id='$user_id'";
			
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			$stmt->execute();
			
			$sumrowExpenses = $stmt->fetchAll();
			
			return $sumrowExpenses;
		}
		
		 return false;
	}
}

