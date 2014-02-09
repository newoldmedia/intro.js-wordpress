Intro.js Tours for WordPress
====

This is a WordPress implementation of [Afshin Mehrabani](http://afshinm.name/)'s wonderful [Intro.js tool](https://github.com/usablica/intro.js/) for building guided product tours.

[Rakhitha Nimesh](www.innovativephp.com) laid the groundwork with a [mock-up implementation](http://www.sitepoint.com/introduction-product-tours-intro-js/) of Intro.js for WordPress. [New Old Media](http://newoldmedia.net) worked with Rakhitha to expand the functionality to include drag-and-drop reordering of tour steps, easy deletion of steps, a more fully featured tour management system and support for custom URLs in addition to WordPress pages.

###Contributing
This plugin should be considered a work in progress. Pull requests are welcome. 

We will eventually publish on WordPress.org plugin repository, but this repo is where all the action is.

The plugin has been tested with WordPress 3.8.2 and 3.9-alpha.

###Documentation

The documentation is also a work in progress. 

The plugin creates two menus on the WordPress dashboard: **Product Tours** and **Introjs Tours**.

Guidelines for using the plugin
----

- Navigate to Product Tours on left menu and click the Add New button.
- Add a new product tour with name and description.
- Navigate to Introjs Tours on the left menu and click Introjs Tours menu item.
- Select the name of the product tour created above.
- Fill out the remaining details as explained [in the tutorial](http://www.sitepoint.com/introduction-product-tours-intro-js/).
- Use Tooltip Position field to customize the direction of Tooltip and use Tooltip Class for adding custom styles. (Tooltip Class is not required.)
- Click on the Save button and repeat this process until you create all the steps for the tour.
- Then click the Manage Steps section of Introjs Tours menu , select the tour name and click Submit button.
- Sort the steps as needed by dragging and dropping. Also you can use the Delete button to remove unwanted steps.
- At this stage you have completed the creation of product tour.
- Now go to the All Product Tours section in Product Tours menu and copy the shortcode for created product tour.
- Open the starting page of tour and insert the following code. (Use the relevant tour id from the Product Tours page.)
```
[intro_tour id=123 ]
<a id="intro_start">Start Tour</a>
```
- Now visit the page and click the "Start Tour" link you just created to start the tour.
- Use this process to create multiple product tours.