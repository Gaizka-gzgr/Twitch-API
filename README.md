# Twitch API

I'm working on Twitch API. You can login to Twitch and get : code, autorization code and access token, get user logged information (example: id, name, logo and email), get user logged channel information (example: status, views, followers and current game) and get the status channel from the user you want.

More options soon.

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

### Prerequisites

What things you need to install the software and how to install them

* 1. Twitch developmer account.
* 2. Create a Application on the account.
* 3. Client ID & Client Secret from the new application.
* 4. Running PHP Server.


### Installing

* 1. Open ``` twitch.php ``` and configure Client ID, Client Secret and redirect URL from twitch application.
* 2. Open your HTML template and write at top of page ``` require_once (twitch.php); ```
* 3. Write below ```$twitch = new Twitch_functions();```
* 4. Now you can use class functions.

## Deployment

You need a web template and start the Twitch_Functions class for get work.

## Built With

* [ATOM](https://atom.io/)
* [WAMP](http://www.wampserver.com/en/)

## Contributing

Please read [CONTRIBUTING.md](CODE_OF_CONDUCT.md) for details on our code of conduct, and the process for submitting pull requests to us.

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/Gaizka-gzgr/Multiple-Options-Open-Source-Toolkit-MOOST-/tags). 

## Authors

* **Gaizka González Graña** - *Initial work* 

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

## Acknowledgments

* In search of improvement
