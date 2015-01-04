<?php

require_once "Defines.php";
require_once "GraphClient.php";
require_once "GraphEdge.php";

/**
 * Represents a edge from a Facebook graph object
 *
 * @author Jason P Rahman (jprahman93@gmail.com, rahmanj@purdue.edu)
 */
class GraphEdges {

    /**
     * Construct a set of edges from the given object
     *
     * @param string $objectId the ID of the object whose edges this object represents
     * @param object $facebookClient the Graph client these edges will be associated with
     */
    public function __construct($objectId, $facebookClient) {
        if (is_array($objectId)) {
            $this->_objectId = $objectId['id'];
        } else {
            $this->_objectId = $objectId;
        }       
        $this->_facebookClient = $facebookClient;
        $this->_graphEdges = array();
    }


    // TODO Add iterator methods

    /**
     * Retrieve the object, or array of Facebook graph objects
     * representing the edge from the facebook graph object
     *
     * @param string $name the name of the edge to fetch
     * @return mixed Returns either an array of GraphObjects or a string field value
     * @throw NoSuchFieldException, NoSuchEdgeException
     */
    public function __get($name) {
        if (isset($this->_graphEdges[$name])) {
            if ($this->_graphEdges[$name] !== FALSE) {
                return $this->_graphEdges[$name];
            } else {
                throw new GraphNoSuchFieldException($name);
            }
        } else {
            // Attempt to load the edge since we haven't already tried
            $edge = $this->_loadEdge($name);
            if ($edge !== FALSE) {
                $this->_graphEdges[$name] = $edge;
                return $edge;
            } else {
                $this->_graphEdges[$name] = FALSE;
                throw new GraphNoSuchFieldException($name);  
            }
        }
    }


    /**
     * Check if a edge exists
     *
     * @param string $name the name of the edge to retrive
     * @return bool TRUE if the edge does exist, FALSE otherwise
     */
    public function __isset($name) {
        if (isset($this->_graphEdges[$name])
                        && $this->_graphEdges[$name] !== FALSE) {
            return TRUE;
        } else {
            // Try to search for a edge with the given name that we haven't tried to load
            $connnection = $this->_loadEdge($name);
            $this->_graphEdges[$name] = $edge;
            if ($edge !== FALSE) {
                return TRUE;
            }
            return FALSE;
        }
    }


    /**
     * Loads the edge with the given name
     *
     * @param string $name the name of the edge to retrieve
     * @return mixed a GraphEdge object if it exists, FALSE otherwise
     */
    private function _loadEdge($name) {
        try {
            $result = new GraphEdge($this->_objectId, $name, $this->_facebookClient);
        } catch (Exception $e) {
            // The edge couldn't be created, probably because 
            // it didn't exist or we didn't have access rights
            return FALSE;
        }
        return $result;
    }


    /**
     * ID for the object these edges are associated with
     *
     * @var string 
     */
    private $_objectId;


    /**
     * Cache of Facebook graph edges, filled lazily
     * Equal to FALSE if the edge with a given key doesn't exist
     *
     * @var array
     */
    private $_graphEdges;


    /**
     * Client used to retrieve this object and other objects
     * that need to be fetched
     *
     * @var object
     */
    private $_facebookClient;
}
?>
