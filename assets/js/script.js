(() => {
    "use strict";

    window.onload = (e) => {
        LinkHandler('admin/dashboard', 'Dashboard', {
            'admin': 'admin/dashboard',
            'dashboard': 'admin/dashboard'
        });
    };

    const navList = document.querySelectorAll('nav ul li');
    navList.forEach((navItem, index) => {
        if (navItem.classList.contains('listening')) return;
        navItem.addEventListener('click', (e) => {
            // Prevent the a tag from redirecting
            e.preventDefault();
            // If it's already active
            if (navItem.classList.contains('active')) return;
            // Remove active class from the previous active link
            navList.forEach(navItem => {
                navItem.classList.remove('active');
                navItem.querySelector('svg')?.classList.remove('nc-int-icon-state-b');
            });
            // make this link active
            navItem.querySelector('svg')?.classList.add('nc-int-icon-state-b');
            navItem.classList.add('active');
            document.querySelector('nav .indicator')?.style.setProperty('--pos', index);
            // Load the page
            let link = navItem.querySelector('a')?.dataset?.link;
            // Turns "admin/student-management.html" into "Student Management"
            let title = link?.split('/').pop().split('.')[0].split('-').map(str => str.charAt(0).toUpperCase() + str.substring(1)).join(' ');
            LinkHandler(link, title);
            
        });
    });
    
    function LinkHandler(link, title = null, breadcrumbs = null) {
        if (link == null) return;
        fetch(`/${link}`, {
            // body: formData
        }).then(res => {
            if (res.status >= 200 && res.status < 300) {
                return res.text();
            }
            throw new Error(res.statusText);
        }).then(data => {
            if (data?.type === "refresh") {
                window.location.reload();
                // Shouldn't need the return
                return;
            }
            document.body.querySelector('main').innerHTML = data;
            title != null && (document.getElementById('page-title').innerText = title);
            // breadcrumbs != null && Breadcrumbs(breadcrumbs);
            // history.pushState({}, "", '/' + link.split('.')[0]);
            LoadPageEvents();
        }).catch(error => {
            if (error === null || error === '') error = "An Unknown Error Occurred";
            console.error("error", error);
        });
    }
    /*
    function Breadcrumbs(crumbs) {
        if (crumbs == null) return;
        if (typeof crumbs !== 'object') return;
        const breadcrumbsElement = document.getElementById('breadcrumbs');
        breadcrumbsElement.innerHTML = '';
        Object.keys(crumbs).forEach((key, index, arr) => {
            // The "data-link" existing decides whether it is underlined or not by the CSS.
            if (crumbs[key] == '')
                breadcrumbsElement.innerHTML += `<a>${key}</a>`;
            else
                breadcrumbsElement.innerHTML += `<a data-link="${crumbs[key]}">${key}</a>`;
            if (index < arr.length - 1)
                breadcrumbsElement.innerHTML += '<i>/</i>';
        });
       
    }
    function GetCrumbsFromLink(link) {
        if (link == null || link == '') return;
        link = link.split('/');
        // If the last element has a '.' (file extension)
        if (link.slice(-1)[0].includes('.')) {
            // Remove it form the array (.pop())
            // Remove the '.' and everything after it
            // Then add it back to the array (.push())
            link.push(link.pop().split('.')[0]);
        }
        console.log(link)
    }
    */


    function LoadPageEvents() {
        document.querySelectorAll('.input-switch')?.forEach(inputSwitch => {
            if (inputSwitch.classList.contains('events-listening')) return;
            const slider = inputSwitch.querySelector('.input-switch-slider');
            inputSwitch.querySelectorAll('.input-switch-option label')?.forEach((switchOption, index) => {
                switchOption.addEventListener('change', () => {
                    switchOption.classList.remove('error');
                    if (!switchOption?.checked == false) return;
                    slider.style.left = `${100 / inputSwitch.style.getPropertyValue('--count') * index}%`;
                });
            });
            inputSwitch.classList.add('events-listening');
        });
        document.querySelectorAll('#student-management .table tr')?.forEach(tableRow => {
            tableRow.querySelector('.icons .table-edit-btn')?.addEventListener('click', (e) => {
                if (e.target.classList.contains('events-listening')) return;
                ModifyUser(tableRow?.dataset?.userid, "/php/getstudentdata.php");
                e.target.classList.add('events-listening');
            });
            tableRow.querySelector('.icons .table-delete-btn')?.addEventListener('click', (e) => {
                if (e.target.classList.contains('events-listening')) return;
                DeleteUser(tableRow?.dataset?.userid, "/php/deletestudent.php");
                e.target.classList.add('events-listening');
            });
        });
        document.querySelectorAll('#lecturer-management .table tr')?.forEach(tableRow => {
            tableRow.querySelector('.icons .table-edit-btn')?.addEventListener('click', (e) => {
                if (e.target.classList.contains('events-listening')) return;
                ModifyUser(tableRow?.dataset?.userid, "/php/getlecturerdata.php");
                e.target.classList.add('events-listening');
            });
            tableRow.querySelector('.icons .table-delete-btn')?.addEventListener('click', (e) => {
                if (e.target.classList.contains('events-listening')) return;
                DeleteUser(tableRow?.dataset?.userid, "/php/deletelecturer.php");
                e.target.classList.add('events-listening');
            });
        });
        // If there has been an error and a field has a red border
        //  then remove it if the input is modified.
        document.querySelectorAll('form .form-input')?.forEach(inputField => {
            inputField.querySelectorAll('*[name]')?.forEach(input => {
                if (input.classList.contains('events-listening')) return;
                input.addEventListener('change', () => {
                    inputField.classList.remove('error');
                });
                input.classList.add('events-listening');
            });
        });
    }

    function DeleteUser(userid, page) {
        if (userid == null || page == null) return;
        if (!confirm("Are you sure you wish to delete this user?")) return;
        const formData = new FormData();
        formData.append('userID', userid);
        fetch(page, {
            method: "POST",
            body: formData,
        }).then(res => {
            if (res.status >= 200 && res.status < 300) {
               return res.text();
            }
            throw new Error(res.statusText);
        }).then(data => {
            data = JSON.parse(data);
            if (data?.type === "refresh") window.location.reload();
            else if (data?.type === "error") {
                DisplayModel('popup', [
                    ['popup-title', "Error"],
                    ['popup-msg', data.msg]
                ], {
                    class: "error"
                });
            } else if (data?.type === "success") {
                DisplayModel('popup', [
                    ['popup-title', "Success"],
                    ['popup-msg', data.msg]
                ], {
                    class: "success",
                    closeAll: true
                });
            }
        }).catch(error => {
            if (error === null || error === '') error = "An Unknown Error Occurred";
            console.error(error);
            DisplayModel('popup', [
                ['popup-title', "Error"],
                ['popup-msg', error]
            ], {
                class: "error"
            });
        });
    }
    function ModifyUser(userid, page) {
        if (userid == null || page == null) return;
        document.querySelectorAll(`#dialog-edit-user *[name="${data.inpuut}"]`).forEach(input => {
            input.classList.remove('error');
        });
        document.querySelector('#dialog-edit-user .error-msg').innerHTML = '';
        const formData = new FormData();
        formData.append('userID', userid);
        fetch(page, {
            method: "POST",
            body: formData,
        }).then(res => {
            if (res.status >= 200 && res.status < 300) {
               return res.text();
            }
            throw new Error(res.statusText);
        }).then(data => {
            data = JSON.parse(data);
            if (data?.type === "refresh") window.location.reload();
            else if (data?.type === "error") {
                if (data?.input != null) {
                    document.querySelector(`#dialog-edit-user *[name="${data.inpuut}"]`).classList.add('error');
                    document.querySelector('#dialog-edit-user .error-msg').innerHTML = data.msg;
                } else {
                    DisplayModel('popup', [
                        ['popup-title', "Error"],
                        ['popup-msg', data.msg]
                    ], {
                        class: "error"
                    });
                }
            } else if (data?.type === "success") {
                DisplayModel('popup', [
                    ['popup-title', "Success"],
                    ['popup-msg', data.msg]
                ], {
                    class: "success",
                    closeAll: true
                });
            } else if (data?.type === "data") {
                // Object.keys(data.data).forEach(key => {
                //     const input = document.querySelector(`#dialog-edit-user *[name="${key}"]`);
                //     if (input?.type === "radio" || input?.type === "checkbox") {
                //         document.querySelector(`#dialog-edit-user *[name="${key}"][value="${data[key]}"]`)?.checked = true;
                //     } else {
                //         input?.value = data[key];
                //     }
                // });
                // Gonna manually input all this bit for now
                let statusID = "form-status-";
                if (data?.data?.status = 'inactive') statusID + '1';
                else if (data?.data?.status = 'pending') statusID + '2';
                else if (data?.data?.status = 'active') statusID + '3';
                DisplayModel('dialog-edit-user', [
                    ['firstname', data?.data?.firstname],
                    ['lastname', data?.data?.lastname],
                    ['studentID', data?.data?.studentID],
                    ['email', data?.data?.email],
                    [statusID, true],
                ], {
                    closeAll: true
                });
            }
        }).catch(error => {
            if (error === null || error === '') error = "An Unknown Error Occurred";
            console.error(error);
            DisplayModel('popup', [
                ['popup-title', "Error"],
                ['popup-msg', error]
            ], {
                class: "error"
            });
        });
    }

    function DisplayModel(id, data = {}, options) {
        if (id == null) return;
        const modal = document.getElementById(id);
        if (modal.tagName !== "DIALOG") return;
        modal.className = '';
        if (options?.closeAll === true) document.querySelectorAll('dialog').forEach(dialog => {
            dialog.close('close');
        })
        // Clear form. (should already be cleared anyway)
        modal.querySelector('form')?.reset();
        // The default [] is still an array
        if (!Array.isArray(data)) return;
        // Loop through data that is already in each input
        data.forEach(row => {
            const input = document.getElementById(row[0]);
            if (input == null) return;
            // Radio/Checkbox specific
            if (input.type === "radio" || input.type === "checkbox") {
                if (row[1] === true) input.checked = true;
                else input.checked = false;
            } else {
                // If it doesn't have a value, just put an empty string
                input.value = row[1] ?? '';
            }
        });

        if (options?.modal === false) modal.show();
        else modal.showModal();

        if (options?.class != null) modal.classList.add(options.class)
    }

})();