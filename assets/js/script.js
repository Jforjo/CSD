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

    function PopulateTable(tableID, page) {
        if (page == null) return;
        const section = document.getElementById(tableID);
        if (section == null) return;
        const loader = section.querySelector('.lds-ring');
        loader.classList.remove('hidden');
        const output = section.querySelector('.table table tbody');
        const limit = document.getElementById('user-management-perpage').value;
        // -1 because page 1 starts at offset of 0
        const offset = ( document.querySelector('#pagination-menu li.active').dataset.id - 1 ) * limit;
        const formData = new FormData();
        formData.append('limit', limit);
        formData.append('offset', offset);
        fetch(page, {
            method: "POST",
            body: formData,
        }).then(res => {
            if (res.status >= 200 && res.status < 300) {
               return res.text();
            }
            throw new Error(res.statusText);
        }).then(data => {
            try {
                data = JSON.parse(data);
                if (data?.type === "refresh") window.location.reload();
                else if (data?.type === "error") {
                    DisplayModel('popup', [
                        ['popup-title', "Error"],
                        ['popup-msg', data.msg]
                    ], {
                        class: "error"
                    });
                }
            } catch {
                output.innerHTML = data;
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
        }).finally(() => {
            loader.classList.add('hidden');
            LoadPageEvents();
            document.getElementById('pagination-showing').innerHTML = offset + 1;
            document.getElementById('pagination-perpage').innerHTML = Math.min(offset + limit, document.getElementById('pagination-total').innerHTML);
        });
    }
    function SetPagination(value) {
        const menu = document.getElementById('pagination-menu');
        if (menu == null) return;
        const btns = menu.querySelectorAll('li');
        if (value < 1 || value > btns.length) return;
        btns.forEach(btn => {
            btn.classList.remove('active', 'inactive');
            btn.classList.add('hidden');
        });
        if (btns.length <= 5) {
            btns.forEach(btn => {
                btn.classList.replace('hidden', 'inactive');
                if (btn.dataset.id == value) btn.classList.replace('inactive', 'active');
            });
        } else if (value < 3) {
            btns.forEach(btn => {
                if (btn.dataset.id <= 5) btn.classList.replace('hidden', 'inactive');
                if (btn.dataset.id == value) btn.classList.replace('inactive', 'active');
            });
        } else if (value > btns.length - 2) {
            btns.forEach(btn => {
                if (btn.dataset.id > btns.length - 5) btn.classList.replace('hidden', 'inactive');
                if (btn.dataset.id == value) btn.classList.replace('inactive', 'active');
            });
        } else {
            btns.forEach(btn => {
                if (btn.dataset.id > value - 2 && btn.dataset.id <= value + 2) btn.classList.replace('hidden', 'inactive');
                if (btn.dataset.id == value) btn.classList.replace('inactive', 'active');
            });
        }
    }
    function SetPerPage() {
        const total = document.getElementById('pagination-total').innerHTML;
        const perpage = document.getElementById('user-management-perpage').value;
        const paginationMenu = document.getElementById('pagination-menu');
        paginationMenu.classList.remove('events-listening');
        paginationMenu.innerHTML = '';
        for (let i = 1; i <= Math.ceil(total / perpage); i++) {
            paginationMenu.innerHTML += `
                <li data-id="${i}">
                    <span>${i}</span>
                </li>
            `;
        }
        SetPagination(1);
        LoadPageEvents();
    }

    function SetInputSwitch(radioID) {
        document.querySelectorAll('.input-switch')?.forEach(inputSwitch => {
            const slider = inputSwitch.querySelector('.input-switch-slider');
            inputSwitch.querySelectorAll('.input-switch-option input')?.forEach((switchOption, index) => {
                if (switchOption.id != radioID) return;
                inputSwitch.classList.remove('error');
                switchOption.checked = true;
                slider.style.left = `${100 / inputSwitch.style.getPropertyValue('--count') * index}%`;
            });
        });
    }
    function LoadPageEvents() {
        document.querySelectorAll('.input-switch')?.forEach(inputSwitch => {
            if (inputSwitch.classList.contains('events-listening')) return;
            const slider = inputSwitch.querySelector('.input-switch-slider');
            inputSwitch.querySelectorAll('.input-switch-option input')?.forEach((switchOption, index) => {
                switchOption.addEventListener('change', () => {
                    if (switchOption?.checked == false) return;
                    SetInputSwitch(switchOption.id)
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


        // Automatiicaly populate table on load
        const studentManagement = document.getElementById('student-management');
        const lecturerManagement = document.getElementById('lecturer-management');

        if (studentManagement != null && studentManagement.classList.contains('loaded') == false) {
            PopulateTable('student-management', '/php/loadstudenttable.php');
            studentManagement.classList.add('loaded');
        }
        if (lecturerManagement != null && lecturerManagement.classList.contains('loaded') == false) {
            PopulateTable('lecturer-management', '/php/loadlecturertable.php');
            lecturerManagement.classList.add('loaded');
        }

        if (document.getElementById('user-management-perpage')?.classList.contains('events-listening') === false) {
            document.querySelector('#student-management #user-management-perpage')?.addEventListener('change', () => {
                SetPerPage();
                PopulateTable('student-management', '/php/loadstudenttable.php');
            });
            document.querySelector('#lecturer-management #user-management-perpage')?.addEventListener('change', () => {
                SetPerPage();
                PopulateTable('lecturer-management', '/php/loadlecturertable.php');
            });
            document.getElementById('user-management-perpage').classList.add('events-listening');
        }
        if (document.getElementById('pagination-menu')?.classList.contains('events-listening') === false) {
            document.querySelectorAll('#student-management #pagination-menu li')?.forEach(btn => {
                btn.addEventListener('click', () => {
                    SetPagination(btn.dataset.id);
                    PopulateTable('student-management', '/php/loadstudenttable.php');
                });
            });
            document.querySelectorAll('#lecturer-management #pagination-menu li')?.forEach(btn => {
                btn.addEventListener('click', () => {
                    SetPagination(btn.dataset.id);
                    PopulateTable('lecturer-management', '/php/loadlecturertable.php');
                });
            });
            document.getElementById('pagination-menu').classList.add('events-listening');
        }

        if (document.querySelector('.pagination')?.classList.contains('events-listening') === false) {
            document.querySelectorAll('#student-management .pagination .arrow')[0]?.addEventListener('click', () => {
                SetPagination(+document.querySelector('#pagination-menu li.active')?.dataset.id - 1);
                PopulateTable('student-management', '/php/loadstudenttable.php');
            });
            document.querySelectorAll('#student-management .pagination .arrow')[1]?.addEventListener('click', () => {
                SetPagination(+document.querySelector('#pagination-menu li.active')?.dataset.id + 1);
                PopulateTable('student-management', '/php/loadstudenttable.php');
            });
            document.querySelectorAll('#lecturer-management .pagination .arrow')[0]?.addEventListener('click', () => {
                SetPagination(+document.querySelector('#pagination-menu li.active')?.dataset.id - 1);
                PopulateTable('lecturer-management', '/php/loadlecturertable.php');
            });
            document.querySelectorAll('#lecturer-management .pagination .arrow')[1]?.addEventListener('click', () => {
                SetPagination(+document.querySelector('#pagination-menu li.active')?.dataset.id + 1);
                PopulateTable('lecturer-management', '/php/loadlecturertable.php');
            });
            document.querySelector('.pagination').classList.add('events-listening');
        }
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
        document.querySelectorAll(`#dialog-edit-user *[name]`).forEach(input => {
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
                let stateID = "form-state-";
                if (data?.data?.state == 'inactive') stateID += '1';
                else if (data?.data?.state == 'pending') stateID += '2';
                else if (data?.data?.state == 'active') stateID += '3';
                DisplayModel('dialog-edit-user', [
                    ['form-firstname', data?.data?.firstname],
                    ['form-lastname', data?.data?.lastname],
                    ['form-studentID', data?.data?.studentID],
                    ['form-email', data?.data?.email],
                    [stateID, true],
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

    function DisplayModel(id, data = [], options) {
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
                if (row[1] === true) SetInputSwitch(row[0]);
            } else if (input.tagName === "INPUT") {
                // If it doesn't have a value, just put an empty string
                input.value = row[1] ?? '';
            } else {
                input.innerHTML = row[1] ?? '';
            }
        });

        if (options?.modal === false) modal.show();
        else modal.showModal();

        if (options?.class != null) modal.classList.add(options.class)
    }

})();