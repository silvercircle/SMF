<?php
/*
 * this is outsourced from the Activities language file, because all these strings are only needed
 * in the user's profile secion. We avoid some language file bloat...
 */
$txt['act_optout_desc'] = 'Select the activity entries that should be created on your behalf. Any activity you uncheck here will prevent the activity stream from recording it. It will also stop other users from receiving notification from events done by you.<br><br>You will still receive notifications from events done by other users (see below for selecting the notification types you want to receive).';
$txt['notify_optout_desc'] = 'Select what types of notifications from activities by other forum members you want to receive.';

$txt['actdesc_like_given'] = 'Liking a post will create this type of activity and the post owner will receive a notification about this event.';
$txt['actdesc_new_topic'] = 'When you create a new topic, this type of activity will be recorded in the global activity stream.';
$txt['actdesc_new_reply'] = 'When you reply to a topic, this type of activity will be recorded and the topic owner will receive a notification.';
$txt['actdesc_modify_post'] = 'Activity type that is triggered when you modify a post.';
$txt['actdesc_pm_sent'] = 'When you send a personal message, this activity will be created and the PM receiver will get a notification. Note that this is a <strong>private</strong> activity type - nobody except you and the PM receiver can see it (not even administrators) and it will NOT reveal the PMs you send to others.';
$txt['actdesc_post_quoted'] = 'When you quote a message, the author of the quoted message will receive a notification about this type of activity.';
$txt['actdesc_user_tagged'] = 'When you "tag" (mention) one or more users in a message, these users will receive a notification. This is a private activity and will not appear in the global activity stream. It will only send notifications to the tagged members.';

$txt['ndesc_like_given'] = 'You will receive a notification when some other member liked a message posted by you.';
$txt['ndesc_new_reply'] = 'You will receive a notification when a reply is posted to a topic you have started.';
$txt['ndesc_pm_sent'] = 'You will receive a notification when someone sent you a new personal message.';
$txt['ndesc_post_quoted'] = 'You will receive a notification when someone quoted a message posted by you.';
$txt['ndesc_user_tagged'] = 'You will receive a notification when someone mentioned ("tagged") you in a message.';

$txt['showActivitiesSettings'] = 'Activities and notifications - Settings';
$txt['showActivitiesSettings_desc'] = 'Here you can customize the activity types that should be recorded from your forum activities as well as the notifications you want to receive.';
