function togglePassword() {
    var passwordField = document.getElementById("password");
    var toggleText = document.querySelector(".toggle-password");
    if (passwordField.type === "password") {
        passwordField.type = "text";
        toggleText.textContent = "Hide";
    } else {
        passwordField.type = "password";
        toggleText.textContent = "Show";
    }
}

//window.onload = function() {
//    document.getElementById('email').value = '';
//    document.getElementById('password').value = '';
//};

function doValidate() {
    console.log('Validating...');
    try {
        const addr = document.getElementById('email').value;
        const pw = document.getElementById('password').value;
        console.log("Validating addr="+addr+" pw="+pw);
        
        if (!addr || !pw) {
            alert("All fields must be filled out");
            return false;
        }
        if (addr.indexOf('@') === -1) {
            alert("Invalid email address");
            return false;
        }
        return true;
    } catch(e) {
        console.error('Validation error:', e);
        return false;
    }
}