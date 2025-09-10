<?php

// Add field khi tạo Destination
add_action( 'destination_add_form_fields', function() { ?>
    <div class="form-field term-destination-images-wrap">
        <label><?php _e( 'Ảnh liên quan (nhiều)', 'your-textdomain' ); ?></label>
        <button class="button" id="nm-add-destination-images"><?php _e('Chọn ảnh', 'your-textdomain'); ?></button>
        <div id="nm-selected-destination-images" style="margin-top:10px;"></div>
        <input type="hidden" id="nm-destination-images-ids" name="nm_destination_images_ids" value="" />
    </div>
<?php });

// Edit field khi sửa Destination
add_action( 'destination_edit_form_fields', function( $term ) {
    $images = get_term_meta( $term->term_id, 'destination_images', true );
    if ( ! is_array( $images ) ) $images = [];
    $ids_str = implode( ',', $images ); ?>
    <tr class="form-field term-destination-images-wrap">
        <th scope="row"><label><?php _e( 'Ảnh liên quan (nhiều)', 'your-textdomain' ); ?></label></th>
        <td>
            <button class="button" id="nm-add-destination-images"><?php _e('Chọn ảnh', 'your-textdomain'); ?></button>
            <div id="nm-selected-destination-images" style="margin-top:10px;">
                <?php foreach ( $images as $id ) :
                    $img = wp_get_attachment_image_url( $id, 'thumbnail' );
                    if ( $img ): ?>
                        <div class="nm-thumb" data-id="<?php echo esc_attr($id); ?>" style="display:inline-block;margin:5px;position:relative;">
                            <img src="<?php echo esc_url($img); ?>" style="width:80px;height:80px;object-fit:cover;border-radius:6px;" />
                            <button class="nm-remove-image" style="position:absolute;top:2px;right:2px;background:#000;color:#fff;border:none;border-radius:50%;cursor:pointer;">×</button>
                        </div>
                    <?php endif;
                endforeach; ?>
            </div>
            <input type="hidden" id="nm-destination-images-ids" name="nm_destination_images_ids" value="<?php echo esc_attr( $ids_str ); ?>" />
        </td>
    </tr>
<?php }, 10, 1 );

// Save meta
add_action( 'created_destination', 'nm_save_destination_images' );
add_action( 'edited_destination', 'nm_save_destination_images' );
function nm_save_destination_images( $term_id ) {
    if ( isset($_POST['nm_destination_images_ids']) ) {
        $raw = sanitize_text_field( wp_unslash( $_POST['nm_destination_images_ids'] ) );
        $ids = array_filter( array_map( 'intval', explode( ',', $raw ) ) );
        if ( ! empty( $ids ) ) {
            update_term_meta( $term_id, 'destination_images', $ids );
        } else {
            delete_term_meta( $term_id, 'destination_images' );
        }
    }
}
