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
        GROUP BY 1";

	    $courses = $DB->get_records_sql($sql);


    $activitysince = get_config('block_heatmap', 'activitysince');
        if ($activitysince === false) {
            $activitysince = 'sincestart';
        }

	  // get data using courses data

    $data = array();
    
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
            //print_r($sql);
            $views = $DB->get_records_sql($sql, $params);
            $myArray[] = $views;
	      }
    }
  // flag data that needs deleted

    $sql_update = 'UPDATE mdl_block_heatmap SET status = 1';
    $DB->execute($sql_update, $parms=null);

    var_dump($myArray);

    foreach ($myArray as $key) {
        foreach ($key as $k){
        var_dump($k);
        $lastinsertid = $DB->insert_record('block_heatmap', $k, false);
        }
    }

    // delete old data
    $params = array('status' => '1');
    $DB->delete_records('block_heatmap',$params);

    }                                                                                                                               
} 

