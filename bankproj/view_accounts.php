<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You need to be logged in to access this page");
    die(header("Location: login.php"));
}
?>

<?php
//fetching
$id = get_user_id();
$results = [];
if (isset($id)) {
    $db = getDB();
    $stmt = $db->prepare("SELECT id, account_number, account_type, balance FROM Accounts WHERE user_id=:id LIMIT 5");
    $r = $stmt->execute([":id" => $id]);
    if ($r) {
	$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
        flash("There was a problem fetching the results");
    }
}
?>

<div class="results">
    <?php if (count($results) > 0): ?>
        <div class="list-group-item">
            <?php foreach ($results as $r): ?>
                <div class="list-group-flush">
                    <span>
                    	 <span><h3><b><u> Account Information:</h3></b></u> </span>
	           </span>
		   <div class="list-group-item">
			<div><pre class="space">Account Number: <?php safer_echo($r["account_number"]) ?></pre></div>
                        <div><pre class="space">Account Type:   <?php safer_echo(getAccount($r["account_type"])) ?></pre></div>                   
                        <div><pre class="space">Balance:        $<?php safer_echo($r["balance"]);?></pre></div>
                       
			<a type="button" class = "btn btn-primary" href="transaction.php?type=<?php safer_echo("deposit");?>">Deposit</a>
			<a type="button" class = "btn btn-primary" href="transaction.php?type=<?php safer_echo("withdraw");?>">Withdraw</a>
			<a type="button" class = "btn btn-primary" href="transaction.php?type=<?php safer_echo("transfer");?>">Transfer</a>
			<a type="button" class = "btn btn-primary" href="ext_transaction.php?type=<?php safer_echo("ext_transfer");?>">External Transfer</a>
			<a type="button" class = "btn btn-primary" href="view_transactions.php?id=<?php safer_echo($r['id']);?>">Transactions</a>
                   </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No results</p>
    <?php endif; ?>
</div>
<?php require(__DIR__ . "/partials/flash.php"); ?>
