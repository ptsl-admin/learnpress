<?php

use LearnPress\Helpers\Template;
use LearnPress\TemplateHooks\Instructor\ListInstructorsTemplate;

/**
 * REST API LP Instructor.
 *
 * @class LP_REST_Instructor_Controller
 * @author thimpress
 * @version 1.0.0
 */
class LP_REST_Instructor_Controller extends LP_Abstract_REST_Controller {
	public function __construct() {
		$this->namespace = 'lp/v1';
		$this->rest_base = 'instructors';

		parent::__construct();
	}

	public function register_routes() {
		$this->routes = array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'list_instructors' ),
				'args'                => array(
					'posts_per_page' => array(
						'required'    => false,
						'type'        => 'integer',
						'description' => 'The posts per page must be an integer',
					),
					'page'           => array(
						'required'    => false,
						'type'        => 'integer',
						'description' => 'The page must be an integer',
					),
				),
				'permission_callback' => '__return_true',
			),
		);

		parent::register_routes();
	}

	/**
	 * Get list instructor attend
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return LP_REST_Response
	 */
	public function list_instructors( WP_REST_Request $request ): LP_REST_Response {
		$response = new LP_REST_Response();

		try {
			$params = $request->get_params();
			$args   = apply_filters(
				'learnpress/instructor-list/args',
				array(
					'number'   => $params['number'] ?? 4,
					'paged'    => $params['paged'] ?? 1,
					'orderby'  => $params['orderby'] ?? 'display_name',
					'order'    => $params['order'] ?? 'asc',
					'role__in' => [ 'lp_teacher', 'administrator' ],
				)
			);

			$query = new WP_User_Query( $args );

			$instructors = $query->get_results();
			$template    = Template::instance();
			//Content
			ob_start();
			if ( empty( $instructors ) ) {
				$template->get_frontend_template(
					'instructor-list/no-instructors-found.php'
				);
			} else {
				/**
				 * @var LP_User $instructor
				 */
				$instructors_template = ListInstructorsTemplate::instance();
				foreach ( $instructors as $instructor_obj ) {
					$instructor = learn_press_get_user( $instructor_obj->ID );
					echo $instructors_template->instructor_item( $instructor );
				}
			}
			$response->data->content = ob_get_clean();
			//Paginate
			$instructor_total = $query->get_total();

			$response->data->pagination = learn_press_get_template_content(
				'shared/pagination.php',
				array(
					'total' => intval( ceil( $instructor_total / $args['number'] ) ),
					'paged' => $args['paged'],
				)
			);

			$response->status = 'success';
		} catch ( Throwable $e ) {
			ob_end_clean();
			$response->status  = 'error';
			$response->message = $e->getMessage();
		}

		return $response;
	}
}
