<?php
	/**
	* This is a Database processor class and functions that serve as the
	* backend processor for the whole application using sqlite database.
	* This functions is written in dynamic format that can work for most
	* CRUD operation using PHP and SQLITE3 but only the delete is not added
	* due to the fact the deleting of record is not proper in the world of
	* record keeping. Moreso PDO and prepared statement is used to tackle
	* against SQL Injection and someother database attacks
	*** Oloruntoba Samson A.K.A Samprog ...... coding is like honey.....
	*/
	class JenkinJob
	{
		private $conn; //a variabe that can only be accessed only form this class
		public $row_num; // a variable that can be accessed from disffent class of this project
		private $memory_db; //a variabe that can only be accessed only form this class
		function __construct()
		{
			try
			{
				//creating the database if not exist and the memory
				$this->conn = new PDO('sqlite:jobs.sqlite3');
				$this->memory_db = new PDO('sqlite::memory:');
				//call the table creation function
				$this->CreateTables();
			}
			catch(PDOException $e) 
			{
			    // Print PDOException message
				$this->createLog($ex);
			    echo $e->getMessage();
			}
		}

		//This function create a table named contact_book with needed fields
		function CreateTables()
		{
			$this->conn->exec("CREATE TABLE IF NOT EXISTS job_book (
                    id INTEGER PRIMARY KEY, 
                    job TEXT, 
                    status TEXT, 
                    time TEXT)");
		}

		//This function insert record into the database, only two (2) parameter is passed for 
		//it to work as desired, the Table you want to insert to and the data in an associate
		//array format in which you want to insert into the database
		function InsertDB($table, $data)
		{
			try
			{
				$fieldname = "";
				$fieldprep = "";
				foreach(array_keys($data) as $key)
				{
				    $fieldname .= $key . ", ";
				    $fieldprep .= ":" . $key . ", ";
				}
				$fieldname = substr($fieldname, 0, strlen($fieldname)-2);
				$fieldprep = substr($fieldprep, 0, strlen($fieldprep)-2);

				$insert = "INSERT INTO " . $table . " (" . $fieldname . ") VALUES (" . $fieldprep. ")";
				
				$stmt = $this->conn->prepare($insert);
			    // Bind parameters to statement variables
			    foreach(array_keys($data) as $key)
				{
				    $stmt->bindParam(':' . $key, $data[$key]);
				}
			 
			    $stmt->execute();
				return "1";
			}
			catch(Exception $ex)
			{
				$this->createLog($ex);
				return "-1";
			}
		}

		//This parts erase the table completely and re-create like (Truncate function)
		function Truncate()
		{
			$drop = $this->conn->exec("DROP TABLE IF EXISTS job_book");
			$this->CreateTables();
		}

		//This is a Log files creation which record errors and time of the error
		function createLog($error)
		{
			$myfile = fopen("logs.txt", "a") or die("Unable to open file!");
			$date = date('d-m-Y h:i:s A');
			$txt = $date . "    Error: " . $error;
			fwrite($myfile, "\n". $txt);
			fclose($myfile);
		}
	}
?>