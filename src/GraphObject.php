<?php
require_once "Defines.php";
require_once "GraphClient.php";
require_once "GraphFields.php";
require_once "GraphConnections.php";


/**
 * Class to represent an object in the Facebook object graph
 *
 * @author Jason P Rahman (jprahman93@gmail.com, rahmanj@purdue.edu)
 */
class GraphObject {


    /**
     * Construct an instance of a Facebook graph object
     *
     * @param mixed $objectId the ID of the object to create or an associative array which includes the ID
	 * @param array $fields a list of fields to fetch for the object
     * @param object $client the Graph client object to
     *                       be used to create the object
     * @throws
     */
    public function __construct($objectId, $client) {
        // Fetch the results from Facebook and save everything we need
        $fields = $client->getObject($objectId);
        $this->_objectId = $objectId;

        
        $this->_fields = new GraphFields($fields, $client);
        $this->_connections = new GraphConnections($objectId, $client);

        $this->_facebookClient = $client;       
    }


    /**
     * Get a property with the specified name
     *
     * @param string 
     * @return object a GraphFields or GraphConnections object
     * @throws Exception
     */
    public function __get($name) {
        if ($name === "connections") {
            return $this->_connections;
        } else {
            return $this->_fields->__get($name);
        }
    }

    /**
     *
     *
     *
     *
     */
    public function __isset($name) {
        try {
            $this->__get($name);
            return TRUE;
        } catch (Exception $e) {
            return FALSE;
        }
    }


    /**
     * Performs a given action on the Facebook graph API object
     *
     * @param string $name the name of the action to perform
     * @param array $args the arguments to pass to the function
     *
     * @return mixed
     */
    public function __call($name, $args) {
        
        switch ($name) {
            case "like":
                // TODO
                break;
            case "unlike":
                // TODO
                break;
        }
    }


    /**
     * Facebook graph objectId for the current object
     *
     * @var string
     */
    private $_objectId;


    /**
     * Client used to retrieve this object and other objects
     * that need to be fetched
     *
     * @var object
     */
    private $_facebookClient;   
}


/**
 * Represents an exception raised when an attempt is made
 * to retrieve a object field that doesn't exist
 *
 * @author Jason P Rahman (jprahman93@gmail.com, rahmanj@purdue.edu)
 *
 */
class GraphNoSuchFieldException extends Exception {
    
    public function __construct($name) {
        parent::__construct("No field named " . $name . " found");
    }
}
?>
