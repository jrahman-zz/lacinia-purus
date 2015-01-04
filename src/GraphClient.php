<?php

require_once "Defines.php";

define('FACEBOOK_SDK_V4_SRC_DIR', Defines::facebookApiDir . "/src/Facebook/");
require_once __DIR__ . "/" . Defines::facebookApiDir . "/autoload.php";

use Facebook\FacebookSession;

/**
 *
 * Class to represent a client connection to Facebook used
 * to retrieve objects from Facebook on behalf of the website
 *
 * @author Jason P Rahman (jprahman93@gmail.com, rahmanj@purdue.edu)
 *
 */
class GraphClient {

    /**
     * Create a new instance of a GraphClient
     *
     * @param string $appId the Facebook application ID
     * @param string $appSecret the Facebook application secret 
     */
    public function __construct($appId, $appSecret) {

        // Setup the Facebook API to use our app credentials
        Facebook::setDefaultApplication($appId, $appSecret);

        try {
            $this->_session = FacebookSession::newAppSession();
            $this->_session->validate();
        } catch (Exception $e) {
            throw new GraphInitializationException($e->getMessage());
        }
    }

    /**
     * Get an object through the Graph API via it's object ID
     *
     * @param string $objectId the ID of the object to fetch from Facebook
	 * @param string $fields an array of strings denoting the field to fetch
     * @return array an associative array of fields for the given object
     */
    public function getObject($objectId, $fields) {
        try {
			if (is_array($fields) && count($fields)) {
				$query_string = "?fields=";
				$query_string .= implode(",", $fields);
			} else {
				$query_string = "";
			}
            $array = $this->_makeGetRequest($objectId . $query_string);
        } catch (GraphApiException $e) {
            $this->_throwException($e);
        } catch (Exception $e) {

        }
        return $array;
    }


    /**
     * Fetch the list of objects in the given connection
     *
     * @param string $objectId the ID of the object at the source of the connection
     * @param string $edgeName the name of the kind of edge to get
     * @return array an array of associative arrays containing the partial fields of
     *               the objects in the connection
     */
    public function getEdge($objectId, $edgeName, $limit = "") {
        try {
            $response = $this->_makeGetRequest($objectId . "/" . $edgeName);
        } catch (GraphApiException $e) {
            $this->_throwException($e);
        } catch (Exception $e) {
            throw $e;
        }
        return $response;
    }

       
    /**
     * Make a GET reqeust to the Graph API 
     *
     * @param string $queryString the string to pass to the Graph API
     * @return array associative array parsed from the JSON response data
     * @throws 
     */
    private function _makeGetRequest($queryString) {
        $request = new FacebookRequest($this->_session, 'GET', $queryString);
        return $request->execute();
    }


    /**
     * Make a POST reqeust to the Graph API 
     *
     * @param string $id the ID of the object to make the post request to
     * @param 
     * @return array associative array parsed from the JSON response data
     * @throws GraphApiException
     */
    private function _makePostRequest($id, $postContent) {
        // TODO Finish this
        $queryString = $id . "/"; // TODO, finish this
        $request = new FacebookRequest($this->_session, 'POST', $queryString);
        $this->_facebookClient->api($queryString, 'POST');
    }


    /**
     * Process a GraphApiException a throw a new exception based on the details
     *
     * @param object $exception
     */
    private function _throwException($exception) {
        // TODO Finish this
        // We are to parse the exception and throw our own type based on it

        throw $exception;
    }


    /**
     * Get a public access token for the site
     *
     * @param string $appId the AppID to create the access token with
     * @param string $appSecret the App Secret to create the access token with
     * @return string an access token suitable for accessing public content on Facebook
     */
    private function _getAccessToken($appId, $appSecret) {
        $appTokenUrl = "https://graph.facebook.com/oauth/access_token?"
        . "client_id=" . $appId
        . "&client_secret=" . $appSecret 
        . "&grant_type=client_credentials";

        $response = file_get_contents($appTokenUrl);
        $params = null;
        parse_str($response, $params);
        return $params['access_token'];
    }

    const OAUTH_ERROR_CODE = "298";
    const INVALID_CONNECTION_CODE = "2500";


    /**
     * The Graph API session in use    
     *
     * @var object
     */
    private $_session;    
}


/**
 * Class to represent an exception raised when the client
 * cannot be initialized correctlty
 *
 * @author Jason P Rahman (jprahman93@gmail.com, rahmanj@purdue.edu)
 */
class GraphInitializationException extends Exception {
    
    /**
     * Create a new instance of the exception
     *
     * @param string $message message to include in the exception
     */
    public function __construct($message) {
        parent::__construct($message);
    }
}

/**
 * Class to represent an exception raised when an object doesn't exist
 *
 * @author Jason P Rahman (jprahman93@gmail.com, rahmanj@purdue.edu)
 */
class GraphNoSuchObjectException extends Exception {


    /**
     * Construct 
     *
     * @param string $id the ID of the object we attempted to fetch
     */
    public function __construct($id) {
        parent::__construct("No object with ID " . $id . " could be found");
    }
}

/**
 * Class to represent an exception raised when a
 * connection doesn't exist for a given object ID
 *
 * @author Jason P Rahman (jprahman93@gmail.com, rahmanj@purdue.edu)
 * 
 */
class GraphNoSuchEdgeException extends Exception {


    /**
     * Construct an instance of the exception
     *
     * @param string $name the name of the connection that couldn't be found
     */
    public function __construct($name) {
        parent::__construct("No connection with name " . $name . " could be found");
    }
}


/**
 * Class to represent an exception raised when an attempt to
 * access an object for which access permissions do not exist
 *
 * @author Jason P Rahman (jprahman93@gmail.com. rahmanj@purdue.edu)
 */
class GraphOAuthAccessException extends Exception {
    

    /**
     * Construct an instance of the exception
     */
    public function __construct() {
        parent::__construct("Access not allowed for this object");
    }
}


/**
 * Class to represent an exception raised when an unknown error occurs
 *
 * @author Jason P Rahman (jprahman93@gmail.com, rahmanj@purdue.edu)
 */
class GraphUnknownException extends Exception {


    /**
     * Construct an instance of the exception
     *
     * @param string $message the message describing the (unknown) exception
     */
    public function __construct($message) {
        parent::__construct($message);
    }
}
?>
