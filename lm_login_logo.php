<?php
/**
 * Plugin Name: LM login Logo
 * Plugin URI: http://www.logomind.nl
 * Description: Lets you change your admin login logo
 * Version: 1.0
 * Author: Buddy Jansen
 * Author URI: http://www.logomind.nl
 * License: GPLv2 or later
 */

class lm_login_logo {
    

    /*
     * Constructor
     */
    public function __construct() {
        add_action( 'admin_menu', array($this, 'lm_al_add_menu'));
        add_action( 'login_enqueue_scripts', array($this, 'lm_custom_css' ));
        add_action( 'admin_print_scripts', array($this, 'wp_gear_manager_admin_scripts' ));
        add_action( 'admin_print_styles', array($this, 'wp_gear_manager_admin_styles' ));
        add_action( 'admin_head', array($this, 'custom_js'));
        add_filter( 'login_headerurl', array($this, 'lm_al_url' ));

    }
    
    /*
     * Wordpress media box javascript
     */
    public function custom_js() {
        ?>
        <script language="JavaScript">
        jQuery(document).ready(function($){

$('.custom_media_upload').click(function() {

        var send_attachment_bkp = wp.media.editor.send.attachment;
        var button = $(this);

        wp.media.editor.send.attachment = function(props, attachment) {

            $(button).prev().prev().attr('src', attachment.url);
            $(button).prev().val(attachment.url);

            wp.media.editor.send.attachment = send_attachment_bkp;
        }

        wp.media.editor.open(button);

        return false;       
    });

});
</script>
<?php
    }
    
    /*
     * Wordpress media box
     */
    public function wp_gear_manager_admin_scripts() {
        wp_enqueue_media();
    }
    
    /*
     * Edit logo URL on login page and returns the value
     */
    public function lm_al_url() {
        global $wpdb;
        foreach ($wpdb->get_results('SELECT post_content_filtered FROM wp_posts WHERE post_type = "lm_admin_logo"') as $key => $row) {
           $lm_admin_link = $row->post_content_filtered;
        }
        return $lm_admin_link;
    
    }
    
    /*
     * Custom CSS for the logo
     */
    public function lm_custom_css() { 
        global $wpdb;
        foreach ($wpdb->get_results('SELECT post_content FROM wp_posts WHERE post_type = "lm_admin_logo" ') as $key => $row) {
            $lm_admin_logo = $row->post_content;
        }
        
        ?>

    <style type="text/css">
        body.login div#login h1 a {
            background-image: url('<?php echo $lm_admin_logo; ?>');
            padding-bottom: 20px;
            width:300px;
            height:100px;
            -webkit-background-size: auto !important;
            background-size: auto !important;
        }
        
        .lm_admin_logo td {
            width:350px;
        }
    </style>
<?php }
    
    /*
     * Add menu item
     */
    public function lm_al_add_menu() {
        add_menu_page('Admin logo', 'Login logo', 'manage_options', 'LM_admin_logo', array($this, 'lm_al_menu_page'), plugins_url( 'lm_login_logo/icon_wordpress.png' ));
    }
    
    /*
     * Function that shows the plugin content
     */
    public function lm_al_menu_page() {
        global $wpdb;
        
        foreach ($wpdb->get_results('SELECT * FROM wp_posts WHERE post_type = "lm_admin_logo" ') as $key => $row) {
            $lm_admin_logo = $row->post_content;
            $postid = $row->ID;
            $lm_admin_link = $row->post_content_filtered;
        }
    ?>
       
    <div class="wrap">
        <h2>LM login logo</h2>
        
        <div class="lm_admin_logo">
            <form action="" method="POST">
                <table>
                <tr>
                <td><label for="lm_admin_link">Login logo link:</label></td>
                <td><input type="text" name="lm_admin_link" value="<?php echo $lm_admin_link; ?>"></td>
                </tr><br>
                <tr>
                <td><label for="lm_admin_link">Login logo:</label></td>
                <td><input type="text" class="custom_media_url" name="lm_admin_logo" value="<?php echo $lm_admin_logo; ?>" style="margin-bottom:10px; clear:right;">
                    <a href="#" class="button custom_media_upload">Select image</a><br></td>
                </tr>
                <tr>
                    <td></td>
                    <td><i>( Max-width 320px | Max-height 120px )</i></td>
                </tr>
                </table>
                <input type="submit" name="submit" value="Upload" class="button button-primary">
            </form>
            
            <?php 
            
            $post = array(
                'post_type'             =>      'lm_admin_logo',
                'post_content'          =>      $_POST['lm_admin_logo'],
                'post_status'           =>      'publish',
                'post_content_filtered' =>      $_POST['lm_admin_link']
            );
            
            $post2 = array(
                'ID'                    =>      $postid,
                'post_type'             =>      'lm_admin_logo',
                'post_content'          =>      $_POST['lm_admin_logo'],
                'post_content_filtered' =>      $_POST['lm_admin_link']
            );
            
            if ($_POST['submit'] && $lm_admin_logo == '') {
                wp_insert_post($post);
                echo '<META HTTP-EQUIV="REFRESH" CONTENT="0">' ;
            } elseif($_POST['submit']) {
                wp_update_post($post2);
                echo '<META HTTP-EQUIV="REFRESH" CONTENT="0">' ;
            }
            
            ?>
            
            <img src="<?php echo $lm_admin_logo ?>" class="custom_media_image" style="margin: 10px 10px 0px 0px; display:inline-block; max-width:300px;" />
        </div>
            
        
    </div>
    <?php
    }
}

$lm_login_logo = new lm_login_logo();