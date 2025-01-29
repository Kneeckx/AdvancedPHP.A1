<?php
/**
 * Plugin Name: Volunteer Opportunity Plugin
 * Description: A plugin to create, manage, and display volunteer opportunities.
 * Author: Nicolas Chiasson
 */

 /**
  * Create a table in the volunteer database when the plugin is activated
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

/**
 * Drop the table in the volunteer database when the plugin is deactivated
 */
function myplugin_deactivate() {
    global $wpdb;
    $wpdb->query("DELETE FROM VolunteerInfo;");
}
register_deactivation_hook( __FILE__, 'myplugin_deactivate' );    
    
/**
 * Add a shortcode to display the volunteer opportunities
 */
function wporg_shortcode($atts = [], $content = null){
    global $wpdb;
    $atts = shortcode_atts(
        array(
            'volunteerid' => '',
            'position' => '',
            'organization' => '',
            'type' => '',
            'email' => '',
            'description' => '',
            'location' => '',
            'hours' => '',
            'skillsrequired' => ''
        ),
        $atts,
        'volunteer'
    );

    $query_call = "SELECT * FROM VolunteerInfo WHERE 1=1";
    $params = array();
    $parameter_check = false;
    foreach($atts as $attribute => $value){
        if(!empty($value)){
            $parameter_check = true;
            if($attribute == 'hours'){
                $query_call .= " AND $attribute <= %d";
            } else
            $query_call .= " AND $attribute = %s";
            $params[] = $value;
        }
    }
    $query = $wpdb -> prepare($query_call, $params);
    $results = $wpdb->get_results($query);

    if (!empty($results)) {
        // Generate the HTML table
        $output = '<table>';
        $output .= '<tr><th>Volunteer ID</th><th>Position</th><th>Organization</th><th>Type</th><th>Email</th><th>Description</th><th>Location</th><th>Hours</th><th>Skills Required</th></tr>';
        foreach ($results as $row) {
            $color = "";
            if(!$parameter_check){
                if($row->Hours < 10){
                    $color = "green";
                }else if($row->Hours >= 10 && $row->Hours <=100){
                    $color = "yellow";
                }else if($row->Hours > 100){
                    $color = "red";
                }
            }
            $output .= '<tr style="background-color:'.$color.';">';
            $output .= '<td>' . esc_html($row->VolunteerID) . '</td>';
            $output .= '<td>' . esc_html($row->Position) . '</td>';
            $output .= '<td>' . esc_html($row->Organization) . '</td>';
            $output .= '<td>' . esc_html($row->Type) . '</td>';
            $output .= '<td>' . esc_html($row->Email) . '</td>';
            $output .= '<td>' . esc_html($row->Description) . '</td>';
            $output .= '<td>' . esc_html($row->Location) . '</td>';
            $output .= '<td>' . esc_html($row->Hours) . '</td>';
            $output .= '<td>' . esc_html($row->SkillsRequired) . '</td>';
            $output .= '</tr>';
        }
        $output .= '</table>';

        return $output;
    }
}
add_shortcode('volunteer', 'wporg_shortcode');

/**
 * Add a form page to create, update, and delete volunteer opportunities
 */
function wp_volunteer_adminpage_html() {
    // check user capabilities
    if ( !current_user_can( 'manage_options' ) ) {
        return;
    }
    handle_form();
    handle_update_form();
    handle_delete_form();
    ?>
    <style>
        .main-contain {
            display: grid;
            grid-template-columns: 1fr 1fr;
            
        }
        .container {
            display: flex;
            align-items: center;
            padding: 10px;
            margin: 10px;
        }
        .container form {
            padding: 20px;
            margin: 20px;
            border: 1px solid black;
            border-radius: 10px;
            text-align: right;
            justify-content: center;
        }
        .container label {
            font-weight: bold;
        }
        .view-contain {
            padding: 10px;
            margin: 10px;
            justify-content: center;
        }
        .title-label {
            font-size: 20px;
            font-weight: bold;
            font-style: italic;
            padding: 10px;
        }
        .input {
            margin: 2px;
            border-radius: 5px;
            width: 300px;
        }
        .select {
            margin: 2px;
            border-radius: 5px;
            width: 300px;
        }
    </style>
    <div class="main-contain">
        <div class="group-contain">
            <div class="container">
                <h1><?php esc_html( get_admin_page_title() ); ?></h1>
                <form action="<?php admin_url('options-general.php?page=volunteer/volunteer.php')?>"
                method="post">
                    <label for="create" class="title-label">CREATE</label>
                    <br>
                    <label for="position">Position</label>
                    <input type="text" name="position" class="input">
                    <br>
                    <label for="organization">Organization</label>
                    <input type="text" name="organization" class="input">
                    <br>
                    <label for="type">Job Type</label>
                    <select name="type" class="select">
                        <option value="one-time">One-time</option>
                        <option value="recurring">Recurring</option>
                        <option value="seasonal">Seasonal</option>
                    </select>
                    <br>
                    <label for="email">E-mail</label>
                    <input type="email" name="email" class="input">
                    <br>
                    <label for="description">Description</label>
                    <textarea name="description" class="input"></textarea>
                    <br>
                    <label for="location">Location</label>
                    <input type="text" name="location" class="input">
                    <br>
                    <label for="hours">Hours</label>
                    <input type="number" name="hours" class="input">
                    <br>
                    <label for="skills">Skills Required</label>
                    <textarea name="skills" class="input"></textarea>
                    <br>
                    <input type="submit" name="submit">
                    <br>
                </form>
            </div>

            <div class="container">
                <h1><?php esc_html( get_admin_page_title() ); ?></h1>
                <form action="<?php admin_url('options-general.php?page=volunteer/volunteer.php')?>"
                method="post">
                    <label for="update" class="title-label">UPDATE</label>
                    <br>
                    <label for="volunteer-id">Volunteer ID</label>
                    <input type="number" name="volunteer-id" class="input">
                    <br>
                    <label for="position-up">Position</label>
                    <input type="text" name="position-up" class="input">
                    <br>
                    <label for="organization-up">Organization</label>
                    <input type="text" name="organization-up" class="input">
                    <br>
                    <label for="type-up">Job Type</label>
                    <select name="type-up" class="select">
                        <option value="one-time">One-time</option>
                        <option value="recurring">Recurring</option>
                        <option value="seasonal">Seasonal</option>
                    </select>
                    <br>
                    <label for="email-up">E-mail</label>
                    <input type="email" name="email-up" class="input">
                    <br>
                    <label for="description-up">Description</label>
                    <textarea name="description-up" class="input"></textarea>
                    <br>
                    <label for="location-up">Location</label>
                    <input type="text" name="location-up" class="input">
                    <br>
                    <label for="hours-up">Hours</label>
                    <input type="number" name="hours-up" class="input">
                    <br>
                    <label for="skills-up">Skills Required</label>
                    <textarea name="skills-up" class="input"></textarea>
                    <br>
                    <input type="submit" name="submit-update" value="Update">
                    <br>
                </form>
            </div>
            
            <div class="container">
                <h1><?php esc_html( get_admin_page_title() ); ?></h1>
                <form action="<?php admin_url('options-general.php?page=volunteer/volunteer.php')?>"
                method="post">
                    <label for="update" class="title-label">DELETE</label>
                    <br>
                    <label for="delete-volunteer-id">Volunteer ID</label>
                    <input type="number" name="delete-volunteer-id" class="input">
                    <br>  
                    <input type="submit" name="delete-button" value="Delete">
                </form>
            </div>
        </div>
    </div>
    <div class="container">
        <p><a href="<?php admin_url('options-
        general.php?page=volunteer/volunteer.php')?>?page=volunteer&amp;somekey=somevalue">my link
        action</a></p>
        <p>POST array: <?php var_dump($_POST) ?></p>
        <p>GET array: <?php var_dump($_GET) ?></p>
    </div>
    <?php
}
/**
 * Handle the form submission, insert the data into the database
 */
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

        $wpdb->query("INSERT INTO VolunteerInfo (Position, 
                                                 Organization, 
                                                 Type, 
                                                 Email,
                                                 Description, 
                                                 Location, 
                                                 Hours, 
                                                 SkillsRequired) 
                      VALUES ('$position',
                              '$organization', 
                              '$type', 
                              '$email', 
                              '$description', 
                              '$location', 
                              '$hours', 
                              '$skills');");
    }
}
/**
 * Handle the update form submission, update the data in the database
 */
function handle_update_form(){
    if(isset($_POST['submit-update'])){
        global $wpdb;
        $volunteerID = $_POST['volunteer-id'];
        $position = $_POST['position-up'];
        $organization = $_POST['organization-up'];
        $type = $_POST['type-up'];
        $email = $_POST['email-up'];
        $location = $_POST['location-up'];
        $hours = $_POST['hours-up'];
        $description = $_POST['description-up'];
        $skills = $_POST['skills-up'];

        $wpdb->query("UPDATE VolunteerInfo 
                      SET Position='$position', 
                          Organization='$organization', 
                          Type='$type', 
                          Email='$email', 
                          Description='$description', 
                          Location='$location', 
                          Hours='$hours', 
                          SkillsRequired='$skills'
                      WHERE VolunteerID='$volunteerID';"); 
    }
}
/**
 * Handle the delete form submission, delete the data from the database
 */
function handle_delete_form(){
    if(isset($_POST['delete-button'])){
        global $wpdb;
        $volunteerID = $_POST['delete-volunteer-id'];
        $wpdb->query("DELETE FROM VolunteerInfo WHERE VolunteerID='$volunteerID';");
    }
}
/**
 * Add the admin page to the wordpress menu
 */
function wp_volunteer_adminpage() {
    add_menu_page(
    'Volunteer',
    'Volunteer',
    'manage_options',
    'volunteer',
    'wp_volunteer_adminpage_html',
    20
    );
}
add_action( 'admin_menu', 'wp_volunteer_adminpage' );
?>