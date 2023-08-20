const sendForm = (form) =>
    fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
    });

const onSubmit = (event) => {
    event.preventDefault();
    const form = event.currentTarget;
    const formData = new FormData(form);
    const error_block = document.querySelector("#error");
    const result_block = document.querySelector("#result");
    if (error_block) {
        error_block.classList.remove('error');
        error_block.classList.add('hidden');
        error_block.innerHTML = '';
    }
    if (result_block) {
        result_block.classList.add('hidden');
    }
    for (const [key, value] of formData) {
        const field_error = document.querySelector("#" + key);
        const field = document.querySelector("input[name='"+ key +"']");
        field.classList.remove('error_field');
        if (field_error) {
            field_error.remove();
        }
    }
    sendForm(form)
        .then(response => response.json())
        .then(data => {
            if (typeof data.error != 'undefined') {
                for (const key of Object.keys(data.error)) {
                    if (key == 0) {
                        error_block.classList.remove('hidden');
                        error_block.classList.add('error');
                        error_block.innerHTML = data.error;
                    } else {
                        const field = document.querySelector("input[name='" + key + "']");
                        field.classList.add('error_field')
                        const error_message = document.createElement('div');
                        error_message.textContent = data.error[key];
                        error_message.classList.add('error_message');
                        error_message.id = key;
                        field.parentNode.insertBefore(error_message, field.nextSibling);
                    }
                }
            }
            if (typeof data.activation != 'undefined') {
                form.classList.add('hidden');
                const info = document.querySelector("#activation");
                info.classList.remove('hidden');
            }
            if (typeof data.redirect != 'undefined') {
                location.href = data.redirect;
            }
            if (typeof data.result != 'undefined') {
                const old_url = document.querySelector("#old_url");
                const result_message = document.querySelector("#result_message");
                result_block.classList.remove('hidden');
                old_url.innerHTML = data.result.old;
                result_message.innerHTML = data.result.new;
                document.querySelector("input[name='url']").value = '';
            }
        });
};

const init = () => {
    const form = document.querySelector('form');
    form.addEventListener('submit', onSubmit);
};

document.addEventListener("DOMContentLoaded", init);