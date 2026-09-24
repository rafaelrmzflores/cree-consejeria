<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class CREE_Client_Login_Shortcode {

    public function __construct() {
        add_shortcode( 'cree_client_login', array( $this, 'render_login_form' ) );
    }

    public function render_login_form() {
        // Redirect active clients to portal if already logged in
        if ( is_user_logged_in() ) {
            if ( ! current_user_can( 'edit_posts' ) ) {
                wp_redirect( home_url( '/client-portal/' ) );
                exit;
            }
            return '<div class="cree-login-card"><p>You are currently logged in as staff. Access the <a href="' . esc_url( home_url( '/therapist-portal/' ) ) . '">Therapist Portal</a>.</p></div>';
        }

        ob_start();
        
        $login_error = isset( $_GET['login'] ) && $_GET['login'] === 'failed';
        $logged_out  = isset( $_GET['loggedout'] ) && $_GET['loggedout'] === 'true';
        ?>
        <style>
            /* Container Card */
            .cree-login-card {
                max-width: 420px;
                margin: 40px auto;
                padding: 32px;
                border-radius: 12px;
                background: #ffffff;
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
                border: 1px solid #e5e7eb;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, sans-serif;
            }
            .cree-login-card h2 {
                margin: 0 0 6px 0;
                text-align: center;
                color: #111827;
                font-size: 24px;
                font-weight: 700;
            }
            .cree-login-card p.cree-subtitle {
                text-align: center;
                color: #6b7280;
                font-size: 14px;
                margin: 0 0 24px 0;
            }

            /* Feedback Alerts */
            .cree-alert {
                padding: 12px 16px;
                border-radius: 8px;
                margin-bottom: 20px;
                font-size: 13.5px;
                line-height: 1.4;
            }
            .cree-alert-error {
                background-color: #fef2f2;
                border: 1px solid #fecaca;
                color: #991b1b;
            }
            .cree-alert-info {
                background-color: #eff6ff;
                border: 1px solid #bfdbfe;
                color: #1e40af;
            }

            /* WP Native Form Styling Overrides */
            #cree-custom-login-form p {
                margin-bottom: 18px;
            }
            #cree-custom-login-form label {
                display: block;
                font-size: 13px;
                font-weight: 600;
                color: #374151;
                margin-bottom: 6px;
            }
            #cree-custom-login-form input[type="text"],
            #cree-custom-login-form input[type="password"] {
                width: 100%;
                padding: 10px 40px 10px 12px; /* Right padding preserves room for toggle button */
                border: 1px solid #d1d5db;
                border-radius: 6px;
                font-size: 14px;
                color: #111827;
                box-sizing: border-box;
                transition: border-color 0.15s ease, box-shadow 0.15s ease;
            }
            #cree-custom-login-form input[type="text"]:focus,
            #cree-custom-login-form input[type="password"]:focus {
                outline: none;
                border-color: #2563eb;
                box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
            }

            /* Password Field Relative Container */
            #cree-custom-login-form .login-password {
                position: relative;
            }

            /* Toggle Password Eye Icon Button */
            .cree-toggle-pass-btn {
                position: absolute;
                right: 10px;
                bottom: 8px;
                background: transparent;
                border: none;
                cursor: pointer;
                padding: 4px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #6b7280;
                border-radius: 4px;
                transition: color 0.15s ease;
            }
            .cree-toggle-pass-btn:hover {
                color: #111827;
            }
            .cree-toggle-pass-btn svg {
                width: 18px;
                height: 18px;
                fill: none;
                stroke: currentColor;
                stroke-width: 2;
                stroke-linecap: round;
                stroke-linejoin: round;
            }

            /* Remember Me Checkbox */
            #cree-custom-login-form .login-remember {
                display: flex;
                align-items: center;
                margin-bottom: 20px;
            }
            #cree-custom-login-form .login-remember label {
                margin-bottom: 0;
                font-size: 13px;
                color: #4b5563;
                cursor: pointer;
            }
            #cree-custom-login-form .login-remember input[type="checkbox"] {
                margin-right: 8px;
                accent-color: #2563eb;
            }

            /* Submit Button */
            #cree-custom-login-form input[type="submit"] {
                width: 100%;
                padding: 11px 16px;
                background-color: #2563eb;
                color: #ffffff;
                border: none;
                border-radius: 6px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                transition: background-color 0.15s ease;
            }
            #cree-custom-login-form input[type="submit"]:hover {
                background-color: #1d4ed8;
            }

            /* Footer Links */
            .cree-login-footer {
                margin-top: 20px;
                padding-top: 16px;
                border-top: 1px solid #f3f4f6;
                text-align: center;
                font-size: 13px;
            }
            .cree-login-footer a {
                color: #2563eb;
                text-decoration: none;
            }
            .cree-login-footer a:hover {
                text-decoration: underline;
            }
        </style>

        <div class="cree-login-card">
            <h2>Client Portal</h2>
            <p class="cree-subtitle">Sign in to view and manage your forms</p>

            <?php if ( $login_error ) : ?>
                <div class="cree-alert cree-alert-error">
                    <strong>Invalid Credentials:</strong> Please verify your email address and password.
                </div>
            <?php endif; ?>

            <?php if ( $logged_out ) : ?>
                <div class="cree-alert cree-alert-info">
                    You have been logged out successfully.
                </div>
            <?php endif; ?>

            <?php
            $args = array(
                'echo'           => true,
                'redirect'       => home_url( '/client-portal/' ),
                'form_id'        => 'cree-custom-login-form',
                'label_username' => __( 'Email Address' ),
                'label_password' => __( 'Password' ),
                'label_remember' => __( 'Remember Me' ),
                'label_log_in'   => __( 'Sign In' ),
                'id_username'    => 'cree_user_login',
                'id_password'    => 'cree_user_pass',
                'remember'       => true,
                'value_remember' => true
            );
            
            wp_login_form( $args );
            ?>

            <div class="cree-login-footer">
                <a href="<?php echo esc_url( wp_lostpassword_url( home_url( '/client-login/' ) ) ); ?>">
                    Forgot your password?
                </a>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passInput = document.getElementById('cree_user_pass');
            if (!passInput) return;

            const passParent = passInput.parentElement;
            
            // SVG Icons for Eye (Show) and Eye-Off (Hide)
            const eyeOpenSvg = '<svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
            const eyeClosedSvg = '<svg viewBox="0 0 24 24"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>';

            const toggleBtn = document.createElement('button');
            toggleBtn.type = 'button';
            toggleBtn.className = 'cree-toggle-pass-btn';
            toggleBtn.setAttribute('aria-label', 'Toggle password visibility');
            toggleBtn.innerHTML = eyeOpenSvg;

            passParent.appendChild(toggleBtn);

            toggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (passInput.type === 'password') {
                    passInput.type = 'text';
                    toggleBtn.innerHTML = eyeClosedSvg;
                } else {
                    passInput.type = 'password';
                    toggleBtn.innerHTML = eyeOpenSvg;
                }
            });
        });
        </script>
        <?php
        return ob_get_clean();
    }
}
new CREE_Client_Login_Shortcode();