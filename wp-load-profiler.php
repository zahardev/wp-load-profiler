<?php
/*
Plugin Name: WP Load Profiler
Description: Monitoring WordPress loading pages time
Version:     0.3
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
		global $timestart;

		$late  = 1000000;
		$early = - 1000000;

		$this->profiler['last_time'] = $this->profiler['start_time'] = $timestart;

		$actions = [
			'muplugins_loaded',
			'plugins_loaded',
			'init',
			'wp_loaded',
			'template_redirect',
			'wp_head',
			'shutdown',
		];

		foreach ( $actions as $action ) {
			add_action( $action, [ $this, 'before_' . $action ], $early );
			add_action( $action, [ $this, 'after_' . $action ], $late );
		}

		add_action( 'shutdown', [ $this, 'shutdown' ], $late );
	}

	public function __call( $method, $args ) {
		$this->check( $method );
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
		$current_time = microtime( true );

		$this->profiler['checks'][] = [
			'description'          => $description,
			'time_from_last_check' => $current_time - $this->profiler['last_time'],
			'time_from_start'      => $current_time - $this->profiler['start_time'],
		];

		$this->profiler['last_time'] = $current_time;
	}
}

WP_Load_Profiler::instance()->init();
