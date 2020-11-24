<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>

<form method="POST">
	<label >Account Number</label>
	<input type="number" name="account_number"/>
	<label>Account Type</label>
	<select name="account type">
		<option value="0">Checking</option>
		<option value="1">Savings</option>
	</select>
	<label>Balance</label>
	<input type="number"  name="balance"/>
	<input type="submit" name="save" value="Create"/>
</form>

<?php
if(isset($_POST["save"])){
	//TODO add proper validation/checks
	$acctnum = $_POST["account_number"];
	$accttype = $_POST["account type"];
	$bal = $_POST["balance"];
	$nst = date('Y-m-d H:i:s');//calc
	$user = get_user_id();
	$db = getDB();
	$stmt = $db->prepare("INSERT INTO ACCOUNTS (account_number,account type, balance, opened_date, user_id) VALUES(:account_number, :account_type, :balance, :nst,:user)");
	$r = $stmt->execute([
		":account_number"=>$acctnum,
		":account type"=>$accttype,
		":balance"=>$bal,
		":nst"=>$nst,
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
<?php require(__DIR__ . "/partials/flash.php");
