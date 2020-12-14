<?php
require_once (__DIR__ . "/partials/nav.php");
ini_set('display_errors',1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
function getRealTimeBalance($acctid){
    $db = getDB();
    $q = "SELECT ifnull(SUM(amount), 0) as total from Transactions WHERE act_src_id=:id";
    $stmt = $db->prepare($q);
    $s = $stmt->execute([":id" => $acctid]);
    if ($s){
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $total = (float)$result["total"]; 
        return $total;
    }
    return 0;
}

function updateBalance($accountid){
    $db = getDB();
    $q = "UPDATE Accounts SET balance=(SELECT ifnull(SUM(amount), 0) as total from Transactions WHERE act_src_id-:id) WHERE id=:id";  
    $stmt = $db->prepare($q);
    $s = $stmt->execute([":id" => $accountid]);
}

function do_bank_action($account1, $account2, $amountChange, $type){
	$db = getDB();
	
	
	$a1total = getRealTimeBalance($account1);
	$a2total = getRealTimeBalance($account2); //TODO get total of account 2
	$a1total += $amountChange;
	$a2total -= $amountChange; 
	$query = "INSERT INTO `Transactions` (`act_src_id`, `act_dest_id`, `amount`, `action_type`, `expected_total`) VALUES(:p1a1, :p1a2, :p1change, :type, :a1total), (:p2a1, :p2a2, :p2change, :type, :a2total)";
	
	$stmt = $db->prepare($query);
	$stmt->bindValue(":p1a1", $account1);
	$stmt->bindValue(":p1a2", $account2);
	$stmt->bindValue(":p1change", $amountChange);
	$stmt->bindValue(":type", $type);
	$stmt->bindValue(":a1total", $a1total);
	//flip data for other half of transaction
	$stmt->bindValue(":p2a1", $account2);
	$stmt->bindValue(":p2a2", $account1);
	$stmt->bindValue(":p2change", ($amountChange*-1));
	$stmt->bindValue(":type", $type);
	$stmt->bindValue(":a2total", $a2total);
	$result = $stmt->execute();
	if($result){
	   updateBalance($account1);
	   updateBalance($account2);
	}
	echo var_export($result, true);
	echo var_export($stmt->errorInfo(), true);
	return $result;

}
?>
<form method="POST">
	<input type="text" name="account1" placeholder="Account Number">
	<!-- If our sample is a transfer show other account field-->
	<?php if($_GET['type'] == 'transfer') : ?>
	<input type="text" name="account2" placeholder="Other Account Number">
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
	$db = getDB();
	$q = "SELECT id from Accounts WHERE account_number='000000000000'";
	$stmt = $db->prepare($q);
        $s = $stmt->execute();
        $results = $stmt->fetch(PDO::FETCH_ASSOC);
	$worldID = $results["id"];	 
	switch($type){
		case 'deposit':
			do_bank_action($worldID, $_POST['account1'], ($amount * -1), $type);
			break;
		case 'withdraw':
			do_bank_action($_POST['account1'], $worldID, ($amount * -1), $type);
			break;
		case 'transfer':
			//TODO figure it out
			break;
	}
}
require(__DIR__ . "/partials/flash.php");
?>
