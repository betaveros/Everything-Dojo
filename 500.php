<?php
  $title = "500";
  include("include/include.php");
  get_header();
?>

<img class="internal-error-img" style="width:80vh" src="data:image/png;base64,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" />
<h1 class="error-header">500 Internal Server Error</h1>
<p class="error-explain">Something broke! AHHHHHHHHH! I think it was your second cousin's brother's grand-uncle's niece's seventh cousin twice removed's 7th cousin 7 times removed's great-great-grandparents. Anywho, you can try a couple of things:</p>
<ul class="error-action">
  <li><a class="back-link" href="javascript:history.back()">Go back</a> from whence you came!</li>
  <li>Go to the <a class="index-link" href="index.php">index</a>.</li>
  <li>Take a look at all the fancy-schmancy styles available to you on the <a class="db-link" href="themedb.php">Theme Database</a> page.</li>
  <li>Build your own theme with the <a class="themizer-link" href="themizer.php">Themizer</a>.</li>
  <li>If you want to test out a theme before using it on your blog, <a class="tryit-link" href="tryit.php">Try-it</a> is the place for you.</li>
  <li>Talk about stuff (like themes, HTML, and rants on 500 pages) on the <a class="discussion-link" href="discussion.php">fora</a> of this site.</li>
  <li>Report this page to us at <a class="email" href="mailto:error@everythingdojo.com">error@everythingdojo.com</a></li>
<ul>

<?php include('include/footer.php'); ?>
