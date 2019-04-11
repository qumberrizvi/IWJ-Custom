<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class IWJ_Template_Loader {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_filter( 'template_include', array( __CLASS__, 'template_loader' ) );
		//add_filter( 'comments_template', array( __CLASS__, 'comments_template_loader' ) );
	}
	
	public static function template_loader( $template ) {
        $file = '';
        $find = array();
		if ( is_single() && get_post_type() == 'iwj_employer' ) {
			$file 	= 'single-employer.php';
			$find[] = $file;
			$find[] = IWJ()->template_path() . $file;
		}elseif ( is_single() && get_post_type() == 'iwj_candidate' ) {
			$file 	= 'single-candidate.php';
			$find[] = $file;
			$find[] = IWJ()->template_path() . $file;
		}elseif(is_single() && get_post_type() == 'iwj_job' ){
            $file 	= 'single-job.php';
            $find[] = $file;
            $find[] = IWJ()->template_path() . $file;
        }elseif(is_tax(iwj_get_job_taxonomies())){
            $file 	= 'archive-job.php';
            $find[] = $file;
            $find[] = IWJ()->template_path() . $file;
        }elseif(is_single() && get_post_type() == 'iwj_resume' ){
            $file 	= 'single-resume.php';
            $find[] = $file;
            $find[] = IWJ()->template_path() . $file;
        }

		if ( $file ) {
			$template = locate_template( array_unique( $find ) );
			if ( ! $template) {
				$template = IWJ()->plugin_path() . '/templates/' . $file;
			}
		}

		return $template;
	}

	/**
	 * Load comments template.
	 *
	 * @param mixed $template
	 * @return string
	 */
	public static function comments_template_loader( $template ) {
		if ( get_post_type() !== 'tour' ) {
			return $template;
		}

		$check_dirs = array(
			trailingslashit( get_stylesheet_directory() ) . IWJ()->template_path(),
			trailingslashit( get_template_directory() ) . IWJ()->template_path(),
			trailingslashit( get_stylesheet_directory() ),
			trailingslashit( get_template_directory() ),
			trailingslashit( IWJ()->plugin_path() ) . 'templates/'
		);

		foreach ( $check_dirs as $dir ) {
			if ( file_exists( trailingslashit( $dir ) . 'single-tour-reviews.php' ) ) {
				return trailingslashit( $dir ) . 'single-tour-reviews.php';
			}
		}
	}
}

IWJ_Template_Loader::init();
