<?php

class IWJ_Admin_Radiotax {
	public $taxonomy;
	public $taxonomy_metabox_id;
	public $post_type;

	static public function init(){
		//Load admin scripts
		add_action('admin_enqueue_scripts',array(__CLASS__,'admin_script'));
	}

	function __construct($taxonomy, $post_type)
	{
		$this->taxonomy = $taxonomy;
		$this->taxonomy_metabox_id = $taxonomy.'div';
		$this->post_type = $post_type;

		//Remove old taxonomy meta box

        add_action('admin_menu', array( $this , 'remove_meta_box'));

		//Add new taxonomy meta box
		add_action( 'add_meta_boxes', array($this ,'add_meta_box'));
	}

	public static function admin_script(){
		wp_register_script( 'radiotax', IWJ_PLUGIN_URL.'/assets/js/radiotax.js', array('jquery'), null, true ); // We specify true here to tell WordPress this script needs to be loaded in the footer
	}

	public function remove_meta_box(){
   		remove_meta_box($this->taxonomy_metabox_id, $this->post_type, 'normal');
	} 


	public function add_meta_box() {
		$tax = get_taxonomy($this->taxonomy);
		add_meta_box( 'metabox'.$this->taxonomy.'_id', $tax->labels->name ,array($this,'metabox'), $this->post_type ,'side','core');
	}  
        

	//Callback to set up the metabox  
	public function metabox( $post ) {

		wp_enqueue_script( 'radiotax');
		//Get taxonomy and terms
       	 $taxonomy = $this->taxonomy;
      
       	 //Set up the taxonomy object and get terms  
       	 $tax = get_taxonomy($taxonomy);  
       	 $terms = get_terms($taxonomy,array('hide_empty' => 0));  
      
       	 //Name of the form  
       	 $name = 'tax_input[' . $taxonomy . ']';  
      
       	 //Get current and popular terms  
       	 $popular = get_terms( $taxonomy, array( 'orderby' => 'count', 'order' => 'DESC', 'number' => 10, 'hierarchical' => false ) );  
       	 $postterms = get_the_terms( $post->ID,$taxonomy );  
       	 $current = ($postterms ? array_pop($postterms) : false);  
       	 $current = ($current ? $current->term_id : 0);  
       	 ?>  
      
		<div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv iwj-radiotax" data-taxonomy="<?php echo $taxonomy; ?>">
			<!-- Display tabs-->
			<ul id="<?php echo $taxonomy; ?>-tabs" class="category-tabs">
				<li class="tabs"><a href="#<?php echo $taxonomy; ?>-all" tabindex="3"><?php echo $tax->labels->singular_name; ?></a></li>
				<li class="hide-if-no-js"><a href="#<?php echo $taxonomy; ?>-pop" tabindex="3"><?php _e( 'Most Used', 'iwjob' ); ?></a></li>
			</ul>

			<!-- Display taxonomy terms -->
			<div id="<?php echo $taxonomy; ?>-all" class="tabs-panel">
				<ul id="<?php echo $taxonomy; ?>checklist" class="list:<?php echo $taxonomy?> categorychecklist form-no-clear">
				<?php foreach($terms as $term){
       				 $id = $taxonomy.'-'.$term->term_id;
					$value= "value='{$term->term_id}'";
				        echo "<li id='$id'><label class='selectit'>";
				        echo "<input type='radio' id='in-$id' name='{$name}'".checked($current,$term->term_id,false)." {$value} />$term->name<br />";
				        echo "</label></li>";
		       	 }?>
				</ul>
			</div>

			<!-- Display popular taxonomy terms -->
			<div id="<?php echo $taxonomy; ?>-pop" class="tabs-panel" style="display: none;">
				<ul id="<?php echo $taxonomy; ?>checklist-pop" class="categorychecklist form-no-clear" >
				<?php foreach($popular as $term){
				        $id = 'popular-'.$taxonomy.'-'.$term->term_id;
                    $value= "value='{$term->term_id}'";
				        echo "<li id='$id'><label class='selectit'>";
				        echo "<input type='radio' id='in-$id'".checked($current,$term->term_id,false)." {$value} />$term->name<br />";
				        echo "</label></li>";
				}?>
				</ul>
			</div>
			<div class="wp-hidden-children" id="<?php echo $taxonomy; ?>-add">
				<a href="#" id="" class="hide-if-no-js radio-tax-toggle-add taxonomy-add-new" >+<?php echo esc_attr( $tax->labels->add_new_item ); ?></a>
				<p class="category-add wp-hidden-child">
					<input type="text" name="new<?php echo $taxonomy; ?>" id="new-<?php echo $taxonomy; ?>" class="form-required form-input-tip" value="" tabindex="3" aria-required="true"/>
					<input type="button" class="button radio-tax-add " value="<?php echo esc_attr( $tax->labels->add_new_item ); ?>">
					<?php wp_nonce_field( 'radio-tax-add-'.$taxonomy, '_wpnonce_radio-add-tag', false ); ?>
				</p>
			</div>
		</div>
        <?php  
    }
}

IWJ_Admin_Radiotax::init();
?>
