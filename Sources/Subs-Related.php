<?php
/**
 * Related Topics
 *
 * @package RelatedTopics
 * @version 1.4
 */

// Main funtions
function loadRelated($topic)
{
	global $modSettings, $context, $smcFunc;

	$context['can_approve_posts_boards'] = boardsAllowedTo('approve_posts', false);

	// Otherwise use customized fulltext index
	$request = $smcFunc['db_query']('', '
		SELECT IF(rt.id_topic_first = {int:topic}, rt.id_topic_second, rt.id_topic_first) AS id_topic
		FROM {db_prefix}related_topics AS rt
			JOIN {db_prefix}topics AS t ON (t.id_topic = IF(rt.id_topic_first = {int:topic}, rt.id_topic_second, rt.id_topic_first))
			JOIN {db_prefix}boards AS b ON (b.id_board = t.id_board)
		WHERE (id_topic_first = {int:topic} OR id_topic_second = {int:topic})
			AND {query_see_board}
		ORDER BY rt.score DESC
		LIMIT {int:limit}',
		array(
			'topic' => $topic,
			'limit' => $modSettings['relatedTopicsCount'],
		)
	);

	if ($smcFunc['db_num_rows']($request) == 0)
	{
		$smcFunc['db_free_result']($request);

		return false;
	}

	$topics_ids = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
		$topics_ids[] = $row['id_topic'];
	$smcFunc['db_free_result']($request);

	return prepareTopicArray($topics_ids);
}

function prepareTopicArray($topic_ids)
{
	global $scripturl, $txt, $db_prefix, $modSettings, $options, $context;
	global $user_info, $board_info, $settings, $smcFunc;

	// Setup the default topic icons...
	$stable_icons = array('xx', 'thumbup', 'thumbdown', 'exclamation', 'question', 'lamp', 'smiley', 'angry', 'cheesy', 'grin', 'sad', 'wink', 'moved', 'recycled', 'wireless');
	$context['icon_sources'] = array();
	foreach ($stable_icons as $icon)
		$context['icon_sources'][$icon] = 'images_url';

	if (empty($topic_ids))
		return false;
	
	$context['messages_per_page'] = empty($modSettings['disableCustomPerPage']) && !empty($options['messages_per_page']) && !WIRELESS ? $options['messages_per_page'] : $modSettings['defaultMaxMessages'];

	$result = $smcFunc['db_query']('substring', '
		SELECT
			t.id_topic, t.num_replies, t.locked, t.num_views, t.is_sticky, t.id_poll, t.id_previous_board,
			' . ($user_info['is_guest'] ? '0' : 'IFNULL(lt.id_msg, IFNULL(lmr.id_msg, -1)) + 1') . ' AS new_from,
			t.id_last_msg, t.approved, t.unapproved_posts, ml.poster_time AS last_poster_time,
			ml.id_msg_modified, ml.subject AS last_subject, ml.icon AS last_icon,
			ml.poster_name AS last_member_name, ml.id_member AS last_id_member,
			IFNULL(meml.real_name, ml.poster_name) AS last_display_name, t.id_first_msg,
			mf.poster_time AS first_poster_time, mf.subject AS first_subject, mf.icon AS first_icon,
			mf.poster_name AS first_member_name, mf.id_member AS first_id_member,
			IFNULL(memf.real_name, mf.poster_name) AS first_display_name, SUBSTRING(ml.body, 1, 385) AS last_body,
			SUBSTRING(mf.body, 1, 385) AS first_body, ml.smileys_enabled AS last_smileys,
			mf.smileys_enabled AS first_smileys, b.id_board, b.name As board_name
		FROM {db_prefix}topics AS t
			INNER JOIN {db_prefix}boards AS b ON (b.id_board = t.id_board)
			INNER JOIN {db_prefix}messages AS ml ON (ml.id_msg = t.id_last_msg)
			INNER JOIN {db_prefix}messages AS mf ON (mf.id_msg = t.id_first_msg)
			LEFT JOIN {db_prefix}members AS meml ON (meml.id_member = ml.id_member)
			LEFT JOIN {db_prefix}members AS memf ON (memf.id_member = mf.id_member)' . ($user_info['is_guest'] ? '' : '
			LEFT JOIN {db_prefix}log_topics AS lt ON (lt.id_topic = t.id_topic AND lt.id_member = {int:current_member})
			LEFT JOIN {db_prefix}log_mark_read AS lmr ON (lmr.id_board = t.id_board AND lmr.id_member = {int:current_member})'). '
		WHERE t.id_topic IN ({array_int:topic_list})
			AND (t.approved = {int:is_approved}' . ($user_info['is_guest'] ? '' : ' OR t.id_member_started = {int:current_member}') . ')',
		array(
			'current_member' => $user_info['id'],
			'topic_list' => $topic_ids,
			'is_approved' => 1,
		)
	);

	// Stolen from SMF
	while ($row = $smcFunc['db_fetch_assoc']($result))
	{
		if ($row['id_poll'] > 0 && $modSettings['pollMode'] == '0')
			continue;

		if (!empty($settings['message_index_preview']))
		{
			// Limit them to 128 characters - do this FIRST because it's a lot of wasted censoring otherwise.
			$row['first_body'] = strip_tags(strtr(parse_bbc($row['first_body'], $row['first_smileys'], $row['id_first_msg']), array('<br />' => '&#10;')));
			if ($smcFunc['strlen']($row['first_body']) > 128)
				$row['first_body'] = $smcFunc['substr']($row['first_body'], 0, 128) . '...';
			$row['last_body'] = strip_tags(strtr(parse_bbc($row['last_body'], $row['last_smileys'], $row['id_last_msg']), array('<br />' => '&#10;')));
			if ($smcFunc['strlen']($row['last_body']) > 128)
				$row['last_body'] = $smcFunc['substr']($row['last_body'], 0, 128) . '...';

			// Censor the subject and message preview.
			censorText($row['first_subject']);
			censorText($row['first_body']);

			// Don't censor them twice!
			if ($row['id_first_msg'] == $row['id_last_msg'])
			{
				$row['last_subject'] = $row['first_subject'];
				$row['last_body'] = $row['first_body'];
			}
			else
			{
				censorText($row['last_subject']);
				censorText($row['last_body']);
			}
		}
		else
		{
			$row['first_body'] = '';
			$row['last_body'] = '';
			censorText($row['first_subject']);

			if ($row['id_first_msg'] == $row['id_last_msg'])
				$row['last_subject'] = $row['first_subject'];
			else
				censorText($row['last_subject']);
		}

		// Decide how many pages the topic should have.
		$topic_length = $row['num_replies'] + 1;
		if ($topic_length > $context['messages_per_page'])
		{
			$tmppages = array();
			$tmpa = 1;
			for ($tmpb = 0; $tmpb < $topic_length; $tmpb += $context['messages_per_page'])
			{
				$tmppages[] = '<a href="' . $scripturl . '?topic=' . $row['id_topic'] . '.' . $tmpb . '">' . $tmpa . '</a>';
				$tmpa++;
			}
			// Show links to all the pages?
			if (count($tmppages) <= 5)
				$pages = '&#171; ' . implode(' ', $tmppages);
			// Or skip a few?
			else
				$pages = '&#171; ' . $tmppages[0] . ' ' . $tmppages[1] . ' ... ' . $tmppages[count($tmppages) - 2] . ' ' . $tmppages[count($tmppages) - 1];

			if (!empty($modSettings['enableAllMessages']) && $topic_length < $modSettings['enableAllMessages'])
				$pages .= ' &nbsp;<a href="' . $scripturl . '?topic=' . $row['id_topic'] . '.0;all">' . $txt['all'] . '</a>';
			$pages .= ' &#187;';
		}
		else
			$pages = '';

		// We need to check the topic icons exist...
		if (empty($modSettings['messageIconChecks_disable']))
		{
			if (!isset($context['icon_sources'][$row['first_icon']]))
				$context['icon_sources'][$row['first_icon']] = file_exists($settings['theme_dir'] . '/images/post/' . $row['first_icon'] . '.gif') ? 'images_url' : 'default_images_url';
			if (!isset($context['icon_sources'][$row['last_icon']]))
				$context['icon_sources'][$row['last_icon']] = file_exists($settings['theme_dir'] . '/images/post/' . $row['last_icon'] . '.gif') ? 'images_url' : 'default_images_url';
		}
		else
		{
			if (!isset($context['icon_sources'][$row['first_icon']]))
				$context['icon_sources'][$row['first_icon']] = 'images_url';
			if (!isset($context['icon_sources'][$row['last_icon']]))
				$context['icon_sources'][$row['last_icon']] = 'images_url';
		}

		$context['related_topics'][$row['id_topic']] = array(
			'id' => $row['id_topic'],
			'board' => array(
				'id' => $row['id_board'],
				'name' => $row['board_name'],
				'link' => '<a href="' .$scripturl . '?board=' . $row['id_board'] . '.0">' . $row['board_name'] . '</a>',
				'can_approve_posts' => in_array(0, $context['can_approve_posts_boards']) || in_array($row['id_board'], $context['can_approve_posts_boards']),
			),
			'first_post' => array(
				'id' => $row['id_first_msg'],
				'member' => array(
					'username' => $row['first_member_name'],
					'name' => $row['first_display_name'],
					'id' => $row['first_id_member'],
					'href' => !empty($row['first_id_member']) ? $scripturl . '?action=profile;u=' . $row['first_id_member'] : '',
					'link' => !empty($row['first_id_member']) ? '<a href="' . $scripturl . '?action=profile;u=' . $row['first_id_member'] . '" title="' . $txt['profile_of'] . ' ' . $row['first_display_name'] . '">' . $row['first_display_name'] . '</a>' : $row['first_display_name']
				),
				'time' => timeformat($row['first_poster_time']),
				'timestamp' => forum_time(true, $row['first_poster_time']),
				'subject' => $row['first_subject'],
				'preview' => $row['first_body'],
				'icon' => $row['first_icon'],
				'icon_url' => $settings[$context['icon_sources'][$row['first_icon']]] . '/post/' . $row['first_icon'] . '.gif',
				'href' => $scripturl . '?topic=' . $row['id_topic'] . '.0',
				'link' => '<a href="' . $scripturl . '?topic=' . $row['id_topic'] . '.0">' . $row['first_subject'] . '</a>'
			),
			'last_post' => array(
				'id' => $row['id_last_msg'],
				'member' => array(
					'username' => $row['last_member_name'],
					'name' => $row['last_display_name'],
					'id' => $row['last_id_member'],
					'href' => !empty($row['last_id_member']) ? $scripturl . '?action=profile;u=' . $row['last_id_member'] : '',
					'link' => !empty($row['last_id_member']) ? '<a href="' . $scripturl . '?action=profile;u=' . $row['last_id_member'] . '">' . $row['last_display_name'] . '</a>' : $row['last_display_name']
				),
				'time' => timeformat($row['last_poster_time']),
				'timestamp' => forum_time(true, $row['last_poster_time']),
				'subject' => $row['last_subject'],
				'preview' => $row['last_body'],
				'icon' => $row['last_icon'],
				'icon_url' => $settings[$context['icon_sources'][$row['last_icon']]] . '/post/' . $row['last_icon'] . '.gif',
				'href' => $scripturl . '?topic=' . $row['id_topic'] . ($row['num_replies'] == 0 ? '.0' : '.msg' . $row['id_last_msg']) . '#new',
				'link' => '<a href="' . $scripturl . '?topic=' . $row['id_topic'] . ($row['num_replies'] == 0 ? '.0' : '.msg' . $row['id_last_msg']) . '#new" ' . ($row['num_replies'] == 0 ? '' : 'rel="nofollow"') . '>' . $row['last_subject'] . '</a>'
			),
			'is_sticky' => !empty($modSettings['enableStickyTopics']) && !empty($row['is_sticky']),
			'is_locked' => !empty($row['locked']),
			'is_poll' => $modSettings['pollMode'] == '1' && $row['id_poll'] > 0,
			'is_hot' => $row['num_replies'] >= $modSettings['hotTopicPosts'],
			'is_very_hot' => $row['num_replies'] >= $modSettings['hotTopicVeryPosts'],
			'is_posted_in' => false,
			'icon' => $row['first_icon'],
			'icon_url' => $settings[$context['icon_sources'][$row['first_icon']]] . '/post/' . $row['first_icon'] . '.gif',
			'subject' => $row['first_subject'],
			'new' => $row['new_from'] <= $row['id_msg_modified'],
			'new_from' => $row['new_from'],
			'newtime' => $row['new_from'],
			'new_href' => $scripturl . '?topic=' . $row['id_topic'] . '.msg' . $row['new_from'] . '#new',
			'pages' => $pages,
			'replies' => $row['num_replies'],
			'views' => $row['num_views'],
			'approved' => $row['approved'],
			'unapproved_posts' => $row['unapproved_posts'],
		);

		determineTopicClass($context['related_topics'][$row['id_topic']]);
	}
	$smcFunc['db_free_result']($result);
}

// Helper functions
function initRelated()
{
	global $context, $modSettings;

	// Already initialized?
	if (isset($context['relatedClass']))
		return true;
	
	$context['rt_ignore'] = empty($modSettings['relatedIgnoredboards']) ? array() : explode(',', $modSettings['relatedIgnoredboards']);

	// Recycle board should be ignored by default
	if (!empty($modSettings['recycle_enable']) && !empty($modSettings['recycle_board']))
		$context['rt_ignore'][] = $modSettings['recycle_board'];
	
	// Make sure each board is only one time there
	$context['rt_ignore'] = array_unique($context['rt_ignore']);

	// No methods selected? No need to do this then
	if (empty($modSettings['relatedIndex']))
		return false;

	$context['relatedClass'] = array();

	$relatedIndexes = explode(',', $modSettings['relatedIndex']);

	foreach ($relatedIndexes as $indexType)
	{
		$indexType = ucwords($modSettings['relatedIndex']);
		$class = 'RelatedTopics' . $indexType;
		loadClassFile('Subs-Related' . $indexType . '.php');
		$context['relatedClass'][] = new $class;
	}

	if (empty($context['relatedClass']))
		return false;

	return true;
}

// Update related topics
function relatedUpdateTopics($topics, $dont_remove = false)
{
	global $context;
	
	// Make sure $topics is array
	if (!is_array($topics))
		$topics = array((int) $topics);
		
	if (empty($topics))
		return;
		
	if (!isset($context['relatedClass']) && !initRelated())
		return;
	
	// First remove old related topics
	if (!$dont_remove)
		relatedRemoveTopics($topics, true);
	
	foreach ($context['relatedClass'] as $class)
		$class->updateTopics($topics);
}

// Remove related topics
function relatedRemoveTopics($topics, $only_relations = false)
{
	global $context, $smcFunc;

	// Make sure $topics is array
	if (!is_array($topics))
		$topics = array((int) $topics);
		
	if (empty($topics))
		return;
	
	// Remove topics from handlers only if its really going to be removed
	if ($only_relations)
	{
		if (!isset($context['relatedClass']) && !initRelated())
			return;
		
		foreach ($context['relatedClass'] as $class)
			$class->removeTopics($topics);
	}
		
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}related_topics
		WHERE id_topic_first IN({array_int:topics}) OR id_topic_second IN ({array_int:topics})',
		array(
			'topics' => $topics,
		)
	);
	
	return true;
}

// Add relation to database (called from related topic handlers)
function relatedAddRelatedTopic($topics, $source = '')
{
	global $smcFunc;
	
	if (empty($topics))
		return false;
	
	$rows = array();
	
	foreach ($topics as $id_topic_1)
	{
		list ($id_topic_1, $id_topic_2, $score) = $id_topic_1;
		
		$rows[] = array(min($id_topic_1, $id_topic_2), max($id_topic_1, $id_topic_2), $score);
	}

	$smcFunc['db_insert']('replace',
		'{db_prefix}related_topics',
		array('id_topic_first' => 'int', 'id_topic_second' => 'int', 'score' => 'float',),
		$rows,
		array('id_topic_first', 'id_topic_second')
	);
}

?>