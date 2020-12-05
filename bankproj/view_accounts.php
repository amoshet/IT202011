<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You must be logged in to access this page");
    die(header("Location: login.php"));
}
$check = true;
$results = [];

if ($check) {
    $db = getDB();
    $user = get_user_id();
    $stmt = $db->prepare("SELECT id, account_number, account_type, balance from Accounts WHERE user_id=:user LIMIT 10");
    $r = $stmt->execute([":user"=> $user ]);
    if ($r) {
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
        flash("There was a problem fetching the results");
    }
}
?>
<div class="results">
    <?php if(count($results) > 0): ?>
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
                        <div>Balance</div>
                        <div><?php safer_echo($r["balance"]); ?></div>
                    </div>
                        <div>
                            <a href="view_transactions.php?id=<?php safer_echo($r['id']); ?>">View Transaction History</a>
                        </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No results</p>
    <?php endif; ?>
</div>

<?php require(__DIR__ . "/partials/flash.php");?>
