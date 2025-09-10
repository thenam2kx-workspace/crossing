<?php
/**
 * taxonomy-trip-types-images.php
 *
 * Tách riêng mã quản trị để thêm/luu nhiều ảnh cho taxonomy `trip_types`.
 * Cách dùng:
 * 1. Đặt file này vào: wp-content/themes/your-child-theme/inc/taxonomy-trip-types-images.php
 * 2. Include file trong functions.php của child-theme:
 *    require_once get_stylesheet_directory() . '/inc/taxonomy-trip-types-images.php';
 * 3. Tạo file JS: /assets/js/taxonomy-images.js (mã JS phải giống hướng dẫn trước).
 * 4. Tải ảnh placeholder vào: /assets/images/placeholder.jpg (tùy chọn).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! function_exists( 'nm_enqueue_taxonomy_media' ) ) :
    add_action( 'admin_enqueue_scripts', 'nm_enqueue_taxonomy_media' );
    function nm_enqueue_taxonomy_media( $hook ) {
        // Only load on term add/edit pages (edit-tags.php or term.php)
        if ( ! in_array( $hook, array( 'term.php', 'edit-tags.php' ), true ) ) {
            return;
        }

        $screen = get_current_screen();
        if ( empty( $screen->taxonomy ) || $screen->taxonomy !== 'trip_types' ) {
            return;
        }

        wp_enqueue_media();

        $js_path = get_stylesheet_directory() . '/assets/js/taxonomy-images.js';
        wp_enqueue_script(
            'nm-taxonomy-images',
            get_stylesheet_directory_uri() . '/assets/js/taxonomy-images.js',
            array( 'jquery' ),
            file_exists( $js_path ) ? filemtime( $js_path ) : false,
            true
        );

        wp_localize_script( 'nm-taxonomy-images', 'nmTaxImg', array(
            'removeText' => __( 'Xóa', 'your-textdomain' ),
        ) );
    }
endif;


if ( ! function_exists( 'nm_trip_types_add_field' ) ) :
    // Show field on "Add new term" form
    add_action( 'trip_types_add_form_fields', 'nm_trip_types_add_field', 10, 2 );
    function nm_trip_types_add_field( $taxonomy ) { ?>
        <div class="form-field form-required term-trip-images-wrap">
            <label><?php _e( 'Ảnh liên quan (nhiều)', 'your-textdomain' ); ?></label>
            <div id="nm-trip-images-add-area">
                <button class="button" id="nm-add-trip-images"><?php _e( 'Chọn ảnh', 'your-textdomain' ); ?></button>
                <div id="nm-selected-trip-images" style="margin-top:10px;"></div>
                <input type="hidden" id="nm-trip-images-ids" name="nm_trip_images_ids" value="" />
                <p class="description"><?php _e( 'Bạn có thể thêm nhiều ảnh cho term này.', 'your-textdomain' ); ?></p>
            </div>
        </div>
    <?php }
endif;


if ( ! function_exists( 'nm_trip_types_edit_field' ) ) :
    // Show field on "Edit term" form
    add_action( 'trip_types_edit_form_fields', 'nm_trip_types_edit_field', 10, 2 );
    function nm_trip_types_edit_field( $term, $taxonomy ) {
        $images = get_term_meta( $term->term_id, 'trip_type_images', true );
        if ( ! is_array( $images ) ) {
            $images = array();
        }
        $ids_str = implode( ',', $images ); ?>
        <tr class="form-field term-trip-images-wrap">
            <th scope="row"><label><?php _e( 'Ảnh liên quan (nhiều)', 'your-textdomain' ); ?></label></th>
            <td>
                <div id="nm-trip-images-edit-area">
                    <button class="button" id="nm-add-trip-images"><?php _e( 'Chọn ảnh', 'your-textdomain' ); ?></button>
                    <div id="nm-selected-trip-images" style="margin-top:10px;">
                        <?php
                        foreach ( $images as $id ) {
                            $img = wp_get_attachment_image_url( $id, 'thumbnail' );
                            if ( $img ) {
                                echo '<div class="nm-thumb" data-id="' . esc_attr( $id ) . '" style="display:inline-block;margin-right:8px;position:relative;">';
                                echo '<img src="' . esc_url( $img ) . '" style="width:80px;height:80px;object-fit:cover;border-radius:6px;" />';
                                echo '<button class="nm-remove-image" style="position:absolute;right:2px;top:2px;">×</button>';
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                    <input type="hidden" id="nm-trip-images-ids" name="nm_trip_images_ids" value="<?php echo esc_attr( $ids_str ); ?>" />
                    <p class="description"><?php _e( 'Bạn có thể kéo thêm hoặc xóa ảnh. Ảnh được lưu theo thứ tự.', 'your-textdomain' ); ?></p>
                </div>
            </td>
        </tr>
    <?php }
endif;


if ( ! function_exists( 'nm_save_trip_types_images' ) ) :
    // Save term meta when new term created
    add_action( 'created_trip_types', 'nm_save_trip_types_images', 10, 2 );
    add_action( 'edited_trip_types', 'nm_save_trip_types_images', 10, 2 );
    function nm_save_trip_types_images( $term_id, $tt_id = null ) {
        if ( ! isset( $_POST['nm_trip_images_ids'] ) ) {
            return;
        }

        $raw = sanitize_text_field( wp_unslash( $_POST['nm_trip_images_ids'] ) );
        if ( empty( $raw ) ) {
            delete_term_meta( $term_id, 'trip_type_images' );
            return;
        }

        $ids = array_filter( array_map( 'intval', explode( ',', $raw ) ) );

        if ( ! empty( $ids ) ) {
            update_term_meta( $term_id, 'trip_type_images', $ids );
        } else {
            delete_term_meta( $term_id, 'trip_type_images' );
        }
    }
endif;

?>