<?php
/**
 * generic activity stream strings
 */
$txt['activity_format_member_intro'] = '<a href="@SCRIPTURL@?action=profile;u=%s">%s</a> ';
$txt['activity_format_member_intro_you'] = 'You ';
$txt['act_recent_board'] = 'Recent activity for: %s';
$txt['act_recent_global'] = 'Recent site activity';
$txt['act_no_results'] = 'The activity stream for the current context is empty';
$txt['act_no_results_title'] = 'No results';
$txt['act_recent_notifications'] = 'Recent notifications';
$txt['act_no_unread_notifications'] = 'No unread notifications';
$txt['act_mark_all_read'] = 'Mark all as seen';
$txt['act_view_all'] = 'View all';
$txt['activities_label'] = 'Activities';
$txt['notifications_label'] = 'Notifications';
$txt['unknown activity stream type'] = 'Unknown activity stream type';
$txt['activity_missing_format'] = 'Missing activity formatting string (atype = %d)';
$txt['activity_missing_callback'] = 'Invalid or missing formatter callback for atype = %d';

/**
 * format activities
 * key is composed of:
 * a) a constant acfmt_
 * b) the content of activity_types.id_desc column
 * c) a _
 * d) a numeric entry that corresponds to one of the activity_types.f_* columns.
 * e) f_neutral  (a neutral activity - you're neither the content owner nor the user who did something on the content)
 *    f_you      (you did something - e.g. you liked a post, you replied to a topic, you reported a post).
 *    f_your     (someone else did something on a piece of conted owned by you - e.g. someone liked one of your posts).
 *    f_you_your (you did something on content owned by you - e.g. you replied to a thread that was started by you).
 *
 *    not all 4 *must* exist for each type - multiple f_* columns can refer to the same formatting string as long as it
 *    makes sense.
 *
 * activity_types.formatter holds the function name of the formatting function. It is actfmt_default() for most stock
 * activity types, but mods could provide their own functions. A special hook will provide the ability to include new
 * formatting functions (as of now, unimplemented, will come with the hook overhaul).
 *
 * %foo$s placeholder refer to array keys in log_activities.params and _vsprintf() in Subs-Activities.php deals with the actual
 * formatting.
 *
 * meaning of class="_m": this link, when clicked, will mark the notification as read when using the inline notification
 * popup.
 * also, @SCRIPTURL@ is always replaced by $scripturl to construct valid links and @URL_MEMBER@ is a shortcut for the
 * URL of the member who created the activity. @NM@ will be replaced by the ;nmdismiss action that triggers the mark
 * seen event for the notification.
 */

$txt['acfmt_like_given_1_a'] = '<a class="_m" href="@SCRIPTURL@?topic=%id_topic$s.msg%id_content$s#msg%id_content$s">A post</a> in <a href="@SCRIPTURL@?topic=%id_topic$s">%topic_title$s</a> has been rated with <strong>%rating_type$s</strong>';
$txt['acfmt_like_given_2_a'] = 'You rated <a class="_m" href="@SCRIPTURL@?topic=%id_topic$s.msg%id_content$s#msg%id_content$s">a post</a> with <strong>%rating_type$s</strong> in <a href="@SCRIPTURL@?topic=%id_topic$s">%topic_title$s</a>';
$txt['acfmt_like_given_3_a'] = '<a class="_m" href="@SCRIPTURL@?topic=%id_topic$s.msg%id_content$s@NM@#msg%id_content$s">Your post</a> in <a href="@SCRIPTURL@?topic=%id_topic$s">%topic_title$s</a> has been rated with <strong>%rating_type$s</strong>';

$txt['acfmt_like_given_1'] = '<a href="@URL_MEMBER@">%member_name$s</a> rated <a class="_m" href="@SCRIPTURL@?topic=%id_topic$s.msg%id_content$s#msg%id_content$s">a post</a> with <strong>%rating_type$s</strong> in <a href="@SCRIPTURL@?topic=%id_topic$s">%topic_title$s</a>';
$txt['acfmt_like_given_2'] = 'You rated <a class="_m" href="@SCRIPTURL@?topic=%id_topic$s.msg%id_content$s#msg%id_content$s">a post</a> with <strong>%rating_type$s</strong> in <a href="@SCRIPTURL@?topic=%id_topic$s">%topic_title$s</a>';
$txt['acfmt_like_given_3'] = '<a href="@URL_MEMBER@">%member_name$s</a> rated <a class="_m" href="@SCRIPTURL@?topic=%id_topic$s.msg%id_content$s@NM@#msg%id_content$s">your post</a> with <strong>%rating_type$s</strong> in <a href="@SCRIPTURL@?topic=%id_topic$s">%topic_title$s</a>';

$txt['acfmt_new_topic_4'] = '<a href="@URL_MEMBER@">%member_name$s</a> posted a <a class="_m" href="@SCRIPTURL@?topic=%id_topic$s">new topic</a> in <a href="@SCRIPTURL@?board=%id_board$s">%board_name$s</a>';
$txt['acfmt_new_topic_5'] = 'You posted a <a href="@SCRIPTURL@?topic=%id_topic$s">new topic</a> in <a href="@SCRIPTURL@?board=%id_board$s">%board_name$s</a>';

$txt['acfmt_new_reply_6'] = '<a href="@URL_MEMBER@">%member_name$s</a> <a class="_m" href="@SCRIPTURL@?topic=%id_topic$s.msg%id_content$s@NM@#msg%id_content$s">replied</a> to <a href="@SCRIPTURL@?topic=%id_topic$s">%topic_title$s</a> in <a href="@SCRIPTURL@?board=%id_board$s">%board_name$s</a>';
$txt['acfmt_new_reply_7'] = 'You <a class="_m" href="@SCRIPTURL@?topic=%id_topic$s.msg%id_content$s@NM@#msg%id_content$s">replied</a> to <a href="@SCRIPTURL@?topic=%id_topic$s">%topic_title$s</a> in <a href="@SCRIPTURL@?board=%id_board$s">%board_name$s</a>';
$txt['acfmt_new_reply_8'] = '<a href="@URL_MEMBER@">%member_name$s</a> <a class="_m" href="@SCRIPTURL@?topic=%id_topic$s.msg%id_content$s@NM@#msg%id_content$s">replied</a> to <a href="@SCRIPTURL@?topic=%id_topic$s">your topic</a> in <a href="@SCRIPTURL@?board=%id_board$s">%board_name$s</a>';
$txt['acfmt_new_reply_9'] = 'You <a class="_m" href="@SCRIPTURL@?topic=%id_topic$s.msg%id_content$s@NM@#msg%id_content$s">replied</a> to <a href="@SCRIPTURL@?topic=%id_topic$s">your topic</a> in <a href="@SCRIPTURL@?board=%id_board$s">%board_name$s</a>';

$txt['acfmt_modify_post_1'] = '<a href="@URL_MEMBER@">%member_name$s</a> modified <a class="_m" href="@SCRIPTURL@?topic=%id_topic$s.msg%id_content$s#msg%id_content$s">a post</a> in <a href="@SCRIPTURL@?topic=%id_topic$s">%topic_title$s</a>';
$txt['acfmt_modify_post_2'] = 'You modified <a class="_m" href="@SCRIPTURL@?topic=%id_topic$s.msg%id_content$s#msg%id_content$s">a post</a> in <a href="@SCRIPTURL@?topic=%id_topic$s">%topic_title$s</a>';
$txt['acfmt_modify_post_3'] = '<a href="@URL_MEMBER@">%member_name$s</a> modified <a class="_m" href="@SCRIPTURL@?topic=%id_topic$s.msg%id_content$s#msg%id_content$s">your post</a> in <a href="@SCRIPTURL@?topic=%id_topic$s">%topic_title$s</a>';
$txt['acfmt_modify_post_4'] = 'You modified <a class="_m" href="@SCRIPTURL@?topic=%id_topic$s.msg%id_content$s#msg%id_content$s">your post</a> in <a href="@SCRIPTURL@?topic=%id_topic$s">%topic_title$s</a>';

$txt['acfmt_signed_up_10'] = '<a class="_m" href="@URL_MEMBER@@NM@">%member_name$s</a> signed up as a new member';

$txt['acfmt_pm_sent_1'] = '<a href="@URL_MEMBER@">%member_name$s</a> has sent a new <a class="_m" href="@SCRIPTURL@?action=pm;pmid=%id_content$s;kstart;f=inbox;start=0@NM@#msg%id_content$s">private message</a>';
$txt['acfmt_pm_sent_2'] = 'You have sent a new <a href="@SCRIPTURL@?action=pm;pmid=%id_content$s;kstart;f=sent;start=0@NM@#msg%id_content$s">private message</a>';
$txt['acfmt_pm_sent_3'] = '';
$txt['acfmt_pm_sent_4'] = '';

$txt['acfmt_post_quoted_1'] = '<a href="@URL_MEMBER@">%member_name$s</a> quoted <a class="_m" href="@SCRIPTURL@?topic=%id_topic$s.msg%id_content$s#msg%id_content$s">a post</a> by <a href="@URL_OWNER@">%owner_name$s</a> in <a href="@SCRIPTURL@?topic=%id_topic$s">%topic_title$s</a>';
$txt['acfmt_post_quoted_2'] = 'You quoted <a class="_m" href="@SCRIPTURL@?topic=%id_topic$s.msg%id_content$s#msg%id_content$s">a post</a> by <a href="@URL_OWNER@">%owner_name$s</a> in <a href="@SCRIPTURL@?topic=%id_topic$s">%topic_title$s</a>';
$txt['acfmt_post_quoted_3'] = '<a href="@URL_MEMBER@">%member_name$s</a> quoted <a class="_m" href="@SCRIPTURL@?topic=%id_topic$s.msg%id_content$s#msg%id_content$s">your post</a> in <a href="@SCRIPTURL@?topic=%id_topic$s">%topic_title$s</a>';

$txt['acfmt_user_tagged_1'] = 'You were mentioned in <a class="_m" href="@SCRIPTURL@?topic=%id_topic$s.msg%id_content$s@NM@#msg%id_content$s">a message</a> posted by <a href="@URL_MEMBER@">%member_name$s</a>.';

