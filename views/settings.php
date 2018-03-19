<div class="wrap">
    <h1>Lki Feed</h1>
    <em>Provide your client id, client secret and redirect</em>
    <?php if ($_POST): ?>
        <div class="notice notice-success">
            <p>Settings Updated!</p>
        </div>
    <?php endif; ?>
    <form method="post">
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Client Key</th>
                <td><input type="text" name="lki_client_id" value="<?php echo esc_attr(get_option('lki_client_id')); ?>"></td>
            </tr>
            <tr valign="top">
                <th scope="row">Client Secret</th>
                <td><input type="text" name="lki_client_secret" value="<?php echo esc_attr(get_option('lki_client_secret')); ?>"></td>
            </tr>
            <tr valign="top">
                <th scope="row">Client Redirect</th>
                <td><input type="text" name="lki_client_redirect" value="<?php echo esc_attr(get_option('lki_client_redirect')); ?>"></td>
            </tr>
            <tr valign="top">
                <td><?php $lkifeed->getfeedApi()->echoAuthLink(); ?></td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
</div>