<?php
require_once (__DIR__ . "/partials/nav.php");

if (!is_logged_in()) {
    flash("You need to login first!");
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    die(header("Location: login.php"));
}

ini_set('display_errors',1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// preparing account id and accounts for dropdown list
$db = getDB();
$stmt = $db->prepare("SELECT id, account_number from Accounts LIMIT 10");
$r = $stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
// TODO Add dropdown list
<form method="POST">
	<input type="text" name="account1" placeholder="Source Account Number">
	<!-- If our sample is a transfer show other account field-->
	<?php if($_GET['type'] == 'transfer') : ?>
	<input type="text" name="account2" placeholder="Destination Account Number">
	<?php endif; ?>
	
	<input type="number" name="amount" placeholder="$0.00"/>
	<input type="hidden" name="type" value="<?php echo $_GET['type'];?>"/>
	
	<!--Based on sample type change the submit button display-->
	<input type="submit" value="Move Money"/>
</form>

<?php
if(isset($_POST['type']) && isset($_POST['account1']) && isset($_POST['amount'])){
	$type = $_POST['type'];
	$amount = (int)$_POST['amount'];
	switch($type){
		case 'deposit':
			do_bank_action($getWorldID, $_POST['account1'], ($amount * -1), $type);
			break;
		case 'withdraw':
			do_bank_action($_POST['account1'], $getWorldID, ($amount * -1), $type);
			break;
		case 'transfer':
			//TODO create function for transfer
			break;
	}
}
require(__DIR__ . "/partials/flash.php");
?>
