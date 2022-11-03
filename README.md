# Helsinki TPR Plugin
Integrates the [Helsinki TPR API](https://hel.fi/palvelukarttaws/restpages/) into Wordpress sites. Adds custom post type for fetching units into a site, and a custom Gutenberg block for displaying unit information on a page.

## Dependencies

### Required
- None

### Recommended
These are not required, but without them the unit block will not be displayed as intended, and will require custom styling.

- [Helsinki Theme](https://github.com/City-of-Helsinki/wordpress-helfi-helsinkiteema)
- [Helsinki WordPress plugin](https://github.com/City-of-Helsinki/wordpress-helfi-hds-wp)

## Configuration

### Fetching units

Once installed, a new custom post type `TPR Units` will be created and displayed in the admin submenu.

A new unit can be added to the site from the `Add new unit` subpage by searching units by their name. Found results will be displayed in a table. A unit can be imported to the site with the `Import unit` -option.

Importing a unit creates a post for said unit. Unit information can not be edited, as the unit information is being fetched from the API-source.

### Displaying units

Once a unit has been added to the site, it is possible to display it on a page with a block.

Simply insert a `Helsinki - TPR Unit` block onto a page, and from the block settings select the unit to be displayed. From the block settings it is possible to choose which information of the unit should be shown on the page.

## Development

### Assets
(S)CSS and JS source files are stored in `/src`. Asset complitation is done with [Gulp](https://gulpjs.com/) and the processed files can be found in `/assets`.

Install dependencies with `npm install`. Build assets with `gulp scripts` and `gulp styles` or watch changes with `gulp watch`.

## Collaboration
Raise [issues](https://github.com/City-of-Helsinki/wordpress-helfi-tpr/issues) for found bugs or development ideas. Feel free to send [pull requests](https://github.com/City-of-Helsinki/wordpress-helfi-tpr/pulls) for bugfixes and new or improved features.