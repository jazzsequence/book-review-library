=== Book Review Library ===
Contributors: jazzs3quence
Donate link: https://www.dwolla.com/hub/jazzsequence
Tags: book, book review, library, librarian, reading level, custom post type
Requires at least: 3.6
Tested up to: 4.6
Stable tag: 1.4.19
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

A book cataloguing and review system designed with bookophiles and librarians in mind.

== Description ==

The Book Review Library plugin allows you to create a library of books, with reviews, that include sortable meta information like author, illustrator, genre, subjects as well as ratings. This enables you to list all the books of a particular type (e.g. all books written by a particular author or with a specific rating). With very little effort, the book review system will be up and running on your site right away, with built in filters to add this extra information to the page without any custom theming.

A configurable shortcode is also built in, to display a book list. `[book-reviews]` will display all books, while `[book-reviews count=3]` will only display 3 books and `[book-reviews count=3 covers=true]` will display those reviews with their covers, if they exist.

Two sidebar widgets have also been included. A Recent Reviews widget will display the most recent book reviews that have been added, and a Related Books widget will display a list of similar books when you are looking at a single book or book list by common genre tags.

Theme developers haven't been forgotten either. If you want to customize the way the reviews display, create and edit these template files:

* `taxonomy.php`
* `archive-book-review.php`
* `single-book-review.php`

Additionally, there are a number of template tags that can be used in your theme, which are found in `inc/func.php`.

This plugin uses the [Genericons icon font](http://genericons.com) in both the dashboard and the front end to display the book icons and star ratings.

= Translators! =

New translations site is up at http://translations.jazzsequence.com/book-review-library/
None of the old accounts work anymore. Please email me at hello at chrisreynolds dot io to be added as a translator.

* **Italian translation** by [tristano-ajmone](http://wordpress.org/support/profile/tristano-ajmone)
* **Hungarian translation** by [Ignácz József](http://joco1114.dyndns.org/)
* **French translation** by claire idrac
* **Russian translation** by [Diana Kononova](http://wordpress.org/support/profile/diana-kononova)
* **Arabic translation** by Salim Solomon
* **Spanish translation** by Tierras del Rincon
* **Persian (Farsi) translation** by [Masoud Allameh](http://www.masoudallameh.com/)
* **Polish translation** by Rafał Szampera
* **Catalan translation** by Jordi Ramirez **New in 1.4.10!**


= About this plugin =

I sat down last year with a librarian and a volunteer at the [Open Classroom charter school library](http://ocslc.org/library/) and asked them what they wanted from the website. The result is this plugin. It was built with a real use-case in mind based on specific feedback I received about things they wanted to share with the school and wider community. If you have questions or suggestions, feel free to [let me know](http://museumthemes.com/book-review-library/youve-got-questions-weve-got-answers/).

== Shortcodes & Shortcode Parameters ==

Book Review Library currently supports one shortcode. This page will list the shortcode variations and parameters supported and describe what each available option and parameter does.

= Book Reviews =

`[book-reviews]`

Displays a list of books. If used with no additional parameters, this will display *all* books, ordered by date added, with no covers and no review or excerpt displayed.

= Count =

`[book-reviews count=5]`

Defines how many books to display on a page. Takes any interger. Omit to display all posts. The above shortcode would display the 5 most recent book reviews. Can be used in conjunction with any of the other parameters.

= Covers =

`[book-reviews covers=true]`

Displays the book cover if it's been added to the review and *if the theme supports it*. Only accepted argument is `true`. Requires the theme to support post thumbnails. The above shortcode would display all books with book covers.

= Order By =

`[book-reviews order_by=title]`

Changes the order in which the books are displayed. By default, lists by date added. Accepted arguments are `date_added`, `author` -- lists by author's *first* name (unless authors have been added last name first, e.g. "Reynolds Chris"), `title` -- lists by book title. The above shortcode would display all books alphabetically by title.

= Format =

`[book-reviews format=excerpt]`

Determines whether to display the full review or an excerpt. Default is no review text displayed. Accepted arguments are `full` -- displays full book review, `excerpt` -- displays an excerpt of the review or `none`. The above shortcode would display all book reviews with an excerpt of each review.

= Author =

`[book-reviews author=j-k-rowling]`

Filters all book reviews by single author. Any **slug** of an existing book author is accepted. The above shortcode would display all books by J.K. Rowling. Alternately, when wrapped in quotes, you can use the full name of any existing book author. The following would also work to display all books by J.K. Rowling:

`[book-reviews author="J.K. Rowling"]`

= Genre =

`[book-reviews genre=sci-fi]`

Filters all book reviews by genre. Any existing genre slug is accepted. The above shortcode would display all books in the Sci-Fi genre. When wrapped in quotes, you can use the full name of any existing genre **if the name matches the slug**. The following would display all the books in the "Science Fiction" genre if that genre had a slug of `science-fiction`:

`[book-reviews genre="Science Fiction"]`

== Installation ==

1. Upload `book-review-library.zip` to the `/wp-content/plugins/` directory or use the built-in plugin installer on the WordPress Plugin Dashboard page.
1. Activate the plugin through the 'Plugins' menu in WordPress
1. That's it! Start writing reviews. :)

== Frequently Asked Questions ==

[Ask some!](https://jazzsequence.com/about/#ninja_forms_form_2_response_msg)

= I have HTML displaying at the top of my page/next to the book cover/somewhere else. How do I get rid of this? =
This is most likely because your theme is using `the_title` in a link or somewhere else. This plugin adds a filter to `the_title` to display the book's author. To prevent this behavior (and fix your problem), go to the Options page in the Book Reviews menu and disable "Display author with title". The author will no longer display with the book title and will, instead, be displayed with the genre information.

= I get a 404 error when I try to view a book review. =
Go to Permalinks in the General Settings and just re-save your settings. This will update your .htaccess file and should resolve the problem.

= I have *xxxx* problem when I sort by author... =
Here are the known issues with sorting by author. Unfortunately, at this time, there isn't much that can be done about most of them because sorting by author is kind of a hack...

**Books with multiple authors display multiple times in the list.**

Reason: This is because we're doing a separate loop through all the books for each author. When it sees that book with multiple authors again, it will add that as a separate entry.

Workaround: If you have one book that has multiple authors, the easiest solution is to add them as a *single* author, e.g. "Brad Williams Ozh Richard and Justin Tadlock" -- that way the book will only have a single "author" term associated with it.

**When I sort by author, it's sorting by their *first name*, not their *last name*.**

Reason: This is because each author is entered into the database as a "term", similar to a category. So, while I can make sure that "Orange" comes after "Apple" I can't do anything about "Orange Mackenzie" coming after "Apple Smith" programmatically, because if I tried to feed "Smith, Apple" back into the loop that's being done to pull up the list of books, "Smith, Apple" wouldn't match any entries and no results would be found.

Workaround: If you must have books sorted by author *last name*, you can add the authors last name first, e.g. "Williams Brad".

== Screenshots ==

1. Add new screen

2. Genres (example of the taxonomy screens)

3. Admin menu

4. Options page

5. New user roles

6. Example display page with related books widget

7. Book Reviews admin page

== Upgrade Notice ==

= 1.4.19 =
* This update fixes a bug with WordPress 4.6. All users should upgrade to this version prior to updating WordPress.

= 1.4.7 =
* 3 new translation strings have been added in this version. Translators, please submit your translations via http://translations.jazzsequence.com/projects/book-review-library

== Changelog ==

= 1.4.19 =
* more fixes for same issue.

= 1.4.18 =
* fixed issue where taxonomies that were disabled were throwing an error when we were checking if terms from those taxonomies existed. same issue that was resolved in 1.4.17, just more places that needed the fix applied to them.

= 1.4.17 =
* fixed issue where next/previous post link for non-reviews wasn't showing at all after last update.
* fixed issue where taxonomies that were disabled were throwing an error when we were checking if terms from those taxonomies existed


= 1.4.16 =
* updated readme, new working links
* fixed next/previous post link for non-reviews

= 1.4.15 =
* updated readme.txt note for translators
* tested on WordPress version 4.4

= 1.4.14 =
* fixes next/previous post author displaying the wrong author. Issue reported [here](https://wordpress.org/support/topic/incorrect-author-in-previous-post-navigation)

= 1.4.13 =
* fixes WP_Widget issue reported [here](https://wordpress.org/support/topic/wp-43-notice)

= 1.4.12 =
* adds excerpt support

= 1.4.11 =
* added image size for book covers. users can use that instead of default post thumbnail size or they can use the thumbnail setting. controlled from the options page.

= 1.4.10 =
* added Catalan language file

= 1.4.9 =
* Fixed a bug where the book author wasn't displaying if the title was filtered by WordPress before being passed to Book Review Library's title filter
* changed string to boolean value for covers check
* added Arabic language file
* added Spanish language file
* added Farsi language file
* added Polish language file

= 1.4.8 =
* fixed a conflict with other plugins that use post types which prevented single posts of other post types from displaying
* added Russian language file
* Additional Information box always displays
* Changed the display of the taxonomy boxes so they don't do weird things when you move them (see: http://wordpress.org/support/topic/edit-post-screen-elements-go-wonky-when-rearranged?replies=4)

= 1.4.7 =
* added new option to display the book author on a separate line than the title
* updated .pot file

= 1.4.6 =
* changed behavior of book list by author to always display books by title rather than forcing an order_by parameter to define order of the books
* added new `genre` shortcode parameter
* fixed menu icon for WordPress 3.8
* fixed issue preventing librarians from saving book review options (note: current solution is a workaround)

= 1.4.5 =
* fixed issue where the author was displaying twice when using the shortcode
* prevented css and iconfont from loading on non-book review pages (thanks [Mte90](http://wordpress.org/support/topic/include-css-only-on-the-page-of-the-plugin))
* updated documentation
* added full Hungarian translation
* added full French translation
* fixed an undefined notice if order_by is not defined
* added new `author` shortcode parameter

= 1.4.4 =
* fixed the i18n on "Review Authors"
* added full Italian language file (thanks [tristano-ajmone](http://wordpress.org/support/profile/tristano-ajmone)!)
* added initial Hungarian language file (thanks Ignácz József!)
* added partial French language file (thanks claire idrac!)

= 1.4.3 =
* fixed taxonomy archive page permalink issue
* fixed i18n issues with untranslatable strings
* removed the "Add New Rating" and "Most Used" links for ratings


= 1.4.2 =
* fixed an issue where meta boxes randomly disappeared after 1.4.0 update

= 1.4.1 =
* fixed issue where plugin could not be activated if Organize Series was installed

= 1.4 =
* added new shortcode parameter: `format`. Accepted arguments are `format=full`, `format=excerpt` and `format=none` (default). This allows the user to control whether the full review or an excerpt of the review will display when using the shortcode.
* fixed issue where text on the page always displayed under the shortcode content
* fixed issue where librarian was not able to edit the options
* added ISBN support -- you can now add ISBN codes and search by them
* added support to search by other book metadata (author, genre, subject, reviewer, etc)
* added full i18n support

= 1.3.5 =
* checks if role exists before removing caps on deactivation
* moves the flush rewrite function to after the post type is registered

= 1.3.4 =
* flushes rewrite rules on activation. should resolve issues relating to posts not being viewable without re-saving the permalink settings.

= 1.3.3 =
* fixed issue where a fatal error was thrown trying to add capabilities to WordPress user roles if they do not exist

= 1.3.2 =
* applied 1.3.1 fix to excerpt filter, too

= 1.3.1 =
* fixes "this book is currently checked out" that displays on non-book posts

= 1.3 =
* added new shortcode parameter `order_by` -- accepted arguments are 'title', 'date_added' (default), and 'author'. See Frequently Asked Questions for more information about sorting by author.

= 1.2.1 =
* Enabling/disabling some options now affect columns in the book review list

= 1.2.0 =
* adds optional support for comments on book reviews

= 1.1.0 =
* adds option to disable the author in the book title (fixes formatting/html issue for themes that use the_title in `<a>` tags)
* fixed some i18n issues
* fixed "in stock" tag displaying in the shortcode when stock option was disabled
* fixed an issue that ran the content filter on pages using the book review shortcode

= 1.0.3 =
* fixes layout issues caused by `float: left` on the book covers.

= 1.0.2 =
* runs a check on the_excerpt and the_content filters to make sure those are being called in a loop. If not, they don't apply. This prevents issues in formatting if those template tags are being used inside an HTML tag.

= 1.0.1 =
* fixed bug that displayed in/out of stock on all pages (thanks Digital Mosquito for reporting it)

= 1.0 =
* initial release
