<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You need to be logged in to access this page");
    die(header("Location: login.php"));
}
?>

<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>


<?php  //TODO Work on pagination attempt
$page = 1;
$per_page = 10;
if(isset($_GET["page"])){
    try {
        $page = (int)$_GET["page"];
    }
    catch(Exception $e){

    }
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
    $stmt = $db->prepare("SELECT A1.account_number as Src, A2.account_number as Dest, expected_total, memo, T.action_type, T.amount from Transactions as T JOIN Accounts as A1 on A1.id = T.act_src_id JOIN Accounts as A2 on A2.id = T.act_dest_id WHERE T.act_src_id=:id LIMIT 10");
    $r = $stmt->execute([":id" => $id]);
    if ($r) {
	$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
        flash("There was a problem fetching the results");
    }
}
?>

<table class="table table-bordered">
    <?php if (count($results) > 0): ?>
        <thead>
	 <tr class="text-center">
	   <th scope="col">Account Number (Source)</th>
           <th scope="col">Account Number (Dest)</th>
           <th scope="col">Transaction Type</th>
           <th scope="col">Change</th>
           <th scope="col">Memo</th>
           <th scope="col">Balance</th>
	  </tr>
	</thead>

            <?php foreach ($results as $r): ?>
                <tbody>
		  <tr>
                    <td class="text-center"><?php safer_echo($r["Src"]);?></td>
                    <td class="text-center"><?php safer_echo($r["Dest"]);?></td>
		    <td class="text-center"><?php safer_echo($r["action_type"]);?></td>
                    <td class="text-center"><?php safer_echo($r["amount"]);?></td>
                    <td class="text-center"><?php safer_echo($r["memo"]);?></td>
                    <td class="text-center"><?php safer_echo($r["expected_total"]);?></td>
                </tr>
            <?php endforeach; ?>
	  </tbody>
        </table>
    <?php else: ?>
        <p>No results</p>
    <?php endif; ?>

<p></p>
  <ul class="pagination pagination-lg">
    <li><a href="#">1</a></li>
    <li><a href="#">2</a></li>
    <li><a href="#">3</a></li>
    <li><a href="#">4</a></li>
    <li><a href="#">5</a></li>
  </ul>
<?php require(__DIR__ . "/partials/flash.php"); ?>
