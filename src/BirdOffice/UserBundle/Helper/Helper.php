<?php

namespace BirdOffice\UserBundle\Helper;



use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\Constraints as Assert;

class Helper {
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * Permet de retourner une erreur ajax avec le bon message en fonction du code erreur envoyé
     * @param $errorCode
     * @param string $notificationType
     * @param null $infos
     * @param null $specificMessage
     * @return Response
     */
    public function returnAjaxError($errorCode, $notificationType = 'error', $infos = null, $specificMessage = null){
        switch ($errorCode) {
            case 103:
                $message = 'La recherche est vide';
                break;
            case 130:
            case 131:
            case 132:
                $message = 'Vous n\'êtes pas autorisé à accéder à cette page (seulement ROLE_SUPER_ADMIN)';
                break;
            case 200:
                $message = 'Tout a fonctionné correctement';
                break;
            default:
                $message = $this->translator->trans('bo.global.errorProcessing', array(), 'messages');
                break;
        }
        $message .= ' - '.$errorCode;
        if (null !== $infos) {
            $message .= ' (' . $infos . ')';
        }

        if (null !== $specificMessage){
            $message = $specificMessage;
        }

        $return = json_encode(array('responseCode'=>$errorCode, 'message' => $message, 'notification' => $notificationType));
        return new Response($return, 200, array('Content-Type'=>'application/json'));
    }


    /**
     * Permet de récupérer la bon route en php
     * @param $route
     * @param $params
     * @param string $locale
     * @param Controller $controller
     * @return string
     */
    public function getPath($route, $params, $locale = 'fr', Controller $controller) {
        if ($locale !== 'fr') {
            $route .= ucfirst($locale);
            $params['_locale'] = $locale;
        }
        return $controller->generateUrl($route, $params);
    }


    /**
     * Fonction curl pour récupérer le contenu d'un fichier en curl
     * @param $url
     * @param int $javascript_loop
     * @param int $timeout
     * @return array
     */
    public function get_fcontent( $url,  $javascript_loop = 0, $timeout = 5 ) {
        $url = str_replace( "&amp;", "&", urldecode(trim($url)) );

        $cookie = tempnam ("/tmp", "CURLCOOKIE");
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_COOKIEJAR, $cookie );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $ch, CURLOPT_ENCODING, "" );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
        curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
        curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
        $content = curl_exec( $ch );
        $response = curl_getinfo( $ch );
        curl_close ( $ch );

        if ($response['http_code'] == 301 || $response['http_code'] == 302) {
            ini_set("user_agent", "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1");

            if ( $headers = get_headers($response['url']) ) {
                foreach( $headers as $value ) {
                    if ( substr( strtolower($value), 0, 9 ) == "location:" )
                        return $this->get_fcontent( trim( substr( $value, 9, strlen($value) ) ) );
                }
            }
        }

        if (    ( preg_match("/>[[:space:]]+window\.location\.replace\('(.*)'\)/i", $content, $value) || preg_match("/>[[:space:]]+window\.location\=\"(.*)\"/i", $content, $value) ) && $javascript_loop < 5) {
            return $this->get_fcontent( $value[1], $javascript_loop+1 );
        } else {
            return array( $content, $response );
        }
    }


    /**
     * permet d'obtenir une date en RFC3339 (pour google calendar)
     *
     * @param integer $timestamp
     * @return string date in RFC3339
     */
    function date3339($timestamp=0) {

        if (!$timestamp) {
            $timestamp = time();
        }
        $date = date('Y-m-d\TH:i:s', $timestamp);

        $matches = array();
        if (preg_match('/^([\-+])(\d{2})(\d{2})$/', date('O', $timestamp), $matches)) {
            $date .= $matches[1].$matches[2].':'.$matches[3];
        } else {
            $date .= 'Z';
        }
        return $date;
    }


    /**
     * @param $value
     * @param $type
     * @param $env
     * @return bool|int
     */
    function regexpValidator($value, $type, $env) {

        switch ($type) {

            case 'textAllChars' :
                $result = preg_match('/^.+$/U', $value);
                break;
            case 'textAllCharsOrEmpty' :
                $result = preg_match('/^(.+)?$/U', $value);
                break;
            case 'bigFloat' :
                $result = preg_match('/^[0-9]{1,7}([,|\.][0-9]{1,})?$/', $value);
                break;
            case 'bigFloatOrNeg' :
                $result = preg_match('/^\-?[0-9]{1,7}([,|\.][0-9]{1,})?$/', $value);
                break;
            case 'float' : // à tester
                $result = preg_match('/^[0-9]{1,7}([,|\.][0-9]{1,2})?$/', $value);
                break;
            case 'floatOrEmpty' : // à tester
                $result = preg_match('/^([0-9]{1,7}([,|\.][0-9]{1,2})?)?$/', $value);
                break;
            case 'zipCode' : // à tester
                $result = preg_match('/^[a-zA-Z0-9\/ \._-]{3,10}$/', $value);
                break;
            case 'int' :
                $result = preg_match('/^[0-9]{1,7}$/', $value);
                break;
            case 'intOrNeg' :
                $result = preg_match('/^\-?[0-9]{1,7}$/', $value);
                break;
            case 'intOrEmpty' :
                $result = preg_match('/^([0-9]{1,7})?$/', $value);
                break;
            case 'password' : // à tester
                $result = preg_match('/^.{6,30}$/', $value);
                break;
            case 'hoursDay' : // à tester
                $result = preg_match('/^[0-9]{1,2}(\.5)?$/', $value);
                break;
            case 'numberDays' : // à tester
                $result = preg_match('/^[0-9]{1,4}$/', $value);
                break;
            case 'phone' : // à tester
                $result = preg_match('/^[0-9\.\(\)\+ -]+$/', $value);
                break;
            case 'phoneOrEmpty' : // à tester
                $result = preg_match('/^([0-9\.\(\)\+ -]+)?$/', $value);
                break;
            case 'name' : // à tester
                $result = preg_match('/^[a-zA-ZÀÁÂÆÇÈÉÊËÌÍÎÏÑÒÓÔŒÙÚÛÜÝŸàáâæçèéêëìíîïñòóôœùúûüýÿ\' \.-]{1,255}$/', $value);
                break;
            case 'email' : // à tester
                $result = preg_match('/^([a-zA-Z0-9\._-]+@[a-zA-Z0-9\.-]+\.[a-zA-Z]{2,6})$/', $value);
                break;
            case 'urlFormat' : // à tester
                $result = preg_match('/^([a-zA-Z0-9-]+)$/', $value);
                break;
            case 'website' : // à tester
                $result = preg_match('/^(http(s)?:\/\/)?[a-zA-Z][a-zA-Z0-9_\/\.-]+\.[a-zA-Z0-9_\/\.\#-]+$/', $value);
                break;
            case 'isChecked' : // à tester
                $result = $value === 'on' ? true : false;
                break;
            default:
                return false;
        }

        if (!$result && 'prod' !== $env) {
            dump($value, $type);
        }
        return $result;
    }

}