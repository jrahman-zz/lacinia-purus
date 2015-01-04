<?php

require_once "Defines.php";
require_once "GraphClient.php";

/**
 * Represents the fields of a Facebook graph object
 *
 * @author Jason P Rahman (jprahman93@gmail.com, rahmanj@purdue.edu)
 *
 */
class GraphFields implements Iterator {

    /**
     * Construct an instance of a set of fields
     *
     * @param array $fields an array of associative arrays holding the partial subobject information
     * @param object $facebookClient the Graph client associated with these fields
     */
    public function __construct($object, $facebookClient) {
        $this->_hasBeenExpanded = FALSE;
        $this->_object = $object;
        $this->_propertyNames = array();
        for ($object->getPropertyNames as $name) {
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
        if () {
            // TODO, wrap GraphObject return values
            return $this->_object->getProperty($name);
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
        if (array_key_exists($name, $this->_propertyNames)
            && $this->_propertyNames[$name] > 0) {
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
