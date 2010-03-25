<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Jelly Model namespace
 *
 * @author avis <smgladkovskiy@gmail.com>
 */
class Model_Namespace extends Jelly_Model {

	/**
	 * Initializating model meta information
	 *
	 * @TODO: сделать возможность join'ить parent
	 * @param Jelly_Meta $meta
	 */
    public static function initialize(Jelly_Meta $meta)
    {
        $meta->table('namespaces')
             ->fields(array(
                 'id' => new Field_Primary,
                 'parent' => new Field_BelongsTo(array(
					 'model' => 'namespace',
					 'foreign' => 'namespace',
					 'column' => 'parent_id'
				 )),

                 'name' => new Field_String,
				 'description' => new Field_String(array(
					 'column' => 'value'
				 )),
             ))
//			->load_with(array(
//				'parent'
//			))
		;
    }
} // End Jelly Model namespace