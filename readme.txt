=== Plugin Name ===
Contributors: avenger339, Everyblock
Tags: widgets, everyblock, widget, embed, code, embed code
Tested up to: 3.6.1
Requires at least: 3.6.1
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily add or remove widgets to a WordPress post or page. The widget allows easy access to control widgets and edit embed codes.

== Description ==

Easily add or remove widgets to a WordPress post or page. Once a widget is added, add the shortcode [display_widgets] to the post or page. The widget allows easy access to control widgets and edit embed codes.

* Easily add or remove widgets to existing pages
* Easily customize the design of the embed lightbox
* Supports iFrames

== Installation ==
1.  Add the Widget Control folder into wp-content/plugins.
2.  Navigate to the Plugins Page.
3.  Click "Activate" Under "Widget Control".

== Screenshots ==
1. How to add or remove a widget.

2. Adding a widget.

== Frequently Asked Questions ==

= How do I add a widget to a post or page? =

In the Edit Post / Page page of the post / page you want to add widgets to, add the shortcode [display_widgets] to the post where you want the widgets to render.

In the dashboard, mouse over the "Widget Control" heading and click the "Add Widget to Post" or "Add Widget to Page" buttons.  From there, select an existing page / post from the dropdown.  Enter a name and the embed code, and click "Add new Widget".  

The post should update with the new widgets.

= How do I change the design of the embed code / lightbox? =

The files 'lightbox.js' and 'embedcode.php' has the embed code / lightbox in it for simple editing in a text editor.

= Why do the widgets dissapear while the lightbox is on the screen in Internet Explorer? =

In IE, Flash content that has the wmode set to it's default setting of "window" will always render above all content.  The behavior is a precaution to prevent the embedded widget from appearing above the lightbox.  It should only happen in Internet Explorer.

== Changelog ==

= 1.0 =
* First release.

= 1.0.1 =
* Small bug fixes.

== Upgrade Notice ==

= 1.0.1 =
* Small bug fixes.