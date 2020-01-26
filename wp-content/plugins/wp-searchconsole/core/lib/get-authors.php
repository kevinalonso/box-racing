<?php
/**
 *
 * @package: advanced-wordpress-plugin/core/
 * on: 24.06.2016
 * @since 0.1
 *
 * Add core functionality files.
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('wpsearchconsole_get_authors')) {

    class wpsearchconsole_get_authors
    {

        //Get user arrays who can write posts
        public function user_details()
        {

            $users_obj = get_users(array('role' => 2));
            $user_details = array(0 => __('All Assignees', 'wpsearchconsole'));

            if (!$users_obj || !is_array($users_obj)) {
                return;
            }

            foreach ($users_obj as $val) {

                $user = ($val ? get_object_vars($val) : false);
                if ($user && is_array($user) && array_key_exists('allcaps', $user)) {

                    $allcaps = $user['allcaps'];

                    if ($allcaps && is_array($allcaps) && array_key_exists('level_2', $allcaps)) {

                        $level_2 = $allcaps['level_2'];

                        if ($level_2 == 1) {

                            if (array_key_exists('data', $user)) {

                                $user_data = get_object_vars($user['data']);

                                if ($user_data && is_array($user_data) && array_key_exists('ID', $user_data)) {

                                    $user_details[$user_data['ID']] = $user_data['display_name'];

                                }
                            }

                        }
                    }
                }
            }

            return $user_details;
        }
    }
}
?>