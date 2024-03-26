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

    function PopulateTable(tableID, page) {
        if (page == null) return;
        const section = document.getElementById(tableID);
        if (section == null) return;
        const loader = section.querySelector('.lds-ring');
        loader.classList.remove('hidden');
        const output = section.querySelector('.table table tbody');
        const limit = document.getElementById('user-management-perpage').value;
        // -1 because page 1 starts at offset of 0s
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
                if (btn.dataset.id >= value - 2 && btn.dataset.id <= value + 2) btn.classList.replace('hidden', 'inactive');
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
                fetch('/php/acceptstudent.php', {
                    method: "POST",
                    body: new FormData(e.target),
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
        document.querySelectorAll('#student-management .table tr')?.forEach(tableRow => {
            if (tableRow.classList.contains('events-listening') != false) return;
            tableRow.querySelector('.icons .table-edit-btn')?.addEventListener('click', () => {
                ModifyUser(tableRow?.dataset?.userid, "/php/getstudentdata.php");
            });
            tableRow.querySelector('.icons .table-delete-btn')?.addEventListener('click', () => {
                DeleteUser(tableRow?.dataset?.userid, "/php/deletestudent.php");
            });
            tableRow.querySelector('.icons .table-promote-btn')?.addEventListener('click', () => {
                PromoteUser(tableRow?.dataset?.userid, "/php/promotestudent.php");
            });
            tableRow.classList.add('events-listening');
        });
        document.querySelectorAll('#lecturer-management .table tr')?.forEach(tableRow => {
            if (tableRow.classList.contains('events-listening') != false) return;
            tableRow.querySelector('.icons .table-edit-btn')?.addEventListener('click', () => {
                ModifyUser(tableRow?.dataset?.userid, "/php/getlecturerdata.php");
            });
            tableRow.querySelector('.icons .table-delete-btn')?.addEventListener('click', () => {
                DeleteUser(tableRow?.dataset?.userid, "/php/deletelecturer.php");
            });
            tableRow.querySelector('.icons .table-demote-btn')?.addEventListener('click', () => {
                DemoteUser(tableRow?.dataset?.userid, "/php/demotelecturer.php");
            });
            tableRow.classList.add('events-listening');
        });
        document.querySelectorAll('#quiz-management .table tr')?.forEach(tableRow => {
            if (tableRow.classList.contains('events-listening') != false) return;
            tableRow.querySelector('.icons .table-edit-btn')?.addEventListener('click', () => {
                ModifyQuiz(tableRow?.dataset?.quizid, "/php/getquizdata.php");
            });
            tableRow.querySelector('.icons .table-delete-btn')?.addEventListener('click', () => {
                DeleteQuiz(tableRow?.dataset?.quizid, "/php/deletequiz.php");
            });
            tableRow.querySelector('.icons .table-assignquiz-btn')?.addEventListener('click', () => {
                DisplayModel('dialog-assign-quiz', [
                    ['form-assignQuizID', tableRow?.dataset?.quizid]
                ], {
                    closeAll: true
                });
            });
            tableRow.classList.add('events-listening');
        });
        document.querySelectorAll('#question-management .table tr')?.forEach(tableRow => {
            if (tableRow.classList.contains('events-listening') != false) return;
            tableRow.querySelector('.icons .table-edit-btn')?.addEventListener('click', () => {
                ModifyQuestion(tableRow?.dataset?.questionid, "/php/getquestiondata.php");
            });
            tableRow.querySelector('.icons .table-delete-btn')?.addEventListener('click', () => {
                DeleteQuestion(tableRow?.dataset?.questionid, "/php/deletequestion.php");
            });
            tableRow.querySelector('.icons .table-linkquiz-btn')?.addEventListener('click', () => {
                DisplayModel('dialog-link-question', [
                    ['form-linkQuestion', tableRow.querySelector('td').innerText],
                    ['form-linkQuestionID', tableRow?.dataset?.questionid]
                ], {
                    closeAll: true
                });
            });
            tableRow.querySelector('.icons .table-deletelinkquiz-btn')?.addEventListener('click', () => {
                if (!confirm("Are you sure you wish to delete this link?")) return;
                let urlparams = new URLSearchParams(location.search);
                SimpleForm([
                    ["quizID", urlparams.get('quiz')],
                    ["questionID", tableRow?.dataset?.questionid]
                ], "/php/deletequizquestionlink.php");
            });
            tableRow.classList.add('events-listening');
        });
        document.querySelectorAll('#subject-management .table tr')?.forEach(tableRow => {
            if (tableRow.classList.contains('events-listening') != false) return;
            tableRow.querySelector('.icons .table-edit-btn')?.addEventListener('click', () => {
                ModifySubject(tableRow?.dataset?.subjectid, "/php/getsubjectdata.php");
            });
            tableRow.classList.add('events-listening');
        });
        const editStudentForm = document.querySelector('#student-management + #dialog-edit-user form');
        if (editStudentForm?.classList.contains('events-listening') === false) {
            editStudentForm.addEventListener('submit', (e) => {
                e.preventDefault();
                ModifyUser(null, "/php/editstudent.php");
            });
            editStudentForm.classList.add('events-listening');
        }
        const editLecturerForm = document.querySelector('#lecturer-management + #dialog-edit-user form');
        if (editLecturerForm?.classList.contains('events-listening') === false) {
            editLecturerForm.addEventListener('submit', (e) => {
                e.preventDefault();
                ModifyUser(null, "/php/editlecturer.php");
            });
            editLecturerForm.classList.add('events-listening');
        }
        const editQuizForm = document.querySelector('#quiz-management + #dialog-edit-quiz form');
        if (editQuizForm?.classList.contains('events-listening') === false) {
            editQuizForm.addEventListener('submit', (e) => {
                e.preventDefault();
                if (editQuizForm.querySelector('input#form-quizID').value != '')
                    ModifyQuiz(null, "/php/editquiz.php");
                else
                    ModifyQuiz(null, "/php/createquiz.php");
            });
            editQuizForm.classList.add('events-listening');
        }
        const editQuestionForm = document.querySelector('#question-management + #dialog-edit-question form');
        if (editQuestionForm?.classList.contains('events-listening') === false) {
            editQuestionForm.addEventListener('submit', (e) => {
                e.preventDefault();
                if (editQuestionForm.querySelector('input#form-questionID').value != '')
                    ModifyQuestion(null, "/php/editquestion.php");
                else
                    ModifyQuestion(null, "/php/createquestion.php");
            });
            editQuestionForm.classList.add('events-listening');
        }
        const editSubjectForm = document.querySelector('#subject-management + #dialog-edit-subject form');
        if (editSubjectForm?.classList.contains('events-listening') === false) {
            editSubjectForm.addEventListener('submit', (e) => {
                e.preventDefault();
                if (editSubjectForm.querySelector('input#form-subjectID').value != '')
                    ModifySubject(null, "/php/editsubject.php");
                else
                    ModifySubject(null, "/php/createsubject.php");
            });
            editSubjectForm.classList.add('events-listening');
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

        const createQuizBtn = document.querySelector('#quiz-management .table-btns .create');
        if (createQuizBtn?.classList.contains('events-listening') === false) {
            createQuizBtn.addEventListener('click', () => {
                CreateQuiz();
            });
            createQuizBtn.classList.add('events-listening');
        }
        const createQuestionBtn = document.querySelector('#question-management .table-btns .create');
        if (createQuestionBtn?.classList.contains('events-listening') === false) {
            createQuestionBtn.addEventListener('click', () => {
                CreateQuestion();
            });
            createQuestionBtn.classList.add('events-listening');
        }
        const createSubjectBtn = document.querySelector('#subject-management .table-btns .create');
        if (createSubjectBtn?.classList.contains('events-listening') === false) {
            createSubjectBtn.addEventListener('click', () => {
                CreateSubject();
            });
            createSubjectBtn.classList.add('events-listening');
        }


        // Automatiicaly populate table on load
        const studentManagement = document.getElementById('student-management');
        const lecturerManagement = document.getElementById('lecturer-management');
        const quizManagement = document.getElementById('quiz-management');
        const questionManagement = document.getElementById('question-management');
        const subjectManagement = document.getElementById('subject-management');

        if (studentManagement != null && studentManagement.classList.contains('loaded') == false) {
            PopulateTable('student-management', '/php/loadstudenttable.php');
            studentManagement.classList.add('loaded');
        } else if (lecturerManagement != null && lecturerManagement.classList.contains('loaded') == false) {
            PopulateTable('lecturer-management', '/php/loadlecturertable.php');
            lecturerManagement.classList.add('loaded');
        } else if (quizManagement != null && quizManagement.classList.contains('loaded') == false) {
            PopulateTable('quiz-management', '/php/loadquiztable.php');
            quizManagement.classList.add('loaded');
        } else if (questionManagement != null && questionManagement.classList.contains('loaded') == false) {
            PopulateTable('question-management', `/php/loadquestiontable.php${location.search}`);
            questionManagement.classList.add('loaded');
        } else if (subjectManagement != null && subjectManagement.classList.contains('loaded') == false) {
            PopulateTable('subject-management', '/php/loadsubjecttable.php');
            subjectManagement.classList.add('loaded');
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
            document.querySelector('#quiz-management #user-management-perpage')?.addEventListener('change', () => {
                SetPerPage();
                PopulateTable('quiz-management', '/php/loadquiztable.php');
            });
            document.querySelector('#question-management #user-management-perpage')?.addEventListener('change', () => {
                SetPerPage();
                PopulateTable('question-management', '/php/loadquestiontable.php');
            });
            document.querySelector('#subject-management #user-management-perpage')?.addEventListener('change', () => {
                SetPerPage();
                PopulateTable('subject-management', '/php/loadsubjecttable.php');
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
            document.querySelectorAll('#quiz-management #pagination-menu li')?.forEach(btn => {
                btn.addEventListener('click', () => {
                    SetPagination(btn.dataset.id);
                    PopulateTable('quiz-management', '/php/loadquiztable.php');
                });
            });
            document.querySelectorAll('#question-management #pagination-menu li')?.forEach(btn => {
                btn.addEventListener('click', () => {
                    SetPagination(btn.dataset.id);
                    PopulateTable('question-management', '/php/loadquestiontable.php');
                });
            });
            document.querySelectorAll('#subject-management #pagination-menu li')?.forEach(btn => {
                btn.addEventListener('click', () => {
                    SetPagination(btn.dataset.id);
                    PopulateTable('subject-management', '/php/loadsubjecttable.php');
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
            
            document.querySelectorAll('#quiz-management .pagination .arrow')[0]?.addEventListener('click', () => {
                SetPagination(+document.querySelector('#pagination-menu li.active')?.dataset.id - 1);
                PopulateTable('quiz-management', '/php/loadquiztable.php');
            });
            document.querySelectorAll('#quiz-management .pagination .arrow')[1]?.addEventListener('click', () => {
                SetPagination(+document.querySelector('#pagination-menu li.active')?.dataset.id + 1);
                PopulateTable('quiz-management', '/php/loadquiztable.php');
            });
            
            document.querySelectorAll('#question-management .pagination .arrow')[0]?.addEventListener('click', () => {
                SetPagination(+document.querySelector('#pagination-menu li.active')?.dataset.id - 1);
                PopulateTable('question-management', '/php/loadquestiontable.php');
            });
            document.querySelectorAll('#question-management .pagination .arrow')[1]?.addEventListener('click', () => {
                SetPagination(+document.querySelector('#pagination-menu li.active')?.dataset.id + 1);
                PopulateTable('question-management', '/php/loadquestiontable.php');
            });
            
            document.querySelectorAll('#subject-management .pagination .arrow')[0]?.addEventListener('click', () => {
                SetPagination(+document.querySelector('#pagination-menu li.active')?.dataset.id - 1);
                PopulateTable('subject-management', '/php/loadsubjecttable.php');
            });
            document.querySelectorAll('#subject-management .pagination .arrow')[1]?.addEventListener('click', () => {
                SetPagination(+document.querySelector('#pagination-menu li.active')?.dataset.id + 1);
                PopulateTable('subject-management', '/php/loadsubjecttable.php');
            });

            document.querySelector('.pagination').classList.add('events-listening');
        }


        const linkQuestion = document.getElementById('dialog-link-question');
        if (linkQuestion?.classList.contains('events-listening') === false) {
            linkQuestion.querySelector('form').addEventListener('submit', (e) => {
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
                ], "/php/assignquiz.php");
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
    function PromoteUser(userid, page) {
        if (userid == null || page == null) return;
        if (!confirm("Are you sure you wish to promote this user?")) return;
        SimpleForm([["userID", userid]], page);
    }
    function DemoteUser(userid, page) {
        if (userid == null || page == null) return;
        if (!confirm("Are you sure you wish to demote this user?")) return;
        SimpleForm([["userID", userid]], page);
    }
    function DeleteUser(userid, page) {
        if (userid == null || page == null) return;
        if (!confirm("Are you sure you wish to delete this user?")) return;
        SimpleForm([["userID", userid]], page);
    }
    function DeleteQuiz(quizid, page) {
        if (quizid == null || page == null) return;
        if (!confirm("Are you sure you wish to delete this quiz?")) return;
        SimpleForm([["quizID", quizid]], page);
    }
    function DeleteQuestion(questionid, page) {
        if (questionid == null || page == null) return;
        if (!confirm("Are you sure you wish to delete this question?")) return;
        SimpleForm([["questionID", questionid]], page);
    }
    function ModifyUser(userid, page) {
        if (page == null) return;
        document.querySelectorAll(`#dialog-edit-user *[name]`).forEach(input => {
            input.classList.remove('error');
        });
        document.querySelector('#dialog-edit-user .error-msg').innerHTML = '';
        const formData = new FormData(document.querySelector('#dialog-edit-user form'));
        if (userid != null) formData.append('userID', userid);
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
                    document.querySelector(`#dialog-edit-user *[name="${data.input}"]`).classList.add('error');
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
                    ['form-userID', data?.data?.userID],
                    ['form-firstname', data?.data?.firstname],
                    ['form-lastname', data?.data?.lastname],
                    ['form-studentID', data?.data?.studentID],
                    ['form-email', data?.data?.email],
                    [stateID, true],
                ], {
                    closeAll: true
                });
                document.querySelector('#dialog-edit-user button[type="submit"]').innerHTML = "Edit";
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
    function ModifyQuiz(quizid, page) {
        if (page == null) return;
        document.querySelectorAll(`#dialog-edit-quiz *[name]`).forEach(input => {
            input.classList.remove('error');
        });
        document.querySelector('#dialog-edit-quiz .error-msg').innerHTML = '';
        const formData = new FormData(document.querySelector('#dialog-edit-quiz form'));
        if (quizid != null) formData.append('quizID', quizid);
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
                    document.querySelector(`#dialog-edit-quiz *[name="${data.input}"]`).classList.add('error');
                    document.querySelector('#dialog-edit-quiz .error-msg').innerHTML = data.msg;
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
                DisplayModel('dialog-edit-quiz', [
                    ['form-quizID', data?.data?.quizID],
                    ['form-title', data?.data?.title],
                    ['form-subject', data?.data?.subjectID],
                    ['form-available', data?.data?.available],
                ], {
                    closeAll: true
                });
                document.querySelector('#dialog-edit-quiz button[type="submit"]').innerHTML = "Edit";
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
    function ModifyQuestion(questionid, page) {
        if (page == null) return;
        document.querySelectorAll(`#dialog-edit-question *[name]`).forEach(input => {
            input.classList.remove('error');
        });
        document.querySelector('#dialog-edit-question .error-msg').innerHTML = '';
        const formData = new FormData(document.querySelector('#dialog-edit-question form'));
        if (questionid != null) formData.append('questionID', questionid);
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
                    document.querySelector(`#dialog-edit-question *[name="${data.input}"]`).classList.add('error');
                    document.querySelector('#dialog-edit-question .error-msg').innerHTML = data.msg;
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
                let correct = "form-correct-";
                if (data?.data?.correctAnswer == '1') correct += '1';
                else if (data?.data?.correctAnswer == '2') correct += '2';
                else if (data?.data?.correctAnswer == '3') correct += '3';
                else if (data?.data?.correctAnswer == '4') correct += '4';
                DisplayModel('dialog-edit-question', [
                    ['form-questionID', data?.data?.questionID],
                    ['form-question', data?.data?.question],
                    ['form-subject', data?.data?.subjectID],
                    [correct, true],
                    ['form-answerOne', data?.data?.answerOne],
                    ['form-answerTwo', data?.data?.answerTwo],
                    ['form-answerThree', data?.data?.answerThree],
                    ['form-answerFour', data?.data?.answerFour],
                ], {
                    closeAll: true
                });
                document.querySelector('#dialog-edit-question button[type="submit"]').innerHTML = "Edit";
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
    function ModifySubject(subjectid, page) {
        if (page == null) return;
        document.querySelectorAll(`#dialog-edit-subject *[name]`).forEach(input => {
            input.classList.remove('error');
        });
        document.querySelector('#dialog-edit-subject .error-msg').innerHTML = '';
        const formData = new FormData(document.querySelector('#dialog-edit-subject form'));
        if (subjectid != null) formData.append('subjectID', subjectid);
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
                    document.querySelector(`#dialog-edit-subject *[name="${data.input}"]`).classList.add('error');
                    document.querySelector('#dialog-edit-subject .error-msg').innerHTML = data.msg;
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
                DisplayModel('dialog-edit-subject', [
                    ['form-subjectID', data?.data?.subjectID],
                    ['form-name', data?.data?.name],
                ], {
                    closeAll: true
                });
                document.querySelector('#dialog-edit-subject button[type="submit"]').innerHTML = "Edit";
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

    function CreateQuiz() {
        DisplayModel('dialog-edit-quiz', [], {
            closeAll: true
        });
        document.querySelector('#dialog-edit-quiz button[type="submit"]').innerHTML = "Create";
    }
    function CreateQuestion() {
        DisplayModel('dialog-edit-question', [], {
            closeAll: true
        });
        document.querySelector('#dialog-edit-question button[type="submit"]').innerHTML = "Create";
    }
    function CreateSubject() {
        DisplayModel('dialog-edit-subject', [], {
            closeAll: true
        });
        document.querySelector('#dialog-edit-subject button[type="submit"]').innerHTML = "Create";
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