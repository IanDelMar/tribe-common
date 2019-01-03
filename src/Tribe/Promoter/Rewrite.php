<?php

/**
 * Rewrite Configuration Class
 * Permalinks magic Happens over here!
 */
class Tribe__Promoter__Rewrite extends Tribe__Rewrite {

	/**
	 * Tribe__Promoter__Rewrite constructor.
	 *
	 * @since TBD
	 *
	 * @param WP_Rewrite|null $wp_rewrite
	 */
	public function __construct( WP_Rewrite $wp_rewrite = null ) {
		$this->rewrite = $wp_rewrite;
	}

	/**
	 * Generate the Rewrite Rules
	 *
	 * @since TBD
	 *
	 * @param  WP_Rewrite $wp_rewrite WordPress Rewrite that will be modified, pass it by reference (&$wp_rewrite)
	 */
	public function filter_generate( WP_Rewrite $wp_rewrite ) {
		$this->setup( $wp_rewrite );

		/**
		 * Use this to change the Tribe__Promoter__Rewrite instance before new rules
		 * are committed.
		 *
		 * Should be used when you want to add more rewrite rules without having to
		 * deal with the array merge, noting that rules for Event Tickets are
		 * themselves added via this hook (default priority).
		 *
		 * @param Tribe__Promoter__Rewrite $rewrite
		 *
		 * @since TBD
		 */
		do_action( 'tribe_common_promoter_pre_rewrite', $this );

		/**
		 * Provides an opportunity to modify Event Tickets' rewrite rules before they
		 * are merged in to WP's own rewrite rules.
		 *
		 * @param array $events_rewrite_rules
		 * @param Tribe__Promoter__Rewrite $tribe_rewrite
		 * @param WP_Rewrite $wp_rewrite WordPress Rewrite that will be modified.
		 *
		 * @since TBD
		 */
		$this->rules = apply_filters( 'tribe_common_rewrite_rules_custom', $this->rules, $this, $wp_rewrite );

		$wp_rewrite->rules = $this->rules + $wp_rewrite->rules;
	}

	/**
	 * Sets up the rules required by Event Tickets.
	 *
	 * This should be called during tribe_tickets_pre_rewrite, which means other plugins needing to add rules
	 * of their own can do so on the same hook at a lower or higher priority, according to how specific
	 * those rules are.
	 *
	 * @since TBD
	 *
	 * @param Tribe__Promoter__Rewrite $rewrite
	 */
	public function generate_core_rules( Tribe__Promoter__Rewrite $rewrite ) {
		$rewrite->add( array( '{{ promoter-auth }}' ), array( 'promoter-auth-check' => 1 ) );
	}

	/**
	 * Add attendee-info rewrite tag.
	 *
	 * @since TBD
	 */
	public function add_rewrite_tags() {
		add_rewrite_tag( '%promoter-auth-check%', '([^&]+)' );
	}

	/**
	 * Get the base slugs for the Plugin Rewrite rules
	 *
	 * WARNING: Don't mess with the filters below if you don't know what you are doing
	 *
	 * @since TBD
	 *
	 * @param  string $method Use "regex" to return a Regular Expression with the possible Base Slugs using l10n
	 * @return object         Return Base Slugs with l10n variations
	 */
	public function get_bases( $method = 'regex' ) {
		$common = Tribe__Main::instance();

		/**
		 * If you want to modify the base slugs before the i18n happens filter this use this filter
		 * All the bases need to have a key and a value, they might be the same or not.
		 *
		 * Each value is an array of possible slugs: to improve robustness the "original" English
		 * slug is supported in addition to translated forms for month, list, today and day: this
		 * way if the forms are altered (whether through i18n or other custom mods) *after* links
		 * have already been promulgated, there will be less chance of visitors hitting 404s.
		 *
		 * The term "original" here for:
		 * - events
		 * - event
		 *
		 * Means that is a value that can be overwritten and relies on the user value entered on the
		 * options page.
		 *
		 * @param array $bases
		 *
		 * @since TBD
		 */
		$bases = apply_filters( 'tribe_tickets_rewrite_base_slugs', array(
			'promoter-auth' => array( 'promoter-auth' ),
		) );

		// Remove duplicates (no need to have 'month' twice if no translations are in effect, etc)
		$bases = array_map( 'array_unique', $bases );

		// By default we load the Default and our plugin domains
		$domains = apply_filters( 'tribe_tickets_rewrite_i18n_domains', array(
			'default'             => true, // Default doesn't need file path
			'the-events-calendar' => $common->plugin_dir . 'lang/',
		) );

		/**
		 * Use `tribe_common_rewrite_i18n_slugs_raw` to modify the raw version of the l10n slugs bases.
		 *
		 * This is useful to modify the bases before the method is taken into account.
		 *
		 * @param array  $bases   An array of rewrite bases that have been generated.
		 * @param string $method  The method that's being used to generate the bases; defaults to `regex`.
		 * @param array  $domains An associative array of language domains to use; these would be plugin or themes language
		 *                        domains with a `'plugin-slug' => '/absolute/path/to/lang/dir'`
		 *
		 * @since TBD
		 */
		$bases = apply_filters( 'tribe_common_rewrite_i18n_slugs_raw', $bases, $method, $domains );

		if ( 'regex' === $method ) {
			foreach ( $bases as $type => $base ) {
				// Escape all the Bases
				$base = array_map( 'preg_quote', $base );

				// Create the Regular Expression
				$bases[ $type ] = '(?:' . implode( '|', $base ) . ')';
			}
		}

		/**
		 * Use `tribe_common_rewrite_i18n_slugs` to modify the final version of the l10n slugs bases
		 *
		 * At this stage the method has been applied already and this filter will work with the
		 * finalized version of the bases.
		 *
		 * @param array  $bases   An array of rewrite bases that have been generated.
		 * @param string $method  The method that's being used to generate the bases; defaults to `regex`.
		 * @param array  $domains An associative array of language domains to use; these would be plugin or themes language
		 *                        domains with a `'plugin-slug' => '/absolute/path/to/lang/dir'`
		 *
		 * @since TBD
		 */
		return (object) apply_filters( 'tribe_common_rewrite_i18n_slugs', $bases, $method, $domains );
	}
}
