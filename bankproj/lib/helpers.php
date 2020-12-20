
<?php
session_start();//we can start our session here so we don't need to worry about it on other pages
require_once(__DIR__ . "/db.php");
//this file will contain any helpful functions we create
//I have provided two for you
function is_logged_in() {
    return isset($_SESSION["user"]);
}

function has_role($role) {
    if (is_logged_in() && isset($_SESSION["user"]["roles"])) {
        foreach ($_SESSION["user"]["roles"] as $r) {
            if ($r["name"] == $role) {
                return true;
            }
        }
    }
    return false;
}

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
    $q = "UPDATE Accounts SET balance=(SELECT ifnull(SUM(amount), 0) as total from Transactions WHERE act_src_id=:id) WHERE id=:id";  
    $stmt = $db->prepare($q);
    $s = $stmt->execute([":id" => $accountid]);
}

function do_bank_action($account1, $account2, $amountChange, $type, $memo){
	$db = getDB();
	
	$a1total = getRealTimeBalance($account1);
	$a2total = getRealTimeBalance($account2); 
	$a1total += $amountChange;
	$a2total -= $amountChange; 
	$query = "INSERT INTO `Transactions` (`act_src_id`, `act_dest_id`, `amount`, `action_type`, `memo`, `expected_total`) VALUES(:p1a1, :p1a2, :p1change, :type, :memo, :a1total), (:p2a1, :p2a2, :p2change, :type, :memo, :a2total)";
	
	$stmt = $db->prepare($query);
	$stmt->bindValue(":p1a1", $account1);
	$stmt->bindValue(":p1a2", $account2);
	$stmt->bindValue(":p1change", $amountChange);
	$stmt->bindValue(":type", $type);
	$stmt->bindValue(":memo", $memo);
	$stmt->bindValue(":a1total", $a1total);
	//flip data for other half of transaction
	$stmt->bindValue(":p2a1", $account2);
	$stmt->bindValue(":p2a2", $account1);
	$stmt->bindValue(":p2change", ($amountChange*-1));
	$stmt->bindValue(":type", $type);
	$stmt->bindValue(":memo", $memo);
	$stmt->bindValue(":a2total", $a2total);
	$result = $stmt->execute();
	if($result){
	   updateBalance($account1);
	   updateBalance($account2);
	}
	//echo var_export($result, true);
	//echo var_export($stmt->errorInfo(), true);
	return $result;

}

function getWorldID(){
	$db = getDB();
	$q = "SELECT id from Accounts WHERE account_number='000000000000'";
	$stmt = $db->prepare($q);
        $s = $stmt->execute();
        $results = $stmt->fetch(PDO::FETCH_ASSOC);
	$worldID = $results["id"];
	
	return $worldID;
}

function getAccNum($id){
	$db = getDB();
	$stmt = $db->prepare("SELECT account_number from Accounts WHERE id=:q");
	$results = $stmt->execute([":q" => $id]);
	
	return $results["account_number"];
}

function get_username() {
    if (is_logged_in() && isset($_SESSION["user"]["username"])) {
        return $_SESSION["user"]["username"];
    }
    return "";
}

function get_email() {
    if (is_logged_in() && isset($_SESSION["user"]["email"])) {
        return $_SESSION["user"]["email"];
    }
    return "";
}

function get_user_id() {
    if (is_logged_in() && isset($_SESSION["user"]["id"])) {
        return $_SESSION["user"]["id"];
    }
    return -1;
}

function safer_echo($var) {
    if (!isset($var)) {
        echo "";
        return;
    }
    echo htmlspecialchars($var, ENT_QUOTES, "UTF-8");
}

function flash($msg) {
    if (isset($_SESSION['flash'])) {
        array_push($_SESSION['flash'], $msg);
    }
    else {
        $_SESSION['flash'] = array();
        array_push($_SESSION['flash'], $msg);
    }

}

function getMessages() {
    if (isset($_SESSION['flash'])) {
        $flashes = $_SESSION['flash'];
        $_SESSION['flash'] = array();
        return $flashes;
    }
    return array();
}
function getURL($path) {
    if (substr($path, 0, 1) == "/") {
        return $path;
    }
    return $_SERVER["CONTEXT_PREFIX"] . "/repo/bankproj/$path";
}

function getAccount($n){
    switch ($n) {
        case "checking":
            echo "Checking";
            break;
        case "savings":
            echo "Savings";
            break;
        case "loan":
            echo "Loan";
            break;
        case "world":
            echo "World";
            break;
        default:
            echo "Unsupported state: " . safer_echo($n);
            break;
        }
}


?>
