<?php require_once(__DIR__ . "/../partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: ../login.php"));
}
?>

<form method="POST">
	<label >Account Number</label>
	<input type="number" name="account_number"/>
	<label>Account Type</label>
	<select name="account_type">
		<option value="checking">Checking</option>
		<option value="savings">Savings</option>
	</select>
	<label>Balance</label>
	<input type="number"  name="balance"/>
	<input type="submit" name="save" value="Create"/>
</form>

<?php
if(isset($_POST["save"])){
	//TODO add proper validation/checks
	$acctnum = $_POST["account_number"];
	$accttype = $_POST["account_type"];
	$bal = $_POST["balance"];
	$user = get_user_id();
	$db = getDB();
	$stmt = $db->prepare("INSERT INTO Accounts (account_number,account_type, balance, user_id) VALUES(:account_number, :account_type, :balance, :user)");
	$r = $stmt->execute([
		":account_number"=>$acctnum,
		":account_type"=>$accttype,
		":balance"=>$bal,
		":user"=>$user
	]);
	if($r){
		flash("Created successfully with id: " . $db->lastInsertId());
	}
	else{
		$e = $stmt->errorInfo();
		flash("Error creating: " . var_export($e, true));
	}
}
?>
<?php require(__DIR__ . "/../partials/flash.php");
