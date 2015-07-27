<?php
namespace Api;

/**
    $test = new \StdClass;
    $test->test ='test';
    \Api\Cache::set('testId', $test);

    print_r(\Api\Cache::get('testId'));
*/
class Cache{

    const dbName = 'wgh';
    const collectionName = 'cache';

    private static $client;
    private static $db = null;
    public static $collection = null;

    public static function init(){
         if(!self::$collection){
             self::$client = new \MongoClient();
             self::$db =  self::$client->{self::dbName};
             self::$collection = self::$db->{self::collectionName};
         }
    }

    public static function set($id, $document){
        self::init();
        if(is_array($document)){
            $document['_cacheId'] = $id;
            $document['_cacheTime']  = time();
        }else{
            $document->_cacheId   = $id;
            $document->_cacheTime = new \MongoDate();
        }

        self::$collection->insert($document);

    }

    /**
     * @returns values|null
     */
    public static function get($id){
        //@TODO: expiry
        return self::$collection->findOne(array('_cacheId' => $id));
    }

}
