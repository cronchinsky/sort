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
 * This file keeps track of upgrades to the sort module
 *
 * Sometimes, changes between versions involve alterations to database
 * structures and other major things that may break installations. The upgrade
 * function in this file will attempt to perform all the necessary actions to
 * upgrade your older installation to the current version. If there's something
 * it cannot do itself, it will tell you what you need to do.  The commands in
 * here will all be database-neutral, using the functions defined in DLL libraries.
 *
 * @package    mod
 * @subpackage sort
 * @copyright  2011 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Execute sort upgrade from the given old version
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_sort_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager(); // loads ddl manager and xmldb classes

     if ($oldversion < 2012082101) {

        // Define field grade to be added to sort
        $table = new xmldb_table('sort');
        $field = new xmldb_field('grade', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'popupwidth');

        // Conditionally launch add field grade
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // sort savepoint reached
        upgrade_mod_savepoint(true, 2012082101, 'sort');
    }

        if ($oldversion < 2012082200) {

        // Define field correct_answer to be added to sort_studentwork
        $table = new xmldb_table('sort_studentwork');
        $field = new xmldb_field('correct_answer', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0', 'name');

        // Conditionally launch add field correct_answer
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // sort savepoint reached
        upgrade_mod_savepoint(true, 2012082200, 'sort');
    }
    
    
        if ($oldversion < 2012091800) {

        // Define field has_correct to be added to sort
        $table = new xmldb_table('sort');
        $field = new xmldb_field('has_correct', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, '0', 'grade');

        // Conditionally launch add field has_correct
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // sort savepoint reached
        upgrade_mod_savepoint(true, 2012091800, 'sort');
    }


    return true;
}
