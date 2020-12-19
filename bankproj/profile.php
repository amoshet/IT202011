<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
//Note: we have this up here, so our update happens before our get/fetch
//that way we'll fetch the updated data and have it correctly reflect on the form below
//As an exercise swap these two and see how things change
if (!is_logged_in()) {
    flash("You need to login first!");
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    die(header("Location: login.php"));
}

$db = getDB();
//preloads first and last name into profile
$stmt = $db->prepare("SELECT firstName, lastName from Users WHERE id = :id LIMIT 1");
            $stmt->execute([":id" => get_user_id()]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                $firstName = $result["firstName"];
		$lastName = $result["lastName"];
	    }
//save data if we submitted the form
if (isset($_POST["saved"])) {
    $newFirstName = $_POST["firstName"];
    $newLastName = $_POST["lastName"];
    $isValid = true;
    //check if our email changed
    $newEmail = get_email();
    if (get_email() != $_POST["email"]) {
        //TODO we'll need to check if the email is available
        $email = $_POST["email"];
        $stmt = $db->prepare("SELECT COUNT(1) as InUse from Users where email = :email");
        $stmt->execute([":email" => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $inUse = 1;//default it to a failure scenario
        if ($result && isset($result["InUse"])) {
            try {
                $inUse = intval($result["InUse"]);
            }
            catch (Exception $e) {

            }
        }
        if ($inUse > 0) {
            flash("Email is already in use");
            //for now we can just stop the rest of the update
            $isValid = false;
        }
        else {
            $newEmail = $email;
        }
    }
    $newUsername = get_username();
    if (get_username() != $_POST["username"]) {
        $username = $_POST["username"];
        $stmt = $db->prepare("SELECT COUNT(1) as InUse from Users where username = :username");
        $stmt->execute([":username" => $username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $inUse = 1;//default it to a failure scenario
        if ($result && isset($result["InUse"])) {
            try {
                $inUse = intval($result["InUse"]);
            }
            catch (Exception $e) {

            }
        }
        if ($inUse > 0) {
            flash("<br> Username is already in use");
            //for now we can just stop the rest of the update
            $isValid = false;
        }
        else {
            $newUsername = $username;
        }
    }

    if ($isValid) {
        $stmt = $db->prepare("UPDATE Users set email= :email, username= :username, firstName= :firstName, lastName= :lastName where id = :id");
        $r = $stmt->execute([":email" => $newEmail, ":username" => $newUsername, ":firstName" => $newFirstName, ":lastName" => $newLastName, ":id" => get_user_id()]);
        if ($r) {
            flash("Updated profile");
        }
        else {
            flash("Error updating profile");
        }
        //password is optional, so check if it's even set
        //if so, then check if it's a valid reset request
        if (!empty($_POST["password"]) && !empty($_POST["confirm"])) {
            if ($_POST["password"] == $_POST["confirm"]) {
                $password = $_POST["password"];
                $originalpass = $_POST["opw"];
		$stmt = $db->prepare("SELECT password from Users WHERE id = :id LIMIT 1");
		$ex = $stmt->execute([":id" => get_user_id()]);
		if ($ex)
		{
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$password_hash_from_db = $result["password"];
                if (password_verify($originalpass, $password_hash_from_db)) {  
                $hash = password_hash($password, PASSWORD_BCRYPT);
                //this one we'll do separate
                $stmt = $db->prepare("UPDATE Users set password = :password where id = :id");
                $r = $stmt->execute([":id" => get_user_id(), ":password" => $hash]);
                if ($r) {
                    flash("Reset password");
                }
                else {
                    flash("Error resetting password");
                }
		}
		}
            }
        }
//fetch/select fresh data in case anything changed
        $stmt = $db->prepare("SELECT email, username, firstName, lastName from Users WHERE id = :id LIMIT 1");
        $stmt->execute([":id" => get_user_id()]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $email = $result["email"];
            $username = $result["username"];
	    $firstName = $result["firstName"];
	    $lastName = $result["lastName"];
            //let's update our session too
            $_SESSION["user"]["email"] = $email;
            $_SESSION["user"]["username"] = $username;
        }
    }
    else {
        //else for $isValid, though don't need to put anything here since the specific failure will output the message
    }
}

require(__DIR__ . "/partials/flash.php");

?>

<form method="POST">
    <label for="email">Email</label>
    <input type="email" name="email" value="<?php safer_echo(get_email());?>"/>
    <label for="username">Username</label>
    <input type="text" maxlength="60" name="username" value="<?php safer_echo(get_username());?>"/>
    <label for="firstName">First Name</label>
    <input type="text" maxlength="30" name="firstName" value="<?php safer_echo($firstName);?>"/>
    <label for="lastName">Last Name</label>
    <input type="text" maxlength="30" name="lastName" value="<?php safer_echo($lastName);?>"/>
    <!-- DO NOT PRELOAD PASSWORD-->
    <label for="opw">Current Password</label>
    <input type="password" name="opw"/>
    <label for="pw">Password</label>
    <input type="password" name="password"/>
    <label for="cpw">Confirm Password</label>
    <input type="password" name="confirm"/>
    <input type="submit" name="saved" value="Save Profile"/>
</form>
