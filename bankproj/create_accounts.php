<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: ./login.php"));
}
?>

<form method="POST">
	<label>Account Type</label>
	<select name="account_type">
		<option value="checking">Checking</option>
		<option value="savings">Savings</option>
	</select>
	<input type="number"  name="balance" placeholder="Balance"/>
	<input type="submit" name="save" value="Create"/>
</form>

<?php
if(isset($_POST["save"])){
	//TODO add proper validation/checks
	$bal = (float)$_POST["balance"]; //makes sure balance is > 5, before creating account
	if($bal < 5){  
	    flash("Minimum $5 balance in order to open an account");
	}
       	else{
	    $acctnum = rand(100000000000, 999999999999);
            $accttype = $_POST["account_type"];
	    $user = get_user_id();
	    $db = getDB();
	    $stmt = $db->prepare("INSERT INTO Accounts (account_number,account_type, user_id) VALUES(:account_number, :account_type, :user)");
	    $r = $stmt->execute([
	        ":account_number"=>$acctnum,
	        ":account_type"=>$accttype,
	        ":user"=>$user
	    ]);
	    if($r){
	        $accountID = $db->lastInsertId();
		do_bank_action(getWorldID(), $accountID, ($bal*-1), "open", "new account");
	    	flash("Account created successfully! Your new account has an id number of: " . $accountID);
		die(header("Location: ./view_accounts.php"));  
	    }
	    else{
	        flash("There was an error creating your account. Please try again.");
	    }
        }
}
?>
<?php require(__DIR__ . "/partials/flash.php");
