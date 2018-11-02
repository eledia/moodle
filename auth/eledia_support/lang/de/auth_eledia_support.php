<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 *
 * @package     auth
 * @subpackage  eledia_support
 * @author      Benjamin Wolf <support@eledia.de>
 * @copyright   2018 eLeDia GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['auth_eledia_supporttitle'] = 'eLeDia Support Login';
$string['auth_eledia_supportdescription'] = 'Diese Authentifizierung wird ausschließlich für Support-Zugänge durch eLeDia genutzt. Bitte deaktivieren Sie diese Methode nicht, da Sie damit verhindern, dass eLeDia für Sie Supportleistungen erbringen kann.
Mitarbeiter von eLeDia loggen sich hierüber persönlich ein. Dadurch kann von Ihnen datenschutzkonform nachvollzogen werden, welcher Mitarbeiter sich wann eingeloggt hat.<br />
    <br />&copy; eLeDia GmbH';
$string['account_expiration'] = 'Account-Gültigkeit (nach Tagen der Inaktivität)';
$string['account_expiration_desc'] = 'Der Supportaccount von eLeDia wird nach der angegeben Anzahl an Tagen automatisch gelöscht.';
$string['auth_emailnoemail'] = 'Der Versuch ist gescheitert, eine Bestätigungs E-Mail zu senden!';
$string['alreadyconfirmed'] = 'Die Registrierung wurde bereits bestätigt';

$string['confirmation_email'] = 'E-Mail-Adresse zur Bestätigung neuer Accounts';
$string['confirmed'] = 'Die Registrierung wurde bestätigt.';
$string['confirmation_email_desc'] = 'Wenn Sie hier eine E-Mail-Adresse eintragen, ist es erforderlich, neue Support-Accounts durch eLeDia erst manuell zu bestätigen, bevor sie genutzt werden können.
Dies erfolgt in der Nutzerverwaltung. Wenn Sie diese Funktion aktivieren, kann der Support erst erbracht werden, wenn Sie den Nutzeraccount freigegeben haben.';
$string['confirm_info_email_subject'] = 'Die Registrierung wurde bestätigt';
$string['confirm_info_email_message'] = 'Guten Tag {$a->fullname},

das neue Support Nutzerkonto wurde für {$a->sitename} wurde bestätigt.

{$a->admin} Ihr E-Learning-Team';

$string['emailconfirm'] = 'Bestätigung des Support Accounts';
$string['emailconfirmation'] = 'Guten Tag,

ein neues Support Nutzerkonto wurde für {$a->sitename} mit der email {$a->email} angefordert.
Wir können erst Support leisten, nachdem das Nutzerkonto bestätigt wurde.

Um das Nutzerkonto zu bestätigen gehen Sie bitte zur folgenden Webadresse:

{$a->link}

In den meisten E-Mail-Programmen ist der Link aktiv und muss einfach angeklickt werden.
Sollte das nicht funktionieren, kopieren Sie bitte die Webadresse in die Adresszeile des Browserfensters.
Das Nutzerkonto wird automatisch gelöscht, wenn es nicht über den obigen Link bestätigt wird.

Bei Problemen wenden Sie sich bitte an die Administrator/innen der Website.

{$a->admin} Ihr E-Learning-Team';
$string['emailconfirmationsubject'] = '{$a}: Support Bestätigung';
$string['emailconfirmsent'] = '<p>Um sicherzugehen, dass sich niemand unberechtigt anmeldet, wird eine automatische Benachrichtigung an eine Bestätigungsadesses gesendet.</p>'
        .'<p>Der Account muss dann vom Verantwortlichen bestätigt werden.</p> <p>Bei Problemen wenden Sie sich bitte an die Administrator/innen der Website.</p>';
$string['error_redirectafterlogouturl'] = 'Es sieht so aus als hätten Sie eine ungültige URL eingegeben. Bitte prüfen Sie Ihre Eingaben und versuchen es nochmal.';

$string['noauth'] = 'Sie konnten nicht bestätigt werden. Der Nutzer hat die falsche Authentifizierung.';
$string['nologinpossible'] = 'Direkter Login ist für die SSO Authentifizierung verboten.';
$string['nouser'] = 'Sie konnten nicht bestätigt werden. Der Nutzer wurde nicht gefunden.';
$string['novalidation'] = 'Sie konnten nicht bestätigt werden. Versuchen Sie es bitte erneut.';
$string['notimevalidation'] = 'Sie konnten nicht bestätigt werden. Zugangsschlüssel abgelaufen.';

$string['pluginname'] = 'eLeDia Support Login';
$string['privacy:metadata'] = 'Das eleDia Support Anmelde Plugin speichert keine persönlichen Daten.';

$string['redirectafterlogouturl'] = 'Weiterleitung nach Logout';
$string['redirectafterlogouturl_description'] = 'Sie können den User auf eine bestimmte Seite weiterleiten nachdem er sich vom Moodle-System abgemeldet hat. Geben Sie dazu einen vollständige URL ein, bspw.: http://moodle.org. Eine Weiterleitung erfolgt nur wenn eine URL definiert wurde.';

$string['secretkey'] = 'Schlüssel für Authentifizierung';
$string['secretkey_description'] = 'Dient zur Verschlüsselung der Anmeldung';

$string['task_process'] = 'Abgelaufenen Accounts aufräumen';
