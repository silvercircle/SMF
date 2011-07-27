<?php
// Version: 1.0; Help

// Important! Before editing these language files please read the text at the top of index.german.php.

global $helptxt;
$helptxt = array();

$txt['close_window'] = 'Fenster schlie&szlig;en';

$helptxt[1] = '
	<b>Kategorien bearbeiten</b><br />
	In diesem Menu k&ouml;nnen Sie die &quot;Kategorien&quot; editieren. Kategorien sind die
	oberste Ebene eines Forums. Beispiel: Wenn Sie eine Seite haben, die Informationen
	f&uuml;r &quot;Sport&quot;, &quot;Autos&quot; und &quot;Musik&quot; bereitstellt, w&auml;ren dies die Kategorien
	die Sie erstellen. Innerhalb dieser Kategorien k&ouml;nnen Sie &quot;Unterkategorien&quot; -
	sogenannte &quot;Boards&quot; einf&uuml;gen. Hier ist ein kleines Beispiel:<br />
	<ul>
		<li>
			<b>Sport</b>
			&nbsp;- Eine &quot;Kategorie&quot;
		</li>
		<ul>
			<li>
				<b>Baseball</b>
				&nbsp;- Ein Board innerhalb der Kategorie &quot;Sport&quot;
			</li>
			<ul>
				<li>
					<b>Statistiken</b>
					&nbsp;- Ein untergeordnetes Board des Boards &quot;Baseball&quot;
				</li>
			</ul>
			<li>
				<b>Fu&szlig;ball</b>
				&nbsp;- Ein Board innerhalb der Kategorie &quot;Sport&quot;</li>
		</ul>
	</ul>
	Kategorien erlauben es Ihnen, das Forum in Themen aufzuteilen (&quot;Autos,
	Sport&quot;) und die &quot;Boards&quot; dienen den Mitgliedern dazu, ihre Beitr&auml;ge
	dort hinein zu schreiben. Wenn sich ein Benutzer f&uuml;r Porsche interessiert, w&uuml;rde
	er seinen Eintrag in &quot;Autos -> Porsche&quot; schreiben. Kategorien erlauben den
	Mitgliedern ihre Interessen schnell zu finden: Anstelle von &quot;Fahrzeugen&quot; kann man
	zwischen &quot;Autos&quot; und &quot;Motorr&auml;dern&quot; w&auml;hlen. So wei&szlig; der Benutzer, dass er
	nach &quot;Porsche Carrera S4&quot; nicht in der &quot;Motorrad-Kategorie&quot; suchen muss
	sondern bei den Autos.<br />
	Administrative Funktionen in diesem Men&uuml; sind die Neuerstellung von Boards unter jeder
	Kategorie, die &Auml;nderung der Reihenfolge sowie das komplette L&ouml;schen eines Boards.';

$helptxt[2] = '<b>Forum News editieren</b><br />
	Diese Funktion erlaubt Ihnen das &auml;ndern des Textes, welcher in den News auf dem Board-Index
	angezeigt wird. Hier k&ouml;nnen Sie alles reinschreiben was Sie m&ouml;chten (z.B. &quot;Verpasse nicht die
	Besprechung &uuml;ber das neue SMF Forum diesen Freitag !&quot;). Jede News-Meldung wird durch dr&uuml;cken
	der &quot;Enter-Taste&quot; voneinander getrennt.';

$helptxt[3] = '<b>Nutzungsbedingungen editieren</b><br />
	Diese Funktion erlaubt es Ihnen, den Text f&uuml;r die Nutzungsbedingungen zu &auml;ndern, welcher bei einer Neuregistrierung angezeigt wird.
	Sie k&ouml;nnen zu dem Original-Text neue Bedingungen hinzuf&uuml;gen oder l&ouml;schen.';

$helptxt[4] = '<b>Mitglieder anzeigen&#8260;l&ouml;schen</b><br />
	Zeigt alle Mitglieder im Forum an. Hier sehen Sie eine Liste aller Mitglieder, welche in Ihrem Forum registriert sind.
	Sie k&ouml;nnen auf den Namen eines Mitgliedes klicken, um weitere Informationen &uuml;ber ihn/sie abzurufen (Homepage, Alter u.s.w.).
	Als Administrator sind Sie au&szlig;erdem in der Lage, diese Angaben zu &auml;ndern. Sie haben die volle Kontrolle &uuml;ber die Daten der Mitglieder, inkl. der M&ouml;glichkeit sie zu l&ouml;schen.';

$helptxt[6] = '<b>E-Mail an Mitglieder</b><br />
	Von hier aus k&ouml;nnen Sie allen registrierten Mitgliedern mit E-Mail Adresse eine Nachricht schreiben. Sie k&ouml;nnen die Liste &auml;ndern
	oder allen eine E-Mail schreiben, was bei wichtigen Mitteilungen n&uuml;tzlich ist. Benutzen Sie die Funktion nicht zu oft, sonst
	haben die Mitglieder das Gef&uuml;hl, sie w&uuml;rden zu viele Nachrichten ohne ihre Zustimmung bekommen.';

$helptxt[7] = '<b>Bann-Liste editieren</b><br />
	SMF bietet die M&ouml;glichkeit, bestimmte Mitglieder zu &quot;bannen&quot;, weil sie aufgrund von Spam u.a. gegen die Nutzungsbedingungen
	versto&szlig;en haben. Als Administrator k&ouml;nnen Sie in jedem Beitrag die IP Adresse des Benutzers sehen. Diese tragen Sie einfach in die
	Bann-Liste ein und der betreffende Benutzer kann nicht mehr unter dieser Adresse schreiben.<br />Sie haben auch die M&ouml;glichkeit, die Leute durch die Eingabe ihrer E-Mail-Adresse bannen.';

$helptxt[8] = '<b>Reservierte Namen</b><br />
	Diese Funktion erlaubt es Ihnen Namen festzulegen, welche neue oder existierende Mitglieder nicht als Benutzernamen
	nehmen d&uuml;rfen.';

$helptxt[9] = '<b>Forum Template editieren</b><br />
	Diese Funktion zeigt einen einfachen Editor f&uuml;r den Template Code an. Das &quot;Template&quot; (in Ihrem &quot;SMF&quot;
	Verzeichnis, so wie Sie es angegeben haben) definiert das Aussehen des Forums. Es ist eine HTML Datei, welche Sie
	nach Ihren W&uuml;nschen &auml;ndern k&ouml;nnen - entweder mit diesem oder einem anderen Editor (Dreamweaver, FrontPage etc.).';

$helptxt[10] = '<b>Einstellungen zu den installierten Mods</b><br />
	SMF hat vorinstallierte Modifikationen, welche hier eingestellt werden k&ouml;nnen.';

$helptxt[11] = '<b>Zensierte W&ouml;rter bearbeiten</b><br />
	SMF erm&ouml;glicht es, bestimmte W&ouml;rter zu zensieren, indem es sie durch andere ersetzt.<br />
	F&uuml;gen Sie ein Wort pro Zeile hinzu, so wie es im folgenden Beispiel gemacht wurde:<br />
	Daniel=bester &Uuml;bersetzer<br />
	<br />
	<i>(* can als Wildcard bnutzt werden.)</i>';

$helptxt['number_format'] = '<b>Nummern Format</b><br />
	Sie k&ouml;nnen diese Option dazu benutzen, das Format der Zahlen zu w&auml;hlen, in welchem sie angezeigt werden. Das Format der Einstellung ist:<br />
	<div style="margin-left: 2ex;">1,234.00</div><br />
	Das \'Komma\' ist das Tausender Trennzeichen, der \'Punkt\' das Dezimal Trennzeichen und abschlie&szlig;end die Anzahl der Nullen als Rundungsstellen.';

$helptxt['time_format'] = '<b>Zeit Format</b><br />
	Sie k&ouml;nnen hier einzustellen, wie das Datum und die Zeit angezeigt werden. Die Vorgabe folgt den
	PHP Richtlinien und ist im folgenden beschrieben (mehr Details unter <a href="http://www.php.net/manual/function.strftime.php">php.net</a>).<br />
	<br />
	Die folgenden Buchstaben sind bei der Einstellung zu verwenden (Gro&szlig;-/Kleinschreibung beachten!): <br />
	<span class="smalltext">
	&nbsp;&nbsp;%a - abgek&uuml;rzter Name des Wochentags<br />
	&nbsp;&nbsp;%A - voller Name des Wochentags<br />
	&nbsp;&nbsp;%b - abgek&uuml;rzter Monatsname<br />
	&nbsp;&nbsp;%B - voller Monatsname<br />
	&nbsp;&nbsp;%d - Tag des Monats (01 bis 31) <br />
	&nbsp;&nbsp;%D<b>*</b> - das gleiche wie %m/%d/%y <br />
	&nbsp;&nbsp;%e<b>*</b> - Tag des Monats (1 bis 31)<br />
	&nbsp;&nbsp;%H - Stunde einer 24-Stunden Uhr (von 00 bis 23) <br />
	&nbsp;&nbsp;%I - Stunde einer 12-Stunden Uhr (von 01 bis 12) <br />
	&nbsp;&nbsp;%m - aktueller Monat als Zahl (01 to 12) <br />
	&nbsp;&nbsp;%M - Minute als Zahl <br />
	&nbsp;&nbsp;%p - entweder &quot;am&quot; oder &quot;pm&quot; zu der eingestellten Zeit hinzuf&uuml;gen<br />
	&nbsp;&nbsp;%R<b>*</b> - Zeit in 24-Stunden Anzeige <br />
	&nbsp;&nbsp;%S - Sekunde als Dezimalzahl <br />
	&nbsp;&nbsp;%T<b>*</b> - aktuelle Zeit, gleichwertig zu %H:%M:%S <br />
	&nbsp;&nbsp;%y - 2-stelliges Jahr (00 to 99) <br />
	&nbsp;&nbsp;%Y - 4-stelliges Jahr<br />
	&nbsp;&nbsp;%Z - Zeit-Zone, Name oder Abk&uuml;rzung der Zeit-Zone <br />
	&nbsp;&nbsp;%% - ein \'%\' Zeichen <br />
	<br />
	<i>* Funktioniert nicht auf Windows basierenden Servern</i></span>';

$helptxt[13] = '<b>Live Ank&uuml;ndigungen</b><br />
	Diese Box zeigt aktuelle Meldungen von <a href="http://www.simplemachines.org/">www.simplemachines.org</a>.
	Sie sollten hier regelm&auml;&szlig;ig wegen Updates, neuen Versionen und wichtigen Informationen vom Simple Machines Team nachschauen.';

$helptxt[14] = '<b>Registrierungen verwalten</b><br />
	Hier findet man alle Funktionen die das Verwalten von neuen Mitgliedern ben&ouml;tigt. Es gibt drei Bereiche, welche je nach Einstellungen des Forums sichtbar sind:<br /><br />
	<ul>
		<li>
			<b>Erwarte Genehmigung</b><br />
			Dieser Bereich wird nur angezeigt, wenn Sie die Genehmigung aller neuen Registrierungen durch den Administrator aktiviert haben.
			Jeder Benutzer der sich registriert wird erst Mitglied des Forums sein, wenn der Administrator den Zugang genehmigt.
			Der Bereich listet alle Mitglieder inkl. E-Mail und IP Adresse, die auf ihre Genehmigung warten. Sie k&ouml;nnen
			w&auml;hlen, ob Sie das Mitglied akzeptieren oder ablehnen, indem Sie das kleine K&auml;stchen neben dem Mitglied
			w&auml;hlen und in der Drop-Down Box die entsprechende Aktion aussuchen. Sollten Sie ein Mitglied ablehnen,
			k&ouml;nnen Sie es wahlweise mit oder ohne Benachrichtigung l&ouml;schen.<br /><br />
		</li>
		<li>
			<b>Erwarte Aktivierung</b><br />
			Dieser Bereich ist nur sichtbar, wenn Sie die Aktivierung der Mitglieder Zug&auml;nge eingeschaltet haben. Von hier
			aus k&ouml;nnen Sie alle Mitglieder ansehen, die Ihren Zugang noch nicht aktiviert haben. Sie haben die M&ouml;glichkeit,
			die Mitglieder zu genehmigen, abzulehnen oder an die Aktivierung zu erinnern. Wie oben schon erw&auml;hnt kann das
			L&ouml;schen ohne oder mit Benachrichtigung erfolgen.<br /><br />
		</li>
		<li>
			<b>Neues Mitglied registrieren</b><br />
			In diesem Bereich k&ouml;nnen Sie auf Wunsch neue Mitglieder registrieren. Dies ist n&uuml;tzlich bei Foren, wo die
			Registrierung deaktiviert ist oder wenn der Administrator einen Testzugang erstellen m&ouml;chte. Wenn die Option
			der Aktivierung des Zugangs eingeschaltet ist, erhalten die Mitglieder eine E-Mail mit dem Aktivierungslink,
			welchem sie folgen m&uuml;ssen bevor sie den Zugang nutzen k&ouml;nnen. Alternativ k&ouml;nnen Sie auch w&auml;hlen,
			dass dem neuen Mitglied eine E-Mail mit dem Passwort an die angegebene Adresse geschickt wird.
		</li>
	</ul>';
$helptxt[15] = '<b>Moderations Log</b><br />
	Dieser Bereich erlaubt den Administratoren alle Aktionen der Moderatoren zu verfolgen. Damit Moderatoren keine Hinweise zu ihren
	Aktionen l&ouml;schen k&ouml;nnen, ist es erst 24 Stunden danach m&ouml;glich, diese Eintr&auml;ge zu entfernen. Die \'Objekte\'
	Spalte listet alle Details zu der betreffenden Aktion auf.';
$helptxt[16] = '<b>Error Log</b><br />
	Das Error Log listet alle Fehler, die von Benutzern im Forum produziert worden sind, nach Datum sortiert auf. Um den neuesten Fehler
	zuerst anzuzeigen, klicken Sie auf den kleinen schwarzen Pfeil neben dem Datum. Weiterhin k&ouml;nnen Sie die Fehlermeldungen
	nach der Art des Fehlers filtern, indem Sie auf die Grafik neben der entsprechenden Angabe klicken (z.B. filtern nach Mitglied).
	Wenn ein Filter aktiv ist, werden nur die &uuml;bereinstimmenden Fehler angezeigt.';
$helptxt[17] = '<b>Theme Einstellungen</b><br />
	Der Bereich erlaubt das Ver&auml;ndern der Einstellungen jedes einzelnen Themes. Die Einstellungen betreffen u.a. das Theme Verzeichnis
	und die URL Informationen sowie viele Einstellungen zum Layout. Die meisten Themes enthalten eine Vielzahl von konfigurierbaren Optionen,
	welche es erlauben, das Theme den pers&ouml;nlichen W&uuml;nschen anzupassen.';
$helptxt['smileys'] = '<b>Smiley Center</b><br />
	Here you can add and remove smileys, and smiley sets.  Note importantly that if a smiley is in one set, it\'s in all sets - otherwise, it might
	get confusing for your users using different sets.';

// Mod Settings

$helptxt['topicSummaryPosts'] = 'Erlaubt das Einstellen der Anzahl von Eintr&auml;gen, die beim Antwort-Bildschirm in der Zusammenfassung angezeigt werden.';
$helptxt['enableStickyTopics'] = 'Top Themen sind Themen, welche an erster Stelle der Themenliste verbleiben. Sie werden meistens f&uuml;r wichtige
		Nachrichten verwendet. Nur Administratoren und Moderatoren k&ouml;nnen Themen zu Top Themen machen.';
$helptxt['userLanguage'] = 'Erm&ouml;glicht dem Benutzer die Auswahl einer individuellen Sprache im Forum. Betrifft nicht die Standardeinstellung.';
$helptxt['trackStats'] = 'Statistiken:<br />Erlaubt es den Mitgliedern verschiedene Statistiken zu sehen, z.B. die neuesten Eintr&auml;ge, die am
		meist besuchten Themen,	die neuesten Themen und viele andere.<hr />
		Hits:<br />F&uuml;gt den Statistiken eine weitere Spalte mit den Hits des Forum hinzu.';
$helptxt['titlesEnable'] = 'Erlaubt den Mitgliedern sich selbst einen frei definierbaren &quot;Titel&quot; zu geben, welcher unter dem Namen angezeigt
		wird.<br /><i>Beispiel:</i><br />Daniel<br />Bester &Uuml;bersetzer';
$helptxt['topbottomEnable'] = 'F&uuml;gt einen &quot;nach unten&quot; bzw. &quot;nach oben&quot; Button hinzu, welcher ein schnelles hoch-/runterbewegen innerhalb
		der Seite erm&ouml;glicht.';
$helptxt['onlineEnable'] = 'Ein Bild zeigt den Online Status des Benutzers an (Online/Offline).';
$helptxt['todayMod'] = 'Zeigt &quot;Heute&quot; oder &quot;Gestern&quot; anstatt des Datums an.';
$helptxt['enablePreviousNext'] = 'Zeigt einen Link zum n&auml;chsten bzw. vorherigen Thema an.';
$helptxt['pollMode'] = 'Erlaubt den Mitgliedern Umfragen zu starten. Sie k&ouml;nnen angeben, wer Umfragen starten darf: Nur der Administrator
		oder alle Mitglieder.<br />Weiterhin kann gew&auml;hlt werden, wer die Umfragen editieren darf.';
$helptxt['enableVBStyleLogin'] = 'Zeigt ein kleines Login-Feld am unteren Ende der Seite an.';
$helptxt['enableCompressedOutput'] = 'Aktiviert die komprimierte Daten&uuml;bertragung um Bandbreite zu sparen. Erfordert ein installiertes \'zlib\' auf dem Server.';
$helptxt['databaseSession_enable'] = 'Diese Option verwendet die Datenbank zum Speichern von Sitzungen und ist das Beste f&uuml;r eine ausgeglichene Belastung des Servers. Sie hilft bei den sogenannten \'Timeouts\' und macht das Forum unter Umst&auml;nden schneller. Sie funktioniert jedoch nicht, wenn die PHP Funktion \'session.auto_start\' angeschaltet ist.';
$helptxt['databaseSession_loose'] = 'Sollten Sie diese Option aktivieren, wird die ben&ouml;tigte Bandbreite des Forums abnehmen. Jedoch wird bei einem Klick auf den Zur&uuml;ck-Button im Browser die vorherige Seite nicht neu geladen, die \'Neue Beitr&auml;ge\' Symbole und andere Werte werden nicht aktualisiert.';
$helptxt['databaseSession_lifetime'] = 'Anzahl der Sekunden f&uuml;r die L&auml;nge einer Datenbank Sitzung. Sollte eine Sitzung eine Zeit lang nicht gebraucht werden, wird sie als &quot;verloren gegangen&quot; bezeichnet. Empfohlen wird mindestens der Wert 2400.';
$helptxt['m16'] = 'Die Aktivierung bewirkt die Anzeige der aktuellen Position in einer einzigen Zeile, im Gegensatz zu einer baumartigen Ansicht';
$helptxt['m17'] = '&Auml;ndert das Layout der angezeigten Statistiken auf der Startseite.';
$helptxt['enableErrorLogging'] = 'Erfasst alle Fehlermeldungen im Forum (z.B. fehlerhafter Login etc.).';
$helptxt['notifyAnncmnts_UserDisable'] = 'Erlaubt Mitgliedern die Deaktivierung von Benachrichtigungen von Ank&uuml;ndigungsboards.';
$helptxt['compactTopicPagesEnable'] = 'Zeigt nur eine bestimmte Anzahl der Seitennummern an.<br /><i>Beispiel:</i>
		&quot;3&quot; f&uuml;r: 1 ... 4 [5] 6 ... 9 <br />
		&quot;5&quot; f&uuml;r: 1 ... 3 4 [5] 6 7 ... 9';
$helptxt['timeLoadPageEnable'] = 'Zeigt unten auf jeder Seite die Zeit in Sekunden an, die SMF f&uuml;r das Erstellen gebraucht hat.';
$helptxt['removeNestedQuotes'] = 'Zeigt nur das Zitat des betreffenden Eintrages an und keine weiteren.';
$helptxt['simpleSearch'] = 'Zeigt eine vereinfachte Suchemaske an.';
$helptxt['maxwidth'] = 'Erlaubt die Angabe einer maximalen Bildergr&ouml;&szlig;e. Bilder die kleiner sind, werden dadurch nicht beeintr&auml;chtigt.';
$helptxt['mail_type'] = 'Erlaubt die Wahl des standardm&auml;&szlig;igen PHP Mailprogrammes oder des Servers vom eigenen E-Mail Konto.
		Tragen Sie die Angaben Ihres Postausgangsservers hier ein (wird bei Wahl von \'sendmail\' nicht ben&ouml;tigt).';
$helptxt['attachmentEnable'] = 'Dateianh&auml;nge sind Dateien, die Mitglieder hochladen und an einen Beitrag anf&uuml;gen k&ouml;nnen.<br /><br />
		<b>Dateiendung pr&uuml;fen</b>:<br />M&ouml;chten Sie die Dateierweiterungen der Dateianh&auml;nge pr&uuml;fen?<br />
		<b>Erlaubte Dateitypen</b>:<br />Sie k&ouml;nnen hier die erlaubten Dateitypen eingeben, die hochgeladen werden d&uuml;rfen.<br />
		<b>Dateianhang als Bild im Beitrag anzeigen</b>:<br />Wenn die hochgeladene Datei ein Bild ist, wird es unterhalb des Beitrags als solches angezeigt.<br />
		<b>Dateien Upload Pfad</b>:<br />Der Pfad zum Dateianhangsordner<br />(Beispiel: /home/sites/ihreseite/www/forum/attachments)<br />
		<b>Dateianhangs URL</b>:<br /> The URL to your attachment folder<br />(Beispiel: http://www.ihreseite.de/forum/attachments)<br />
		<b>Max. Gr&ouml;&szlig;e des Upload-Verzeichnisses</b> (in KB):<br />W&auml;hlen Sie, wie gro&szlig; das Upload-Verzeichnis auf dem Server maximal sein darf.<br />
		<b>Max. Gr&ouml;&szlig;e der Dateianh&auml;nge pro Beitrag</b> (in KB):<br />W&auml;hlen Sie die maximale Gr&ouml;&szlig;e, die alle Dateianh&auml;nge pro Beitrag haben k&ouml;nnen.<br />
		<b>Max. Gr&ouml;&szlig;e pro Dateianhang</b> (in KB):<br />W&auml;hlen Sie die maximale Gr&ouml;&szlig;e, die ein Dateianhang haben darf.<br />
		<b>Max. Anzahl der Dateianh&auml;nge pro Beitrag</b>:<br />W&auml;hlen Sie maximale Anzahl an Dateianh&auml;ngen, die ein Benutzer an ein Beitag anh&auml;ngen darf.';
$helptxt['karmaMode'] = 'Karma ist eine Funktion, welche die Beliebtheit eines Mitgliedes anzeigt. Sie k&ouml;nnen die Anzahl der Beitr&auml;ge festlegen, ab der Karma genutzt werden darf,
		die Zeit zwischen zwei Abstimmungen und ob der Administrator auch von diesem Zeitlimit erfasst werden soll.';
// Old information!
$helptxt['cal_enabled'] = 'Der Kalender kann genutzt werden, um Geburtstage oder andere Ereignisse anzuzeigen.<hr />
		<b>Tag als Link zu neuem Ereignis anzeigen:</b><br />Erlaubt es, ein neues Ereignis zu erstellen wenn der Benutzer auf die Tageszahl klickt.<br />
		<b>Die Woche mit Montag beginnen:</b><br />Montag als Wochenanfang anzeigen.<br />
		<b>Wochennummer zeigen:</b><br />Zeigt die x. Woche an.<br />
		<b>Feiertage im Index anzeigen</b><br />
		<b>Geburtstage im Index anzeigen</b><br />
		<b>Ereignisse im Index anzeigen</b><br />
		<b>Min. Jahr:</b><br />Bestimmt das &quot;erste&quot; Jahr im Kalender.<br />
		<b>Max. Jahr:</b><br />Bestimmt das &quot;letzte&quot; Jahr im Kalender.<br />
		<b>Titel-Farbe:</b><br />Bestimmt die Farbe des aktuellen Monats.<br />
		<b>Farbe des aktuellen Tages:</b><br />Bestimmt die Farbe des aktuellen Tages.<br />
		<b>Geburtstagsfarbe:</b><br />Bestimmt die Farbe des Textes &quot;Geburtstag&quot;<br />
		<b>Ereignisfarbe:</b><br />Bestimmt die Farbe des Textes &quot;Ereignis&quot;<br />
		<b>Urlaubsfarbe:</b><br />Bestimmt die Farbe des Wortes &quot;Urlaub&quot;.<br />
		<b>Ereignisse d&uuml;rfen &uuml;ber mehrere Tage gehen:</b><br />Aktivieren Sie diese Option, so das Ereignisse &uuml;ber mehrere Tage gehen k&ouml;nnen<br />
		<b>Max. Ereignisdauer:</b><br />Bestimmt die Anzahl der Tage, die ein Ereignis maximal dauern kann<br />
		<b>Alle Mitglieder k&ouml;nnen schreiben:</b><br />Bestimmt, ob alle Mitglieder Ereignisse schreiben d&uuml;rfen<br />
		<b>Mitgliedergruppen die schreiben d&uuml;rfen:</b><br />Bestimmt die Mitgliedergruppen, die Ereignisse schreiben d&uuml;rfen<br />
		<b>Mitglieder die Ereignisse anlegen d&uuml;rfen:</b><br />Bestimmt die Mitglieder, die Ereignisse anlegen d&uuml;rfen<br />
		<b>Board in welches geschrieben werden soll:</b><br />W&auml;hle das Board, in welches die Ereignisse geschrieben werden sollen';
$helptxt['localCookies'] = 'SMF benutzt Coookies um die Login Informationen auf dem Computer zu speichern.
	Cookies k&ouml;nnen global (meineseite.de) oder lokal (meineseite.de/pfad/zum/forum) gespeichert werden.<br />
	Aktivieren Sie diese Option, wenn Sie automatisch ausgeloggt werden.<hr />
	Global gespeicherte Cookies sind weniger sicher, wenn sie auf auf einem Shared Server benutzt werden (z.B. Tripod).<hr />
	Lokal gespeicherte Cookies funktionieren nicht au&szlig;erhalb des Forum Verzeichnisses. Wenn das Forum unter www.meineseite.de/forum liegt, k&ouml;nnen Dateien wie www.meineseite.de/index.php nicht auf die Cookie Informationen zugreifen.
	Wenn Sie die Datei SSI.php benutzen, werden globale Cookies zwingend (!) ben&ouml;tigt.';
$helptxt['disabledBBC'] = 'Diese Option erlaubt das (teilweise) Deaktivieren des Bulletin Board Codes in Ihrem Forum. Tippen Sie dazu - durch Komma getrennt - die gew&uuml;nschten Codes in das Feld.<br /><br />
	<b>Beispiel:</b><br />\'move,glow,table,tr,td\' - Dies w&uuml;rde folgende Tags deaktivieren: Marquee, Gl&uuml;hen und alle Tabellen Tags.<br /><br />
	Bitte beachten Sie, dass dies nicht f&uuml;r alle Tags funktioniert.';
$helptxt['enableBBC'] = 'Erlaubt den Mitgliedern die Benutzung von Bulletin Board Code (BBC) im Forum, welcher den Text formatiert, Bilder einf&uuml;gen kann und vieles mehr.';
$helptxt['enableNewReplyWarning'] = 'Wenn aktiviert, bekommt das Mitglied - welches gerade auf einen Beitrag antwortet - eine Warnmeldung gezeigt, wenn in der Zwischenzeit ein weiterer Benutzer geantwortet hat. Damit kann man seinen Beitrag anpassen und vermeidet doppelte Antworten.';
$helptxt['time_offset'] = 'Nicht immer ist die Server Zeit gleich der Zeit, die vom Forum genutzt werden soll. Hier k&ouml;nnen Sie die Zeitdifferenz in Stunden eintragen (positive/negative Zahl), welche den Unterschied zwischen dem Server und der Forum Zeit machen.';
$helptxt['spamWaitTime'] = 'Tragen Sie hier das Zeitintervall ein, das ein Benutzer zwischen zwei Beitr&auml;gen einhalten muss. Dies kann zum Verhindern des sogenannten "Spammens" beitragen.';
$helptxt['enablePostHTML'] = 'Dies erlaubt das Benutzen von h&auml;ufigen HTML Befehlen:
	&lt;b&gt;, &lt;u&gt;, &lt;i&gt;, &lt;pre&gt;, &lt;blockquote&gt;, &lt;img src=&quot;&quot; /&gt;, &lt;a href=&quot;&quot;&gt;, and &lt;br /&gt;.';

$helptxt['themes'] = 'Hier k&ouml;nnen Sie das Standard Theme w&auml;hlen, welches Theme die G&auml;ste sehen sollen, sowie andere Optionen. Klicken Sie im rechten Rahmen auf ein Theme um die Einstellungen daf&uuml;r zu &auml;ndern.';
$helptxt['theme_install'] = 'Dies erlaubt es Ihnen, neue Themes zu installieren. Sie k&ouml;nnen ein schon vorhandenes Verzeichniss nutzen, ein Zip-Paket hochladen oder das vorhandene Theme kopieren<br /><br />Beachten Sie, dass das Verzeichnis bzw. das Zip-Paket die Datei <tt>theme_info.xml</tt> enthalten muss.';
$helptxt['enableEmbeddedFlash'] = 'Diese Option erlaubt es Benutzern, Flash in ihren Beitr&auml;gen zu nutzen (wie Bilder). Dies kann ein Sicherheitsrisiko sein! BENUTZUNG AUF EIGENE GEFAHR!';
$helptxt['xmlnews_enable'] = 'Erlaubt Benutzern zu den <a href="%s?action=.xml;sa=news">Letzten Neuigkeiten</a> zu verlinken. Es wird empfohlen, die Gr&ouml;&szlig;e der Neuesten Beitr&auml;ge/News zu begrenzen, da es zu falschen Darstellungen in manchen Programmen wie Trillian kommen kann.';
$helptxt['hotTopicPosts'] = '&Auml;ndert die Zahl der Beitr&auml;ge, nach denen ein Thema den Status &quot;hei&szlig;&quot; oder &quot;sehr hei&szlig;&quot; erh&auml;lt.';
$helptext['globalCookies'] = '
	Erm&ouml;glicht die Nutzung von Subdomains unabh&auml;ngigen Cookies. Ein Beispiel:<br />
	Ihre Seite hat die Domain http://www.simplemachines.org,<br />
	Ihr Forum hat die Domain http://forum.simplemachines.org,<br />
	Diese Ver&auml;nderung erm&ouml;glicht es auf die Forum Cookies ihrer Seite zuzugreifen.';
$helptxt['redirectMetaRefresh'] = 'SMF benutzt normalerweise einen &quot;Location&quot; header, welcher Sie zu den verschiedenen Orten im Forum weiterleitet. Auf manchen alten Servern ist es m&ouml;glich, dass es nicht funktioniert.<br /><br />Aktivieren Sie diese Option, wenn Sie Probleme mit dem eingeloggt bleiben haben.';
$helptxt['securityDisable'] = '<i>Deaktiviert</i> die erneute Passwort&uuml;berpr&uuml;fung f&uuml;r den Administratorbereich. NICHT EMPFEHLENSWERT!';
$helptxt['securityDisable_why'] = 'Das ist Ihr aktuelles Passwort (dasselbe, was Sie f&uuml;r das Einloggen benutzen).<br /><br />Warum Sie es eingeben sollten ? Damit Sie sich im Klaren dar&uuml;ber sind, dass <b>Sie</b> die &Auml;nderungen im Administrations Bereich machen und daf&uuml;r verantwortlich sind.';
$helptxt['emailmembers'] = 'In dieser Nachricht k&ouml;nnen Sie folgende Variablen benutzen:<br />
	{$board_url} - URL zu Ihrem Forum.<br />
	{$current_time} - Die aktuelle Zeit.<br />
	{$member.email} - Die aktuelle E-Mail Adresse des Mitgliedes.<br />
	{$member.link} - Den aktuellen Link zum betreffenden Mitglied.<br />
	{$member.id} - Die aktuelle Mitglieds ID.<br />
	{$member.name} - Den aktuellen Mitgliedsnamen (f&uuml;r private Mitteilungen).<br />
	{$latest_member.link} - Den Link zum neuesten Mitglied.<br />
	{$latest_member.id} - Die ID des neuesten Mitglieds.<br />
	{$latest_member.name} - Der Name des neuesten Mitglieds.';
$helptxt['attachmentEncryptFilenames'] = 'Verschl&uuml;sselte Dateinamen erlauben die Nutzung eines gleichen Dateinamens, sicheres Hochladen einer .php Datei und erh&ouml;ht die Sicherheit im Allgemeinen. Andererseits macht es das Wiederherstellen der Datenbank nach einem gro&szlig;en Crash schwieriger.';

$helptxt['failed_login_threshold'] = 'Gibt die Nummer der erfolglosen Login Versuche an, bevor der Benutzer zum Passwort Erinnerungs Bildschirm weitergeleitet wrid.';
$helptxt['edit_wait_time'] = 'Anzahl der Sekunden, bevor das Datum des letzten Editierens gespeichert wird.';
$helptxt['enableSpellChecking'] = 'Aktiviert die Rechtschreibepr&uuml;fung. Sie M&Uuml;SSEN die pspell Bibliothek auf dem Server installiert haben und PHP muss so konfiguriert sein, dass es selbige auch benutzt. Ihr Server ' . (function_exists('pspell_new') == 1 ? 'HAT' : 'HAT NICHT') . ' diese Funktion.';
$helptxt['lastActive'] = 'Gibt die Anzahl der Minuten an, in welcher die Besucher auf dem Board-Index als aktiv gekennzeichnet werden. Standard sind 15 Minuten.';

$helptxt['autoOptDatabase'] = 'Diese Funktion optimiert die Datenbank alle angegebenen Tage. Geben Sie 1 ein, um die Datenbank t&auml;glich zu optimieren. Sie k&ouml;nnen ebenfalls eine max. Zahl von Benutzern angeben die online sind, damit es keine Probleme mit der Servergeschwindigkeit gibt.';
$helptxt['autoFixDatabase'] = 'Diese Funktion repariert automatisch auftretende Fehler, wobei die Benutzer nichts davon merken werden. Das kann sinnvoll sein, andererseits ist das Forum solange funktionsunt&uuml;chtig, bis Sie es selbst merken werden. Ihnen wird in dem Fall eine E-Mail zugesendet.';

$helptxt['enableParticipation'] = 'Zeigt ein ver&auml;ndertes Symbol vor den Themen, in denen man selbst geantwortet hat.';

$helptxt['db_persist'] = 'Erh&ouml;ht die Geschwindigkeit zur Datenbank, indem eine Verbindung dauerhaft aufrecht erhalten wird. Wenn Sie einen dedizierten Server benutzen, k&ouml;nnte es Probleme mit Ihrem Host geben.';

$helptxt['queryless_urls'] = 'Ver&auml;ndert das Format der URL\'s, so dass Suchmaschinen sie besser aufnehmen (z.B. index.php/topic,1.html).<br /><br />Diese Option funktioniert ' . (strpos(php_sapi_name(), 'apache') !== false ? '' : 'nicht') . ' mit Ihrem Server.';
$helptxt['fixLongWords'] = 'Diese Option verk&uuml;rzt W&ouml;rter einer bestimmten L&auml;nge (Autolenkrad = Autol...), so dass diese nicht das Layout des Forums zerst&ouml;ren.';

$helptxt['who_enabled'] = 'Erlaubt das Ein-/Ausschalten der \'Wer ist online\' Funktion, bei der die Benutzer sehen k&ouml;nnen, wer online ist und wer gerade was macht.';

$helptxt['recycle_enable'] = '&quot;Wiederherstellung&quot; von gel&ouml;schten Themen und Beitr&auml;gen in die entsprechenden Boards.';

$helptxt['default_personalText'] = 'Gibt den Text an, der als standardm&auml;&szlig;iger &quot;Pers&ouml;nlicher Text&quot; angezeigt wird.';

$helptxt['modlog_enabled'] = '&Uuml;berwacht alle Aktionen der Moderatoren.';

$helptxt['guest_hideContacts'] = 'Diese Option versteckt die E-Mail Adresse und die Messenger Angaben aller Mitglieder vor G&auml;sten.';

$helptxt['registration_method'] = 'Diese Option stellt verschiedene M&ouml;glichkeiten der Registrierung zur Verf&uuml;gung. Sie k&ouml;nnen aus folgenden w&auml;hlen:<br /><br />
	<ul>
		<li>
			<b>Registrierung deaktiviert:</b><br />
				Deaktiviert die Registrierung, so dass sich kein neues Mitglied in diesem Forum registrieren kann.<br />
		</li><li>
			<b>Sofortige Registrierung</b><br />
				Neue Mitglieder k&ouml;nnen sich sofort einloggen und Beitr&auml;ge schreiben, nachdem Sie sich registriert haben.<br />
		</li><li>
			<b>Mitglieder Aktivierung</b><br />
				Nach der Registrierung erhalten neue Mitglieder eine E-Mail mit einem Aktivierungs Link, welchen Sie anklicken m&uuml;ssen, bevor sie das Forum nutzen k&ouml;nnen.<br />
		</li><li>
			<b>Mitglieder Genehmigung</b><br />
				Alle neuen Mitglieder m&uuml;ssen zuerst vom Administrator akzeptiert werden, bevor sie das Forum nutzen k&ouml;nnen.
		</li>
	</ul>';

$helptxt['send_validation_onChange'] = 'Alle Mitglieder m&uuml;ssen bei einer &Auml;nderung der E-Mail Adresse diese best&auml;tigen, bevor sie ihren Zugang wieder benutzen k&ouml;nnen..';
$helptxt['send_welcomeEmail'] = 'Allen Mitgliedern wird eine Willkommens E-Mail geschickt, wenn sie sich im Forum anmelden.';
$helptxt['allow_hideOnline'] = 'Aktivieren Sie diese Option, k&ouml;nnen alle Mitglieder ihren Online Status verstecken (au&szlig;er vor Administratoren). Wenn Sie diese Option deaktivieren k&ouml;nnen nur Mitglieder den Status verstecken, welche die M&ouml;glichkeit haben das Forum moderieren. Das deaktivieren &auml;ndert keinen Status eines Mitglieds, es verhindert nur das Verstecken des Status in der Zukunft.';
$helptxt['allow_hideEmail'] = 'Wenn diese Option aktiviert ist, k&ouml;nnen Mitglieder w&auml;hlen, ob sie ihre E-Mail Adresse vor andern Benutzern verstecken d&uuml;rfen. Der Administrator hingegen kann alle E-Mail Adressen betrachten.';

$helptxt['latest_support'] = 'Dieser Bereich zeigt die h&auml;ufigsten Probleme und Fragen zur Ihrer Server Konfiguration. Diese Informationen werden nicht gespeichert.<br /><br />Sollte es bei &quot;Lade Support Informationen...&quot; stehen bleiben, kann Ihr Computer wahrscheinlich nicht zu <a href="http://www.simplemachines.org/">www.simplemachines.org</a> verbinden.';
$helptxt['latest_packages'] = 'Hier k&ouml;nnen Sie ein paar der beliebtesten und zuf&auml;llig ausgew&auml;hlten Modifikationen bzw. Pakete sehen, welche leicht und schnell zu installieren sind.<br /><br />Wenn dieser Bereich nicht sichtbar ist, kann Ihr Computer wahrscheinlich nicht zu <a href="http://www.simplemachines.org/">www.simplemachines.org</a> verbinden.';
$helptxt['latest_themes'] = 'Dieser Bereich zeigt die neuesten und beliebtesten Themes von<a href="http://www.simplemachines.org/">www.simplemachines.org</a> an. Sollte er nicht sichtbar sein, kann Ihr Copmuter wahrscheinlich nicht zu <a href="http://www.simplemachines.org/">www.simplemachines.org</a> verbinden.';

$helptxt['secret_why_blank'] = 'Zu Ihrer Sicherheit wird die Antwort (genauso wie Ihr Passwort) zu Ihrer Frage verschl&uuml;sselt, so dass SMF Ihnen nur sagen kann ob es richtig ist, jedoch nicht die Antwort oder das Passwort selbst nennen kann!';
$helptxt['moderator_why_missing'] = 'Da die Moderatoren von Board zu Board ausgew&auml;hlt werden, m&uuml;ssen Sie diese im Bereich <a href="javascript:window.open(\'%s?action=manageboards\'); self.close();">Verwalte Boards</a> eintragen.';

$helptxt['permissions'] = 'Berechtigungen haben die Funktion, bestimmten Gruppen Aktionen zu erlauben oder zu verbieten<br /><br />Sie k&ouml;nnen mit Hilfe der Check-Boxen mehrere Boards gleichzeitig &auml;ndern oder die Berechtigungen einer bestimmten Gruppe &auml;ndern, in dem Sie auf \'&Auml;ndern\' klicken.';
$helptxt['permissions_board'] = 'Wenn ein Board auf \'Global\' gesetzt ist, hat es keine speziellen Berechtigungen. \'Lokal\' dagegen hat eigene Berechtigungen, welche sich von anderen Boards unterscheiden. Dies erlaubt unterschiedliche Berechtigungen zwischen den Boards.';
$helptxt['permissions_quickgroups'] = 'Erlaubt das Verwenden der &quot;vordefinierten&quot; Berechtigungen - Standard bedeutet \'nichts spezielles\', Beschr&auml;nkt hei&szlig;t \'wie ein Gast\', Moderator vergibt Rechte \'wie einem Moderator\' und \'Wartungsmodus\' bedeutet, dass die Berechtigungen einem Administrator sehr nahe kommen.';
$helptxt['membergroups'] = 'In SMF gibt es zwei Arten von Gruppen, denen die Mitglieder zugeteilt sind:
	<ul>
		<li><b>Regul&auml;re Gruppen:</b> In eine regul&auml;re Gruppe werden Mitglieder nicht automatisch eingeteilt. Um ein Mitglied einer dieser Gruppen zuzuordnen, gehen Sie in das Profil des Mitgliedes und klicken auf &quot;Zugangseinstellungen&quot;. Von hier aus k&ouml;nnen Sie nun das Mitglied verschiedenen Gruppen zuordnen.</li>
		<li><b>Beitragsabh&auml;ngige Gruppen:</b> Im Gegenteil zu regul&auml;ren Gruppen k&ouml;nnen Sie beitragsabh&auml;ngige Gruppen nicht zuordnen. Stattdessen werden Mitglieder diesen automatisch zugeordnet, wenn sie eine bestimmte Zahl an Beitr&auml;gen geschrieben haben.</li>
	</ul>';

$helptxt['calendar_how_edit'] = 'Sie k&ouml;nnen die Ereignisse editieren, indem Sie auf den roten Stern (*) neben dem Namen klicken.';

$helptxt['maintenance_general'] = 'Von hier aus k&ouml;nnen Sie alle Tabellen in der Datenbank optimieren (sie werden kleiner und schneller), kontrollieren ob Sie die neueste Version haben, alle Fehler finden die das Board betreffen, die Forumswerte neu berechnen und Log Dateien l&ouml;schen.<br /><br />Die letzten zwei Optionen sollten Sie nur bei Fehlern im Board anwenden, sind jedoch nicht sch&auml;dlich, wenn Sie sie trotzdem benutzen.';
$helptxt['maintenance_backup'] = 'Dieser Bereich erlaubt eine Sicherung von allen Beitr&auml;gen, Einstellungen, Mitgliedern und anderen Informationen Ihres Forums in einer (wom&ouml;glich gro&szlig;en) Datei.<br /><br />Es ist empfehlenswert, dies regelm&auml;&szlig;ig zu tun - am besten w&ouml;chentlich -, um die Datensicherheit zu erh&ouml;hen.';
$helptxt['maintenance_rot'] = 'Dies erlaubt das <b>komplette</b> und <b>unwiderrufliche</b> L&ouml;schen alter Themen. Es ist empfehlenswert, davor eine Sicherung Ihrer Daten zu machen f&uuml;r den Fall, dass Sie etwas l&ouml;schen, was Sie nicht wollten.<br /><br />Nutzen Sie diese Option mit Vorsicht.';

$helptxt['avatar_allow_server_stored'] = 'Erlaubt den Mitgliedern ein Profilbild zu w&auml;hlen, welches auf Ihrem Server liegt. Diese Bilder sind normalerweise am selben Platz wie SMF, nur im avatar Verzeichnis.<br />Tip: Wenn Sie Ordner im avatar Verzeichnis erstellen, k&ouml;nnen Sie dadurch &quot;Kategorien&quot; erstellen.';
$helptxt['avatar_allow_external_url'] = 'Erlaubt den Mitgliedern die Eingabe einer URL zu ihrem eigenen Profilbild. Der Nachteil dieser Option ist, dass zu gro&szlig;e Bilder benutzt werden, welche dann den Aufbau des Forums zerst&ouml;ren bzw. andere Sachen &uuml;berlappen oder Inhalte enthalten, die nicht Ihrem Geschmack entsprechen.';
$helptxt['avatar_check_size'] = 'Diese Option hilft, die Gr&ouml;&szlig;e des Profilbildes zu &uuml;berpr&uuml;fen, kann aber die Geschwindigkeit des Boards vermindern. Benutzen Sie die Funktion mit Vorischt.';
$helptxt['avatar_allow_upload'] = 'Erlaubt den Mitgliedern das hochladen eigener Profilblider auf Ihren Server. Es bietet den Vorteil, dass Sie eine bessere Kontrolle &uuml;ber die Bilder haben, sie schneller in der Gr&ouml;&szlig;e ge&auml;ndert werden k&ouml;nnen und nicht von fremden, langsamen Servern geladen werden m&uuml;ssen.<br /><br />Der Nachteil ist, dass die Bilder Platz auf Ihrem Server wegnehmen, was man nicht untersch&auml;tzen sollte.';
$helptxt['avatar_download_png'] = 'PNGs sind gr&ouml;&szlig;er, bieten aber eine bessere Kompression. Sollte dies nicht aktiviert sein, werden stattdessen JPEG Bilder benutzt - welche meistens kleiner in der Gr&ouml;&szlig;e sind, jedoch eine schlechtere Qualit&auml;t bieten.';

$helptxt['disableHostnameLookup'] = 'Deaktiviert die Suche nach den Host Namen, was manche Server sehr langsam macht. Beachten Sie, dass dies das Bannen von Mitgliedern uneffektiver macht.';

$helptxt['search_match_complete_words'] = 'Diese Option limitiert die Suche auf Resultate, die nur das entsprechende Wort enthalten. Ist sie deaktiviert, zeigt die Suche nach dem Wort \'und\' auch Ergebnisse wie \'wund\', \'Pfund\' oder \'Hund\'. Wenn Sie die Option aktivieren, werden nur Ergebnisse mit dem Wort \'und\' angezeigt.<br /><br />Bitte beachten Sie, dass das aktivieren dieser Option die Suche verlangsamen kann, speziell wenn die Menge der Beitr&auml;ge sehr hoch ist.';
$helptxt['disableTemporaryTables'] = 'Diese Option verhindert das Benutzen von tempor&auml;ren Tabellen. Dies macht die Suche etwas langsamer, ist aber sinnvoll wenn Sie keine Berechtigung haben, tempor&auml;re Tabellen in der Datenbank zu erstellen.';
$helptxt['search_cache_size'] = 'Zwischenspeichern der Suche reduziert die Zugriffe auf die Datenbank. Nachdem der Benutzer einen Suchbegriff eingegeben hat, wird das Ergebnis in der Datenbank zwischengespeichert. Auf diesem Weg sind die folgenden Seiten der Suchresultate sofort verf&uuml;gbar und m&uuml;ssen nicht nachgeladen werden.<br /><br />Benutzen Sie diese Option, um die Zahl der gespeicherten Suchresultate zu begrenzen. Das Erh&ouml;hen der Zahl verbraucht mehr Speicherplatz in der Datenbank (ca. 20 KB pro Resultat).';
$helptxt['search_weight_frequency'] = 'Gewichtungsfaktoren werden benutzt, um die Relevanz eines Suchresultates zu bestimmen. Ver&auml;ndern Sie diese Gewichtungen, um die Resultate auf Ihr Forum abzustimmen. Das Forum einer News-Seite zum Beispiel ben&ouml;tigt eine hohe Gewichtung auf \'Alter der neuesten &Uuml;bereinstimmung\'. Alle Werte sind relativ zueinander und sollten positive Zahlen sein.<br /><br />Dieser Faktor z&auml;hlt die Anzahl der &uuml;bereinstimmenden Beitr&auml;ge und teilt sie durch die gesamte Zahl der Beitr&auml;ge innerhalb eines Themas.';
$helptxt['search_weight_age'] = 'Gewichtungsfaktoren werden benutzt, um die Relevanz eines Suchresultates zu bestimmen. Ver&auml;ndern Sie diese Gewichtungen, um die Resultate auf Ihr Forum abzustimmen. Das Forum einer News-Seite zum Beispiel ben&ouml;tigt eine hohe Gewichtung auf \'Alter der neuesten &Uuml;bereinstimmung\'. Alle Werte sind relativ zueinander und sollten positive Zahlen sein.<br /><br />Dieser Faktor bewertet das Alter der neuesten &Uuml;bereinstimmung innerhalb eines Themas. Je neuer der Beitrag, desto h&ouml;her ist die Bewertung.';
$helptxt['search_weight_length'] = 'Gewichtungsfaktoren werden benutzt, um die Relevanz eines Suchresultates zu bestimmen. Ver&auml;ndern Sie diese Gewichtungen, um die Resultate auf Ihr Forum abzustimmen. Das Forum einer News-Seite zum Beispiel ben&ouml;tigt eine hohe Gewichtung auf \'Alter der neuesten &Uuml;bereinstimmung\'. Alle Werte sind relativ zueinander und sollten positive Zahlen sein.<br /><br />Dieser Faktor basiert auf der Themengr&ouml;&szlig;e. Je mehr Beitr&auml;ge innerhalb eines Themas, desto h&ouml;her ist die Bewertung.';
$helptxt['search_weight_subject'] = 'Gewichtungsfaktoren werden benutzt, um die Relevanz eines Suchresultates zu bestimmen. Ver&auml;ndern Sie diese Gewichtungen, um die Resultate auf Ihr Forum abzustimmen. Das Forum einer News-Seite zum Beispiel ben&ouml;tigt eine hohe Gewichtung auf \'Alter der neuesten &Uuml;bereinstimmung\'. Alle Werte sind relativ zueinander und sollten positive Zahlen sein.<br /><br />Dieser Faktor schaut nach dem Vorhandensein einer Suchanfrage innerhalb des Betreffs eines Themas.';
$helptxt['search_weight_first_message'] = 'Gewichtungsfaktoren werden benutzt, um die Relevanz eines Suchresultates zu bestimmen. Ver&auml;ndern Sie diese Gewichtungen, um die Resultate auf Ihr Forum abzustimmen. Das Forum einer News-Seite zum Beispiel ben&ouml;tigt eine hohe Gewichtung auf \'Alter der neuesten &Uuml;bereinstimmung\'. Alle Werte sind relativ zueinander und sollten positive Zahlen sein.<br /><br />Dieser Faktor schaut nach der &Uuml;bereinstimmung des Suchbegriffs im ersten Beitrag eines Themas.';

$helptxt['see_admin_ip'] = 'IP Adressen werden Administratoren und Moderatoren zur besseren Moderation bzw. Verfolgung angezeigt. Beachten Sie, dass IP Adressen einen Benutzer nicht eindeutig identifizieren und bei den meisten Leuten nach einiger Zeit wechseln.<br /><br />Mitglieder k&ouml;nnen Ihre eigenen IP Adressen sehen.';
$helptxt['see_member_ip'] = 'Ihre IP Adresse wird nur Ihnen und Moderatoren angezeigt. Beachten Sie, dass diese Daten nicht die Person identifizieren, da IP Adressen h&auml;ufig nach einer gewissen Zeit wechseln.<br /><br />Sie k&ouml;nnen keine IP Adressen anderer Benutzer sehen und diese k&ouml;nnen Ihre nicht sehen.';

?>