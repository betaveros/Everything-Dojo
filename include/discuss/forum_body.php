<?php
  $id = $_GET['f'];
  if (empty($id)) {
    $id = 1;
  } elseif($id == '') {
    redirect(URL_DISCUSS);
  } else {
    if (!isset($_SESSION['user_id'])) {
      $user_id = 0;
    } else {
      $user_id = $_SESSION['user_id'];
    }
    $topics = $discuss->get_topics(intval($id), $user_id);
  }

  if ($id == 1) {
    $typearg = 1;
  } else if ($id == 4) {
    if ($_SESSION['user_level'] < 3){
      echo "<meta http-equiv=\"refresh\" content=\"0;URL=/index.php\">";
    }
  } else {
    $typearg = 0;
  }
  $forum_data = $discuss->get_fora($id);
  $type = $forum_data['type'];
?>

<section id="discuss-topics" style="clear:left;">
  <h3 style="text-align: center;"><?php echo $forum_data['name']; ?></h3>
  <p style="text-align: center;"><?php echo $forum_data['description']; ?></p>
  <?php
  if ($user_id > 0 && intval($id) != 1) {
    echo "<a id=\"topic-a-topic\" style=\"left:5%;position:relative\">+ Create New Topic</a><br />";
    if (!empty($_SESSION['err'])) {
      echo '<div id="errors">';
      foreach ($_SESSION['err'] as $error) {
        echo '<p class="invalid">' . $error . '</p><br />';
      }
      echo '</div>';
    }
    unset($_SESSION['err']);
    $stickies = array();
    foreach($topics as $topic){
      if ($topic['type'] == 2){
        $stickies[] = $topic;
      }
    }
    ?>
  <fieldset id="topic-create-topic">
  <legend>Create new topic</legend>
  <form action="index.php" method="post" id="form">
    <div class="field">
      Title: <input type="text" name="title" value="" /><br/>
    </div>
    <div class="field">
      <div class="field split left">
        Message: <a href="https://help.github.com/articles/github-flavored-markdown" title="Github Flavored Markdown" style="color:#777;font-size:.8em;line-height:2em" target="_blank" tabindex="1">(Parsed with Github Flavored Markdown)</a>
        <br />
        <textarea name="desc-source" placeholder="Write your post here..." style="vertical-align:top; height:200px;"></textarea>
        <input type="hidden" name="desc" />
      </div>
      <div class="field split right">
        <div class="topic-text" name="preview"></div>
      </div>
    </div>
    <input type="button" value="Cancel" class="danger cancel" id="cancel" />
    <input type="button" value="Post" id="post" disabled />
    <input type="hidden" name="forum" value="<?php echo $id;?>" />
    <input type="hidden" name="mode" value="topic">
    <input type="submit" style="display:none" />
  </form>
  </fieldset>

  <script>
    $("#topic-create-topic").hide();
    $("#topic-a-topic").click(function(){
      $("#topic-a-topic").hide();
      $("#topic-create-topic").slideToggle(300);
    });
    $("#cancel").click(function () {
      $("#topic-a-topic").show();
      $("#topic-create-topic").slideToggle(300);
    });
    $("#post").click(function () {
      $("[name='desc']").val($("[name='preview']").html());
      $("#form").submit();
    })
  </script>
  <?php }
  if (!empty($stickies)){ ?>
    <table class="discuss-table" style="background-color: #fbff67;">
      <thead style="border-bottom: 1px black solid;">
        <tr>
          <td colspan="2">Sticky</td>
          <td class="med-col center">Author</td>
          <td class="small-col center">Comments</td>
          <td class="small-col center">Viewed By</td>
          <td class="med-col center">Last Post</td>
        <tr>
      </thead>
      <tbody>
  <?php foreach($stickies as $sticky){ ?>
        <tr class="topic <?php echo $sticky['read'] != 1 ? ' unread-topic' : ''; ?>" style="cursor:pointer;" onclick="window.location.href='<?php echo URL_DISCUSS; ?>?view=topic&f=<?php echo intval($id); ?>&t=<?php echo $sticky['topic_id']; ?>'">
          <td class="tiny-col"><p class="topic-icon"></p></td>
          <td><?php echo htmlspecialchars($sticky['title']); ?></td>
          <td class="center"><?php echo get_user($sticky['user_id']); ?></td>
          <td class="center"><?php echo (($type == 1) ? ($discuss->get_comment_count($sticky['topic_id'], $type) - 1) : ($discuss->get_comment_count($sticky['topic_id'], $type))); ?></td>
          <td class="center"><?php echo $discuss->get_views($sticky['topic_id'], 1 - $typearg); ?></td>
          <td class="center"><?php $lastpost = array_values($discuss->get_posts(intval($sticky['topic_id']), 'all', $typearg));
          if (empty($lastpost)) {
            echo date('M d, Y g:i a', $sticky['time'])."<br /><b>".get_user($sticky['user_id'])."</b>";
          } else {
            echo date('M d, Y g:i a', $lastpost[count($lastpost)-1]['time'])."<br /><b>".get_user($lastpost[count($lastpost)-1]['user_id'])."</b> ";
          } ?></td>
        </tr>
  <?php } ?>
      </tbody>
    </table>
  <?php }
  if ($_SESSION['user_id'] != 0) { ?>
  <br/>
  <a href="javascript:;" onClick="mark_all_read(<?php echo $id . ', ' . $_SESSION['user_id']; ?>)" style="left: 5%; position: relative;">Mark All Read</a>
  <?php } ?>
    <table class="discuss-table">
      <thead style="border-bottom: 1px black solid;">
        <tr>
          <td colspan="2">Topic</td>
          <td class="med-col center">Author</td>
          <td class="small-col center">Comments</td>
          <td class="small-col center">Viewed By</td>
          <td class="med-col center">Last Post</td>
        <tr>
      </thead>
      <tbody>
      <?php if (count($topics) == 0) { ?>
        <tr style="cursor:pointer; border-left-width: 0;">
          <td colspan="2">No topics</td>
          <td class="center">-</td>
          <td class="center">-</td>
          <td class="center">-</td>
          <td class="center">-</td>
        </tr>
      <?php } else {
      foreach ($topics as $topic) {
        $username = get_user($topic['user_id']);
        $comments = $discuss->get_comment_count($topic['topic_id'], $type);
        if ($type == 1) {
          $comments = $comments - 1;
        }
        ?>
        <tr class="topic<?php if ($topic['read'] != 1) echo ' unread=topic'; ?>" style="cursor:pointer;?>" onclick="window.location.href='<?php echo URL_DISCUSS; ?>?view=topic&f=<?php echo intval($id); ?>&t=<?php echo $topic['topic_id']; ?>'">
          <td class="tiny-col"><p class="topic-icon"></p></td>
          <td><?php echo htmlspecialchars($topic['title']); ?></td>
          <td class="center"><?php echo $username; ?></td>
          <td class="center"><?php echo $comments; ?></td>
          <td class="center"><?php echo $discuss->get_views($topic['topic_id'], 1 - $typearg); ?></td>
          <td class="center"><?php $lastpost = array_values($discuss->get_posts(intval($topic['topic_id']), 'all', $typearg));
          if (empty($lastpost)) {
            echo date('M d, Y g:i a', $topic['time'])."<br /><b>".get_user($topic['user_id'])."</b>";
          } else {
            echo date('M d, Y g:i a', $lastpost[count($lastpost)-1]['time'])."<br /><b>".get_user($lastpost[count($lastpost)-1]['user_id'])."</b> ";
          } ?>
          </td>
        </tr>
      <?php }
      } ?>
      </tbody>
    </table>
    <?php if ($_SESSION['user_id'] != 0) { ?>
    <br/>
    <a href="javascript:;" onClick="mark_all_read(<?php echo $id . ', ' . $_SESSION['user_id']; ?>)" style="left: 5%; position: relative;">Mark All Read</a>
    <?php } ?>
</section>
