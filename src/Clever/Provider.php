<?php

namespace SocialiteProviders\Clever;

use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider
{
    /**
     * Unique Provider Identifier.
     */
    const IDENTIFIER = 'CLEVER';

    /**
     * {@inheritdoc}
     */
    protected $scopes = [
        'read:district_admins_basic', 
        'read:school_admins_basic', 
        'read:students_basic', 
        'read:teachers_basic', 
        'read:user_id', 
    ];

    /**
     * {@inheritdoc}
     */
    protected $scopeSeparator = ' ';

    /**/

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://clever.com/oauth/authorize/', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://clever.com/oauth/tokens';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://api.clever.com/v3.0/me', [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['user_id'],
            'nickname' => null,
            'name' => $user['name']['first'].' '.$user['name']['last'],
            'email' => $user['email'],
            'avatar' => null,
            'first_name' => $user['name']['first'],
            'last_name' => $user['name']['last'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code'
        ]);
    }
}
