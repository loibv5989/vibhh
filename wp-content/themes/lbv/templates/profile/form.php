<?php

defined('ABSPATH') || exit;
$current_user  = wp_get_current_user();
?>

<div class="fup-container">
    <?php if ( isset( $_GET['profile_updated'] ) && $_GET['profile_updated'] === 'true' ) : ?>
        <div class="fup-notice fup-success">
            <p><?php esc_html_e( '✓ Profile has been successfully updated!', 'lbv' ); ?></p>
        </div>
    <?php endif; ?>

    <?php if ( ! empty( $this->errors ) ) : ?>
        <div class="fup-notice fup-error">
            <?php foreach ( $this->errors as $error ) : ?>
                <p>✗ <?php echo esc_html( $error ); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="fup-form">
        <section class="fup-section">
            <h2><?php esc_html_e( 'Name', 'lbv' ); ?></h2>
            <div class="fup-field">
                <label for="author_url"><?php esc_html_e( 'User URL', 'lbv' ); ?></label>
                <?php $author_url = get_author_posts_url( $current_user->ID ); ?>
                <a id="author_url" href="<?php echo esc_url( $author_url ); ?>" class="input-like">
                    <?php echo esc_html( $author_url ); ?>
                </a>
            </div>

            <div class="fup-field">
                <label for="user_login"><?php esc_html_e( 'Username', 'lbv' ); ?></label>
                <input type="text" id="user_login" value="<?php echo esc_attr( $current_user->user_login ); ?>" disabled>
                <small><?php esc_html_e( 'Usernames cannot be changed.', 'lbv' ); ?></small>
            </div>

            <div class="fup-row">
                <div class="fup-field">
                    <label for="first-name"><?php esc_html_e( 'First Name', 'lbv' ); ?></label>
                    <input type="text" name="first-name" id="first-name" value="<?php echo esc_attr( get_user_meta( $current_user->ID, 'first_name', true ) ); ?>">
                </div>
                <div class="fup-field">
                    <label for="last-name"><?php esc_html_e( 'Last Name', 'lbv' ); ?></label>
                    <input type="text" name="last-name" id="last-name" value="<?php echo esc_attr( get_user_meta( $current_user->ID, 'last_name', true ) ); ?>">
                </div>
            </div>

            <div class="fup-field">
                <label for="nickname"><?php esc_html_e( 'Nickname', 'lbv' ); ?> <span class="fup-required">*</span></label>
                <input type="text" name="nickname" id="nickname" value="<?php echo esc_attr( get_user_meta( $current_user->ID, 'nickname', true ) ); ?>" required>
            </div>

            <div class="fup-field">
                <label for="display_name"><?php esc_html_e( 'Display name publicly as', 'lbv' ); ?></label>
                <select name="display_name" id="display_name">
                    <?php
                    $display_options = $this->get_display_name_options( $current_user );
                    foreach ( $display_options as $option ) :
                        ?>
                        <option value="<?php echo esc_attr( $option ); ?>" <?php selected( $current_user->display_name, $option ); ?>>
                            <?php echo esc_html( $option ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </section>

        <section class="fup-section">
            <h2><?php esc_html_e( 'Contact Information', 'lbv' ); ?></h2>

            <div class="fup-field">
                <label for="email"><?php esc_html_e( 'Email', 'lbv' ); ?> <span class="fup-required">*</span></label>
                <input type="email" name="email" id="email" value="<?php echo esc_attr( $current_user->user_email ); ?>" required>
                <small><?php esc_html_e( 'If you change this, a confirmation email will be sent to your new address.', 'lbv' ); ?></small>
            </div>

            <div class="fup-field">
                <label for="url"><?php esc_html_e( 'Website', 'lbv' ); ?></label>
                <input type="url" name="url" id="url" value="<?php echo esc_url( $current_user->user_url ); ?>">
            </div>
        </section>

        <section class="fup-section">
            <h2><?php esc_html_e( 'Biographical Info', 'lbv' ); ?></h2>

            <div class="fup-field">
                <textarea name="description" id="description" rows="5"><?php echo esc_textarea( get_user_meta( $current_user->ID, 'description', true ) ); ?></textarea>
                <small><?php esc_html_e( 'Share a little biographical information to fill out your profile.', 'lbv' ); ?></small>
            </div>

            <div class="fup-field">
                <h2><?php esc_html_e( 'Profile Picture', 'lbv' ); ?></h2>
                <div class="fup-avatar">
                    <div class="show-avatar">
                        <?php
                        $avatar_id = get_user_meta( $current_user->ID, 'lbv_avatar_attachment_id', true );
                        if ( $avatar_id ) {
                            echo wp_get_attachment_image( $avatar_id, array(96, 96), false, array( 'class' => 'fup-current-avatar' ) );
                        } else {
                            echo get_avatar( $current_user->ID, 96 );
                        }
                        ?>
                    </div>
                    <div class="upload-field">
                        <input type="file" name="profile_avatar" id="profile_avatar" accept="image/*">
                        <small><?php esc_html_e( 'Maximum file size: 2MB. Allowed types: JPG, PNG, GIF, WEBP, SVG.', 'lbv' ); ?></small>
                        <?php if ( $avatar_id ) : ?>
                            <br>
                            <div class="remove-avatar">
                                <input type="checkbox" name="remove_avatar" value="1">
                                <span><?php esc_html_e( 'Remove current avatar', 'lbv' ); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="fup-field">
                <h2><?php esc_html_e( 'Notifications', 'lbv' ); ?></h2>
                <?php $newsletter = get_user_meta( $current_user->ID, 'lbv_newsletter', true ); ?>
                <div class="user-newsletter">
                    <input type="checkbox" name="user_newsletter" value="1" <?php checked( $newsletter, 1 ); ?>>
                    <span><?php esc_html_e( 'Sign up for our newsletter', 'lbv' ); ?></span>
                </div>

                <?php $lbv_post_editor = get_user_meta( $current_user->ID, 'lbv_post_editor', true ); ?>
                <div class="user-post-editor">
                    <input type="checkbox" name="lbv_post_editor" value="1" <?php checked( $lbv_post_editor, 1 ); ?>>
                    <span><?php esc_html_e( 'Disable edit notification emails', 'lbv' ); ?></span>
                </div>
            </div>
        </section>

        <section class="fup-section">
            <h2><?php esc_html_e( 'New Password', 'lbv' ); ?></h2>
            <div class="fup-field">
                <label for="pass1"><?php esc_html_e( 'New Password', 'lbv' ); ?></label>
                <input type="password" name="pass1" id="pass1" autocomplete="new-password">
            </div>
            <div class="fup-field">
                <label for="pass2"><?php esc_html_e( 'Confirm New Password', 'lbv' ); ?></label>
                <input type="password" name="pass2" id="pass2" autocomplete="new-password">
            </div>
        </section>

        <?php
        $user_roles = (array) $current_user->roles;
        if ( in_array( 'subscriber', $user_roles ) ) :
            ?>
            <section class="fup-section fup-delete-section">
                <div class="fup-field">
                    <button type="button" class="fup-delete-account-btn" id="show-delete-form"
                            data-username="<?php echo esc_attr( trim($current_user->user_login) ); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-label="Delete">
                            <path d="M3 6h18"></path><path d="M8 6V4h8v2"></path><path d="M6 6l1 14h10l1-14"></path><path d="M10 11v6"></path><path d="M14 11v6"></path>
                        </svg>

                        <?php esc_html_e( 'Delete User', 'lbv' ); ?>
                    </button>

                    <div class="fup-delete-account-form" id="delete-account-form">
                        <div class="fup-delete-warning">
                            <div>
                                <p class="fup-delete-message">
                                    😔 <?php esc_html_e( "We're sorry to see you go! Your account will be permanently deleted and all your data removed.", 'lbv' ); ?>
                                </p>
                                <p class="fup-delete-subtext">
                                    <?php esc_html_e( 'You can always rejoin our community whenever you are ready.', 'lbv' ); ?>
                                </p>
                            </div>
                        </div>

                        <div class="fup-confirm-section">
                            <p class="fup-confirm-title">
                                <?php esc_html_e( 'To confirm account deletion, please type your username: ', 'lbv' ); ?>
                                <code><?php echo esc_html($current_user->user_login); ?></code>
                            </p>
                            <div class="fup-input-group">
                                <input type="text" id="confirm-username-input"
                                       placeholder="<?php esc_attr_e( 'Enter your username exactly as shown above', 'lbv' ); ?>" autocomplete="off" class="fup-confirm-input">
                                <div class="fup-input-feedback" id="username-feedback"></div>
                            </div>
                        </div>

                        <div class="fup-delete-actions">
                            <button type="button" class="fup-btn fup-btn-cancel" id="cancel-delete">
                                <?php esc_html_e( 'Cancel', 'lbv' ); ?>
                            </button>
                            <button type="button" class="fup-btn fup-btn-delete" id="submit-delete" disabled>
                                <span class="fup-btn-delete-text"><?php esc_html_e( 'Goodbye!', 'lbv' ); ?></span>
                                <span class="fup-btn-delete-loading">
                                <span class="fup-spinner"></span><?php esc_html_e( 'Deleting...', 'lbv' ); ?></span>
                            </button>
                        </div>
                    </div>

                    <div class="fup-delete-success" id="delete-success-message">
                        <p class="fup-success-message">
                            <?php esc_html_e( 'Your account has been permanently deleted. Thank you for being part of our community.', 'lbv' ); ?>
                        </p>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php do_action( 'fup_profile_form_fields', $current_user ); ?>

        <div class="fup-submit">
            <?php wp_nonce_field( 'fup_update_profile_' . $current_user->ID, 'fup_nonce' ); ?>
            <button type="submit" name="fup_update_profile" class="fup-btn">
                <?php esc_html_e( 'Update Profile', 'lbv' ); ?>
            </button>
        </div>
    </form>
</div>
