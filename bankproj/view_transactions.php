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
$id = -1;
if(isset($_GET["id"])){
    $id = $_GET["id"];
}
$results = [];
if (isset($id)) {
    $db = getDB();
    $stmt = $db->prepare("SELECT A1.account_number as Src, A2.account_number as Dest, expected_total, memo, T.action_type, T.amount from Transactions as T JOIN Accounts as A1 on A1.id = T.act_src_id JOIN Accounts as A2 on A2.id = T.act_dest_id WHERE T.act_src_id=:id LIMIT 5");
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
        <div class="row text-center title">
	  <div class="col">
	    Account Number (Source)
          </div>
	  <div class="col">
            Account Number (Dest)
          </div>
	  <div class="col">
            Transaction Type
          </div>
	  <div class="col">
            Change
          </div>
	  <div class="col">
            Memo
          </div>
	  <div class="col">
            Balance
          </div>
	  </div>

            <?php foreach ($results as $r): ?>
                <div class="row text-center">
                    <div class="col">
                        <div><?php safer_echo($r["Src"]); ?></div>
                    </div>
                    <div class="col">
                        <div><?php safer_echo($r["Dest"]); ?></div>
                    </div>
                    <div class="col">
		    <div> <?php safer_echo($r["action_type"]);?></div></div>
		    <div class="col">
                        <div><?php safer_echo($r["amount"]); ?></div>
                    </div>
		    <div class="col">
                           <div><?php safer_echo($r["memo"]); ?></div>
                       </div>
		    <div class="col">
                           <div><?php safer_echo($r["expected_total"]); ?></div>
                       </div>
                </div>
            <?php endforeach; ?>
    <?php else: ?>
        <p>No results</p>
    <?php endif; ?>
</div>
<?php require(__DIR__ . "/partials/flash.php"); ?>
