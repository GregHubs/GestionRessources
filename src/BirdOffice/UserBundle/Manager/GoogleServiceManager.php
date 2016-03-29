<?php

namespace BirdOffice\UserBundle\Manager;

use BirdOffice\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

class GoogleServiceManager
{
    /**
     * @var \Google_Client
     */
    protected $client;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var GoogleCalendarManager
     */
    protected $googleCalendarManager;

    /**
     * @param array $config
     * @param EntityManager $em
     * @param GoogleCalendarManager $googleCalendarManager
     */
    public function __construct(array $config, EntityManager $em, GoogleCalendarManager $googleCalendarManager) {
        $client = new \Google_Client($config);

        $client->setApplicationName($config['application_name']);
        $client->setClientId($config['oauth2_client_id']);
        $client->setClientSecret($config['oauth2_client_secret']);
        $client->setRedirectUri($config['oauth2_redirect_uri']);
        $client->setDeveloperKey($config['developer_key']);
        $client->setAccessType('offline');
        // Scopes
        foreach ($config['oauth2_client_scopes'] as $scope) {
            $client->addScope($scope);
        }

        $this->client = $client;
        $this->em = $em;
        $this->googleCalendarManager = $googleCalendarManager;
    }

    /**
     * Permet de connecter le user à son compte Google
     * @param User $user
     * @param bool $saveInSession
     * @return bool
     */
    public function connect(User $user, $saveInSession = true){
        try {
            /*
            $response = true;
            if ($this->isAccessTokenExpired()) {
                $session = new Session();
                if (($session->has('access_token') && $session->get('access_token'))) {
                    $this->setAccessToken($session->get('access_token'));
                    $session->set('access_token', $this->getAccessToken());
                    if ($this->isAccessTokenExpired()) {
                        $response = $this->refreshConnect($user);
                    }
                } else {
                    $response = $this->refreshConnect($user);
                }
            }*/
            $response = $this->refreshConnect($user, $saveInSession);
        } catch (\Exception $e){
            $response = false;
        }
        return $response;
    }

    /**
     * Permet de déconnecter la personne de son compte Google
     * @param User $user
     * @return bool
     */
    public function disconnect(User $user) {
        if (null != $this->client->getAccessToken()) {
            $tokenObject = json_decode($this->client->getAccessToken());
            $token = $tokenObject->access_token;
            $response = $this->client->revokeToken($token);
            if (true !== $response){
                return false;
            }
        }
        $session = new Session();
        $session->remove('access_token');
        $user->setGoogleRefreshToken(null);
   //     $this->googleCalendarManager->removeAllGoogleCalendar($user);
        $this->em->flush();
        return true;
    }

    /**
     * Permet de reconnecté le compte Google si l'access_token n'est plus valide
     * @param User $user
     * @param bool $saveInSession
     * @return bool
     */
    protected function refreshConnect(User $user, $saveInSession = true){
        $response = true;
        $session = new Session();
        $refreshToken = $user->getGoogleRefreshToken();
        if (null != $refreshToken) {
            try {
                $this->client->refreshToken($refreshToken);
                $newAccessToken = $this->getAccessToken();
                if ($saveInSession) {
                    $session->set('access_token', $newAccessToken);
                }
                $this->setAccessToken($newAccessToken);
            } catch (\Exception $e){
                $response = false;
            }
        } else {
            $response = false;
        }

        if ($response === false){   // revoked access
            $this->disconnect($user);
        }

        return $response;
    }

    /**
     * Permet de récupérer les informations du compte Google connecté
     * @return array|null
     */
    public function getGoogleUserInfo(){
        try {
            if (!$this->isAccessTokenExpired()) {
                $google_oauth = new \Google_Service_Oauth2($this->client);
                $userInfo = array();
                $userInfo['email'] = $google_oauth->userinfo->get()->email;
                $userInfo['picture'] = $google_oauth->userinfo->get()->picture;
                $userInfo['name'] = $google_oauth->userinfo->get()->name;
                //$userInfo['gender'] = $google_oauth->userinfo->get()->gender;

                return $userInfo;
            }
        } catch (\Exception $e) {
            return null;
        }
        return null;
    }

    /**
     * @param $redirectUri
     */
    public function setRedirectUri($redirectUri ) {
        $this->client->setRedirectUri($redirectUri);
    }

    /**
     * @return \Google_Client
     */
    public function getGoogleClient() {
        return $this->client;
    }

    /**
     * @param string $accessToken
     */
    public function setAccessToken($accessToken) {
        $this->client->setAccessToken($accessToken);
    }

    /**
     * @param null $code
     * @param User $user
     * @return bool
     */
    public function authenticate($code = null, User $user) {
        try {
            $this->client->authenticate($code);
        } catch (\Exception $e) {
            return false;
        }
        $session = new Session();
        $session->set('access_token', $this->getAccessToken());

        $refreshToken = $this->client->getRefreshToken();
        $user->setGoogleRefreshToken($refreshToken);
        $this->em->flush();

        return true;
    }

    /**
     * Construct the OAuth 2.0 authorization request URI.
     * @return string
     */
    public function createAuthUrl() {
        return $this->client->createAuthUrl();
    }

    /**
     * Get the OAuth 2.0 access token.
     * @return string $accessToken JSON encoded string in the following format:
     * {"access_token":"TOKEN", "refresh_token":"TOKEN", "token_type":"Bearer",
     *  "expires_in":3600,"id_token":"TOKEN", "created":1320790426}
     */
    public function getAccessToken() {
        return $this->client->getAccessToken();
    }

    /**
     * Returns if the access_token is expired.
     * @return bool Returns True if the access_token is expired.
     */
    public function isAccessTokenExpired() {
        return $this->client->isAccessTokenExpired();
    }
}