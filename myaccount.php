<?php
  $title = "My Account";
  include("dbc.php");
  session_start();
  page_protect();
  get_header();
?>
  <h2>Welcome, <?php echo $_SESSION['user_name']; ?></h2>
  <p>This is the my account page. There is basically nothing here right now, but more is always to be added sometime.</p>
  <p>Your User ID: <?php echo $_SESSION['user_id']; ?></p>
  <div id="announcements">
    <?php
    $result = mysql_query("SELECT data FROM data WHERE fetchname = 'announcements'") or die(); 
    $announcements = explode("~", mysql_result($result, 0));
    if (empty($announcements[0])) {
      $title = "Announcements for Users from Admins: There are currently no announcements.";
      unset($announcements[0]);
    } elseif (count($announcements) == 1) {
      $title = "Announcement for Users from Admins";
    } else {
      $title = "Announcements for Users from Admins";
    }
    ?>
    <h4><?php echo $title; ?></h4>
    <?php
    if (count($announcements) > 0) {
      echo "<ul>";
      foreach ($announcements as $value) {
        echo "<li>".$value."</li>";
      }
      echo "</ul>";
    } ?>
  </div>
<?php get_footer(); ?>