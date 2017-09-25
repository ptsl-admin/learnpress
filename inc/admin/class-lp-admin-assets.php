<?php

/**
 * Class LP_Admin_Assets
 *
 * Manage admin assets
 */
class LP_Admin_Assets extends LP_Abstract_Assets {

	/**
	 * Init Asset
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'learn-press/enqueue-script/learn-press-modal-search-items', array(
			'LP_Modal_Search_Items',
			'instance'
		) );
		add_action( 'learn-press/enqueue-script/learn-press-modal-search-users', array(
			'LP_Modal_Search_Users',
			'instance'
		) );
	}


	protected function _get_script_data() {
		return array(
			'learn-press-global'         => array(
				'i18n' => 'This is global script for both admin and site'
			),
			'learn-press-meta-box-order' => apply_filters(
				'learn-press/meta-box-order/script-data',
				array(
					'i18n_error' => __( 'Ooops! Error.', 'learnpress' )
				)
			)
		);
	}

	/**
	 * Get default scripts in admin.
	 *
	 * @return mixed
	 */
	protected function _get_scripts() {
		return apply_filters(
			'learn-press/admin-default-scripts',
			array(
				'lp-vue'                 => array(
					'url' => self::url( 'js/vendor/vue.js' ),
					'ver' => '2.4.0'
				),
				'lp-vuex'                => array(
					'url' => self::url( 'js/vendor/vuex.2.3.1.js' ),
					'ver' => '2.3.1'
				),
				'lp-vue-resource'        => array(
					'url' => self::url( 'js/vendor/vue-resource.1.3.4.js' ),
					'ver' => '1.3.4'
				),
				'lp-sortable'            => array(
					'url' => self::url( 'js/vendor/sortable.1.6.0.js' ),
					'ver' => '1.6.0'
				),
				'lp-vuedraggable'        => array(
					'url'  => self::url( 'js/vendor/vuedraggable.2.14.1.js' ),
					'ver'  => '2.14.1',
					'deps' => array( 'lp-sortable' )
				),
				'learn-press-global'     => array(
					'url'  => $this->url( 'js/global.js' ),
					'deps' => array( 'jquery', 'underscore', 'utils', 'jquery-ui-sortable' )
				),
				'learn-press-utils'      => array(
					'url'  => $this->url( 'js/admin/utils.js' ),
					'deps' => array( 'jquery' )
				),
				'admin'                  => array(
					'url'  => $this->url( 'js/admin/admin.js' ),
					'deps' => array( 'learn-press-global', 'learn-press-utils' )
				),
				'admin-tabs'             => array(
					'url'  => $this->url( 'js/admin/admin-tabs.js' ),
					'deps' => array( 'jquery' )
				),
				'angularjs'              => $this->url( 'js/vendor/angular.1.6.4.js' ),
				'tipsy'                  => array(
					'url'  => $this->url( 'js/vendor/jquery-tipsy/jquery.tipsy.js' ),
					'deps' => array( 'jquery' )
				),
				'modal-search'           => array(
					'url'  => $this->url( 'js/admin/controllers/modal-search.js' ),
					'deps' => array( 'jquery', 'utils', 'angularjs' )
				),
				'modal-search-questions' => array(
					'url'  => $this->url( 'js/admin/controllers/modal-search-questions.js' ),
					'deps' => array( 'modal-search' )
				),
				'base-controller'        => array(
					'url'  => $this->url( 'js/admin/controllers/base.js' ),
					'deps' => array( 'jquery', 'utils', 'angularjs' )
				),
				'base-app'               => array(
					'url'  => $this->url( 'js/admin/base.js' ),
					'deps' => array( 'jquery', 'utils', 'angularjs' )
				),
				'question-controller'    => array(
					'url'  => $this->url( 'js/admin/controllers/question.js' ),
					'deps' => array( 'base-controller' )
				),
				'quiz-controller'        => array(
					'url'  => $this->url( 'js/admin/controllers/quiz.js' ),
					'deps' => array( 'base-controller', 'modal-search-questions' )
				),
				'course-controller'      => array(
					'url'  => $this->url( 'js/admin/controllers/course.js' ),
					'deps' => array( 'base-controller' )
				),
				'question-app'           => array(
					'url'  => $this->url( 'js/admin/question.js' ),
					'deps' => array( 'question-controller', 'base-app' )
				),
				'quiz-app'               => array(
					'url'  => $this->url( 'js/admin/quiz.js' ),
					'deps' => array( 'question-controller', 'quiz-controller', 'question-app' )
				),

				'course-editor-v2'               => array(
					'url'     => $this->url( 'js/admin/course-editor-v2.js' ),
					'deps'    => array(
						'lp-vue',
						'lp-vuex',
						'lp-vue-resource',
						'lp-vuedraggable',
					),
					'screens' => array( LP_COURSE_CPT )
				),
				'quiz-editor-v2'                 => array(
					'url'     => $this->url( 'js/admin/quiz-editor-v2.js' ),
					'deps'    => array(
						'lp-vue',
						'lp-vuex',
						'lp-vue-resource',
						'lp-vuedraggable',
					),
					'screens' => array( LP_QUIZ_CPT )
				),
				'learn-press-modal-search-items' => array(
					'url' => $this->url( 'js/admin/modal-search-items.js' )
				),
				'learn-press-modal-search-users' => array(
					'url' => $this->url( 'js/admin/modal-search-users.js' )
				),
				'learn-press-meta-box-order'     => array(
					'url'     => $this->url( 'js/admin/meta-box-order.js' ),
					'deps'    => array(
						'learn-press-global',
						'learn-press-modal-search-items',
						'learn-press-modal-search-users'
					),
					'screens' => array( LP_ORDER_CPT )
				)
			)
		);
	}

	/**
	 * Get default styles in admin.
	 *
	 * @return mixed
	 */
	protected function _get_styles() {
		return apply_filters(
			'learn-press/admin-default-styles',
			array(
				'font-awesome'      => $this->url( 'css/font-awesome.min.css' ),
				'learn-press-admin' => $this->url( 'css/admin/admin.css' )
			)
		);
	}

	/**
	 * Register and enqueue needed scripts and styles
	 */
	public function load_scripts() {
		// Register
		$this->_register_scripts();

		global $current_screen;
		$screen_id = $current_screen ? $current_screen->id : false;

		/**
		 * Enqueue scripts
		 *
		 * TODO: check to show only scripts needed in specific pages
		 */
		if ( $scripts = $this->_get_scripts() ) {
			foreach ( $scripts as $handle => $data ) {
				do_action( 'learn-press/enqueue-script/' . $handle );
				if ( empty( $data['screens'] ) || ! empty( $data['screens'] ) && in_array( $screen_id, $data['screens'] ) ) {
					wp_enqueue_script( $handle );
				}
			}
		}

		/**
		 * Enqueue scripts
		 *
		 * TODO: check to show only styles needed in specific pages
		 */
		if ( $styles = $this->_get_styles() ) {
			foreach ( $styles as $handle => $data ) {
				wp_enqueue_style( $handle );
			}
		}

		do_action( 'learn-press/admin/after-enqueue-scripts' );
	}
}

/**
 * Shortcut function to get instance of LP_Admin_Assets
 *
 * @return LP_Admin_Assets|null
 */
function learn_press_admin_assets() {
	static $assets = null;
	if ( ! $assets ) {
		$assets = new LP_Admin_Assets();
	}

	return $assets;
}

learn_press_admin_assets();