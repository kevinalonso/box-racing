<?php
/**
 *
 * @package: advanced-wordpress-plugin/user/lib/
 * on: 25.05.2015
 * @since 0.1
 * @modified: 1
 *
 * Add roles tab display to user page. Display of Google Tab in settings page.
 *
 */


/**
 * Define the google auth display class
 */
if (!class_exists('wpsearchconsole_role_display')) {

    class wpsearchconsole_role_display
    {

        private $roles;
        private $capability;

        public function __construct()
        {

            $this->roles = $this->roles();
            $this->capability = $this->cap();

            //don't allow anyone change this settings
            if (!current_user_can('administrator')) {
                return;
            }
            $this->open_wpsc_container();
            $this->process('wpsearchconsole_give_access');
            $this->form();
            $this->close_wpsc_container();
        }
        public function open_wpsc_container(){
            ?>
            <div id="wpsc_container" class="wpsc">
            <?php
        }
        public function close_wpsc_container(){
            ?>
            </div>
            <?php
        }
        //Process the data to add capability
        public function process($name)
        {

            if (isset($_POST[$name])) {

                foreach ($this->roles as $key => $role) :

                    $name = ($key ? $key : false);
                    $status = (!isset($_POST[$name])) ? '0' : sanitize_key($_POST[$name]);
                    $role = get_role($key);
                    if ($status == '1') {
                        $role->add_cap($this->capability);
                    } else {
                        if ($key != 'administrator') {
                            $role->remove_cap($this->capability);
                        }
                    }

                endforeach;

            }
        }

        //Initiate the form here
        public function form()
        { ?>

            <form method="post" action="" enctype="multipart/form-data">
                <table class="form-table">
                    <tbody>
                    <tr valign="top">
                        <th scope="row"><?php _e('Roles', 'wpsearchconsole'); ?></th>
                        <td>
                            <?php foreach ($this->roles as $key => $role) :

                                $role_obj = get_role($key);
                                $role_caps = $role_obj->capabilities;

                                $name = ($key ? $key : false);
                                $checked = (array_key_exists($this->capability, $role_caps) ? 1 : 0);
                                $show = ($role ? $role : false);
                                $disabled = ($key == 'administrator' ? ' disabled="disabled"' : false);

                                $this->role_check($name, $checked, $disabled, $role);

                            endforeach; ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <?php submit_button(__('Give Access', 'wpsearchconsole'), 'primary', 'wpsearchconsole_give_access', false); ?>
            </form>
            <?php
        }

        //get the plugin capability
        public function cap()
        {

            return get_option('wpsearchconsole_capability');
        }

        //Get list of roles
        public function roles()
        {

            $roles = get_editable_roles();
            $output = array();
            foreach ($roles as $key => $val) {
                $output[$key] = $val['name'];
            }
            return $output;
        }

        //checkbox for roles
        public function role_check($name, $checked, $disabled, $role)
        { ?>

            <p><label><input type="checkbox" value="1"
                             name="<?php echo $name; ?>" <?php echo checked(1, $checked, false) . $disabled; ?> /> <?php echo $role; ?>
                </label></p>
            <?php
        }
    }
} ?>