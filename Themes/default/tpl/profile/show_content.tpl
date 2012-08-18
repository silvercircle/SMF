<br>
<h1 class="bigheader secondary indent title">
  {(!isset($C.attachments) and empty($C.is_topics)) ? $T.showMessages : ((!empty($C.is_topics)) ? $T.showTopics : $T.showAttachments)} {$T.by} {$C.member.name}
 </h1>
<br>
{if $C.results_counter}
  <div class="pagelinks">
    {$C.page_index}</span>
  </div>
{/if}
{if !isset($C.attachments)}
  {if $C.is_topics}
    <table class="topic_table mlist" style="width:100%;">
      <thead>
      <tr class="mediumpadding" style="margin:2px;">
        <th scope="col" class="glass first_th" style="width:8%;" colspan="2">&nbsp;</th>
        <th scope="col" class="glass lefttext">{$T.subject}</th>
        <th scope="col" class="glass nowrap">{$T.replies}</th>
        <th scope="col" class="glass centertext nowrap">{$T.last_post}</th>
      </tr>
      </thead>
      <tbody>
      {if !empty($C.topics)}
        {foreach $C.topics as $topic}
          {call topicbit topic=$topic}
        {/foreach}
      {else}
        <tr>
          <td colspan="5" class="windowbg centertext">',$txt['member_has_no_topics'],'</td>
        </tr>
      {/if}
      </tbody>
    </table>
  {else}
    echo '
      <div class="posts_container">';
    foreach ($context['posts'] as &$post)
      $context['postbit_callback']($post);
    echo '
      </div>';
  {/if}
{else}
  {
    echo '
    <table class="table_grid mlist" style="width:100%;">
      <thead>
        <tr>
          <th class="glass lefttext" scope="col" style="width:25%;">
            <a href="', $scripturl, '?action=profile;u=', $context['current_member'], ';area=showposts;sa=attach;sort=filename', ($context['sort_direction'] == 'down' && $context['sort_order'] == 'filename' ? ';asc' : ''), '">
              ', $txt['show_attach_filename'], '
              ', ($context['sort_order'] == 'filename' ? '<img src="' . $settings['images_url'] . '/sort_' . ($context['sort_direction'] == 'down' ? 'down' : 'up') . '.gif" alt="" />' : ''), '
            </a>
          </th>
          <th class="glass" scope="col" style="width:12%;">
            <a href="', $scripturl, '?action=profile;u=', $context['current_member'], ';area=showposts;sa=attach;sort=downloads', ($context['sort_direction'] == 'down' && $context['sort_order'] == 'downloads' ? ';asc' : ''), '">
              ', $txt['show_attach_downloads'], '
              ', ($context['sort_order'] == 'downloads' ? '<img src="' . $settings['images_url'] . '/sort_' . ($context['sort_direction'] == 'down' ? 'down' : 'up') . '.gif" alt="" />' : ''), '
            </a>
          </th>
          <th class="glass lefttext" scope="col" style="width:30%;">
            <a href="', $scripturl, '?action=profile;u=', $context['current_member'], ';area=showposts;sa=attach;sort=subject', ($context['sort_direction'] == 'down' && $context['sort_order'] == 'subject' ? ';asc' : ''), '">
              ', $txt['message'], '
              ', ($context['sort_order'] == 'subject' ? '<img src="' . $settings['images_url'] . '/sort_' . ($context['sort_direction'] == 'down' ? 'down' : 'up') . '.gif" alt="" />' : ''), '
            </a>
          </th>
          <th class="glass last_th lefttext" scope="col">
            <a href="', $scripturl, '?action=profile;u=', $context['current_member'], ';area=showposts;sa=attach;sort=posted', ($context['sort_direction'] == 'down' && $context['sort_order'] == 'posted' ? ';asc' : ''), '">
            ', $txt['show_attach_posted'], '
            ', ($context['sort_order'] == 'posted' ? '<img src="' . $settings['images_url'] . '/sort_' . ($context['sort_direction'] == 'down' ? 'down' : 'up') . '.gif" alt="" />' : ''), '
            </a>
          </th>
        </tr>
      </thead>
      <tbody>';

    // Looks like we need to do all the attachments instead!
    $alternate = false;
    foreach ($context['attachments'] as $attachment)
    {
      echo '
        <tr class="', $attachment['approved'] ? ($alternate ? 'windowbg' : 'windowbg2') : 'approvebg', '">
          <td><a href="', $scripturl, '?action=dlattach;topic=', $attachment['topic'], '.0;attach=', $attachment['id'], '">', $attachment['filename'], '</a>', !$attachment['approved'] ? '&nbsp;<em>(' . $txt['awaiting_approval'] . ')</em>' : '', '</td>
          <td align="center">', $attachment['downloads'], '</td>
          <td><a href="', $scripturl, '?topic=', $attachment['topic'], '.msg', $attachment['msg'], '#msg', $attachment['msg'], '" rel="nofollow">', $attachment['subject'], '</a></td>
          <td>', $attachment['posted'], '</td>
        </tr>';
      $alternate = !$alternate;
    }

  // No posts? Just end the table with a informative message.
  if ((isset($context['attachments']) && empty($context['attachments'])) || (!isset($context['attachments']) && empty($context['posts'])))
    echo '
        <tr>
          <td class="tborder windowbg2 padding centertext" colspan="4">
            ', isset($context['attachments']) ? $txt['show_attachments_none'] : ($context['is_topics'] ? $txt['show_topics_none'] : $txt['show_posts_none']), '
          </td>
        </tr>';

    echo '
      </tbody>
    </table>';
  }
  // Show more page numbers.
  if($context['results_counter'])
    echo '
    <div class="pagelinks" style="margin-bottom: 0;">
      <span>', $txt['pages'], ': ', $context['page_index'], '</span>
    </div>';
}
