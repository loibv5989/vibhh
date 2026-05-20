<?php
// lấy thời gian
?>


<div class="contact-container">
    <div class="lbv-topct">
        <p class="lbv-pq">
            <?php _e('If you have any questions, comments, or concerns, please don\'t hesitate to contact us using the form below. We\'re here to help!', 'lbv'); ?>
        </p>

        <div id="contact-loading" style="display: none; text-align: center; margin: 10px 0;">
            <div class="spinner"></div>
            <p><?php _e('Sending message...', 'lbv'); ?></p>
        </div>
    </div>
    <div id="contact-response"></div>
    <form class="contact-form" method="post" action="#" novalidate>
        <div class="row">
            <div class="form-group">
                <label for="fullname">
                    <?php _e('Full Name', 'lbv'); ?> <span class="required">*</span>
                </label>
                <input type="text" id="fullname" name="fullname" placeholder="<?php esc_attr_e('Your name..', 'lbv'); ?>" required minlength="2" maxlength="50" >
            </div>
            <div class="form-group">
                <label for="email">
                    <?php _e('Email', 'lbv'); ?> <span class="required">*</span>
                </label>
                <input type="email" id="email" name="email" placeholder="<?php esc_attr_e('Your email..', 'lbv'); ?>" required >
            </div>
        </div>
        <div class="form-group">
            <label for="subject">
                <?php _e('Subject', 'lbv'); ?> <span class="required">*</span>
            </label>
            <input type="text" id="subject" name="subject" placeholder="<?php esc_attr_e('Subject', 'lbv'); ?>" required minlength="5" maxlength="100" >
        </div>
        <div class="form-group">
            <label for="content">
                <?php _e('Content', 'lbv'); ?> <span class="required">*</span>
            </label>
            <textarea id="content" name="content" placeholder="<?php esc_attr_e('Write something..', 'lbv'); ?>" rows="6" required minlength="10" maxlength="300" ></textarea>
        </div>
        <button type="submit" class="submit-btn">
            <?php _e('Submit', 'lbv'); ?>
        </button>
    </form>

</div>
