{* Added by Related Topics *}
{if !empty($C.related_topics)}
    <h1 class="bigheader">{$T.related_topics}</h1>
    <div class="tborder topic_table">
        <table class="table_grid mlist">
            <thead>
            <tr>
                {if !empty($C.related_topics)}
                    <th scope="col" class="blue_container smalltext first_th" style="width:8%;">&nbsp;</th>
                    <th scope="col" class="blue_container smalltext">{$T.subject} / {$T.started_by}</th>
                    <th scope="col" class="blue_container smalltext centertext" style="width:14%;">{$T.replies}</th>
                    <th scope="col" class="blue_container smalltext last_th" style="width:22%;">{$T.last_post}</th>
                {else}
                    <th scope="col" class="red_container smalltext first_th">&nbsp;</th>
                    <th class="smalltext red_container" colspan="3"><strong>{$T.msg_alert_none}</strong></th>
                    <th scope="col" class="red_container smalltext last_th" width="8%">&nbsp;</th>
                {/if}
            </tr>
            </thead>
            <tbody>
            {foreach from=$C.related_topics item=topic}
            {$color_class = 'gradient_darken_down'}
                <tr>
                    <td class="icon1 {$color_class}">
                        <img src="{$S.images_url}/topic/{$topic.class}.gif" alt="" />
                    </td>
                    <td class="subject {$color_class}">
                        <div {(!empty($topic.quick_mod.modify)) ? ("id=\"topic_{$topic.first_post.id}\" onmouseout=\"mouse_on_div = 0;\" onmouseover=\"mouse_on_div = 1;\" ondblclick=\"modify_topic('{$topic.id}', '{$topic.first_post.id}', '{$C.session_id}', '{$C.session_var}');\"") : ''}>
                            {($T.is_sticky) ? '<strong>' : ''}<span id="msg_{$topic.first_post.id}">{$topic.first_post.link}{($topic.board.can_approve_posts and $topic.approved == 0) ? "&nbsp;<em>({$T.awaiting_approval})</em>" : ''}</span>{($T.is_sticky) ? '</strong>' : ''}
                            {if $topic.new and $C.user.is_logged}
                                <a href="{$topic.new_href}" id="newicon{$topic.first_post.id}"><img src="{$S.images_url}/new.png" alt="{$T.new}" /></a>
                            {/if}
                            <p>{$T.started_by} {$topic.first_post.member.link}
                                <small id="pages{$topic.first_post.id}">{$topic.pages}</small>
                                <small>{$topic.board.link}</small>
                            </p>
                        </div>
                    </td>
                    <td style="padding:2px 5px;" class="nowrap stats {$color_class}">
                        {$topic.replies} {$T.replies}
                        <br>
                        {$topic.views} {$T.views}
                    </td>
                    <td class="lastpost {$color_class}">
                        {$T.by}: {$topic.last_post.member.link}
                        <br>
                        <a class="lp_link" title="{$T.last_post}" href="{$topic.last_post.href}">{$topic.last_post.time}</a>
                    </td>
                </tr>
            {/foreach}
        </table>
    </div>
    <br>
{/if} {* related topics *}
