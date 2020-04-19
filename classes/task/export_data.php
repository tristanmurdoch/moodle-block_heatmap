<?php
namespace block_heatmap\task;

class export_data extends \core\task\scheduled_task {      
    public function get_name() {
        // Shown in admin screens
        return get_string('export_data_task', 'block_heatmap');
    }
                                                                     
    public function execute() {       
    global $CFG, $DB;


	    // get courses with heatmap block
        $sql = "SELECT c.id,c.startdate
            FROM {context} cx
            JOIN {block_instances} bi ON bi.parentcontextid = cx.id 
            JOIN {course} c ON c.id = cx.instanceid 
            WHERE bi.blockname LIKE 'heatmap'
            AND c.id in (SELECT DISTINCT courseid FROM {user_lastaccess}
            WHERE timeaccess >= TRUNC(extract(EPOCH FROM now() - INTERVAL '24 HOURS')))
            GROUP BY 1";

        $courses = $DB->get_records_sql($sql);
        $activitysince = get_config('block_heatmap', 'activitysince');
            if ($activitysince === false) {
                $activitysince = 'sincestart';
            }

	    // get data using courses data
        foreach($courses as $course){
            if(isset($course->id)){
                $timesince = ($activitysince == 'sincestart') ? 'AND timecreated >= :coursestart' : '';
            
                $sql = "SELECT contextinstanceid as cmid, COUNT('x') AS numviews, COUNT(DISTINCT userid) AS distinctusers, '$course->id' as courseid, '0' as status
                        FROM {logstore_standard_log} l
                        WHERE courseid = :courseid
                        $timesince
                        AND anonymous = 0
                        AND crud = 'r'
                        AND contextlevel = :contextmodule
                        GROUP BY contextinstanceid";

	            $params = array('courseid' => $course->id, 'contextmodule' => CONTEXT_MODULE, 'coursestart' => $course->startdate );
                $views = $DB->get_records_sql($sql, $params);

                foreach ($views as $view) {
                    if ($DB->get_record('block_heatmap', (array) $view)) {
                        // TODO use $DB->update_record but need to change table to have ID.
                        echo "record needs updating\n";
                        $DB->delete_records('block_heatmap', (array) $view);
                        $DB->insert_record('block_heatmap', $view, false);
                    } else {
                        echo "record needs inserting\n";
                        $DB->insert_record('block_heatmap', $view, false);
                    }
                }
            }
        }
    }
}
