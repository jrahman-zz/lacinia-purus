<?php

require_once "Defines.php";
require_once "GraphClient.php";

/**
 * Represents the fully materialized fields of a Facebook graph object
 *
 * @author Jason P Rahman (jprahman93@gmail.com, rahmanj@purdue.edu)
 *
 */
class MaterializedGraphFields implements Iterator {

    /**
     * Construct an instance of a set of fields
     *
     * @param object $object A Facebook GraphObject instance
     * @param object $facebookClient the Graph client associated with these fields
     */
    public function __construct($object, $facebookClient) {

        // Convert back to associative array format for ease of handling
        $this->_values = $object;

        // Extract a list of property names for later use
        $this->_propertyNames = array();
        for ($object->getPropertyNames() as $name) {
            $this->_propertyNames[$name] = 1;
        }
        $this->_keys = array_keys($this->_propertyNames);

        $this->_facebookClient = $facebookClient;
    }


    /**
     * Reset the position of the iterator
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
        if ($this->_currentPosition < count($this->_keys)) {
            return TRUE;
        } else {
            return FALSE;
        }   
    }   


    /**
     * Increment the position of the iterator by one
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
        strval($this->_keys[$this->_currentPosition]);
    }  

    
    /**
     * Get the value at the current position of the iterator
     *
     * @return mixed the value at the current position
     */     
    public function current() {
        return $this->__get($this->_keys[$this->_currentPosition]);
    }


    /**
     * Retrieve the object, or array of Facebook graph objects
     * representing the fields of the facebook graph object
     *
     * @param string $name the name of the field to fetch
     * @return mixed Returns either an array of GraphObjects or a string field value
     * @throw GraphNoSuchFieldException
     */
    public function __get($name) {
        if (array_key_exists($name, $this->_propertyNames) && $this->_propertyNames[$name]) {
            $value = $this->_values->getProperty($name);
            if (isobject($value)) {
                
                // We need to check two cases, in the first the embedded object has an ID
                // thus we should wrap it inside a GraphObject so that the GraphObject
                // can automatically handle materializing the object as needed
                // In the second case without an ID, we wrap inside a GraphStruct
                // which will handle complex subfields and embedded arrays
                
                // TODO, update now that $value is a GraphObject from the API
                if (isset($value->id)) {
                    return new GraphObject($value->getProperty("id"), $this->_facebookClient, $value);
                } else {
                    return new GraphStruct($value, $this->_facebookClient);
                }
            } else {
                return $value;
            }
        } else {
            throw new GraphNoSuchFieldException($name);
        }
    }


    /**
     * Check if a field exists
     *
     * @param string $name the name of the field to retrive
     * @return bool TRUE if the field does exist, FALSE otherwise
     */
    public function __isset($name) {
        if (array_key_exists($name, $this->_propertyNames) && $this->_propertyNames[$name]) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Holds the underlying GraphObject
     *
     * @var object
     */
    private $_object;

    /**
     * Client used to retrieve this object and other objects
     * that need to be fetched
     *
     * @var object
     */
    private $_facebookClient;

    /**
     * The current index of the iterator
     *
     * @var integer
     */     
    private $_currentIndex;


    /**
     * Array of keys for each field
     *
     * @var array
     */
    private $_keys;
}
?>
