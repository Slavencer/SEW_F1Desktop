# Job Sew 2024 
## About the work 
It is my project done during my third year of the degree at Uniovi for the subject of Software and Web Standards. 
### description
It is a web page about Formula 1, with information about a specific randomly assigned driver and circuit. 
The driver is Zhou Guanyu, and the circuit is Shanghai.
The project attemps to follow the standards of the web design, so it is adaptable, and lacks id or class unless it was specified in the wordings.
### Technologies
It was developed using Xampp as a local server and visual studio code. It makes use of vanilla html, css, python, js and php, following the web standards most of the time. 
### Web apis used
- OpenWheathermap -> for wheather data
- flickr -> for images
- openexchangerates -> exchange rates
- jolpi ergast -> f1 race schedule
- google maps -> static and dynamic map
### What's not provided
I have not included neither the wordings of the project nor the reports of the usability tests nor the deployment.
Bear this in mind as there may lack context and some exercises were designed simply to teach the technologies, but the implementation would not make a lot of sense in another context. (this is, being prohibited in using id or the fantasy game that is coded only in php)
The original api keys used were removed for obvious reason, switched for placeholders.
## How to install
Bear in bind that these instructions explain how to install and launch the project locally.
First, install Xampp from the official web page, preferably on C:\xampp. Clone the repository into the htdocs folder, and launch both the apache and MySql. At this point, the basic functionality of the page will be available from http://localhost/F1Desktop.
Substitute the placeholder apis for your own or else some funcitonality will be limited. 
Enter the mysql configuration pressing the "admin" button on xampp, and create the tables needed using the two .sql files located on F1Desktop/php, although the records.sql file only specifies the table to be created on a database called "records" that you must create on your own.
Lastly, create a user called "DBUSER2024" with the password "DBPSWD2024", both without quotes, and give him all permissions. 
Now it should work correctly.
## Disclaimer
This project was carried out while learning web design and standards, so it shows the inexperience I had in these technologies. 
This project is expected to aid any students in a similar situation, to aid in their research, but should not be strictly copied as this project lacks professionalism.
The mark of this project was 5.3, so expect the marks to be in such range.
Do not take seriously the commit messages as any message in them is purely sarcastic and stem from the lack of time and organization during the development.
