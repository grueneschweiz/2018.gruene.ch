<?php

namespace SUPT;

require_once __DIR__ . '/Util.php';

/**
 * Handles syncing form submissions to Mailchimp
 */
class MailchimpSaver {

    /**
     * Send data to Mailchimp API
     *
     * @param array $data Form data
     * @throws \Exception On API error
     */
    public static function send_to_mailchimp($data) {
        $data = self::remove_interest_fields($data);

        $response = wp_remote_post(
            MAILCHIMP_SERVICE_ENDPOINT,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode($data),
                'timeout' => 15,
            ]
        );

        if (is_wp_error($response)) {
            Util::debug_log("msg=Mailchimp API error: " . $response->get_error_message());
            throw new \Exception('Mailchimp API error: ' . $response->get_error_message());
        }

        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code < 200 || $status_code >= 300) {
            Util::debug_log("msg=Mailchimp API error: HTTP " . $status_code . " - " . wp_remote_retrieve_response_message($response));
            throw new \Exception('Mailchimp API error: HTTP ' . $status_code . ' - ' . wp_remote_retrieve_response_message($response));
        }
    }

    public static function has_mailchimp_api_key() {
        return defined('MAILCHIMP_SERVICE_ENDPOINT') && MAILCHIMP_SERVICE_ENDPOINT;
    }

    /**
     * Remove any interest fields from the data array since they can cause validation errors
     * This is a workaround that works for now. If we ever need interest fields in the direct
     * Mailchimp API call, this functionality can be extended.
     *
     * @param array $data Form data
     * @return array Cleaned data without interest fields
     */
    private static function remove_interest_fields($data)
    {
        foreach ($data as $key => $value) {
            if (stripos($key, 'interest') !== false) {
                unset($data[$key]);
            }
        }

        return $data;
    }
}
