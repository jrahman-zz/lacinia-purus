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
     * @param object $object Partially materialized object for use, leave empty for full initialization
     * @throws
     */
    public function __construct($objectId, $client, $object = []) {

        // If we were NOT handed a partially materialized object, then fetch the full object
        if (isempty($object)) {
            $object = $client->getObject($objectId);
            $object->_fields = new MaterializedGraphFields($object, $client);
            $this->_materialized = TRUE;
        } else {
            
            $this->_fields = new PartialGraphFields($object, $client); 
            $this->_materialized = FALSE;
            // Sanity check
            if ($object->id !== $objectId) {
                throw Exception("Mismatch between given ID and object: " . $objectId . " != " . $object->id);
            }
        }

        $this->_objectId = $objectId;
 
        $this->_connections = new GraphConnections($objectId, $client);

        $this->_facebookClient = $client;       
    }


    /**
     * Get a property with the specified name
     *
     * @param string 
     * @return mixed A object property or GraphConnections object
     * @throws Exception
     */
    public function __get($name) {
        if ($name === "connections") {
            return $this->_connections;
        } else {
            if ($this->_materialized) {
                return $this->_fields->__get($name);
            } else {
                try {
                    return $this->_fields->__get($name);
                } catch (GraphNoSuchFieldException ex) {
                    // Materialize and retry
                    $this->_materialize();
                    return $this->_fields->__get($name);
                }
            }
        }
    }

    /**
     * Check if a given field is set or not
     *
     * @return bool
     */
    public function __isset($name) {
        return $this->_fields->__isset($name) || $name === "connections";
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
        // TODO Finish this
        $this->_facebookClient->callAction($name, $this->_objectId, $args);

    }


    /**
     * Materialze a given object, previously based off of partial information
     *
     *
     */
    private function _materialze() {
        // TODO
        $this->_materialized = TRUE;
    }

    /**
     * Indicates if this object has been fully materialized or is only partially built
     *
     * @var bool
     */
    private $_materialized;

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
