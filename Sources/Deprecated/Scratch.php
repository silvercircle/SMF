<?php
if (!defined('SMF'))
	die('No access');
function foo()
{
	smf_db_query('UPDATE {db_prefix}members AS m 
		SET m.likes_given = (SELECT COUNT(l.id_user) FROM {db_prefix}likes AS l WHERE l.id_user = m.id_member), 
			m.likes_received = (SELECT COUNT(l1.id_receiver) FROM {db_prefix}likes AS l1 WHERE l1.id_receiver = m.id_member)
		WHERE m.id_member = {int:id_member}',array('id_member' => $id));
}
?>
