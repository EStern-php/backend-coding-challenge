<?php

include_once 'databaseModel.php';

class apiController {

    public $externalData;
    public $externalAddress = 'https://datausa.io/api/data?drilldowns=State&measures=Population';

    public function execute()
    {
        $fullquery = $_GET;
        $year = $_GET['year'] ?? null;
        $startTime = $_GET['startTime'] ?? null;
        $endTime = $_GET['endTime'] ?? null; 

        $apiReturnData = array();

        $externalData = json_decode($this->getExternalData($year));

        $db = new databaseModel();
        $db->connect();
        $db->insertInto($_SERVER['QUERY_STRING']);
        
        switch ($_GET) {
            case isset($_GET['compare']):
                $data = $this->compareStates(explode(' ',$_GET['compare']), $externalData);
                break;
            case isset($_GET['population']):
                $data = $this->getStateWithPopulation($_GET['population'], $externalData);
                break;
            case isset($_GET['popDifference']) && isset($year):
                $lastYearExternalData = json_decode($this->getExternalData((int) $year-1));
                $data = $this->getDifferenceInState(($_GET['popDifference']), $externalData, $lastYearExternalData);
                break;
            case isset($_GET['getOldQueries']):
                $queries = $db->getData($startTime, $endTime);
                $data = array();
                //Loop through $queries and add htmlspecialchars to minimize XSS. Then add to data varible.
                foreach ($queries as $row) {
                    $query = [
                        "query" => htmlspecialchars($row['query']),
                        'dateTime' => $row['dateTime']
                    ];
                    $data[] = $query;
                }
                break;
            default:
                $data = "Your query contains errors. Try again.";
                break;
        }
        die(json_encode($data));
    }

    public function getExternalData($year = 'latest')
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->externalAddress. '&year='.$year);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $data = curl_exec($curl);
        curl_close($curl);

        return $data;
    }

    public function getStateWithPopulation($type ,$externalData) {
        $population = 0;
        $state;

        if ($type == 'largest') {
            foreach ($externalData as $data) {
                foreach ($data as $stateData) {
                    if (!isset($stateData->State)) {
                        continue;
                    }
                    else if ($stateData->Population > $population) {
                        $population = $stateData->Population;
                        $state = $stateData->State;
                    }
                }
            }
        }
        else if ($type == 'smallest') {
            foreach ($externalData as $data) {
                foreach ($data as $stateData) {
                    if (!isset($stateData->State)) {
                        continue;
                    }
                    else if ($population == 0){
                        //If $population is still 0 this is the first state with a population. Set population to this and compare with all other states.
                        $population = $stateData->Population;
                        $state = $stateData->State;
                    }
                    else if ($stateData->Population < $population) {
                        $population = $stateData->Population;
                        $state = $stateData->State;
                    }
                }
            }
        }
        else {
            $state = "Error! Can only get smallest of largest population";
        }

        return $state;
    }

    public function getDifferenceInState($state, $externalData, $lastYearData) {

        $thisYearPopulation = 0;
        $lastYearPopulation = 0;

        foreach ($externalData as $data) {
            foreach ($data as $stateData) {
                if (!isset($stateData->State)) {
                    continue;
                }
                else if (strtolower($stateData->State) == $state){
                    $thisYearPopulation = $stateData->Population;
                }
            }
        }
        foreach ($lastYearData as $lastData) {
            foreach ($lastData as $lastStateData) {
                if (!isset($lastStateData->State)) {
                    continue;
                }
                else if (strtolower($lastStateData->State) == $state){
                    $lastYearPopulation = $lastStateData->Population;
                }
            }
        }
        if ($thisYearPopulation == 0 || $lastYearPopulation == 0) {
            return 'Error! Could not find population in this state at those years.';
        }

        return $thisYearPopulation - $lastYearPopulation;
    }

    public function compareStates($states, $externalData) {
        $population = array();

        if (!isset($states[0]) || !isset($states[1])) {
            return 'Error! There is something wrong with the query';
        }

        foreach ($externalData as $data) {
            foreach ($data as $stateData) {
                if (!isset($stateData->State)) {
                    continue;
                }
                else if (strtolower($stateData->State) == $states[0]) {
                    $population[0] = $stateData->Population;
                }
                else if (strtolower($stateData->State) == $states[1]) {
                    $population[1] = $stateData->Population;
                }
            }
        }

        if (!isset($population[0]) || !isset($population[1])) {
            return 'Error! Could not find the population of these two states';
        }

        $difference = $population[0] - $population[1];
        return $difference;
    }

}
$api= new apiController();
$api->execute();
