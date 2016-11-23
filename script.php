<?php
	//including my connection and database channels
	include_once("dbconnect.php");
	$jekjob = new JenkinJob;
	//pulling from jenkins API server
	$json = file_get_contents('http://localhost:8080/api/json?pretty=true');
    //create new array to store our jenkins job detail
    $transaction = json_decode($json, true);
    
    ?>
    <table border='1' style="border-collapse:collapse;">
        <tr>
            <th>SN</th>
            <th>Job</th>
            <th>Job Status</th>
            <th>Date &amp; Time</th>
        </tr>
    <?php
    $k=1; //Serial No
    // Now we have the following keys in our $transaction array
    $names=$transaction['jobs'];
    //This is where you loop through json array to get your job details excluding timestamp
    foreach($names as $result)
    {
    	$job = $result['name']; //job name
    	$status = $result['color']; // job status
    	//pulling build and checked details from jenkins API through each job
    	$json2 = file_get_contents('http://localhost:8080/job/' . $job . '/api/json?pretty=true&depth=2&tree=builds[builtOn,changeSet,duration,timestamp,id,building,actions[causes[userId]]]');
    	//create new array to store our builds and checked detail
    	$transaction2 = json_decode($json2, true);
    	
    	// Now we have the following keys in our $transaction2 array
    	$timest = $transaction2['builds'];
    	foreach($timest as $result2)
    	{
   	  		$date = new \DateTime(); //instantiate datetime class
   	  		$realdate = $result2['timestamp']/1000; //jenkin timestamp is converted to milliseconds, so we have to divide it by 1000 so as to be in seconds
			$date->setTimestamp($realdate); //setting the timestamp to datetime function
			$hour = date('H'); // Getting hours
			$hour = $hour+1; // making the hour GMT +1
			$job_time = $date->format('d-m-Y ' . $hour . ':i:s A'); // converting the timestamp from seconds to date and time of the event.

			$data = array(
				'job' => $job,
                'status' => $status,
                'time' => $job_time
                );

			//Inserting data after thorough validation and sanitation of user's inputs
 			//Moreso, PDO Prepared statements is used in the database channel for SQL injections
 			$jekjob->Truncate();
 			$insert = $jekjob->InsertDB('job_book', $data);
            $k+=1;
            ?>
                <tr>
                    <td><?php echo $k; ?></td>
                    <td><?php echo $job; ?></td>
                    <td><?php echo $status; ?></td>
                    <td><?php echo $job_time; ?></td>
                </tr>
            <?php
    	}
    }
    echo "Jenkins Job details added successfully";
    echo "<a href='index.php'>Go back</a>";
    echo "</table>";
?>