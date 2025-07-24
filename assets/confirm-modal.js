document.addEventListener('DOMContentLoaded', function () {
    let xhr = new XMLHttpRequest();
    xhr.open('GET', '/profile/settings/popup', true);

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                let data = xhr.responseText;
                console.log(data);
                if (typeof data !== 'undefined') {
                    let popupBody = document.querySelector('.popup-body');
                    if (popupBody) {
                        popupBody.innerHTML = data;

                        let btnPopup = document.getElementById('btn-popup');
                        if (btnPopup) {
                            btnPopup.addEventListener('click', function () {
                                let closePopBtn = document.getElementById('close-btn');
                                let popupWrapper = document.getElementById('popup-wrapper');
                                if (popupWrapper) {
                                    document.getElementById("close-btn").addEventListener("click", () => {
                                        closeDialog(popupWrapper);
                                    });
                                    popupWrapper.style.display = 'block';
                                    popupWrapper.style.opacity = 0;

                                    let fadeIn = setInterval(function () {
                                        let currentOpacity = parseFloat(popupWrapper.style.opacity);
                                        if (currentOpacity < 1) {
                                            popupWrapper.style.opacity = currentOpacity + 0.1;
                                        } else {
                                            clearInterval(fadeIn);
                                        }
                                    }, 30);

                                    document.body.style.overflowY = 'hidden';
                                    popupWrapper.style.overflowY = 'auto';

                                    let deleteBtn = document.getElementById('confirm-btn');
                                    if (deleteBtn) {
                                        deleteProfile(deleteBtn);
                                    }
                                }
                            });
                        }
                    }
                }
            } else {
                alert(xhr.statusText);
            }
        }
    };

    xhr.send();
});

function deleteProfile(deleteBtn) {
    deleteBtn.addEventListener('click', function (e) {
        e.preventDefault();
        
        let xhr = new XMLHttpRequest();
        xhr.open('POST', '/profile/settings/popup/delete', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send();
    });
}

function closeDialog(wrapper) {
    let fadeOut = setInterval(function () {
        let currentOpacity = parseFloat(wrapper.style.opacity);
        if (currentOpacity > 0) {
            wrapper.style.opacity = currentOpacity - 0.1;
        } else {
            clearInterval(fadeOut);
        }
    }, 30);
    wrapper.style.display = 'hidden';
}