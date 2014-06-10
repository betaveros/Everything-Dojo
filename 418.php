<?php
  $title = "418";
  include("dbc.php");
  get_header($title);
?>

<img class="teapot-img" src="data:image/png;base64,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" />
<h1 class="error-header">418 I'm a Teapot</h1>
<p class="error-explain">I'm a teapot. I can't make coffee. Anywho, you can try a couple of things:</p>
<ul class="error-action">
  <li>Brew some <a class="wikipedia-link" href="http://en.wikipedia.org/Tea">tea</a> instead of trying to make coffee. I'm a <em>teapot</em>, after all.
  <li><a class="back-link" href="javascript:history.back()">Go back</a> from whence you came!</li>
  <li>Go to the <a class="index-link" href="index.php">index</a>.</li>
  <li>Take a look at all the fancy-schmancy styles available to you on the <a class="db-link" href="themedb.php">Theme Database</a> page.</li>
  <li>Build your own theme with the <a class="themizer-link" href="themizer.php">Themizer</a>.</li>
  <li>If you want to test out a theme before using it on your blog, <a class="tryit-link" href="tryit.php">Try-it</a> is the place for you.</li>
  <li>Talk about stuff (like themes, HTML, and rants on 418 pages) on the <a class="discussion-link" href="discussion.php">fora</a> of this site.</li>
  <li>Report this page to us at <a class="email" href="mailto:error@everythingdojo.com">error@everythingdojo.com</a></li>
<ul>

<?php include('include/footer.php'); ?>