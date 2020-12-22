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
$srcID = get_user_id();
$db = getDB();      //if they have the same user ID, give me their accout number and ID
$stmt = $db->prepare("SELECT id, account_number from Accounts WHERE user_id=:user_id LIMIT 10");
$r = $stmt->execute([":user_id"=>get_user_id()]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- TODO Add dropdown list -->
<form method="POST"> <!-- working on dropdown list -->
	<label for "account1"><h3> Source Account</h3></label>
	<select name="account1" id="account1">
	  <?php foreach($results as $r):?>
	    <option value="<?php safer_echo($r["id"]);?>"><?php safer_echo($r["account_number"]);?></option>
	  <?php endforeach;?>
	</select>
	<!-- If our sample is a transfer show other account field-->
	<?php if($_GET['type'] == 'transfer') : ?>
	<label for "account2"><h3> Destination Account</h3></label>
	<select name="account2" id="account2">
          <?php foreach($results as $r):?>
            <option value="<?php safer_echo($r["id"]);?>"><?php safer_echo($r["account_number"]);?></option>
          <?php endforeach;?>
        </select>
	<?php endif; ?>
	<!-- TODO adjust this to make it work for external transfer -->
	<?php if($_GET['type'] == 'ext_transfer') : ?>
        <label for "account2"><h3> Destination Account</h3></label>
        <select name="account2" id="account2">
          <?php foreach($results as $r):?>
            <option value="<?php safer_echo($r["id"]);?>"><?php safer_echo($r["account_number"]);?></option>
          <?php endforeach;?>
        </select>
        <?php endif; ?>
	
	<input type="number" name="amount" placeholder="$0.00"/>
	<input type="hidden" name="type" value="<?php echo $_GET['type'];?>"/>
	<input type="text" name="memo" placeholder="Write Memo Here" />
	<!--Based on sample type change the submit button display-->
	<input type="submit" value="Move Money"/>
</form>


<?php
if(isset($_POST['type']) && isset($_POST['account1']) && isset($_POST['amount'])){
	$type = $_POST['type'];
	$amount = (int)$_POST['amount'];
	$memo = "";
	$isvalid = true;
	if($amount <= 0){
	    flash("Amount must be greater than 0!");
	    $isvalid = false;
	}
	if(isset($_POST['memo']) && !empty($_POST['memo'])){
	   $memo = $_POST['memo'];
	}
	else{
	   $memo = "N/A";
	}
	if(isset($_POST['account2']) && $_POST['account1'] == $_POST['account2']){
	    flash("Source and Destination account can not be the same!");
	    $isvalid = false;
	}
	if($isvalid){
	    switch($type){
		case 'deposit':
			do_bank_action(getWorldID(), $_POST['account1'], ($amount * -1), $type, $memo);
			flash("Your transaction has successfully been posted!");
			break;
		case 'withdraw':
			if(getRealTimeBalance($_POST['account1']) >= $amount){
			    do_bank_action($_POST['account1'], getWorldID(), ($amount * -1), $type, $memo);
			    flash("Your transaction has successfully been posted!");
			}else{
			    flash("You do not have enough to withdraw this amount");
			}break;
		case 'transfer':
			if(getRealTimeBalance($_POST['account1']) >= $amount){
			    do_bank_action($_POST['account1'], $_POST['account2'], ($amount * -1), $type, $memo);
			    flash("Your transaction has successfully been posted!");
			}else{
				flash("You do not have enough to transfer this amount");
			}break;
	   }
	}
}
require(__DIR__ . "/partials/flash.php");
?>
