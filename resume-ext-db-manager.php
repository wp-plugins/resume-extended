<?php
/**
 * Manage the creation of the database.
 *
 * A common repository for all the queries and some helper functions.
 *
 * @package resume-extended
 * @since 0.2
 */

class resume_ext_db_manager {
	/**
	 * @static
	 * @access public
	 * @param string $name the name of the table to make
	 * @return string the table name
	 */
	public static function make_name($name) {
		global $wpdb;
		return $wpdb->prefix . RESUME_EXTENDED_NAME . $name;
	}
	/**#@+
	 * Table name for reference in the table sql.
	 */
	const name_vcard      = 'vcard';
	const name_vcard_ci   = 'vcard_contact_info';
	const name_vevent     = 'vevent';

	const name_resume     = 'resume';

	const name_skillset   = 'skillset';
	const name_skill      = 'skill';

	const name_references = 'references';

	const name_eh         = 'employment_history';
	const name_projects   = 'projects';

	const name_degree     = 'degree';

	const name_awards     = 'awards';
	/**#@-*/

	/**#@+
	 * last insert ids of the tables
	 */
	static $id_vcard;
	static $id_vcard_ci;
	static $id_vevent;

	static $id_resume;

	static $id_skillset;
	static $id_skill;

	static $id_references;

	static $id_eh;
	static $id_projects;

	static $id_degree;

	static $id_awards;
	/**#@-*/

	/**#@+
	 * Table SQL
	 *
	 * The table create statment
	 */
	// first param is vcard
	const sql_vcard = "
		CREATE TABLE `%s` (
			`vcard_id` INT UNSIGNED NOT NULL auto_increment,
			`N` VARCHAR(32) NULL,
			`FN` VARCHAR(32) NULL,
			`ORG` VARCHAR(32) NULL,
			`TITLE` VARCHAR(32) NULL,
			`NOTE` VARCHAR(256) NULL,
			`URL` VARCHAR(45) NULL,
			`REV` TIMESTAMP NOT NULL DEFAULT now(),
		PRIMARY KEY (`vcard_id`) )
		ENGINE = InnoDB;";

	// first param is vcard_contact_info
	// second param is vcard
	const sql_vcard_ci = "
		CREATE  TABLE `%s` (
			`vcard_contact_info_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`info_type` ENUM('work','home','other') NULL,
			`pref` TINYINT(1) NULL,
			`TEL` VARCHAR(15) NULL,
			`TEL_type` SET('cell','voice') NULL,
			`EMAIL` VARCHAR(128) NULL COMMENT 'TYPE=internet always',
			`LABEL` VARCHAR(256) NULL ,
			`LABEL_type` SET('dom','intl','postal','parcel') NULL,
			`vcard_id` INT unsigned NULL,
		PRIMARY KEY (`vcard_contact_info_id`),
		INDEX `vcard_id` (`vcard_id` ASC),
		CONSTRAINT `vcard_id_from_vcard_ci`
			FOREIGN KEY (`vcard_id` )
			REFERENCES `%s` (`vcard_id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION)
		ENGINE = InnoDB;";

	// first param is vevent
	const sql_vevent = "
		CREATE TABLE `%s` (
			`vevent_id` INT unsigned NOT NULL auto_increment,
			`DTSTART` DATE NULL,
			`DTEND` DATE NULL,
			`SUMMARY` VARCHAR(128) NULL,
			`DESCRIPTION` VARCHAR(256) NULL,
		PRIMARY KEY (`vevent_id`) )
		ENGINE = InnoDB;";

	// first param is resume
	// second param is vcard
	const sql_resume = "
		CREATE TABLE `%s` (
			`resume_id` INT UNSIGNED NOT NULL auto_increment,
			`vcard_id` INT unsigned NULL,
			page_id BIGINT(20) unsigned,
			`title` VARCHAR(64) NULL,
			`objective` VARCHAR(256) NULL,
			`last_update` TIMESTAMP NOT NULL DEFAULT now(),
		PRIMARY KEY (`resume_id`),
		INDEX `vcard_id` (`vcard_id` ASC),
		CONSTRAINT `vcard_id_from_resume`
			FOREIGN KEY (`vcard_id` )
			REFERENCES `%s` (`vcard_id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION)
		ENGINE = InnoDB;";

	// first param is skillset
	// second param is resume
	const sql_skillset = "
		CREATE TABLE `%s` (
			`skillset_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`resume_id` INT UNSIGNED NULL,
			`name` VARCHAR(32) NULL,
		PRIMARY KEY (`skillset_id`) ,
		INDEX `resume_id` (`resume_id` ASC),
		CONSTRAINT `resume_id_from_skillset`
			FOREIGN KEY (`resume_id`)
			REFERENCES `%s` (`resume_id`)
			ON DELETE CASCADE
			ON UPDATE CASCADE)
		ENGINE = InnoDB;";

	// first param is skill
	// second param is skillset
	const sql_skill = "
		CREATE TABLE `%s` (
			`skill_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`skillset_id` INT UNSIGNED NULL,
			`name` VARCHAR(32) NULL,
		PRIMARY KEY (`skill_id`),
		INDEX `skillset_id` (`skillset_id` ASC),
		CONSTRAINT `skillset_id_from_skill`
			FOREIGN KEY (`skillset_id` )
			REFERENCES `%s` (`skillset_id`)
			ON DELETE CASCADE
			ON UPDATE CASCADE)
		ENGINE = InnoDB;";

	// first param is references
	// second param is vcard
	// third param is resume
	const sql_references = "
		CREATE TABLE `%s` (
			`vcard_vcard_id` INT UNSIGNED NOT NULL,
			`resume_resume_id` INT UNSIGNED NOT NULL,
		PRIMARY KEY (`vcard_vcard_id`, `resume_resume_id`),
		INDEX `fk_vcard_has_resume_vcard` (`vcard_vcard_id` ASC),
		INDEX `fk_vcard_has_resume_resume` (`resume_resume_id` ASC),
		CONSTRAINT `fk_vcard_has_resume_vcard`
			FOREIGN KEY (`vcard_vcard_id`)
			REFERENCES `%s` (`vcard_id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION,
		CONSTRAINT `fk_vcard_has_resume_resume`
			FOREIGN KEY (`resume_resume_id`)
			REFERENCES `%s` (`resume_id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION)
		ENGINE = InnoDB;";

	// first param is employment_history
	// second param is resume
	// third param is vevent
	const sql_employment_history = "
		CREATE TABLE `%s` (
			`employment_history_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`resume_id` INT UNSIGNED NULL,
			`vevent_id` INT unsigned NULL,
			`employer` VARCHAR(32) NULL,
			`title` VARCHAR(32) NULL,
			`status` TINYINT(1) NULL,
		PRIMARY KEY (`employment_history_id`),
		INDEX `resume_id` (`resume_id` ASC),
		INDEX `vevent_id` (`vevent_id` ASC),
		CONSTRAINT `resume_id_from_eh`
			FOREIGN KEY (`resume_id` )
			REFERENCES `%s` (`resume_id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION,
		CONSTRAINT `vevent_id_from_eh`
			FOREIGN KEY (`vevent_id` )
			REFERENCES `%s` (`vevent_id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION)
		ENGINE = InnoDB;";

	// first param is project
	// second param is employment history
	const sql_projects = "
		CREATE TABLE `%s` (
			`project_id` INT UNSIGNED NOT NULL auto_increment,
			`employment_history_id` INT UNSIGNED NULL,
			`name` VARCHAR(32) NULL,
			`description` VARCHAR(128) NULL,
		PRIMARY KEY (`project_id`),
		INDEX `resume_id` (`employment_history_id` ASC),
		CONSTRAINT `resume_id_from_projects`
			FOREIGN KEY (`employment_history_id` )
			REFERENCES `%s` (`employment_history_id` )
			ON DELETE CASCADE
			ON UPDATE CASCADE)
		ENGINE = InnoDB;";

	// first param is degree
	// second is resume
	// third is vevent
	const sql_degree = "
		CREATE TABLE `%s` (
			`degree_id` INT UNSIGNED NOT NULL auto_increment,
			`resume_id` INT UNSIGNED NULL,
			`vevent_id` INT unsigned NULL,
			`institution` VARCHAR(32) NULL,
			`major` VARCHAR(32) NULL,
			`minor` VARCHAR(32) NULL,
			`level` VARCHAR(32) NULL,
			`enrolled` TINYINT(1) NULL,
		PRIMARY KEY (`degree_id`),
		INDEX `resume_id` (`resume_id` ASC),
		INDEX `vevent_id` (`vevent_id` ASC),
		CONSTRAINT `resume_id_from_degree`
			FOREIGN KEY (`resume_id` )
			REFERENCES `%s` (`resume_id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION,
		CONSTRAINT `vevent_id_from_degree`
			FOREIGN KEY (`vevent_id` )
			REFERENCES `%s` (`vevent_id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION)
		ENGINE = InnoDB;";

	// first param is awards
	// second param is resume
	// third param is vevent
	const sql_awards = "
		CREATE TABLE `%s` (
			`resume_id` INT UNSIGNED NOT NULL,
			`vevent_id` INT unsigned NOT NULL,
		INDEX `resume_id` (`resume_id` ASC) ,
		PRIMARY KEY (`resume_id`, `vevent_id`),
		INDEX `vevent_id` (`vevent_id` ASC),
		FOREIGN KEY (`resume_id` )
			REFERENCES `%s` (`resume_id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION,
		FOREIGN KEY (`vevent_id` )
			REFERENCES `%s` (`vevent_id`)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION)
		ENGINE = InnoDB;";
	/**#@-*/

	/**#@+
	 * select data
	 *
	 * the data returned from these queries is suitable for passing to
	 * format_entry_xhtml.
	 */
	// first param is resume
	const sql_select_title = '
		select
			title
		from %1$s
		where resume_id = "%2$d"
		limit 1';
		
	// first param is resume
	// second param is vcard
	// third param is vcard_ci
	// fourth is resume_id
	const sql_select_general = '
		select
			objective as resume_objective,
			FN as resume_name,
			LABEL as resume_address,
			EMAIL as resume_email,
			URL as resume_website
		from %s
		left join %s
			using (vcard_id)
		left join %s
			using (vcard_id)
		where resume_id = "%d"
		limit 1';

	// first is skillset
	// second is skill
	// third is resume_id
	const sql_select_skills = '
		select
			%1$s.name as resume_skillset_name,
			group_concat(%2$s.name SEPARATOR ", ") as resume_skillset_list
		from %1$s
		left join %2$s
			using (skillset_id)
		where resume_id = "%3$d"
		group by %1$s.skillset_id';

	// first param is employment_history
	// second param is vevent
	// third is resume_id
	const sql_select_employment = '
		select
			employment_history_id,
			employer as resume_employer,
			title as resume_job_title,
			date_format(DTSTART, "%%M %%D, %%Y") as resume_start_employ,
			date_format(DTEND, "%%M %%D, %%Y") as resume_end_employ,
			status as resume_currently_employ,
			DESCRIPTION as resume_job_desc
		from %1$s
		left join %2$s
			using (vevent_id)
		where resume_id = "%3$d"';

	// first is projects
	// second is employment_history_id
	const sql_select_projects = '
		select
			name as resume_project_name,
			description as resume_project_desc
		from %1$s
		where employment_history_id = "%2$d"';

	// first param is education
	// second param is vevent
	// third is resume_id
	const sql_select_education = '
		select
			institution as resume_institution,
			major as resume_major,
			minor as resume_minor,
			level as resume_degree,
			date_format(DTEND, "%%M %%D, %%Y") as resume_date_graduated,
			enrolled as resume_currently_enrolled
		from %1$s
		left join %2$s
			using (vevent_id)
		where resume_id = "%3$d"';

	// first param is awards
	// second param is vevent
	// third is resume_id
	const sql_select_awards = '
		select
			SUMMARY as resume_award_title,
			date_format(DTSTART, "%%M %%D, %%Y") as resume_award_date,
			DESCRIPTION as resume_award_desc
		from %1$s
		left join %2$s
			using (vevent_id)
		where resume_id = "%3$d"';
	/**#@-*/
	
	/**#@+
	 * resume editing queries
	 *
	 * These queries are useful the resume editing feature 
	 */
	// first param is resume
	// second param is vcard
	const sql_select_resumes = '
		select
			resume_id,
			%1$s.title,
			FN as formatted_name,
			last_update
		from %1$s
		left join %2$s
			using (vcard_id)
	';
	/**#@-*/
};
?>
