<div id="signInUpModal" class="modal">
    <div class="modal-content" style="background-color: #ffffff !important;">
        <span class="close-button">&times;</span>

        <div id="signInPanel" class="modal-panel active">
            <div class="modal-form-container">
                <h2 class="modal-title">Sign In</h2>
                <form id="signInForm" class="modal-form">
                    <div class="form-group" style="text-align: left;">
                        <label for="loginUsernameEmail">Username or Email</label>
                        <input type="text" id="loginUsernameEmail" name="username_email" placeholder="Enter your username or email" required>
                    </div>
                    <div class="form-group" style="text-align: left;">
                        <label for="loginPassword">Password</label>
                        <input type="password" id="loginPassword" name="password" placeholder="Enter your password" required>
                    </div>
                    <button type="submit" class="btn btn-primary modal-btn">Sign In</button>
                </form>
                <div class="social-login">
                     <a href="google-oauth.php" class="btn btn-google modal-btn"><img src="https://img.icons8.com/color/16/000000/google-logo.png"> Sign In with Google</a>
                </div>
                <p class="modal-bottom-text">Don't have an account? <a href="#" class="switch-to-register">Register here</a></p>
            </div>
        </div>

        <div id="registerPanel" class="modal-panel">
            <div class="modal-form-container">
                <h2 class="modal-title">Register</h2>
                <form id="registerForm" class="modal-form">
                    <div class="form-group" style="text-align: left;">
                        <label for="registerName">Username</label>
                        <input type="text" id="registerName" name="username" placeholder="Enter your username" required>
                    </div>
                    <div class="form-group" style="text-align: left;">
                        <label for="registerEmail">Email Address</label>
                        <input type="email" id="registerEmail" name="email" placeholder="Enter your email address" required>
                        <div id="gmail-error-message" class="email-error-message">Only @gmail.com addresses are allowed.</div>
                    </div>
                    <div class="form-group" style="text-align: left;">
                        <label for="registerPassword">Password</label>
                        <input type="password" id="registerPassword" name="password" placeholder="Create a password" required>
                        <div id="password-validation-rules">
                            <p class="validation-rule-container">
                                <span id="length" class="validation-rule invalid">6+ characters</span>,
                                <span id="capital" class="validation-rule invalid">1 uppercase</span>,
                                <span id="special" class="validation-rule invalid">1 special</span>
                            </p>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary modal-btn">Register</button>
                </form>
                <div class="social-login">
                    <a href="google-oauth.php" class="btn btn-google modal-btn"><img src="https://img.icons8.com/color/16/000000/google-logo.png"> Sign Up with Google</a>
                </div>
                <p class="modal-bottom-text">Already have an account? <a href="#" class="switch-to-signin">Sign In here</a></p>
            </div>
        </div>
    </div>
</div>

<style>
.social-login {
    margin-top: 15px;
    text-align: center;
}
.btn-google {
    background-color: #4285F4;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}
.btn-google:hover {
    background-color: #357ae8;
}
.email-error-message {
    display: none;
    color: #e74c3c;
    font-size: 0.85em;
    margin-top: 5px;
    font-weight: 500;
}
/* New styles for single-line password validation */
#password-validation-rules {
    margin-top: 8px;
    font-size: 0.85em;
}
.validation-rule-container {
    color: #777; /* Default text color for commas */
}
.validation-rule {
    transition: color 0.3s;
}
.validation-rule.invalid {
    color: #e74c3c; /* Red for invalid */
}
.validation-rule.valid {
    color: #2ecc71; /* Green for valid */
}
</style>