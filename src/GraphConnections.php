<?php

require_once "Defines.php";
require_once "GraphClient.php";
require_once "GraphConnection.php";

/**
 * Represents a connection from a Facebook graph object
 *
 * @author Jason P Rahman (jprahman93@gmail.com, rahmanj@purdue.edu)
 */
class GraphConnections {

    /**
     * Construct a set of connections from the given object
     *
     * @param string $objectId the ID of the object whose connections this object represents
     * @param object $facebookClient the Graph client these connections will be associated with
     */
    public function __construct($objectId, $facebookClient) {
        if (is_array($objectId)) {
            $this->_objectId = $objectId['id'];
        } else {
            $this->_objectId = $objectId;
        }       
        $this->_facebookClient = $facebookClient;
        $this->_graphConnections = array();
    }


    // TODO Add iterator methods

    /**
     * Retrieve the object, or array of Facebook graph objects
     * representing the connection from the facebook graph object
     *
     * @param string $name the name of the connection to fetch
     * @return mixed Returns either an array of GraphObjects or a string field value
     * @throw NoSuchFieldException, NoSuchConnectionException
     */
    public function __get($name) {
        if (isset($this->_graphConnections[$name])) {
            if ($this->_graphConnections[$name] !== FALSE) {
                return $this->_graphConnections[$name];
            } else {
                throw new GraphNoSuchFieldException($name);
            }
        } else {
            // Attempt to load the connection since we haven't already tried
            $connection = $this->_loadConnection($name);
            if ($connection !== FALSE) {
                $this->_graphConnections[$name] = $connection;
                return $connection;
            } else {
                $this->_graphConnections[$name] = FALSE;
                throw new GraphNoSuchFieldException($name);  
            }
        }
    }


    /**
     * Check if a connection exists
     *
     * @param string $name the name of the connection to retrive
     * @return bool TRUE if the connection does exist, FALSE otherwise
     */
    public function __isset($name) {
        if (isset($this->_graphConnections[$name])
                        && $this->_graphConnections[$name] !== FALSE) {
            return TRUE;
        } else {
            // Try to search for a connection with the given name that we haven't tried to load
            $connnection = $this->_loadConnection($name);
            $this->_graphConnections[$name] = $connection;
            if ($connection !== FALSE) {
                return TRUE;
            }
            return FALSE;
        }
    }


    /**
     * Loads the connection with the given name
     *
     * @param string $name the name of the connection to retrieve
     * @return mixed a GraphConnection object if it exists, FALSE otherwise
     */
    private function _loadConnection($name) {
        try {
            $result = new GraphConnection($this->_objectId, $name, $this->_facebookClient);
        } catch (Exception $e) {
            // The connection couldn't be created, probably because 
            // it didn't exist or we didn't have access rights
            return FALSE;
        }
        return $result;
    }


    /**
     * ID for the object these connections are associated with
     *
     * @var string 
     */
    private $_objectId;


    /**
     * Cache of Facebook graph connections, filled lazily
     * Equal to FALSE if the connection with a given key doesn't exist
     *
     * @var array
     */
    private $_graphConnections;


    /**
     * Client used to retrieve this object and other objects
     * that need to be fetched
     *
     * @var object
     */
    private $_facebookClient;
}
?>
