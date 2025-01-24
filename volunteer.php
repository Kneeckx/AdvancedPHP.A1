<?php
/**
 * Plugin Name: Volunteer Opportunity Plugin
 * Description: A plugin to create, manage, and display volunteer opportunities.
 * Author: Nicolas Chiasson
 */

function myplugin_activate() {
    global $wpdb;
    $wpdb->query("CREATE TABLE VolunteerInfo (
    VolunteerID int NOT NULL AUTO_INCREMENT,
    Position varchar(255),
    Organization varchar(255),
    Type varchar(255),
    Email varchar(255),
    Description varchar(255),
    Location varchar(255),
    Hours varchar(255),
    SkillsRequired varchar(255),
    PRIMARY KEY(VolunteerID)
    );");
    $wpdb->query("INSERT INTO VolunteerInfo (Position, Organization, Type, Email, Description, Location, Hours, SkillsRequired) VALUES ('Coffee Break', 'Org1', 'Type1', 'coffee@example.com', 'Desc1', 'Location1', 'Hours1', 'Skills1');");
    $wpdb->query("INSERT INTO VolunteerInfo (Position, Organization, Type, Email, Description, Location, Hours, SkillsRequired) VALUES ('Pizza Lunch', 'Org2', 'Type2', 'pizza@example.com', 'Desc2', 'Location2', 'Hours2', 'Skills2');");
}
register_activation_hook( __FILE__, 'myplugin_activate' );


function myplugin_deactivate() {
    global $wpdb;
    $wpdb->query("DELETE FROM VolunteerInfo;");
}
register_deactivation_hook( __FILE__, 'myplugin_deactivate' );    
    

function wporg_shortcode($atts = [], $content = null){
    global $wpdb;
    $query = $wpdb->prepare("SELECT NAME FROM VolunteerInfo WHERE VolunteerID=%d", $atts[0]);
    $results = $wpdb->get_results($query);
    return ($results[0]->NAME);
}
add_shortcode('volunteer', 'wporg_shortcode');


function wp_volunteer_adminpage_html() {
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) {
    return;
    }
    ?>
    <div class="wrap">
    <h1><?php esc_html( get_admin_page_title() ); ?></h1>
    <form action="<?php admin_url('options-general.php?page=volunteer/volunteer.php')?>"
    method="post">
    <label for="position">Position</label>
    <input type="text" name="position">
    <input type="text" name="organization">
    <select name="type">
        <option value="one-time">One-time</option>
        <option value="recurring">Recurring</option>
        <option value="seasonal">Seasonal</option>
    <input type="email" name="email">
    <textarea name="description"></textarea>
    
    <input type="submit">
    </form>
    <p><a href="<?php admin_url('options-
    general.php?page=volunteer/volunteer.php')?>?page=volunteer&amp;somekey=somevalue">my link
    action</a></p>
    <p>POST array: <?php var_dump($_POST) ?></p>
    <p>GET array: <?php var_dump($_GET) ?></p>
    </div>
    <?php
}


function wp_volunteer_adminpage() {
    add_menu_page(
    'Volunteer',
    'Volunteeer',
    'manage_options',
    'volunteer',
    'wp_volunteer_adminpage_html',
    '', // could give a custom icon here
    20
    );
}
add_action( 'admin_menu', 'wp_volunteer_adminpage' );
?>