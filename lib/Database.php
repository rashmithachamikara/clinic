<?php

class Database{
    public $host = DB_HOST;
    public $username = DB_USER;
    public $password = DB_PASS;
    public $db_name = DB_NAME;

    public $link;
    public $error;


    /*
     *  Class constructor
     */

    public function __construct(){
        //Call connect function
        $this->connect();
    }

    /*
     *  Class constructor in case it is required to pass down credentials in somewhere else
     */

    // public function __construct($host, $username, $password, $db_name){
    //     $this->host = $host;
    //     $this->username = $username;
    //     $this->password = $password;
    //     $this->db_name = $db_name;
    //     //Call connect function
    //     $this->connect();
    // }
    
    // -----------------------------------------------------------------------------------


    /*
     *  Connector
     */ 

    private function connect(){
        $this->link = new mysqli($this->host, $this->username, $this->password, $this->db_name);

        if (!$this->link){
            $this->error = "Connection failed! : ". $this->link->connect_error;
            return 0;
        }
    }

    /*
     *  Select
     */

    public function select($query){
        $result = $this->link->query($query); // or die($this->link->error.__LINE__); //Die command is violent. Not to be used regularly.
		if ($this->link->error) {
			$this->error = ("Error : ".$this->link->error.__LINE__);
			return 0;
		}

		if ($result->num_rows > 0){
			return $result;
        } else {
			$this->error = ("Error : No results available in the system for the given input");
			return 0;
		}
    }
    
	/*
	 *	Insert
	 */

	public function insert($query){
		$insert_row = $this->link->query($query); //or $this->link->error.__LINE__;
		if ($this->link->error) {
			$this->error = ("Error : ".$this->link->error.__LINE__);
			return 0;
		}

		//Check if no rows affected
		if($this->link->affected_rows==0){
			$this->error = ("Error : No matches available in the system for the given input");
			return 0;
		}

		//Validate insert
		if($insert_row){
			return 1;
		} else {
			$this->error = ("Error : ".$this->link->error.__LINE__);
			return 0;
		}
	}

	/*
	 *	Update
	 */

	public function update($query){
		$update_row = $this->link->query($query);
		if ($this->link->error) {
			$this->error = ("Error : ".$this->link->error.__LINE__);
			return 0;
		}

		//Check if no rows affected
		if($this->link->affected_rows==0){
			$this->error = ("Error : No matches available in the system for the given input");
			return 0;
		}

		//Validate update
		if($update_row){
			return 1;
		} else {
			$this->error = ("Error : ".$this->link->error.__LINE__);
			return 0;
		}
	}

	/*
	 *	Delete
	 */

	public function delete($query){
		$delete_row = $this->link->query($query);
		if ($this->link->error) {
			$this->error = ("Error : ".$this->link->error.__LINE__);
			return 0;
		}

		//Validate deletion
		if($delete_row){
			return 1;
		} else {
			$this->error = ("Error : ".$this->link->error.__LINE__);
			return 0;
		}
	}
}

