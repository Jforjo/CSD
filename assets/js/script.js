(() => {
    "use strict";

    window.onload = (e) => {
        // LinkHandler('admin/dashboard', 'Dashboard', {
        //     'admin': 'admin/dashboard',
        //     'dashboard': 'admin/dashboard'
        // });
        LoadPageEvents();
    };

    const navList = document.querySelectorAll('body > nav ul li');
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
        const formData = new FormData();
        formData.append('jsfetch', true);
        fetch(`/${link}`, {
            method: "POST",
            body: formData
        }).then(res => {
            if (res.status >= 200 && res.status < 300) {
                return res.text();
            }
            throw new Error(res.statusText);
        }).then(data => {
            try {
                data = JSON.parse(data);
                if (data?.type === "refresh") {
                    window.location.reload();
                    // Shouldn't need the return
                    return;
                }
            } catch {};
            document.querySelector('main').innerHTML = data;
            title != null && (document.getElementById('page-title').innerText = title);
            // breadcrumbs != null && Breadcrumbs(breadcrumbs);
            history.pushState({}, "", '/' + link);
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

    function PopulateTable() {
        const section = document.querySelector("section.management");
        if (section == null) return;
        if (section.dataset?.type == null) return;
        const loader = section?.querySelector('.lds-ring');
        loader.classList.remove('hidden');
        const output = section?.querySelector('.table table tbody');
        const limit = document.getElementById('perpage').value;
        // -1 because page 1 starts at offset of 0s
        const offset = ( document.querySelector('#pagination-menu li.active').dataset.id - 1 ) * limit;
        const formData = new FormData();
        formData.append('limit', limit);
        formData.append('offset', offset);
        fetch(`/php/${section.dataset.type}/loadtable.php${location.search}`, {
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
                    DisplayModel('#popup', [
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
            DisplayModel('#popup', [
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
                if (btn.dataset.id >= value - 2 && btn.dataset.id <= value + 2) btn.classList.replace('hidden', 'inactive');
                if (btn.dataset.id == value) btn.classList.replace('inactive', 'active');
            });
        }
    }
    function SetPerPage() {
        const total = document.getElementById('pagination-total').innerHTML;
        const perpage = document.getElementById('perpage').value;
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
        const logoutbtn = document.getElementById('logout');
        if (logoutbtn != null && logoutbtn.classList.contains('events-listening') === false) {
            logoutbtn.addEventListener('click', () => {
                if (confirm("Are you sure you wish to log out?")) window.location = '/logout';
            });
            logoutbtn.classList.add('events-listening');
        }

        const recentStudent = document.getElementById('recent-student');
        if (recentStudent != null && recentStudent.classList.contains('events-listening') === false) {
            recentStudent.addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                // Absolutely no idea why it doesn't work without this line
                // It just doesn't seem to pass the button value without it
                formData.append(e.submitter.name, e.submitter.value);

                const values = [];
                for (const pair of formData.entries()) {
                    values.push([pair[0], pair[1]]);
                }
                SimpleForm(values, '/php/student/accept.php');
            });
            recentStudent.classList.contains('events-listening');
        }
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

        const section = document.querySelector('section.management');
        // Table buttons in the "Manage" column
        if (section != null && section.dataset?.type != null) {
            section?.querySelectorAll('.table tr')?.forEach(tableRow => {
                if (tableRow.classList.contains('events-listening') != false) return;
                tableRow.querySelector('.icons .table-edit-btn')?.addEventListener('click', () => {
                    Edit([
                        [`${section.dataset.type}ID`, tableRow?.dataset?.id]
                    ], `/php/${section.dataset.type}/getdata.php`);
                });
                tableRow.querySelector('.icons .table-delete-btn')?.addEventListener('click', () => {
                    if (!confirm(`Are you sure you wish to delete this ${section.dataset.type}?`)) return;
                    SimpleForm([[`${section.dataset.type}ID`, tableRow?.dataset?.id]], `/php/${section.dataset.type}/delete.php`);
                });
                tableRow.querySelector('.icons .table-promote-btn')?.addEventListener('click', () => {
                    if (!confirm(`Are you sure you wish to promote this ${section.dataset.type}?`)) return;
                    SimpleForm([[`${section.dataset.type}ID`, tableRow?.dataset?.id]], `/php/${section.dataset.type}/promote.php`);
                });
                tableRow.querySelector('.icons .table-demote-btn')?.addEventListener('click', () => {
                    if (!confirm(`Are you sure you wish to demote this ${section.dataset.type}?`)) return;
                    SimpleForm([[`${section.dataset.type}ID`, tableRow?.dataset?.id]], `/php/${section.dataset.type}/demote.php`);
                });
                tableRow.querySelector('.icons .table-assignquiz-btn')?.addEventListener('click', () => {
                    DisplayModel('#dialog-assign-quiz', [
                        ['form-assignQuizID', tableRow?.dataset?.id]
                    ], {
                        closeAll: true
                    });
                });
                tableRow.querySelector('.icons .table-linkquiz-btn')?.addEventListener('click', () => {
                    DisplayModel('#dialog-link-question', [
                        ['form-linkQuestion', tableRow.querySelector('td').innerText],
                        ['form-linkQuestionID', tableRow?.dataset?.id]
                    ], {
                        closeAll: true
                    });
                });
                tableRow.querySelector('.icons .table-deletelinkquiz-btn')?.addEventListener('click', () => {
                    if (!confirm("Are you sure you wish to delete this link?")) return;
                    let urlparams = new URLSearchParams(location.search);
                    SimpleForm([
                        ["quizID", urlparams.get('quiz')],
                        ["questionID", tableRow?.dataset?.id]
                    ], "/php/deletequizquestionlink.php");
                });
                tableRow.classList.add('events-listening');
            });
        }
        const dialog = document.querySelector('section.management + dialog[data-type="edit"] form');
        if (dialog?.classList.contains('events-listening') === false) {
            const creation = ["quiz", "question", "subject"];
            dialog.addEventListener('submit', (e) => {
                e.preventDefault();
                if (creation.includes(section.dataset.type)) {
                    if (dialog.querySelector(`input#form-${section.dataset.type}ID`).value != '')
                        Edit(null, `/php/${section.dataset.type}/edit.php`);
                    else
                        Edit(null, `/php/${section.dataset.type}/create.php`);
                } else {
                    Edit(null, `/php/${section.dataset.type}/edit.php`);
                }
            });
            dialog.classList.add('events-listening');
        }

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

        const createBtn = section?.querySelector('.table-btns .create');
        if (createBtn?.classList.contains('events-listening') === false) {
            createBtn.addEventListener('click', () => {
                DisplayModel('dialog[data-type="edit"]', [], {
                    closeAll: true
                });
                document.querySelector('dialog[data-type="edit"] button[type="submit"]').innerHTML = "Create";
            });
            createBtn.classList.add('events-listening');
        }


        // Automatiicaly populate table on load
        if (section != null && section.classList.contains('loaded') == false) {
            PopulateTable();
            section.classList.add('loaded');
        }
        if (document.getElementById('management-perpage')?.classList.contains('events-listening') === false) {
            section?.querySelector('#perpage')?.addEventListener('change', () => {
                SetPerPage();
                PopulateTable();
            });
            document.getElementById('perpage').classList.add('events-listening');
        }
        if (document.getElementById('pagination-menu')?.classList.contains('events-listening') === false) {
            section?.querySelectorAll('#pagination-menu li')?.forEach(btn => {
                btn.addEventListener('click', () => {
                    SetPagination(btn.dataset.id);
                    PopulateTable();
                });
            });
            document.getElementById('pagination-menu').classList.add('events-listening');
        }
        if (section?.querySelector('.pagination')?.classList.contains('events-listening') === false) {
            section?.querySelectorAll('.pagination .arrow')[0]?.addEventListener('click', () => {
                SetPagination(+section?.querySelector('#pagination-menu li.active')?.dataset.id - 1);
                PopulateTable();
            });
            section?.querySelectorAll('.pagination .arrow')[1]?.addEventListener('click', () => {
                SetPagination(+section?.querySelector('#pagination-menu li.active')?.dataset.id + 1);
                PopulateTable();
            });
            section?.querySelector('.pagination').classList.add('events-listening');
        }


        const linkQuestion = document.getElementById('dialog-link-question');
        if (linkQuestion?.classList.contains('events-listening') === false) {
            linkQuestion?.querySelector('form').addEventListener('submit', (e) => {
                e.preventDefault();
                SimpleForm([
                    ['questionID', document.getElementById('form-linkQuestionID').value],
                    ['quizID', document.getElementById('form-linkQuiz').value]
                ], "/php/createquizquestionlink.php");
            });
            linkQuestion.classList.add('events-listening');
        }
        const assignQuiz = document.getElementById('dialog-assign-quiz');
        if (assignQuiz?.classList.contains('events-listening') === false) {
            assignQuiz.querySelector('form').addEventListener('submit', (e) => {
                e.preventDefault();
                const values = [];
                // document.getElementById('form-assignStudent').selectedOptions?.forEach(option => {
                //     values.push(option.value);
                // });
                const studentOptions = document.getElementById('form-assignStudent').selectedOptions;
                for (let i = 0; i < studentOptions.length; i++) {
                    values.push(studentOptions[i].value);
                    
                }
                SimpleForm([
                    ['quizID', document.getElementById('form-assignQuizID').value],
                    ['questionCount', document.getElementById('form-questionCount').value],
                    ['students', values],
                ], "/php/quiz/assign.php");
            });
            assignQuiz.classList.add('events-listening');
        }
    }

    function SimpleForm(opt, page) {
        const formData = new FormData();
        opt.forEach(option => {
            formData.append(option[0], option[1]);
        });
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
            } catch {
                throw new Error(data);
            }
            if (data?.type === "refresh") window.location.reload();
            else if (data?.type === "error") {
                DisplayModel('#popup', [
                    ['popup-title', "Error"],
                    ['popup-msg', data.msg]
                ], {
                    class: "error"
                });
            } else if (data?.type === "success") {
                DisplayModel('#popup', [
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
            DisplayModel('#popup', [
                ['popup-title', "Error"],
                ['popup-msg', error]
            ], {
                class: "error"
            });
        });
    }
    function Edit(opt, page) {
        if (page == null) return;
        document.querySelectorAll(`dialog[data-type="edit"] *[name]`).forEach(input => {
            input.classList.remove('error');
        });
        document.querySelector('dialog[data-type="edit"] .error-msg').innerHTML = '';
        const formData = new FormData(document.querySelector('dialog[data-type="edit"] form'));
        Array.isArray(opt) && opt?.forEach(option => {
            formData.append(option[0], option[1]);
        });
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
            } catch {
                throw new Error(data);
            }
            if (data?.type === "refresh") window.location.reload();
            else if (data?.type === "error") {
                if (data?.input != null) {
                    document.querySelector(`dialog[data-type="edit"] *[name="${data.input}"]`).classList.add('error');
                    document.querySelector('dialog[data-type="edit"] .error-msg').innerHTML = data.msg;
                } else {
                    DisplayModel('#popup', [
                        ['popup-title', "Error"],
                        ['popup-msg', data.msg]
                    ], {
                        class: "error"
                    });
                }
            } else if (data?.type === "success") {
                DisplayModel('#popup', [
                    ['popup-title', "Success"],
                    ['popup-msg', data.msg]
                ], {
                    class: "success",
                    closeAll: true
                });
            } else if (data?.type === "data") {
                const values = [];
                Object.keys(data.data).forEach(key => {
                    values.push([`form-${key}`, data.data[key]])
                });
                if (data?.data?.state == 'inactive') values.push(['form-state-1', true]);
                else if (data?.data?.state == 'pending') values.push(['form-state-2', true]);
                else if (data?.data?.state == 'active') values.push(['form-state-3', true]);
                if (data?.data?.correctAnswer == '1') values.push(['form-correct-1', true]);
                else if (data?.data?.correctAnswer == '2') values.push(['form-correct-2', true]);
                else if (data?.data?.correctAnswer == '3') values.push(['form-correct-3', true]);
                else if (data?.data?.correctAnswer == '4') values.push(['form-correct-4', true]);
                DisplayModel('dialog[data-type="edit"]', values, {
                    closeAll: true
                });
                document.querySelector('dialog[data-type="edit"] button[type="submit"]').innerHTML = "Edit";
            }
        }).catch(error => {
            if (error === null || error === '') error = "An Unknown Error Occurred";
            console.error(error);
            DisplayModel('#popup', [
                ['popup-title', "Error"],
                ['popup-msg', error]
            ], {
                class: "error"
            });
        });
    }


    function DisplayModel(id, data = [], options) {
        if (id == null) return;
        const modal = document.querySelector(id);
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
            } else if (input.tagName === "SELECT") {
                // If it doesn't have a value, just put an empty string
                input.value = row[1] ?? '';
                input.querySelectorAll('option')?.forEach(option => {
                    if (option.value == row[1]) option.selected = true;
                })
                // input.querySelector(`option[value='${row[1]}']`).selected = true;
            } else if (input.tagName === "INPUT") {
                // If it doesn't have a value, just put an empty string
                input.value = row[1] ?? '';
            } else if (input.tagName === "TEXTAREA") {
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
