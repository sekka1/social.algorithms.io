<?php
namespace AlgorithmsIO\Utilities{
    /**
     * MySQL Functionality
     * 
     * @author garland
     */
    class MySQL{
        
        private $mysqlHost;
        private $mysqlUser;
        private $mysqlPassword;
        private $mysqlPort;
        
        private $mysqlConnection;
        
        public function __construct() {
        }
        /**
        * 
        * @param string $name
        */
       public function setDatabaseName($name){
           $this->databaseName = $name;
       }
        /**
        * 
        * @param string $host
        * @param string $user
        * @param string $password
        */
       public function setConnection($host, $port, $user, $password){
           $this->mysqlHost = $host;
           $this->mysqlPort = $port;
           $this->mysqlUser = $user;
           $this->mysqlPassword = $password;
           
       }
        /**
         * Make MySQL connection
         * 
         * @return boolean
         */
        public function connect(){
            //$this->mysqlConnection = new \mysqli($this->mysqlHost, $this->mysqlUser, $this->mysqlPassword, $this->databaseName);
            $this->mysqlConnection = new \mysqli('localhost', 'akkadian', 'akkadian1298', 'akkadian');
            if (!$this->mysqlConnection) {
                die('Could not connect: ' . mysql_error());
            }
            return true;
        }
        public function getConnection(){
            return $this->mysqlConnection;
        }
        /**
         * 
         */
        public function closeConnection(){
            mysqli_close($this->mysqlConnection);
        }
    }
}
?>
