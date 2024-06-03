## Battle of Stalingrad

a mini game (text format) that emulates a turn-based battle of Stalingrad
between the two tanks. 

The game is done using php 8.2 with laravel framework and MongoDB the API is documented using Swagger 

## Prerequisites 

- You have installed Docker and Docker Compose. If not, you can download them [here](https://docs.docker.com/engine/install/).
- You have a Linux machine. This guide is tailored for Linux users.

## Prerequisites 

**To install Battle of Stalingrad, follow these steps:**
  1. Clone the repository from GitHub:
     > https://github.com/archisss/BattleOfStalingrad
  2. Navigate to the project directory:
     > cd BattleOfStalingrad
  3. Build the Docker images and start the containers:
     > docker-compose up --build
  4. Go into de container battleofstalingrad-app-1 <br/>Run the BASH or EXEC into the battleofstalingrad-app-1 container and run:
     > composer install <br/><br/>
  NOTE: this is because I use a especific library for connect to the mongo DB database so is needed to reinstall all the vendor packages. 
 
  7. Once the project is up and running go to <br /> http://localhost:8000/start <br />
     This command will setup tanks and maps needed to play the game 

  
## Swagger Documentation

The API run in the port 8000 and you can check it [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation).

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
