<?php

use ImportWP\Common\Attachment\Attachment;
use ImportWP\Common\Filesystem\Filesystem;
use ImportWP\Container;

/**
 * Replace image src with newly attached wp src
 * 
 * @param  array $matches 
 * @return string
 */
function iwp_im_replace_img_src($matches)
{
    switch ($matches[1]) {
        case 'href':
            $wrapper = substr($matches[2], 0, 1);
            $value = trim(substr($matches[2], 1, -1));

            if (!apply_filters('iwp/inline-media/source', true, $value)) {
                return $matches[0];
            }

            /**
             * @var Filesystem $filesystem
             */
            $filesystem = Container::getInstance()->get('filesystem');

            /**
             * @var Attachment $attachment
             */
            $attachment = Container::getInstance()->get('attachment');

            $attachment_id = $attachment->get_attachment_by_hash($value);
            if ($attachment_id <= 0) {

                $result = $filesystem->download_file($value);
                if (is_wp_error($result)) {
                    return $matches[0];
                }

                $attachment_id = $attachment->insert_attachment(null, $result['dest'], $result['mime']);
                if (is_wp_error($attachment_id)) {
                    return $matches[0];
                }

                $attachment->store_attachment_hash($attachment_id, $value);
            }

            $attachment = wp_get_attachment_url($attachment_id);
            return "href={$wrapper}{$attachment}{$wrapper}";

            break;
        case 'src':
            $wrapper = substr($matches[2], 0, 1);
            $value = substr($matches[2], 1, -1);

            if (!apply_filters('iwp/inline-media/source', true, $value)) {
                return $matches[0];
            }

            /**
             * @var Filesystem $filesystem
             */
            $filesystem = Container::getInstance()->get('filesystem');

            /**
             * @var Attachment $attachment
             */
            $attachment = Container::getInstance()->get('attachment');

            $attachment_id = $attachment->get_attachment_by_hash($value);
            if ($attachment_id <= 0) {

                $result = $filesystem->download_file($value);
                if (is_wp_error($result)) {
                    return $matches[0];
                }

                $attachment_id = $attachment->insert_attachment(null, $result['dest'], $result['mime']);
                if (is_wp_error($attachment_id)) {
                    return $matches[0];
                }

                $attachment->store_attachment_hash($attachment_id, $value);
            }

            $attachment = wp_get_attachment_image_src($attachment_id, 'full');
            return "src={$wrapper}{$attachment[0]}{$wrapper}";
        default:
            return $matches[0];
    }
}

/**
 * Search img attributes for src to import from
 * @param  array $matches 
 * @return string
 */
function iwp_im_find_img_tags($matches)
{
    $allowed_attrs = apply_filters('iwp/inline-media/input-attributes', ['src']);
    if (empty($allowed_attrs)) {
        return $matches[0];
    }

    $value = preg_replace_callback('/(' . implode('|', $allowed_attrs) . ')=("[^"]*"|\'[^\']*\')?/i', 'iwp_im_replace_img_src', $matches[0]);
    return $value;
}

function iwp_inline_media($input = '')
{
    $allowed_tags = apply_filters('iwp/inline-media/input-tags', ['img']);
    if (empty($allowed_tags)) {
        return $input;
    }
    return preg_replace_callback('/<(?:' . implode('|', $allowed_tags) . ')[^>]+>/i', 'iwp_im_find_img_tags', $input);
}

add_filter('iwp/template/process_field', function ($value, $key) {

    $allowed_fields = apply_filters('iwp/inline-media/template-fields', ['post_content']);
    if (in_array($key, $allowed_fields)) {
        return iwp_inline_media($value);
    }

    return $value;
}, 10, 2);
