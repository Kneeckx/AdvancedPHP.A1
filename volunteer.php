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
    <br>
    <label for="organization">Organization</label>
    <input type="text" name="organization">
    <br>
    <label for="type">Job Type</label>
    <select name="type">
        <option value="one-time">One-time</option>
        <option value="recurring">Recurring</option>
        <option value="seasonal">Seasonal</option>
    </select>
    <br>
    <label for="email">E-mail</label>
    <input type="email" name="email">
    <br>
    <label for="description">Description</label>
    <textarea name="description"></textarea>
    <br>
    <label for="location">Location</label>
    <input type="text" name="location">
    <br>
    <label for="hours">Hours</label>
    <input type="number" name="hours">
    <br>
    <label for="skills">Skills Required</label>
    <textarea name="skills"></textarea>
    <br>
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

function handle_form(){
    if(isset($_POST['submit'])){
        global $wpdb;
        $position = $_POST['position'];
        $organization = $_POST['organization'];
        $type = $_POST['type'];
        $email = $_POST['email'];
        $location = $_POST['location'];
        $hours = $_POST['hours'];
        $description = $_POST['description'];
        $skills = $_POST['skills'];

        $wpdb->query("INSERT INTO VolunteerInfo (Position, Organization, Type, Email, Description, Location, Hours, SkillsRequired) VALUES ('$position', '$organization', '$type', '$email', '$description', '$location', '$hours', '$skills');");
    }
}


function wp_volunteer_adminpage() {
    add_menu_page(
    'Volunteer',
    'Volunteeer',
    'manage_options',
    'volunteer',
    'wp_volunteer_adminpage_html',
    20
    );
}
add_action( 'admin_menu', 'wp_volunteer_adminpage' );
?>