<?php

/**
 * Class SiteOrigin_Widget_Field_BaseTest
 */
class SiteOrigin_Widget_Field_BaseTest extends WP_UnitTestCase {
	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage SiteOrigin_Widget_Field_Base::__construct: $field_options must contain a 'type' field.
	 */
	public function constructor_throws_error_if_type_not_set() {
		$field_options = array();

		$this->getMockBuilder( 'SiteOrigin_Widget_Field_Base' )
			 ->setConstructorArgs( array( '', '', '', $field_options ) )
			 ->getMockForAbstractClass();
	}

	/**
	 * @test
	 */
	public function constructor_initializes_instance_properties() {

		$field_options = array(
			'type' => 'blueberry',
			'label' => 'A label',
			'default' => 12,
			'description' => 'A description',
			'optional' => true,
			'sanitize' => 'meh'
		);

		$base_field = $this->getMockBuilder( 'SiteOrigin_Widget_Field_Base' )
		                   ->setConstructorArgs( array( '', '', '', $field_options ) )
		                   ->getMockForAbstractClass();

		$this->assertEqualFields( $base_field, $field_options );
	}

	/**
	 * @test
	 */
	public function constructor_uses_default_properties() {

		$default_field_options = array(
			'default' => 12,
			'description' => 'A description',
			'optional' => true,
			'sanitize' => 'meh'
		);

		$field_options = array(
			'type' => 'blueberry',
			'label' => 'A label',
		);

		$combined_options = array_merge( $default_field_options, $field_options );

		$base_field = new SiteOrigin_Widget_Field_Mock( '', '', '', $field_options );

		$this->assertEqualFields( $base_field, $combined_options );
	}

	/**
	 * @test
	 */
	public function constructor_initializes_label_classes() {
		$field_options = array(
			'type' => 'blueberry',
		);

		$base_field = $this->getMockBuilder( 'SiteOrigin_Widget_Field_Base' )
		                   ->setConstructorArgs( array( '', '', '', $field_options ) )
		                   ->getMockForAbstractClass();

		$lbl_cls_key = 'label_classes';
		$label_classes = $base_field->$lbl_cls_key;
		$this->assertCount( 1, $label_classes );
		$this->assertEquals( 'siteorigin-widget-field-label', $label_classes[0], 'SiteOrigin_Widget_Field_Base did not initialize $label_classes correctly' );
	}

	/**
	 * @test
	 */
	public function constructor_initializes_description_classes() {
		$field_options = array(
			'type' => 'blueberry',
		);

		$base_field = $this->getMockBuilder( 'SiteOrigin_Widget_Field_Base' )
		                   ->setConstructorArgs( array( '', '', '', $field_options ) )
		                   ->getMockForAbstractClass();

		$desc_cls_key = 'description_classes';
		$description_classes = $base_field->$desc_cls_key;
		$this->assertCount( 1, $description_classes );
		$this->assertEquals( 'siteorigin-widget-field-description', $description_classes[0], 'SiteOrigin_Widget_Field_Base did not initialize $description_classes correctly' );
	}

	/**
	 * @test
	 */
	public function render_adds_base_field_classes() {
		$field_options = array(
			'type' => 'blueberry',
		);

		/* @var $base_field SiteOrigin_Widget_Field_Base */
		$base_field = $this->getMockBuilder( 'SiteOrigin_Widget_Field_Base' )
		                   ->setConstructorArgs( array( 'test_base', '', '', $field_options ) )
		                   ->getMockForAbstractClass();

		$classes = 'siteorigin-widget-field siteorigin-widget-field-type-blueberry siteorigin-widget-field-test_base';
		$html = get_field_render_output( $base_field );
		$this->assertContains( $classes, $html );
	}

	/**
	 * @test
	 */
	public function render_adds_optional_class() {
		$field_options = array(
			'type' => 'blueberry',
			'optional' => true
		);

		/* @var $base_field SiteOrigin_Widget_Field_Base */
		$base_field = $this->getMockBuilder( 'SiteOrigin_Widget_Field_Base' )
		                   ->setConstructorArgs( array( 'test_base', '', '', $field_options ) )
		                   ->getMockForAbstractClass();

		$optional_class = 'siteorigin-widget-field-is-optional';
		$html = get_field_render_output( $base_field );
		$this->assertContains( $optional_class, $html );
	}

	/**
	 * @test
	 */
	public function render_adds_form_states_data_attributes() {
		$field_options = array(
			'type' => 'blueberry',
			'state_emitter' => array(
				'callback' => 'select',
				'args' => array( 'select_field' )
			),
			'state_handler' => array(
				'select_field[opt1]' => array('show'),
				'select_field[opt2]' => array('hide'),
			)
		);

		/* @var $base_field SiteOrigin_Widget_Field_Base */
		$base_field = $this->getMockBuilder( 'SiteOrigin_Widget_Field_Base' )
		                   ->setConstructorArgs( array( 'test_base', '', '', $field_options ) )
		                   ->getMockForAbstractClass();

		$state_emitter_attribute = 'data-state-emitter="' . esc_attr( json_encode( $field_options['state_emitter'] ) ) . '"';
		$state_handler_attribute = 'data-state-handler="' . esc_attr( json_encode( $field_options['state_handler'] ) ) . '"';
		$html = get_field_render_output( $base_field );
		$this->assertContains( $state_emitter_attribute, $html );
		$this->assertContains( $state_handler_attribute, $html );
	}

	/**
	 * @test
	 */
	public function render_outputs_default_label() {
		$field_options = array(
			'type' => 'blueberry',
			'label' => 'A label',
		);
		/* @var $base_field SiteOrigin_Widget_Field_Base */
		$base_field = $this->getMockBuilder( 'SiteOrigin_Widget_Field_Base' )
		                   ->setConstructorArgs( array( 'test_base', 'test_base_id', '', $field_options ) )
		                   ->getMockForAbstractClass();

		$html = get_field_render_output( $base_field );

		$this->assertContains( '<label for="test_base_id"', $html );
		$this->assertContains( 'A label', $html );
		$this->assertNotContains( '<span class="field-optional">(Optional)</span>', $html );
	}

	/**
	 * @test
	 */
	public function render_outputs_label_with_optional_tag() {
		$field_options = array(
			'type' => 'blueberry',
			'label' => 'A label',
			'optional' => true,
		);
		/* @var $base_field SiteOrigin_Widget_Field_Base */
		$base_field = $this->getMockBuilder( 'SiteOrigin_Widget_Field_Base' )
		                   ->setConstructorArgs( array( 'test_base', 'test_base_id', '', $field_options ) )
		                   ->getMockForAbstractClass();

		$html = get_field_render_output( $base_field );

		$this->assertContains( '<span class="field-optional">(Optional)</span>', $html );
	}

	/**
	 * @test
	 */
	public function render_outputs_default_description() {
		$field_options = array(
			'type' => 'blueberry',
			'description' => 'A description',
		);
		/* @var $base_field SiteOrigin_Widget_Field_Base */
		$base_field = $this->getMockBuilder( 'SiteOrigin_Widget_Field_Base' )
		                   ->setConstructorArgs( array( 'test_base', 'test_base_id', '', $field_options ) )
		                   ->getMockForAbstractClass();

		$html = get_field_render_output( $base_field );

		$this->assertContains( 'A description', $html );
	}

	/**
	 * @test
	 */
	public function sanitize_url_sanitizes_url() { // o_0

		$field_options = array(
			'type' => 'blueberry',
			'sanitize' => 'url',
		);

		/* @var $base_field SiteOrigin_Widget_Field_Base */
		$base_field = $this->getMockBuilder( 'SiteOrigin_Widget_Field_Base' )
		                   ->setConstructorArgs( array( '', '', '', $field_options ) )
		                   ->getMockForAbstractClass();

		$raw_url = 'http://www.example.com/?with_queries=tr[ue&more_queries=also]true';

		$sanitized_url = $base_field->sanitize( $raw_url );

		//Removes square brackets
		$this->assertEquals( 'http://www.example.com/?with_queries=true&more_queries=alsotrue', $sanitized_url );
	}

	/**
	 * @test
	 */
	public function sanitize_url_replaces_post_id_protocol_with_permalink() {

		$post_id = $this->factory->post->create();

		$field_options = array(
			'type' => 'blueberry',
			'sanitize' => 'url',
		);

		/* @var $base_field SiteOrigin_Widget_Field_Base */
		$base_field = $this->getMockBuilder( 'SiteOrigin_Widget_Field_Base' )
		                   ->setConstructorArgs( array( '', '', '', $field_options ) )
		                   ->getMockForAbstractClass();

		$raw_url = 'post:' . $post_id;

		$sanitized_url = $base_field->sanitize( $raw_url );

		$this->assertEquals( 'http://example.org/?p=3', $sanitized_url );
	}

	/**
	 * @test
	 */
	public function sanitize_url_allows_skype_protocol() {

		$field_options = array(
			'type' => 'blueberry',
			'sanitize' => 'url',
		);

		/* @var $base_field SiteOrigin_Widget_Field_Base */
		$base_field = $this->getMockBuilder( 'SiteOrigin_Widget_Field_Base' )
		                   ->setConstructorArgs( array( '', '', '', $field_options ) )
		                   ->getMockForAbstractClass();

		$raw_url = 'skype:somepersonsskypename';

		$sanitized_url = $base_field->sanitize( $raw_url );

		$this->assertEquals( 'skype:somepersonsskypename', $sanitized_url );
	}

	/**
	 * @test
	 */
	public function sanitize_email_sanitizes_email() {

		$field_options = array(
			'type' => 'blueberry',
			'sanitize' => 'email',
		);

		/* @var $base_field SiteOrigin_Widget_Field_Base */
		$base_field = $this->getMockBuilder( 'SiteOrigin_Widget_Field_Base' )
		                   ->setConstructorArgs( array( '', '', '', $field_options ) )
		                   ->getMockForAbstractClass();

		$raw_email = 'us()e[r@example.org';

		$sanitized_email = $base_field->sanitize( $raw_email );

		$this->assertEquals( 'user@example.org', $sanitized_email );
	}
}

function get_field_render_output( $field, $value = '' ) {
	ob_start();
	/* @var $field SiteOrigin_Widget_Field_Base */
	$field->render( $value );
	return ob_get_clean();
}

class SiteOrigin_Widget_Field_Mock extends SiteOrigin_Widget_Field_Base {

	protected function get_default_options() {
		return array(
			'default' => 12,
			'description' => 'A description',
			'optional' => true,
			'sanitize' => 'meh'
		);
	}

	protected function render_field( $value, $instance ) {
		return '';
	}

	protected function sanitize_field_input( $value ) {
		return $value;
	}
}