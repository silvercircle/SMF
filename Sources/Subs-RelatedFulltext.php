<?php
/**
 * Related Topics
 *
 * @package RelatedTopics
 * @version 1.4
 */

class RelatedTopicsFulltext
{
	function recreateIndexTables()
	{
		global $smcFunc, $db_prefix;

		$smcFunc['db_query']('', '
			DROP TABLE IF EXISTS ' . $db_prefix . 'related_subjects',
			array('security_override' => true)
		);

		$smcFunc['db_query']('', '
			CREATE TABLE IF NOT EXISTS ' . $db_prefix . 'related_subjects (
				id_topic int(10) unsigned NOT NULL,
				subject tinytext NOT NULL,
				PRIMARY KEY (id_topic),
				FULLTEXT KEY subject (subject)
			)',
			array('security_override' => true)
		);

		return true;
	}
	
	function updateTopics($topics)
	{
		global $context, $smcFunc;
		
		if (empty($topics))
			return;

		// Get subject from database as we need it
		$request = $smcFunc['db_query']('', '
			SELECT t.id_topic, mf.subject
			FROM {db_prefix}topics AS t
				INNER JOIN {db_prefix}messages AS mf ON (mf.id_msg = t.id_first_msg)
			WHERE t.id_topic IN({array_int:topics})' . (!empty($context['rt_ignore']) ? '
				AND t.id_board NOT IN({array_int:ignored})' : ''),
			array(
				'topics' => $topics,
				'ignored' => $context['rt_ignore'],
			)
		);
		
		$rows = array();
	
		while ($row = $smcFunc['db_fetch_assoc']($request))
			$rows[] = array($row['id_topic'], $row['subject']);
		$smcFunc['db_free_result']($request);
		
		if (empty($rows))
			return true;
		
		// Insert to cache
		$smcFunc['db_insert']('replace',
			'{db_prefix}related_subjects',
			array(
				'id_topic' => 'int',
				'subject' => 'string-255'
			),
			$rows,
			array('id_topic')
		);
		
		// Search for relations
		$relatedRows = array();
		
		foreach ($rows as $id_topic)
		{
			list ($id_topic, $subject) = $id_topic;
			
			$relatedTopics = $this->__searchRelated($subject);
			
			foreach ($relatedTopics as $id_topic_rel)
			{
				list ($id_topic_rel, $score) = $id_topic_rel;
				
				if ($id_topic_rel == $id_topic)
					continue;
				
				$relatedRows[] = array($id_topic, $id_topic_rel, $score);
			}
			unset($relatedTopics);
		}
		
		relatedAddRelatedTopic($relatedRows, 'fulltext');

		return true;
	}

	function removeTopics($topics)
	{
		global $smcFunc;

		$smcFunc['db_query']('', '
			DELETE FROM {db_prefix}related_subjects
			WHERE id_topic IN({array_int:topics})',
			array(
				'topics' => $topics,
			)
		);

		return true;		
	}

	private function __searchRelated($subject)
	{
		global $smcFunc, $modSettings;

		$request = $smcFunc['db_query']('', '
			SELECT rs.id_topic, MATCH(rs.subject) AGAINST({string:subject}) AS score
			FROM {db_prefix}related_subjects AS rs
			WHERE MATCH(rs.subject) AGAINST({string:subject})
			ORDER BY MATCH(rs.subject) AGAINST({string:subject}) DESC
			LIMIT {int:limitTopics}',
			array(
				'subject' => $subject,
				'limitTopics' => round((!empty($modSettings['relatedTopicsCount']) ? (int) $modSettings['relatedTopicsCount'] : 5) * 2.5),
			)
		);

		$return = array();

		while ($row = $smcFunc['db_fetch_assoc']($request))
			$return[$row['id_topic']] = array($row['id_topic'], $row['score']);

		$smcFunc['db_free_result']($request);

		return $return;
	}
}

?>