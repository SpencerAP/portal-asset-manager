# portal-asset-manager

PhotoShelter allows users to host files such as images, JS, and CSS for use in customizing their portals/websites. This is a mini app that helps you see what assets are being stored and manage them as you wish.

The app uses OAuth2 for authenticating into PhotoShelter. No state is persisted though, so you'll have to reauth with every page reload.

## Requirements

A webserver with PHP 7.1+

## Usage

* clone into the docroot (or subdirectory thereof) on a server
* `cp config.sample.php config.php`
* fill out `config.php` with appropriate values
* navigate to the script in your browser
* follow the on-screen directions

## Legal

"PhotoShelter" is a registered trademark of [PhotoShelter](https://www.photoshelter.com/). References to PhotoShelter in this repository are intended to comply with their [trademark guidelines](https://www.photoshelter.com/support/trademark). 

> Feel free to include language on your site explaining that your application is built on the PhotoShelter platform so people understand your product.

This repository implies no ownership of or license to the PhotoShelter trademark or of PhotoShelter's underlying APIs.

## License

[Apache 2.0](http://www.apache.org/licenses/LICENSE-2.0)
