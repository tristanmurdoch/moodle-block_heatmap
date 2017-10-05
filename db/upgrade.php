<?php
function xmldb_block_heatmap_upgrade($oldversion) {
    global $CFG, $DB;
    $dbman = $DB->get_manager();
    if ($oldversion < 2016120706) {
        // Define table heatmap to be created.
        $table = new xmldb_table('block_heatmap');
        // Adding fields to table heatmap.
        $table->add_field('cmid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('numviews', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('distinctusers', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('status', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        // Adding keys to table heatmap.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('cmid'));
        // Adding indexes to table heatmap.
        $table->add_index('courseid', XMLDB_INDEX_NOTUNIQUE, array('courseid'));
 

	if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        // heatmap savepoint reached.
        upgrade_block_savepoint(true, 2016120706, 'heatmap');
}
return true;
}
