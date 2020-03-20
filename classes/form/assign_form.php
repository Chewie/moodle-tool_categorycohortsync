<?php

namespace tool_categorycohortsync\form;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use moodleform;
use core_course_category;

class assign_form extends moodleform {
    public function definition() {
        global $CFG;
        global $DB;

        $mform = $this->_form;

        $mform->addElement('cohort', 'form_cohort', 'cohort');


        $categories = core_course_category::make_categories_list();
        $mform->addElement('select', 'form_category', 'category', $categories);


        $sql = 'SELECT r.* FROM {role} as r
                INNER JOIN {role_context_levels} AS rcl ON r.id = rcl.roleid
                WHERE rcl.contextlevel = 40';
        $roles = $DB->get_records_sql($sql);
        $roles = array_map(function ($r) { return $r->shortname; }, $roles);
        $mform->addElement('select', 'form_role', 'role', $roles);

        $this->add_action_buttons(false, 'Apply');
    }

    function validation($data, $files) {
        return array();
    }
}
