# weatherReport
This Repo related to weather api report service which fetch data from two diffrent api's and create final excel sheet dynamically

how to set up service 

1) first of all need to create project from google cloud console 
2) enabled google sheet api 
3) create api key
4) create auth client id (also add redirect url and test accounts )
5) create service account 
6) download credentials.json file 
7) add configurations of google api inside config.json file 
8) create accurate wheather api key by sign up 
9) create database and export sql file which is placed on root repositary
10) replace that accurate api key with api calls which are called inside create-sheet.php file on that function getWeatherReportData()
11) for setup cron job on server following files need to be run on server 

  callback.php
  create-sheet.php
 
"# whether" 
