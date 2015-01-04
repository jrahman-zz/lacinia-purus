<?php

require_once "GraphClient.php";
require_once "GraphObject.php";
require_once "GraphStruct.php";


/**
* Represents an edge from a GraphObject
*
* @author Jason P Rahman (jprahman93@gmail.com, rahmanj@purdue.edu)
*
*/
class GraphEdge implements Iterator, ArrayAccess {

    /**
     * Construct an instance of a Graph Edge
     *
     * @param string $objectId the ID of the object this edge leads from
     * @param string $name the name of this particular edge
     * @param string $facebookClient 
     */
    public function __construct($objectId, $name, $facebookClient) {

        $this->_objectId = $objectId;
        $this->_edgeName = $name;
        $this->_facebookClient = $facebookClient;

        $this->_objects = $this->_loadEdge($this->_edgeName);

        if (!is_array($this->_objects)) {
            $this->_objects = array($this->_objects);
        }
    }


    /**
     * Check for the existance of the offset 
     *
     * @param mixed $offset the offset into the array to check for
     * @return boolean true if the offset exists, false otherwise
     */
    public function offsetExists($offset) {
        return isset($this->objects[$offset]);
    }
       

    /**
     * Get the value at a certain offset
     *
     * @param mixed $offset the offset of the value to retrieve
     * @return mixed if the offset exists, the value at the offset, null otherwise
     */
    public function offsetGet($offset) {
        return offsetExists($offset) ? $this->objects[$offset] : null;
    }


    /**
     * Set the value at a certain offset
     *
     * @param mixed $offset the offset at which to set a value
     * @param mixed $value the value to set
     */
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->objects[] = $value;
        } else {
            $this->objects[$offset] = $value;
        }
    }

    
    /**
     * Unset the objects at a given offset
     *
     * @param mixed $offset the offset at which to unset the elements
     */
    public function offsetUnset($offset) {
        unset($this->objects[$offset]);
    }
 

    /**
     * Reset the position of the iterator
     *
     */
    public function rewind() {
        $this->_currentPosition = 0;
    }


    /**
     * Check if the current iterator element is still valid 
     *
     * @return TRUE if the current element is still valid, FALSE otherwise
     */
    public function valid() {
        if ($this->_currentPosition < count($this->_objects)) {
            return TRUE;
        } else {
            return FALSE;
        }   
    }   


    /**
     * Increment the position of the iterator by one
     *
     */
    public function next() {
        $this->_currentPosition++;
    } 

    
    /**
     * Get the key at the current position of the iterator
     *
     * @return string the key of the current object  
     */
    public function key() {     
        strval($this->_objects[$this->_currentPosition]->fields->id);
    }  

    
    /**
     * Get the value at the current position of the iterator
     *
     * @return mixed the value at the current position
     */     
    public function current() {
        return $this->_objects[$this->_currentPosition];
    }


    /**
     * Load the objects for the edge with the given name
     *
     * @param string $edgeName the name of the edge to load
     * @return mixed FALSE if the edge couldn't be found,
     *                     an array of GraphObjects otherwise
     */
    private function _loadEdge($edgeName) {
        // Attempt to fetch the graph edge with the given name
        try {
            $edge = $this->_getEdge($edgeName);
        } catch (Exception $e) {
            return FALSE;
        }
                
        // Now that we have our edge array, expand it
        return $this->_expandEdge($edge);
    }

    
    /**
     * Get the edge array for the current object with the given edge name
     *
     * @param string $edgeName the name of the graph edge to fetch
     * @return array an array of associative arrays representing
     *               the fields of each object in the edge
     */
    private function _getEdge($edgeName) {
        $result = $this->_facebookClient->getEdge($this->_objectId, $edgeName, "5");
        return $result;
    }   


    /**
     * Expand the array representing the edge into an array
     * of objects representing the objects in the edge
     *
     * @param array $nodeArray an array of associative arrays holding partial information
     *                               for each object connected via edge
     * @return array an array of GraphObjects representing the endpoint of the edges
     */
    private function _expandEdge($vertexArray) {
        if (is_array($vertexArray) && isset($vertexArray[0]) && is_array($vertexArray[0])) {
            $result = array();
            foreach ($vertexArray as $vertex) {
                if (isset($vertex['id'])) {
                    array_push($result, new GraphObject($vertex, $this->_facebookClient));
                } else {
                    array_push($result, new GraphStruct($vertex, $this->_facebookClient));
                }
            }
        } else {
            if (isset($vertexArray['id'])) {
                $result = new GraphObject($vertexArray, $this->_facebookClient);
            } else {
                $result = new GraphStruct($vertexArray, $this->_facebookClient);
            }
        }
        return $result;
    }


    /**
     * The GraphObjects in the connection
     *
     * @var object
     */
    private $_objects;

    
    /**
     * The current position of the iterator
     */
    private $_currentPosition;


    /**
     * The name of the connection
     *
     * @var string
     */ 
    private $_edgeName;


    /**
     * Client used to retrieve this object and other objects
     * that need to be fetched
     *
     * @var object
     */
    private $_facebookClient;


    /**
     * The ID of the object this connection leads from
     *
     * @var string     
     */    
    private $_objectId;
}
?>
