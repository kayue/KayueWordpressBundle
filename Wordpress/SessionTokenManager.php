<?php

namespace Kayue\WordpressBundle\Wordpress;

/**
 * @see https://developer.wordpress.org/reference/classes/wp_session_tokens/
 */
class SessionTokenManager
{
    /**
     * Generate a session token and attach session information to it.
     */
    public function create($expiration)
    {
        $session = ['expiration' => $expiration];
        $token = $this->generatePassword(43, false, false);

        $this->update($token, $session);

        return $token;
    }

    /**
     * Update a session token.
     */
    public function update($token, $session)
    {
        $verifier = $this->hashToken($token);
        $this->updateSession($verifier, $session);
    }

    protected function updateSession($verifier, $session = null)
    {
        // TODO: Get user meta "session_tokens"
        // $sessions = $this->get_sessions();

        if ($session) {
            $sessions[$verifier] = $session;
        } else {
            unset($sessions[$verifier]);
        }

        $this->updateSessions($sessions);
    }

    protected function updateSessions($sessions)
    {
        // Return all 'expiration' from sessions
        $sessions = array_column($sessions, 'expiration');

        if ($sessions) {
            update_user_meta($this->user_id, 'session_tokens', $sessions);
        } else {
            delete_user_meta($this->user_id, 'session_tokens');
        }
    }

    private function hashToken($token) {
        // If ext/hash is not present, use sha1() instead.
        if (function_exists('hash')) {
            return hash('sha256', $token);
        } else {
            return sha1($token);
        }
    }

    private function generatePassword($length = 12, $special_chars = true, $extra_special_chars = false)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        if ($special_chars) $chars .= '!@#$%^&*()';
        if ($extra_special_chars) $chars .= '-_ []{}<>~`+=,.;:/?|';

        $password = '';

        for ( $i = 0; $i < $length; $i++ ) {
            $password .= substr($chars, wp_rand(0, strlen($chars) - 1), 1);
        }

        return $password;
    }
} 
