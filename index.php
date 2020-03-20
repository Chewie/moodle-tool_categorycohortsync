<?php

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

global $DB;

admin_externalpage_setup('tool_categorycohortsync');

$title = get_string('pluginname', 'tool_categorycohortsync');
$pagetitle = $title;
$url = new moodle_url('/admin/tool/categorycohortsync/index.php');

$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);

$output = $PAGE->get_renderer('tool_categorycohortsync');

echo $output->header();
echo $output->heading($pagetitle);

$form = new tool_categorycohortsync\form\assign_form();

if ($data = $form->get_data()) {
    require_sesskey();

    echo 'COHORT: ' . $data->form_cohort . PHP_EOL;
    echo 'CAT: ' . $data->form_category . PHP_EOL;
    echo 'ROLE: ' . $data->form_role . PHP_EOL;

    $params['cohortid'] = $data->form_cohort;
    $sql = 'SELECT u.* FROM {user} u
            INNER JOIN {cohort_members} cm ON (cm.userid = u.id AND cm.cohortid = :cohortid)';
    $users = $DB->get_records_sql($sql, $params);
    foreach($users as $user) {
        echo $user->username . PHP_EOL;
        $ctx = context_coursecat::instance($data->form_category);
        role_assign($data->form_role, $user->id, $ctx);

        $role = $DB->get_record('role', ['id' => $data->form_role]);

        $categoryName = core_course_category::get($data->form_category)->name;
        $msg = sprintf("%s %s was enrolled as %s in category %s",
                       $user->firstname,
                       $user->lastname,
                       $role->name,
                       $categoryName);
        \core\notification::success($msg);
    }

    echo $output->continue_button(new moodle_url($url));
} else {
    $form->display();
}

echo $output->footer();
