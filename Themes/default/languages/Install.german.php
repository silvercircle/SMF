<?php
// Version: 1.0; Install

$txt['smf_installer'] = 'SMF Installation';
$txt['installer_language'] = 'Sprache';
$txt['congratulations'] = 'Herzlichen Gl&uuml;ckwunsch, die Installation ist abgeschlossen!';
$txt['congratulations_help'] = 'Wenn Sie Unterst&uuml;tzung brauchen oder SMF nicht fehlerfrei l&auml;uft, k&ouml;nnen Sie <a href="http://www.simplemachines.org/community/index.php">im Forum</a> Hilfe anfordern.';
$txt['still_writable'] = 'Ihr Installationsverzeichnis ist noch beschreibbar! Es ist aus Sicherheitsgr&uuml;nden sinnvoll, die CHMOD Berechtigungen zu &auml;ndern, dass es schreibgesch&uuml;tzt ist.';
$txt['delete_installer'] = 'Klicken Sie hier um die Datei install.php zu l&ouml;schen. <i>(funktioniert nicht auf allen Servern)</i>';
$txt['go_to_your_forum'] = 'Jetzt k&ouml;nnen Sie <a href="%s">Ihr neu installiertes Forum</a> ansehen und benutzen. Seien Sie sicher dass Sie eingeloggt sind, bevor Sie versuchen in den Administratorbereich zu gelangen.';
$txt['good_luck'] = 'Viel Gl&uuml;ck!<br />Simple Machines';

$txt['user_refresh_install'] = 'Forum aktualisiert';
$txt['user_refresh_install_desc'] = 'W&auml;hrend der Installation hat das Installationsprogramm eine oder mehrere Datenbanktabellen gefunden, welche schon existieren und ggf. neu erstellt werden.<br />S&auml;mtliche fehlenden Tabellen in Ihrer Installation wurden mit den Standard Daten erstellt, vorhandene Daten wurden jedoch nicht gel&ouml;scht.';

$txt['default_topic_subject'] = 'Willkommen bei SMF!';
$txt['default_topic_message'] = 'Willkommen im Simple Machines Forum!<br /><br />Wir hoffen, dass Ihnen Ihr neues Forum Spa&szlig; macht.&nbsp; Wenn Sie Probleme haben, z&ouml;gern Sie nicht uns [url=http://www.simplemachines.org/community/index.php]um Hilfe zu fragen[/url].<br /><br />Danke!<br />Simple Machines';
$txt['default_board_name'] = 'Allgemeine Diskussionen';
$txt['default_board_description'] = 'Diskutieren Sie in diesem Board &uuml;ber alles was Ihnen einf&auml;llt.';
$txt['default_category_name'] = 'Kategorie';
$txt['default_time_format'] = '%B %d, %Y, %I:%M:%S %p';

$txt['error_message_click'] = 'Klicken Sie hier';
$txt['error_message_try_again'] = 'um den Schritt erneut zu versuchen.';
$txt['error_message_bad_try_again'] = 'um trotzdem zu installieren. Beachten Sie bitte, dass dies <i>nicht</i> empfehlenswert ist.';

$txt['install_settings'] = 'Einstellungen';
$txt['install_settings_info'] = 'Nur ein paar Einstellungen für Sie ;).';
$txt['install_settings_name'] = 'Forum Name';
$txt['install_settings_name_info'] = 'Das ist der Name Ihres Forums, z.B. &quot;Test Forum&quot;.';
$txt['install_settings_name_default'] = 'Mein Forum';
$txt['install_settings_url'] = 'Forum URL';
$txt['install_settings_url_info'] = 'Das ist die URL von Ihrem Forum <b>ohne den abschlie&szlig;enden \'/\'!</b>.<br />In den meisten F&auml;llen k&ouml;nnen Sie den eingestellten Wert belassen.';
$txt['install_settings_compress'] = 'Gzip Output';
$txt['install_settings_compress_title'] = 'Komprimiere Datenausgabe um Bandbreite zu sparen.';
// In this string, you can translate the word "PASS" to change what it says when the test passes.
$txt['install_settings_compress_info'] = 'Diese Option funktioniert nicht auf allen Servern, kann aber eine Menge an Bandbreite sparen.<br />Klicken Sie <a href="install.php?obgz=1&amp;pass_string=Erfolgreich" onclick="return reqWin(this.href, 200, 60);">hier</a> um es zu testen (der Test sollte "Erfolgreich" zur&uuml;ckmelden).';
$txt['install_settings_dbsession'] = 'Datenbank Sitzungen';
$txt['install_settings_dbsession_title'] = 'Benutze Datenbank f&uuml;r Sitzungen anstatt Dateien.';
$txt['install_settings_dbsession_info1'] = 'Diese Option ist grunds&auml;tzlich immer die beste Wahl, da sie Sitzungen zuverl&auml;ssiger macht.';
$txt['install_settings_dbsession_info2'] = 'Diese Option scheint nicht mit Ihrem Server zu funktionieren, Sie k&ouml;nnen es jedoch trotzdem versuchen.';
$txt['install_settings_proceed'] = 'Weiter';

$txt['mysql_settings'] = 'MySQL Server Einstellungen';
$txt['mysql_settings_server'] = 'MySQL Server Name';
$txt['mysql_settings'] = 'MySQL Server Einstellungen';
$txt['mysql_settings_info'] = 'Das sind die Einstellungen, die Sie f&uuml;r Ihren MySQL Server ben&ouml;tigen. Sollten Sie die Daten nicht wissen, fragen Sie Ihren Server Anbieter.';
$txt['mysql_settings_server'] = 'MySQL Server Name';
$txt['mysql_settings_server_info'] = 'Der Name ist meistens localhost oder eine IP Adresse - sollten Sie es nicht wissen, probieren Sie localhost.';
$txt['mysql_settings_username'] = 'MySQL Username';
$txt['mysql_settings_username_info'] = 'Schreiben Sie hier den Usernamen hinein, den Sie ben&ouml;tigen, um zur MySQL Datenbank zu verbinden.<br />Sollten Sie ihn nicht kennen, probieren Sie den Usernamen Ihres FTP Servers, oft sind diese gleich.';
$txt['mysql_settings_password'] = 'MySQL Passwort';
$txt['mysql_settings_password_info'] = 'Schreiben Sie hier das Passwort f&uuml;r die MySQL Datenbank hinein.<br />Sollten Sie es nicht wissen, probieren Sie das von Ihrem FTP Zugang.';
$txt['mysql_settings_database'] = 'MySQL Datenbank Name';
$txt['mysql_settings_database_info'] = 'F&uuml;llen Sie das Feld mit dem Namen der Datenbank, in der SMF seine Daten speichern soll.<br />Wenn die Datenbank nicht existiert, wird die Installation versuchen sie zu erstellen.';
$txt['mysql_settings_prefix'] = 'MySQL Tabellen Prefix';
$txt['mysql_settings_prefix_info'] = 'Das Prefix f&uuml;r jede Tabelle in der Datenbank. <b>Installieren Sie nie zwei Foren mit dem gleichen Prefix!</b><br />Diese Angabe erlaubt mehrere Installationen in einer Datenbank.';

$txt['user_settings'] = 'Erstellt Ihren Zugang';
$txt['user_settings_info'] = 'Die Installation wird nun einen Administrator Zugang f&uuml;r Sie erstellen.';
$txt['user_settings_username'] = 'Ihr Benutzername';
$txt['user_settings_username_info'] = 'W&auml;hlen Sie den Namen, mit dem Sie sich einloggen.<br />Dieser Name kann sp&auml;ter nicht ge&auml;ndert werden, wohl aber der, der angezeigt wird.';
$txt['user_settings_password'] = 'Passwort';
$txt['user_settings_password_info'] = 'F&uuml;llen Sie dieses Feld mit dem gew&uuml;nschten Passwort aus und behalten Sie es gut im Kopf!';
$txt['user_settings_again'] = 'Passwort';
$txt['user_settings_again_info'] = '(zum best&auml;tigen.)';
$txt['user_settings_email'] = 'E-Mail Adresse';
$txt['user_settings_email_info'] = 'Schreiben Sie hier Ihre E-Mail Adresse rein.  <b>Es muss eine g&uuml;ltige E-Mail Adresse sein.</b>';
$txt['user_settings_database'] = 'MySQL Datenbank Passwort';
$txt['user_settings_database_info'] = 'Die Installation erfordert aus Sicherheitsgr&uuml;nden ein g&uuml;ltiges Datenbank Passwort, um Ihren Administrator Zugang zu erstellen.';
$txt['user_settings_proceed'] = 'Fertig';

$txt['ftp_setup'] = 'FTP Verbindungsinformationen';
$txt['ftp_setup_info'] = 'Die Installation kann via FTP zum Server verbinden und die Dateien &uuml;berschreibbar machen, welche diese Option ben&ouml;tigen. Sollte es nicht funktionieren, m&uuml;ssen Sie dies manuell machen. Bitte beachten Sie, dass SSL im Moment nicht unterst&uuml;tzt wird.';
$txt['ftp_server'] = 'Server';
$txt['ftp_server_info'] = 'Das sollte der Server und der Port f&uuml;r Ihren FTP Server sein.';
$txt['ftp_port'] = 'Port';
$txt['ftp_username'] = 'Username';
$txt['ftp_username_info'] = 'Der Benutzername zum Einloggen. <i>Er wird nirgendwo gespeichert.</i>';
$txt['ftp_password'] = 'Passwort';
$txt['ftp_password_info'] = 'Das Passwort zum Einloggen. <i>Es wird nirgendwo gespeichert.</i>';
$txt['ftp_path'] = 'Installationspfad';
$txt['ftp_path_info'] = 'Das ist der <i>relative</i> Pfad, den Sie beim FTP Server benutzen.';
$txt['ftp_connect'] = 'Verbinden';
$txt['ftp_setup_why'] = 'Was macht dieser Schritt?';
$txt['ftp_setup_why_info'] = 'Einige Dateien m&uuml;ssen &uuml;berschreibbar sein, damit SMF richtig funktioniert. Dieser Schritt erm&ouml;glicht es der Installation es selbst zu &auml;ndern. In manchen F&auml;llen kann es vorkommen, dass es nicht funktioniert - dann &auml;ndern Sie bitte bei folgenden Dateien das Attribut (CHMOD) auf 777:';
$txt['ftp_setup_again'] = 'Erneut testen, ob die Dateien &uuml;berschreibbar sind.';

$txt['error_php_too_low'] = 'Warnung! Der Server scheint eine PHP Version zu haben, welche nicht den <b>minimalen Anforderungen</b> von SMF entspricht.<br />Wenn Sie den Server nicht selbst besitzen, sollten Sie Ihren Server Anbieter fragen, ob er die Version aktualisiert, einen anderen Anbieter w&auml;hlen oder sie selbst aktualisieren wenn Sie der Besitzer sind.<br /><br />Sollten Sie sicher sein, dass die PHP Version aktuell genug ist, k&ouml;nnen Sie fortfahren, was jedoch nicht empfehlenswert ist.';
$txt['error_missing_files'] = 'Installationsdateien konnten nicht im Verzeichnis des Skriptes gefunden werden!<br /><br />Bitten vergewissern Sie sich, dass Sie alle Dateien, inklusive der sql Datei, hochgeladen haben und probieren Sie es erneut.';
$txt['error_session_save_path'] = 'Bitte informieren Sie Ihren Server Anbieter, dass der <b>session.save_path der Datei php.ini</b> ung&uuml;ltig ist!  Der Pfad sollte zu einem Verzeichnis ge&auml;ndert werden, welches <b>existiert</b> und vom Benutzer <b>beschreibbar</b> ist.<br />';
$txt['error_windows_chmod'] = 'Sie benutzen einen Windows Server und einige Dateien sind nicht &uuml;berschreibbar. Fragen Sie Ihren Server Anbieter nach <b>Schreibberechtigungen</b> f&uuml;r die Dateien Ihrer SMF Installation. Die folgenden Dateien m&uuml;ssen &uuml;berschreibbar sein:';
$txt['error_ftp_no_connect'] = 'Verbindung zum FTP Server mit den aktuellen Daten nicht m&ouml;glich.';
$txt['error_mysql_connect'] = 'Verbindung zur MySQL Datenbank mit den aktuellen Daten nicht m&ouml;glich.<br /><br />Wenn Sie sich nicht sicher sind, fragen Sie Ihren Server Anbieter nach den richtigen Daten.';
$txt['error_mysql_too_low'] = 'Ihre Version von MySQL ist sehr alt und entspricht nicht den minimalen Anforderungen von SMF.<br /><br />Bitte fragen Sie Ihren Server Anbieter, ob er es aktualisiert oder wechseln Sie den Anbieter.';
$txt['error_mysql_database'] = 'Die Installation konnte nicht auf die &quot;<i>%s</i>&quot; Datenbank zugreifen. Bei manchen Anbietern m&uuml;ssen Sie die Datenbank erst erstellen, bevor Sie diese nutzen k&ouml;nnen. Andere f&uuml;gen dem Datenbanknamen ein Prefix hinzu, z.B. Ihren Usernamen.';
$txt['error_mysql_queries'] = 'Einige der Befehle konnten nicht richtig ausgef&uuml;hrt werden. Dies kann an einer nicht unterst&uuml;tzten oder veralteten Version von MySQL h&auml;ngen.<br /><br />Technische Information der Befehle:';
$txt['error_mysql_queries_line'] = 'Zeile #';
$txt['error_mysql_missing'] = 'Die Installation konnte keine MySQL Unterst&uuml;tzung in PHP finden. Bitte versichern Sie sich bei Ihrem Host, dass PHP wirklich mit MySQL kompiliert wurde oder das die richtige Erweiterung geladen wird.';
$txt['error_user_settings_again_match'] = 'Sie haben zwei verschiedene Passw&ouml;rter eingegeben.!';
$txt['error_user_settings_taken'] = 'Ein Mitglied hat sich schon mit diesem Benutzernamen/Passwort registriert.<br /><br />Ein neuer Zugang wurde nicht erstellt.';
$txt['error_user_settings_query'] = 'Ein Datenbankfehler ist beim Erstellen des Administrator Zugangs aufgetreten. Der Fehler ist:';
$txt['error_subs_missing'] = 'Es ist nicht m&ouml;glich, die Datei Sources/Subs.php zu finden. Bitte vergewissern Sie sich, dass Sie diese vollst&auml;ndig hochgeladen haben und probieren es erneut.';

?>