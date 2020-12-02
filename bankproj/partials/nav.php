<?php
//we'll be including this on most/all pages so it's a good place to include anything else we want on those pages
require_once(__DIR__ . "/../lib/helpers.php");
?>
<link rel="stylesheet" href="<?php echo getURL("static/css/styles.css");?>"
<nav>
<ul class="nav">
    <li><a href="<?php echo getURL("home.php"); ?>">Home</a></li>   
<?php if (!is_logged_in()): ?>
        <li><a href="<?php echo getURL("login.php"); ?>">Login</a></li>
        <li><a href="<?php echo getURL("register.php"); ?>">Register</a></li>
    <?php endif; ?>
<?php if (has_role("Admin")): ?>
            <li><a href="<?php echo getURL("test/test_create_accounts.php"); ?>">Create Account</a></li>
            <li><a href="<?php echo getURL("test/test_list_accounts.php"); ?>">View Accounts</a></li>
        <?php endif; ?>
    <?php if (is_logged_in() && !has_role("Admin")): ?>
	<li><a href="<?php echo getURL("create_accounts.php"); ?>">Open an Account</a></li>
	<li><a href="<?php echo getURL("view_accounts.php"); ?>">Accounts</a></li>
    <?php endif; ?>
    <?php if (is_logged_in()): ?>
        <li><a href="<?php echo getURL("profile.php"); ?>">Profile</a></li>
        <li><a href="<?php echo getURL("logout.php"); ?>">Logout</a></li>
    <?php endif; ?>
</ul>
</nav>
