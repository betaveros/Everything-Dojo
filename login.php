<?php
include("include/include.php");

$table = TB_NAME;
$err = array();

foreach ($_GET as $key => $value) {
  $get[$key] = filter($value); //get variables are filtered.
}

if (isset($_POST['doLogin'])) {

  foreach ($_POST as $key => $value) {
    $data[$key] = filter($value); // post variables are filtered
  }

  $user_email = $data['username'];
  $pass = $data['pwd'];

  $result = $dbc->prepare("SELECT id, pwd, user_name, user_email, approved, user_level, logins_attempted FROM $table WHERE user_name = ?");
  $result->execute(array($user_email));
  $user_array = $result->fetchAll(PDO::FETCH_ASSOC);
  $username_match = count($user_array);
  $user_array = $user_array[0];
  $logins_attempted = $user_array['logins_attempted'];
  $last_attempted_login = (int)intval(explode("|", $logins_attempted)[1]);
  $pwd = $user_array['pwd'];

  // protect against brute forcing
  if ((intval(explode("|", $logins_attempted)[0]) < 10 && intval(explode("|", $logins_attempted)[0]) >= 5) && (time() - $last_attempted_login < 600)) {
    $err[] = "Sorry, but you have exceeded the brute force limit. Please wait " . (10 - floor((time() - $last_attempted_login)/60)) . " minute(s) before logging in again. Thank you."; // temporary until a more user-friendly solution is found
    $notification->insert_notification(6, $user_array['id'], $user_array['id']);
  } elseif (intval(explode("|", $logins_attempted)[0]) < 5 || (time() - $last_attempted_login >= 600)) { // time limit expired || user was not brute-forced
    // Match row found with more than 1 results  - the user is authenticated.
    if ($username_match > 0) {
      if (!$user_array['approved']) {
        $err[] = "Account not activated. Please contact the administrator to activate.";
      }

      //check against salt
      if (password_verify($pass, $pwd)) {
        if (empty($err)) {
          // this sets session and logs user in
          session_start();
          session_regenerate_id(TRUE); //prevent against session fixation attacks.
          // this sets variables in the session
          $_SESSION['user_id']= $user_array['id'];
          $_SESSION['user_name'] = $user_array['user_name'];
          $_SESSION['user_level'] = $user_array['user_level'];
          $_SESSION['HTTP_USER_AGENT'] = sha1($_SERVER['HTTP_USER_AGENT']);
          $_SESSION['REMOTE_ADDR'] = sha1($_SERVER['REMOTE_ADDR']);
          if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $_SESSION['HTTP_X_FORWARDED_FOR'] = sha1($_SERVER['HTTP_X_FORWARDED_FOR']);
          }

          //update the timestamp and key for cookie
          $stamp = time();
          $ckey = GenKey();
          $result = $dbc->prepare("UPDATE $table SET ctime = ?, ckey = ?, logins_attempted = 0 WHERE id = ?");
          $result->execute(array($stamp,$ckey,$user_array['id']));

          header("Location: myaccount.php");
        }
      } else {
        $err[] = "Invalid Login. Please check your username and/or password spelling and try again.";
        $attempts = intval(explode("|", $logins_attempted)[0]);
        $attempts++;
        $attempts = $attempts . "|" . time();
        $update_attempts = $dbc->prepare("UPDATE $table SET logins_attempted = :attempt WHERE user_name = :username");
        $update_attempts->execute(array(
          ":attempt" => $attempts,
          ":username" => $user_array['user_name']
        ));
      } //else if the password is bad

    } else {
      $err[] = "Invalid login. Please check your username and/or password spelling and try again.";
    } //else if the username wasn't found
  } elseif (intval(explode("|", $logins_attempted)[0]) >= 10) {
    $notification->insert_notification(6, $user_array['id'], $user_array['id']);

    $host = $_SERVER['HTTP_HOST'];
    $md5_id = md5($user_array['id'] . time());
    $activ_code = rand(1000, 9999);
    $deactivate = $dbc->prepare("UPDATE $table SET md5_id = :md5_id, activation_code = :activ_code, approved = 0 WHERE id = :id");
    $deactivate->execute(array(
      ":md5_id"     => $md5_id,
      ":activ_code" => $activ_code,
      ":id"         => $user_array['id']
    ));
    $message = "Hello,

Someone appears to have recently tried to hack into your account. As a result, we have deactivated your account for the time being.

To reactivate your account, please visit http://$host/activate.php and enter your email in the last field to receive an email containing your activation code.

We apologize for the inconvenience.

Administrator @ Everything Dojo
__________________________________________________________
This is an automated response. Do not reply to this email.";

    mail($user_array['user_email'], "Your account on http://$host may have been hacked", $message, "From: \"Everything Dojo Forgotbot\" <no-reply@$host>\r\n");
    header("Location: index.php?msg=This account has been disabled");

    exit();
  }

} //if the page is logging in
?>
<?php
  $title = "Login";
  //dbc was already got at the top; don't try again
  get_header(0);
?>
<section id="content">
  <?php //spit out all errors
  if (!empty($err)) {
    echo "<p id=\"errors\">";
    foreach ($err as $e) {
      echo "Error: ".$e."<br />";
    }
    echo "</p>";
  } ?>
  <p>Login in here! Don't have an account yet? Register <a href="register.php">here</a>. Forgot your password? Reset it <a href="forgot.php">here</a>. Registered, but haven't activated your account yet? Activate it <a href="activate.php">here</a>.</p>
  <form action="login.php" method="post" name="logForm" id="logForm">
    <label for="uname">Username</label>
    <input name="username" type="text" class="required" id="uname" size="25"><br />
    <label for="password">Password</label>
    <input name="pwd" type="password" class="required password" id="password" size="25"><br />
    <input name="doLogin" type="submit" value="Login">
  </form>
</section>

<?php get_footer(0); ?>
