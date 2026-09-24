<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class CREE_Client_Login_Shortcode {

    public function __construct() {
        add_shortcode( 'cree_client_login', array( $this, 'render_login_form' ) );
    }

    public function render_login_form() {
        // If user is already logged in, redirect them directly to the client portal
        if ( is_user_logged_in() ) {
            if ( ! current_user_can( 'edit_posts' ) ) {
                wp_redirect( site_url( '/client-portal/' ) );
                exit;
            }
            return '<div class="cree-form-container"><p>You are currently logged in as staff. Access the <a href="' . esc_url( site_url( '/therapist-portal/' ) ) . '">Therapist Portal</a>.</p></div>';
        }

        ob_start();
        
        // Handle error and logout query flags
        $login_error  = isset( $_GET['login'] ) && $_GET['login'] === 'failed';
        $logged_out   = isset( $_GET['loggedout'] ) && $_GET['loggedout'] === 'true';
        ?>
        <div class="cree-form-container" style="max-width: 420px; margin: 40px auto; padding: 25px; border: 1px solid #dcdcde; border-radius: 6px; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <h2 style="margin-top:0; margin-bottom: 15px; text-align: center; color: #1d2327;">Client Portal Login</h2>

            <?php if ( $login_error ) : ?>
                <div style="background-color: #fcf0f2; border-left: 4px solid #d63638; padding: 10px; margin-bottom: 15px; color: #d63638; font-size: 13px;">
                    <strong>Error:</strong> Invalid email address or password.
                </div>
            <?php endif; ?>

            <?php if ( $logged_out ) : ?>
                <div style="background-color: #f0f6fc; border-left: 4px solid #0073aa; padding: 10px; margin-bottom: 15px; color: #0073aa; font-size: 13px;">
                    You have been logged out successfully.
                </div>
            <?php endif; ?>

            <?php
            // WP native login form with custom redirects and CSS styling
            $args = array(
                'echo'           => true,
                'redirect'       => site_url( '/client-portal/' ),
                'form_id'        => 'cree-custom-login-form',
                'label_username' => __( 'Email Address' ),
                'label_password' => __( 'Password' ),
                'label_remember' => __( 'Remember Me' ),
                'label_log_in'   => __( 'Log In' ),
                'id_username'    => 'cree_user_login',
                'id_password'    => 'cree_user_pass',
                'remember'       => true,
                'value_remember' => true
            );
            
            wp_login_form( $args );
            ?>

            <div style="margin-top: 15px; text-align: center; font-size: 13px; border-top: 1px solid #f0f0f1; padding-top: 15px;">
                <a href="<?php echo esc_url( wp_lostpassword_url( site_url( '/client-login/' ) ) ); ?>" style="color: #0073aa; text-decoration: none;">
                    Forgot your password?
                </a>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}