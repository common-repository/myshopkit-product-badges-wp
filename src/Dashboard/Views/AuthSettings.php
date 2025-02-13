<form method="POST" action="<?php echo admin_url('admin.php?page=' . $this->getAuthSlug()); ?>">
    <?php wp_nonce_field('mysmbwp-auth-action', 'mysmbwp-auth-field'); ?>
    <table class="form-table">
        <thead>
        <tr>
            <th><?php echo esc_html__('Magic Badges Auth Settings', 'myshopkit-product-badges-wp'); ?></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th>
                <label for="mysmbwp-username"><?php echo esc_html__('Username',
                        'myshopkit-product-badges-wp'); ?></label>
            </th>
            <td>
                <input id="mysmbwp-username" type="text" name="mysmbwp-auth[username]"
                       value="<?php echo esc_attr($this->aOptions['username']); ?>" required class="regular-text"/>
            </td>
        </tr>
        <tr>
            <th><label for="wookit-app-password"><?php echo esc_html__('Application Password',
                        'myshopkit-product-badges-wp'); ?></label>
            </th>
            <td>
                <input id="mysmbwp-app-password" type="password" name="mysmbwp-auth[app_password]"
                       value="<?php echo esc_attr($this->aOptions['app_password']); ?>" required class="regular-text"/>
            </td>
        </tr>
        </tbody>
    </table>
    <button id="button-save" class="button button-primary" type="submit"><?php esc_html_e('Save Changes',
            'myshopkit-product-badges-wp'); ?></button>
</form>
<?php if (!empty(get_option(MYSHOPKIT_MB_WP_PREFIX . 'purchase_code'))): ?>
    <button
            id="btn-Revoke-Purchase-Code-MBWP"
            class="button button-primary">
        <?php esc_html_e('Revoke Purchase Code', 'myshopkit-product-badges-wp'); ?>
    </button>
<?php endif; ?>
