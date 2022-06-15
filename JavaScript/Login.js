function post(path, params, method='post') {
    const form = document.createElement('form');
    form.method = method;
    form.action = path;
    for (const key in params) {
        if (params.hasOwnProperty(key)) {
            const hiddenField = document.createElement('input');
            hiddenField.type = 'hidden';
            hiddenField.name = key;
            hiddenField.value = params[key];
            form.appendChild(hiddenField);
        }
    }
    document.body.appendChild(form);
    form.submit();
}
function AuthorizeStart(response) {
    const responsePayload = jwt_decode(response.credential);
    var id_token = response.credential;
    post("/AuthSample/Process/GAuth.php",
        {id_token: id_token}
    );
}