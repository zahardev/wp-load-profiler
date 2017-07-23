<?php
/*
Plugin Name: WP Load Profiler
Description: Monitoring WordPress loading pages time
Version:     0.1
Author:      Sergey Zaharchenko <zaharchenko.dev@gmail.com>
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


class WP_Load_Profiler {

	private static $instance;

	private $profiler;

	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}


	private function __construct() {
	}

	public function init() {
		$late                         = 1000000;
		$early                        = - 1000000;
		$this->profiler['start_time'] = microtime( true ) * 1000;
		$this->profiler['last_time']  = $this->profiler['start_time'];

		add_action( 'init', [ $this, 'before_init' ], $early );
		add_action( 'init', [ $this, 'after_init' ], $late );
		add_action( 'wp_loaded', [ $this, 'wp_loaded' ], $late );
		add_action( 'template_redirect', [ $this, 'template_redirect' ], $late );
		add_action( 'shutdown', [ $this, 'shutdown' ], $late );
	}

	public function before_init() {
		$this->check( 'Before init hook' );
	}

	public function after_init() {
		$this->check( 'After init hook' );
	}

	public function wp_loaded() {
		$this->check( 'WP loaded' );
	}

	public function template_redirect() {
		$this->check( 'Before page loading' );
	}

	public function shutdown() {
		$this->check( 'Shutdown' );
		$this->show_profiler();
	}

	public function show_profiler() {
		$profiler_checks = $this->profiler['checks'];
		include 'templates/profiler.tpl.php';
	}

	public function check( $description ) {
		$current_time = microtime( true ) * 1000;

		$this->profiler['checks'][] = [
			'description'          => $description,
			'time_from_last_check' => $current_time - $this->profiler['last_time'],
			'time_from_start'      => $current_time - $this->profiler['start_time'],
		];

		$this->profiler['last_time'] = $current_time;
	}
}

WP_Load_Profiler::instance()->init();
