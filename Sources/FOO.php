<?php
/******************************************************************************
* RepairBoards.php                                                            *
*******************************************************************************
* SMF: Simple Machines Forum                                                  *
* Open-Source Project Inspired by Zef Hemel (zef@zefhemel.com)                *
* =========================================================================== *
* Software Version:           SMF 1.0                                         *
* Software by:                Simple Machines (http://www.simplemachines.org) *
* Copyright 2001-2004 by:     Lewis Media (http://www.lewismedia.com)         *
* Support, News, Updates at:  http://www.simplemachines.org                   *
*******************************************************************************
* This program is free software; you may redistribute it and/or modify it     *
* under the terms of the provided license as published by Lewis Media.        *
*                                                                             *
* This program is distributed in the hope that it is and will be useful,      *
* but WITHOUT ANY WARRANTIES; without even any implied warranty of            *
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                        *
*                                                                             *
* See the "license.txt" file for details of the Simple Machines license.      *
* The latest version can always be found at http://www.simplemachines.org.    *
******************************************************************************/

require("../Settings.php");
require("./Subs.php");

global $db_prefix, $txt, $scripturl, $salvageCatID, $salvageBoardID, $sc, $context, $sourcedir;
$db_connection = @mysql_connect($db_server, $db_user, $db_passwd);
@mysql_select_db("smf", $db_connection);
	
		// Fix all ID_FIRST_MSG, ID_LAST_MSG and numReplies in the topic table.
		$resultTopic = &mysql_query("
			SELECT
				t.ID_TOPIC, MIN(m.ID_MSG) AS myID_FIRST_MSG, t.ID_FIRST_MSG,
				MAX(m.ID_MSG) AS myID_LAST_MSG, t.ID_LAST_MSG, COUNT(m.ID_MSG) - 1 AS myNumReplies,
				t.numReplies
			FROM {$db_prefix}topics AS t
				LEFT JOIN {$db_prefix}messages AS m ON (m.ID_TOPIC = t.ID_TOPIC)
			GROUP BY t.ID_TOPIC
			HAVING ID_FIRST_MSG != myID_FIRST_MSG OR ID_LAST_MSG != myID_LAST_MSG OR numReplies != myNumReplies", $db_connection);
		if (mysql_num_rows($resultTopic) > 0)
		{
			while ($topicArray = mysql_fetch_assoc($resultTopic))
			{
				$result = &mysql_query("
					UPDATE {$db_prefix}topics
					SET ID_FIRST_MSG = '$topicArray[myID_FIRST_MSG]',
						ID_LAST_MSG = $topicArray[myID_LAST_MSG],
						numReplies = '$topicArray[myNumReplies]'
					WHERE ID_TOPIC = $topicArray[ID_TOPIC]
					LIMIT 1", $db_connection);
			}
		}
		mysql_free_result($resultTopic);
																																																																																												       