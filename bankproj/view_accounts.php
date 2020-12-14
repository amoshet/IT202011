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
        <div class="list-group">
            <?php foreach ($results as $r): ?>
                <div class="list-group-item">
                    <div>
                        <div>Account Number:</div>
                        <div><?php safer_echo($r["account_number"]); ?></div>
                    </div>
                    <div>
                        <div>Account Type:</div>
                        <div><?php getAccount($r["account_type"]); ?></div>
                    </div>
                    <div>
                        <div>Balance:</div>
                        <div><?php safer_echo($r["balance"]); ?></div>
                    </div>
                    <div>
                        <a type="button" href="#?>">Deposit</a>
			<a type="button" href="#?>">Withdraw</a>
			<a type="button" href="#?>"><br>Transfer</a>
                        <a type="button" href="view_accounts.php?id=<?php safer_echo($r['id']); ?>">Transactions</a>
                    </div> 
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No results</p>
    <?php endif; ?>
</div>
<?php require(__DIR__ . "/partials/flash.php"); ?>
