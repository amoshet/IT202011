<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!(is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You need to be logged in to access this page");
    die(header("Location: login.php"));
}
?>

<?php
//fetching
$id = get_user_id();
$result = [];
if (isset($id)) {
    $db = getDB();
    $stmt = $db->prepare("SELECT id, account_number, account_type, balance FROM Accounts WHERE user.id = :id LIMIT 5");
    $r = $stmt->execute([":id" => $id]);
    if ($r) {
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
        flash("There was a problem fetching the results");
    }
}
?>
<div class="result">
    <div class="card-title">
        <?php safer_echo($result["account_number"]); ?>
    </div>
    <div class="card-body">
        <div>
            <p>Stats</p>
            <div>Account Type: <?php safer_echo($result["account_type"]); ?></div>
            <div>Balance: <?php safer_echo($result["balance"]); ?></div>
            <div>Owned by: <?php safer_echo($result["username"]); ?></div>
        </div>
    </div>
</div>
<?php else: ?>
    <p>Error looking up id...</p>
<?php endif; ?>
<?php require(__DIR__ . "/partials/flash.php"); ?>
