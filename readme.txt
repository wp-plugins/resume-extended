=== Resume Extended ===
Contributors: AaronAsAChimp
Tags: resume, employment, jobs, page, pages
Requires at least: 2.8
Tested up to: 2.8
Stable tag: 0.2.2

Create and maintain your resume from within wordpress.

== Description ==

Create a resume easily, and allow employers to view it on your blog.

= Current Features =

* Save as a Page on your Wordpress Blog
* Resume Sections
	* Skills
	* Employment History
		* Projects
	* Education
	* Awards & Honors
* Export to:
	* PDF
	* XML Resume Library 
	* enhanced XHTML
	
= Planned Features =

* Edit and Maintain previously created resumes
* Privacy controls
* Resume sections:
	* Publications
	* References
* Export to:
	* Microsoft Word .doc and .docx
	* Text
* Import from:
	* PDF
	* Microsoft Word .doc and .docx
	* Text
	* hResume
		* this includes LinkedIn
	* XML Resume Library **(PLANNED: v0.3)**


== Installation ==

= Requirements =
* PHP 5.2 and later
* MySQL 5 and later
* Wordpress 2.8 and later

= Install it! =
1. Upload `resume-extended` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Create a new resume under 'Resume' section
1. Get a job and make installing this plugin moot.

== Frequently Asked Questions ==

= Does Resume Extended guarantee job placement? =
No, but it can help you create (and in the future, maintian and export) your resume.

= What will happen to my data if I uninstall Resume Extended? =
Currently, when you deactivate the plugin, the database tables are **not removed**, because the export feature in v0.3 is new and and may need some of the bugs worked out.

In future versions the database tables **and the data in them** will be deleted when you uninstall the plugin.  **Please remember to back up**, this is your career after all!

= This is a really awesome plugin, How can I help make it better? =
There are many ways to help out. You could start by installing the Development Version and let me know how it works.

= Who asked these silly questions anyway? =
...Nobody, I just thought they would be useful...

= Well, they aren't very frequently asked, now are they? =
Look, i'm just trying to help.

== Screenshots ==

1. Create a resume with an easy to use form.
2. Spiff up your employment history, no matter how bizarre.

== Changelog ==

= 0.3.0 (Curry) =

* added shiny pretty icon
* added theming
* added pdf, xrl, hResume export
* fixed all the short php tags

= 0.2.2 = 

* fixed display error in admin panel

= 0.2.1 =

* removed debugging code

= 0.2 (Au Gratin) =

* added projects to Employment History.
* store data in database for later functionality.
* removed unnecessary files and further reduces file size
* other bug fixes

= 0.1 (Balut) =

* create a basic resume.
