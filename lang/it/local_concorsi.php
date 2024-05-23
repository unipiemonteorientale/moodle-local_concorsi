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
 * Strings for component 'local_concorsi', language 'it'
 *
 * @package   local_concorsi
 * @copyright 2023 UPO www.uniupo.it
 * @author    Roberto Pinna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Default strings.
$string['pluginname'] = 'Concorsi';

// Availability & privacy strings.
$string['concorsi:createusers'] = 'Creare utenti con credenziali casuali e iscriverli al corso';
$string['privacy:metadata'] = 'Il plugin Concorsi crea utenti con credenziali casuali e assegna loro il ruolo studente al corso attuale. Non salva alcun dato.';

// Settings strings.
$string['roles'] = 'Ruoli';
$string['configroles'] = 'I gestori dei concorsi possono iscrivere gli utenti con i ruoli selezionati';
$string['usernamelength'] = 'Lunghezza Username';
$string['configusernamelength'] = 'La lunghezza degli username generati casualmente';
$string['passwordlength'] = 'Lunghezza Password';
$string['configpasswordlength'] = 'La lunghezza delle password generate casualmente';
$string['idnumberlength'] = 'Lunghezza Codice identificativo';
$string['configidnumberlength'] = 'La lunghezza dei codici identificativi degli utenti, riempiti con zero per raggiungere la lunghezza';
$string['emaildomain'] = 'Dominio email';
$string['configemaildomain'] = 'Il nome di dominio finto per le email degli utenti. Non viene utilizzato per inviare messaggi';
$string['usercardtemplate'] = 'Template scheda utente';
$string['configusercardtemplate'] = 'Il template scheda utente viene utilizzato per generare il file pdf con le credenziali per gli utenti. Pu&ograve; includere tag html';
$string['localstore'] = 'Archivia file credenziali';
$string['configlocalstore'] = 'Archivia i file delle credenziali nel filesystem di Moodle';


// Plugin interface strings.
$string['manageusers'] = 'Gestisci utenti concorso';
$string['addusers'] = 'Aggiungi utenti';
$string['publicexamname'] = 'Nome del concorso';
$string['publicexamdate'] = 'Data del concorso';
$string['numberofusers'] = 'Numero di utenti da creare';
$string['role'] = 'Ruolo';
$string['usercardfiles'] = 'File schede utente';
$string['managefiles'] = 'Gestione file schede utente';
