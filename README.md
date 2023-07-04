# backend-coding-challenge

Write a REST Api where you can query for for the difference between difference states population using: https://datausa.io/api/data?drilldowns=State&measures=Population&year=latest.

Also save all queries made with datetime in a database and provide a way to access this information.

Requirements:
- Use .NET and SQL Server



Results:
- Rest API with that can return:
- - The difference in population between two specific states, filtered by year if chosen
- - The state with the biggest/smallest population
- - 1 other interesting comparison/information from the dataset 
- Some way of accessing logs of queries made


Challenge instructions:
- Fork this repo
- Complete the challenge (and edit the read me with installation/running instructions)
- Email jobb@spinit.se / jens.hendar@spinit.se


Installation/running:

- Copy the 2 files to the same location on the server.
- To use the database you need to create a database named "states" with a table named "queries". That table should have 3 columns, an autoincrementing id, a column for strings (i.e Text, varchar) named "query" and a column named dateTime that is a dateTime.
- If you want you can use your own database. Just remember to change user, password and names in the databaseModel.php file.
- To use the API you send in GET variables named "compare", "population", "popDifference" or "getOldQueries".
- When you send "compare" you need to send in the names, in lowercase, of the 2 states where the population should be compared. E.g "?compare=california+alabama". You can optionally send in the year you want to compare. E.g "?compare=california+alabama&year=2019". If you don't send in a year, the latest will be used.
- If you send "population" it must be followed by "smallest" or "largest" to get the state with the largest or smallest population. E.g "?population=smallest".
- If you send in "popDifference" it should be folled by 1 state to get the population change for a year compared the last year. You also need to send in a year and it will take data from that year and the year before. E.g "?popDifference=hawaii&year=2018".
- The last "getOldQueries" returns all queries that har been inserted in the database. Here you can also sen in "startTime" and "endTime" to get queries between the 2 dateTimes. E.g "?getOldQueries&startTime=2023-07-03%2012:27:36&endTime=2023-07-03%2018:40:36".
- You might want to rename the apiController to some other name.
- If you want to expand with more function you can easily add a new function to the file and add an new case in the switch.