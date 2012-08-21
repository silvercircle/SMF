{function topiclist}
{if isset($C.$id.title)}
  <h1 class="bigheader secondary indent">{$C.$id.title}</h1>
{/if}
<div class="pagelinks floatleft mediumpadding">
  {$C.$id.pages}
</div>
<div class="clear"></div>
{if !empty($C.$id.form)}
  {$form = $C.$id.form}
  <form method="post" accept-charset="UTF-8" id="{$form.id}" action="{$form.href}">
  {foreach $form.hidden_fields as $field}
    <input type="hidden" name="{$field.name}" value="{$field.value}" />
  {/foreach}
{/if}
<div class="framed_region">
  <table class="topic_table table_grid borderless">
  {if !empty($C.$id.items)}
    <thead>
      <tr class="mediumpadding">
      <th scope="col" colspan="2" class="first_th glass" style="width:8%;">&nbsp;</th>
      <th scope="col" class="lefttext nowrap glass"><a rel="nofollow" href="{$C.$id.baseurl};start={$C.start};sort=subject{($C.sort_by == 'subject' and $C.sort_direction == 'down') ? ';desc' : ''}">{$T.subject} {($C.sort_by == 'subject') ? (' <img src="'|cat:$C.images_url|cat:'/sort_'|cat:$C.sort_direction|cat:'.gif" alt="" />') : ''}</a> / {$T.started_by}</th>
      <th scope="col" class="nowrap glass"><a rel="nofollow" href="{$C.$id.baseurl};start={$C.start};sort=replies{($C.sort_by == 'replies' and $C.sort_direction == 'down') ? ';desc' : ''}">{$T.replies} {($C.sort_by == 'replies') ? (' <img src="'|cat:$S.images_url|cat:'/sort_'|cat:$C.sort_direction|cat:'.gif" alt="" />') : ''}</a> / <a href="{$C.$id.baseurl};start={$C.start};sort=views{($C.sort_by == 'views' and $C.sort_direction == 'down') ? ';desc' : ''}">{$T.views} {($C.sort_by == 'views') ? (' <img src="'|cat:$S.images_url|cat:'/sort_'|cat:$C.sort_direction|cat:'.gif" alt="" />') : ''}</a></th>
      <th scope="col" class="centertext nowrap glass"><a rel="nofollow" href="{$C.$id.baseurl};start={$C.start};sort=last_post{($C.sort_by == 'last_post' and $C.sort_direction == 'down') ? ';desc' : ''}">{$T.last_post} {($C.sort_by == 'last_post') ? (' <img src="'|cat:$S.images_url|cat:'/sort_'|cat:$C.sort_direction|cat:'.gif" alt="" />') : ''}</a></th>
      {if !empty($C.can_quick_mod)}
        <th scope="col" class="glass valign last_th" style="width:24px;"><input type="checkbox" class="input_check cb_invertall aligned" /></th>
      {/if}
  {else}
    <thead>
      <tr>
      <th class="red_container"><strong>{$T.msg_alert_none}</strong></th>
  {/if}
    </tr>
    </thead>
    {if !empty($C.unapproved_posts_message)}
      <tr class="windowbg2">
        <td colspan="{(!empty($C.can_quick_mod)) ? '6' : '5'}">
          <span class="alert">!</span>{$C.unapproved_posts_message}
        </td>
      </tr>
    {/if}
    {$C.alt_row = false}
    {foreach $C.$id.items as $topic}
      {call topicbit topic=$topic}
      {$C.alt_row = !$C.alt_row}
    {/foreach}
    </tbody>
  </table>
</div>
{if !empty($C.$id.form)}
  <div class="floatright" style="margin:10px 0;">
  <input type="submit" class="default" name="{$C.$id.form.submit_name}" value="{$C.$id.form.submit_label}" />
  <div class="clear"></div>
  </div>
</form>
{/if}
{/function}