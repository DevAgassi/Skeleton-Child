<?php

if (!defined('ABSPATH')) {
    die();
}

// Disabled by default (previously commented out). Enable when needed:
// define('SKELETON_ENABLE_ACF_TWO_WAY_RELATIONSHIP', true);
if (!defined('SKELETON_ENABLE_ACF_TWO_WAY_RELATIONSHIP') || !SKELETON_ENABLE_ACF_TWO_WAY_RELATIONSHIP) {
    return;
}

add_filter('acf/update_value/name=resource_relationship_person', 'skeleton_acf_relation_two_bind', 10, 3);
add_filter('acf/update_value/name=resource_relationship_topic', 'skeleton_acf_relation_two_bind', 10, 3);

if (!function_exists('skeleton_acf_relation_two_bind')) {
    /**
     * Bind ACF Relationship fields two-ways.
     */
    function skeleton_acf_relation_two_bind($value, $post_id, $field)
    {
        $field_name = $field['name'] ?? '';
        $field_key = $field['key'] ?? '';
        $global_name = 'is_updating_' . $field_name;

        if (!$field_name || !$field_key) {
            return $value;
        }

        // Prevent infinite loop.
        if (!empty($GLOBALS[$global_name])) {
            return $value;
        }

        $GLOBALS[$global_name] = 1;

        if (is_array($value)) {
            foreach ($value as $post_id2) {
                $value2 = get_field($field_name, $post_id2, false);
                if (empty($value2)) {
                    $value2 = [];
                }

                if (in_array($post_id, $value2, true)) {
                    continue;
                }

                $value2[] = $post_id;
                update_field($field_key, $value2, $post_id2);
            }
        }

        $old_value = get_field($field_name, $post_id, false);
        if (is_array($old_value)) {
            foreach ($old_value as $post_id2) {
                if (is_array($value) && in_array($post_id2, $value, true)) {
                    continue;
                }

                $value2 = get_field($field_name, $post_id2, false);
                if (empty($value2)) {
                    continue;
                }

                $pos = array_search($post_id, $value2, true);
                if ($pos !== false) {
                    unset($value2[$pos]);
                    update_field($field_key, $value2, $post_id2);
                }
            }
        }

        $GLOBALS[$global_name] = 0;
        return $value;
    }
}
