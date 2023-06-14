<?php

/**
 * Class LP_Rest_Material_Controller
 * in LearnPres > Tool
 *
 * @since 4.2.2
 * @author khanhbd <email@email.com>
 */
class LP_Rest_Material_Controller extends LP_Abstract_REST_Controller {

	public function __construct() {
		$this->namespace = 'lp/v1/course/';
		$this->rest_base = 'material';

		parent::__construct();
	}

	public function register_routes() {
		$this->routes = array(
			'save-post-materials' => array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'save_material' ),
					'permission_callback' => array( $this, 'check_user_permissons' ),
					'args'                => array(
						'post_id'     => array(
							'description'       => esc_html__( 'The course id or lesson id.', 'learnpress' ),
							'type'              => 'integer',
							'required'          => true,
							'sanitize_callback' => 'absint',
						),
						'data'	=> array(
							'description'		=> esc_html__( 'Data of material', 'learnpress' ),
							'type'				=> 'string',
							'required'			=> true,
							'sanitize_callback'	=> 'sanitize_text_field',
						),
						'file'   => array(
							'description' => esc_html__( 'File.', 'learnpress' ),
							'type'        => 'array',
						),
					),
				),
			),
			'(?P<file_id>[\d]+)' => array(
				'args'   => array(
					'file_id' => array(
						'description' => __( 'A unique identifier for the resource.', 'learnpress' ),
						'type'        => 'integer',
					),
				),
				array(
					'methods'				=> WP_REST_Server::DELETABLE,
					'callback'				=> array( $this, 'delete_material' ),
					'permission_callback' 	=> array( $this, 'check_user_permissons' ),
				),
				array(
					'methods'				=> WP_REST_Server::READABLE,
					'callback'				=> array( $this, 'get_material' ),
					'permission_callback'	=> '__return_true',
				)
			),
		);

		parent::register_routes();
	}

	public function save_material( $request ) {
		$response = array(
			'data'    => array(
				'status' => 400,
			),
			'message' => esc_html__( 'There was an error when save the file.', 'learnpress' ),
		);
		try {
			$item_id 		= $request->get_param( 'post_id' );
			$material_data 	= $request->get_param( 'data' );
			$upload_file 	= $request->get_file_params( 'file' );
			if ( ! $item_id ) {
				throw new Exception( esc_html__( 'Invalid course or lesson', 'learnpress' ) );
			}
			if ( ! $material_data || ! json_decode( wp_unslash( $material_data ), true ) ) {
				throw new Exception( esc_html__( 'Invalid materials', 'learnpress' ) );
			}
			// $material0 = $material_data;
			$material_data =  json_decode( wp_unslash( $material_data ), true );
			$file = $upload_file ?? false;
			$file = $file['file'];
			$file_method = array( 'upload', 'external' );
			// DB Init
			$material_init = LP_Material_Files_DB::getInstance();
			// LP Material Settings
			$max_file_size = (int)LP_Settings::get_option('material_max_file_size');
			$allow_upload_amount = (int) LP_Settings::get_option('material_file_per_page');
			// check file was uploaded
			$uploaded_files = count( $material_init->get_material_by_item_id( $item_id ) );
			// check file amount which can upload
			$can_upload = $allow_upload_amount - $uploaded_files;

			//Check file amount validation
			if ( $allow_upload_amount == 0 ) {
				throw new Exception( esc_html__( 'Material feature is not allowed to upload', 'learnpress' ) );
			} elseif ( $allow_upload_amount > 0 ) {
				if ( count( $material_data ) > $can_upload ) {
					throw new Exception( esc_html__( 'Your uploaded files reach the maximum amount!', 'learnpress' ) );
				}
			}
			
			foreach ( $material_data as $key => $material ) {
				// check file title
				if ( ! $material['label'] ) {
					// throw new Exception( esc_html__( 'Invalid material file title!', 'learnpress' ) );
					$response['items'][ $key ]['message'] = sprintf( esc_html__( 'File %d title is not empty!', 'learnpress' ), $key );
					continue;
				}
				$response['label'][$key] = $material['label'];
				// check file upload method
				if ( ! in_array( $material['method'], $file_method ) ) {
					// throw new Exception( esc_html__( 'Invalid file method', 'learnpress' ) );
					$response['items'][ $key ]['message'] = sprintf( esc_html__( 'File %d method is invalid!', 'learnpress' ), $key );
					continue;
				}
				
				if ( $material['method'] == 'upload' ) {
					if ( ! $material['file'] ) {
						// throw new Exception( esc_html__( 'Invalid upload file', 'learnpress' ) );
						$response['items'][ $key ]['message'] = sprintf( esc_html__( 'File %d is empty!', 'learnpress' ), $key );
						continue;
					}
					$file_key = array_search( $material['file'], $file);
					if ( $file['size'][ $key ] > $max_file_size*1024*1024 ) {
						$response['items'][ $key ]['message'] = sprintf( esc_html__( 'File %d size is too large!', 'learnpress' ), $key );
						continue;
					}
					$movefile = $this->material_upload_file( $file['name'][ $file_key ], $file['tmp_name'][ $file_key ] );
					if ( ! $movefile ) {
						$response['items'][ $key ]['message'] = sprintf( esc_html__( 'Upload File %d is error!', 'learnpress' ), $key );
						continue;
					}
					$file_type = wp_check_filetype( basename( $movefile['type'] ) )['ext'];
					$file_path = str_replace( wp_upload_dir()['baseurl'], '', $movefile['url'] );
				}
				
				if ( $material['method'] == 'external' ) {
					$check_file = $this->check_external_file( $material['link'] );
					if ( ! $check_file ){
						$response['items'][ $key ]['message'] = sprintf( esc_html__( 'File embed %d is invalid!', 'learnpress' ), $key );
						continue;
					}
					if ( $check_file['size'] > $max_file_size*1024*1024 ) {
						$response['items'][ $key ]['message'] = sprintf( esc_html__( 'File %d size is too large!', 'learnpress' ), $key );
						continue;
					}
					$file_type = wp_check_filetype( $check_file['name'] )['ext'];
					$file_path = $material['link'];

				}
				$insert_arr = array( 
						'file_name' 	=> $material['label'],
						'file_type' 	=> $file_type,
						'item_id'		=> (int)$item_id,
						'item_type'		=> get_post_type( $item_id ),
						'method'		=> $material['method'],
						'file_path'		=> $file_path,
						'created_at'	=> current_time('Y-m-d H:i:s')
					 );
				$insert = $material_init->create_material( $insert_arr );
				if ( ! $insert ) {
					$response['items'][ $key ]['message'] = sprintf( esc_html__( 'cannot save file %d', 'learnpress' ), $key );
					continue;
				}
			}
			$response = array(
				'data'    => array(
					'status' => 200,
				),
				'message' => esc_html__( 'The progress was saved! Your file(s) were uploaded successfully!', 'learnpress' ),
			);

		} catch (Exception $e) {
			$response['data']['status'] = 400;
			$response['message']        = $e->getMessage();
		}
		return rest_ensure_response( $response );
	}

	/**
	 * [check_external_file check the file from external url]
	 * @param  [string] $file_url [url]
	 * @return [array||fasle]     [array of file infomations]
	 */
	public function check_external_file( $file_url ) {

		// it allows us to use download_url() and wp_handle_sideload() functions
		$lp_file = LP_WP_Filesystem::instance();

		// download to temp dir
		$temp_file = $lp_file->download_url( $file_url );

		if( is_wp_error( $temp_file ) ) {
			return false;
		}

		//get file properties
		$file = array(
			'name'     => basename( $file_url ),
			'type'     => mime_content_type( $temp_file ),
			'tmp_name' => $temp_file,
			'size'     => filesize( $temp_file ),
		);

		return $file;
	}
	/**
	 * [material_upload_file upload file when user choose upload method]
	 * @param  [string] $file_name [upload file name]
	 * @param  [] $file_tmp  [file content]
	 * @return [array]            [file infomations]
	 */
	public function material_upload_file( $file_name, $file_tmp ){

		$file = wp_upload_bits( $file_name, null, file_get_contents( $file_tmp ) );

		return $file['error'] ? false : $file;
	}
	public function get_material( $request ) {
		$response = array(
			'data'    => array(
				'status' => 400,
			),
			'message' => esc_html__( 'There was an error when call api.', 'learnpress' ),
		);
		try {
			$id = $request['file_id'];
			if ( ! $id ) {
				throw new Exception( esc_html__( 'Invalid identifier', 'learnpress' ) );
			}
			$material_init = LP_Material_Files_DB::getInstance();
			$file = $material_init->get_material( $id );
			if ( $file ){
				$response_data = array(
					'title' => $file->file_name,
					'type'	=> $file->file_type,
					'method'=> $file->method,
					'path'	=> $file->file_path
				);
				$message = esc_html__( 'Get file successfully.', 'learnpress' );
			} else {
				$response_data = [];
				$message = esc_html__( 'The file is not exist', 'learnpress' );
			}
			$response = array(
						'data'    => array(
							'status' 	=> 200,
							'data' 		=> $response_data
						),
						'message' => $message,
					);

		} catch (Throwable $th) {
			$response['message'] = $th->getMessage();
		}
		return rest_ensure_response( $response );
	}

	public function delete_material( $request ) {
		$response = array(
			'data'    => array(
				'status' => 400,
			),
			'message' => esc_html__( 'There was an error when call api.', 'learnpress' ),
		);
		try {
			$id = $request['file_id'];
			if ( ! $id ) {
				throw new Exception( esc_html__( 'Invalid file identifier', 'learnpress' ) );
			}
			// DB Init
			$material_init = LP_Material_Files_DB::getInstance();
			// Delete record
			$delete = $material_init->delete_material( $id );
			if ( $delete ) {
				$message = esc_html__( 'File is deleted.', 'learnpress' );
				$deleted = true;
			} else {
				$message = esc_html__( 'There is an error when delete this file.', 'learnpress' );
				$deleted = false;
			}
			$response = array(
						'data'    => array(
							'status' => 200,
							'delete' => $deleted
						),
						'message' => $message,
					);
		} catch ( Throwable $th ) {
			$response['message'] = $th->getMessage();
		}
		return rest_ensure_response( $response );
	}

	public function check_user_permissons() : bool {
		$permission = false;
		if ( current_user_can( ADMIN_ROLE ) || current_user_can( LP_TEACHER_ROLE ) ) {
			$permission = true;
		}
		return $permission;
	}

}