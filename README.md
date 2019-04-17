# BinchApp

BinchApp is a mobile web app which automatically determines who pays the next round of drink.

# Features

- Quickly create groups of people. No account needed.
- Join various groups and add new people.
- See who has either the lowest paid/consumed ratio or the lower balance.
- Display the history of all previous rounds.

# Installation

## Client

First, run `npm install` to install the required packages. Then, define the requests destination in the `.env.development` and `.env.production` files. 

When developing, run the command ` npm run serve` to start a development server.

In production, run the command `npm run build`. The web app will be created in the `dist` folder. You must then setup your web server so that it forwards all requests to `dist/index.html`.

## Server

First, run `composer install` to install the dependencies. Then, edit the `config.json` file:

- `debug`: set to true when developing.
- `client`: set which requests origin to allow.
- `database`: set the database host, name and user.

You must then setup your web server so that it forwards all requests to `public/index.php`.
